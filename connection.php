<?php

class Connection {
    private static $connection = null;
    private static $host = 'localhost';
    private static $port = 3306;
    private static $user = 'root';
    private static $pass = '--';
    private static $db = '--';

    public static function setupConnection(){
        if (!isset(self::$connection)) {
            try {
                $dsn = "mysql:host=" . self::$host . 
                       ";port=" . self::$port . 
                       ";dbname=" . self::$db . 
                       ";sslmode=REQUIRED";
                
                self::$connection = new PDO($dsn, self::$user, self::$pass, array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true
                ));
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function iud($q){
        $connection = self::setupConnection();
        return $connection->exec($q);
    }

    public static function search($q){
        $connection = self::setupConnection();
        return $connection->query($q);
    }
}
