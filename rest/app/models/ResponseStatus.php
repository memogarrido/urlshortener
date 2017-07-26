<?php
/**
 * Parent class that describe a general response from the service.
 */
class ResponseStatus {

    /**
     * Later on could mean type of error constant with different options for now is just -1 on error or 0 on success
     * @var int
     */
    public $status;
    /**
     * Error message
     * @var string
     */
    public $message;
    

    function getStatus() {
        return $this->status;
    }

    function getMessage() {
        return $this->message;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setMessage($message) {
        $this->message = $message;
    }

}
