<?php

/**
 * Class to represent a link pair (hash & url)
 */
class Link {

    /**
     * Variable that stores a unique hash for a given URL [a-zA-Z0-0]
     * @var string 
     */
    private $hash;

    /**
     * Original URL, is the destination URL when a link is fetched with a hash
     * @var string
     */
    private $urlOrig;

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

}
