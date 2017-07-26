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
    private $creationDate;

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

    /**
     * Function to know size of the links table to have the next ID integer to calculate a hash
     * @return returns hash string
     */
    function calculateNexHash() {
        $stmt = parent::getConnection()->prepare("SELECT count(*)+1 FROM links");
        $count = 0;
        if ($stmt->execute()) {
            $count = $stmt->fetch()[0];
        }
        return IntegerHash::encode($count);
    }

    /**
     * function to insert a new link pair into the database
     * @return \ResponseLinkHash
     */
    function insertLink() {
        $response = new ResponseLinkHash();

        $this->setHash($this->calculateNexHash());
        $sql = "INSERT INTO links ( `hash`, `url_orig`, `creation_date`) VALUES (:hash,:url, NOW());";

        try {
            $stmt = parent::getConnection()->prepare($sql);

            $stmt->bindParam(':hash', $this->getHash());
            $stmt->bindParam(':url', $this->getUrlOrig());
            if ($stmt->execute()) {
                $response->setStatus(0);
                $response->setLink($this);
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
