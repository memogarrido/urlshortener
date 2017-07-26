<?php
/**
 * Parent class to inherit DB models being able to query the database.
 */
class DatabaseEntity {

    private $connection;

    function __construct() {
        try {
            $this->connection = new PDO('mysql:host=localhost:3306;dbname=urlshortener;charset=utf8', 'urlshortenerusr', '1046565124');
        } catch (Exception $e) {
            throw new Exception("An error ocurred when connecting to db" . $e->getMessage());
        }
    }

    function getConnection() {
        return $this->connection;
    }

    function setConnection($connection) {
        $this->connection = $connection;
    }

}
