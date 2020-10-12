<?php
if (!defined('__ROOT__')) {
    define('__ROOT__', dirname(dirname(__FILE__)));
}

include_once(__ROOT__."/config/config.php");

class SQLiteConnection {

    /**
     * PDO instance
     * @var type 
    */
    private $pdo;

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect() {
        if ($this->pdo == null) {
            $this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_DB);
        }
        return $this->pdo;
    }
}

?>