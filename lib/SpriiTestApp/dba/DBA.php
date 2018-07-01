<?php

namespace SpriiTestApp\dba;

class DBA {

    protected $dbh;
    protected $config;
    private static $instance;

    /**
     * 
     * @return DBA
     */
    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    function getConfig() {
        return $this->config;
    }

    function setConfig(\SimpleXMLElement $config) {
        $this->config = $config;
    }

    public function getDbh() {
        if (!($this->dbh instanceof \PDO)) {
            $dsn = "mysql://host={$this->config->db->host};port={$this->config->db->port};dbname={$this->config->db->dbname};";
            $this->dbh = new \PDO($dsn, $this->config->db->username, $this->config->db->password);
        }
        return $this->dbh;
    }

    public function setDbh(\PDO $dbh) {
        $this->dbh = $dbh;
    }

    public function fetchAll($sql, $class) {
        $stmt = $this->getDbh()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    public function fetchColumn($sql, array $params = array()) {
        $stmt = $this->getDbh()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function execute($sql, array $params = array()) {
        $stmt = $this->getDbh()->prepare($sql);
        $result = $stmt->execute($params);
        return $result;
    }

    public function fetchObject($sql, $key, $class) {
        $stmt = $this->getDbh()->prepare($sql);
        $stmt->execute(array($key));
        return $stmt->fetchObject($class);
    }

    private function __construct() {
        ;
    }

}
