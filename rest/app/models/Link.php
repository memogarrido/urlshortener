<?php

/**
 * Class to represent a link pair (hash & url)
 */
class Link extends DatabaseEntity {

    /**
     * Variable that stores a unique hash for a given URL [a-zA-Z0-0]
     * @var string 
     */
    public $hash;

    /**
     * Original URL, is the destination URL when a link is fetched with a hash
     * @var string
     */
    public $urlOrig;

    /**
     * Creation Date of the link
     * @var Date
     */
    public $creationDate;

    function getHash() {
        return $this->hash;
    }

    function getUrlOrig() {
        return $this->urlOrig;
    }

    function getCreationDate() {
        return $this->creationDate;
    }

    function setHash($hash) {
        $this->hash = $hash;
    }

    function setUrlOrig($urlOrig) {
        $this->urlOrig = $urlOrig;
    }

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function isHashAvailable($hash) {
        $stmt = parent::getConnection()->prepare("SELECT url_orig FROM links WHERE hash=:hash");
        $stmt->bindParam(':hash', $hash);
        /* $hash != "link" avoidng a hash that uses out REST
         * also could be solved just by removing l from the alphabet. 
         * or in the future != set of words, 
         * or putting the link in our database
         */
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 0 && $hash != "link") {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception("Error trying to get availability");
        }
    }

    /**
     * Function to know size of the links table to have the next ID integer to calculate a hash
     * @return returns hash string
     */
    function calculateNexHash() { //this can be improved knowing the size of the actual link table
        $range = [0, 62, 3844, 238328, 14776336, 916132832];
        //          1   2     3       4        5 =15
        $uniqueHashFounded = false;
        $i = 0;
        $attemts = 0;
        $hash = "";
        while (!$uniqueHashFounded && $i < 4) {
            $uniqueId = rand($range[$i], $range[$i + 1]);
            $hash = IntegerHash::encode($uniqueId);
            $attemts++; //1
            if ($this->isHashAvailable($hash)) {
                $uniqueHashFounded = true;
            } else if ($attemts == $i) {
                $attemts = 0;
                $i++;
            }
        }
        return $hash;
    }

    /**
     * function to insert a new link pair into the database
     * @return \ResponseLinkHash
     */
    function insertLink() {
        $response = new ResponseLinkHash();
        if (empty($this->getHash())) {

            $this->setHash($this->calculateNexHash());
        }
        if (!ctype_alnum($this->getHash())) {
            $response->setStatus(-1);
            $response->setLink(null);
            $response->setMessage("Not a valid tag");
            return $response;
        } else if (!$this->isHashAvailable($this->getHash())) {
            $response->setStatus(-1);
            $response->setLink(null);
            $response->setMessage("URL already taken");
            return $response;
        }
        $sql = "INSERT INTO links ( `hash`, `url_orig`, `creation_date`) VALUES (:hash,:url, NOW());";

        try {
            $stmt = parent::getConnection()->prepare($sql);
            $stmt->bindParam(':hash', $this->getHash());
            $stmt->bindParam(':url', $this->getUrlOrig());
            if ($stmt->execute()) {
                $response->setStatus(0);
                $response->setLink($this);
            } else {
                $response->setStatus(-1);
                $response->setLink(null);
                $response->setMessage('excecute failed' . json_encode($stmt->errorInfo()));
            }
        } catch (Exception $e) {
            $response->setStatus(-1);
            $response->setLink(null);
            $response->setMessage($e->getMessage());
        }
        return $response;
    }

    /**
     * function to fetch url_orig from a given hash
     * @return \ResponseLinkHash
     */
    function fetchDestinationURL() {
        $response = new ResponseLinkHash();
        $sql = "SELECT url_orig FROM links WHERE hash=:hash";
        try {
            $stmt = parent::getConnection()->prepare($sql);
            $stmt->bindParam(':hash', $this->getHash());
            if ($stmt->execute()) {
                $urlDestino = $stmt->fetch()[0];
                if (isset($urlDestino) && !empty($urlDestino)) {
                    $this->setUrlOrig($urlDestino);
                    $response->setStatus(0);
                    $response->setLink($this);
                } else {
                    $response->setStatus(-1);
                    $response->setLink(null);
                    $response->setMessage('Hash no encontrado');
                }
            } else {
                $response->setStatus(-1);
                $response->setLink(null);
                $response->setMessage('excecute failed');
            }
        } catch (Exception $e) {
            $response->setStatus(-1);
            $response->setMessage($e->getMessage());
        }
        return $response;
    }

    /**
     * function to fetch all stored links
     * @return \ResponseLinkHash
     */
    function getURLs($offsetParam) {
        $offset = 0;
        if (filter_var($offsetParam, FILTER_VALIDATE_INT)) {
            $offset = $offsetParam;
        }

        $response = new ResponseLinks();
        if ($offset >= 0) {
            $sql = "SELECT url_orig, hash, creation_date FROM links order by creation_date desc limit  100 offset :offset";
            try {
                $stmt = parent::getConnection()->prepare($sql);
                $stmt->bindParam(':offset', intval($offset), PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $lstLinks = [];
                    while ($row = $stmt->fetch()) {
                        $objLink = new Link();
                        $objLink->setCreationDate($row["creation_date"]);
                        $objLink->setHash($row["hash"]);
                        $objLink->setUrlOrig($row["url_orig"]);
                        array_push($lstLinks, $objLink);
                    }
                    if (sizeof($lstLinks) > 0) {
                        $response->setStatus(0);
                        $response->setLinks($lstLinks);
                    } else {
                        $response->setStatus(-1);
                        $response->setLinks(null);
                        $response->setMessage('sin resultados');
                    }
                } else {
                    $response->setStatus(-1);
                    $response->setLinks(null);
                    $response->setMessage('excecute failed ' . json_encode($stmt->errorInfo()));
                }
            } catch (Exception $e) {
                $response->setStatus(-1);
                $response->setMessage($e->getMessage());
            }
        } else {
            $response->setStatus(-1);
            $response->setMessage("Offset needs to be greater or equal to 0");
        }

        return $response;
    }

}

/**
 * Class to respond properly to the REST service
 */
class ResponseLinkHash extends ResponseStatus {

    public $link;

    function getLink() {
        return $this->link;
    }

    function setLink($link) {
        $this->link = $link;
    }

}

/**
 * Class to respond properly to the REST service
 */
class ResponseLinks extends ResponseStatus {

    public $links;

    function getLinks() {
        return $this->links;
    }

    function setLinks($links) {
        $this->links = $links;
    }

}
