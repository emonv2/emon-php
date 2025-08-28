<?php
class database
{
    // Your mysql host
    private $hostdb = "localhost";
    // Your mysql username
    private $userdb = "root";
    // Your mysql password
    private $passdb = "eatuany";
    // Your mysql bd name
    private $namedb = "ads_server";
    public $pdo;

    function __construct()
    {
        if (!isset($this->pdo)) {
            try {
                $link = new PDO("mysql:host=" . $this->hostdb . ";dbname=" . $this->namedb, $this->userdb, $this->passdb);
                $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $link->exec("SET CHARACTER SET utf8");
                $this->pdo = $link;
            } catch (PDOException $e) {
                die("Fail to connect" . $e->getMessage());
            }
        }
    }
}
