<?php
require_once CLASS_DIR . 'datastore/Datastore.class.php';

class PostgresqlDatastore {
    private $conn;

    public function connect() {
        if (defined('DB_SERVER') 
            && defined('DB_PORT') 
            && defined('DB_DATABASE') 
            && defined('DB_USERNAME') 
            && defined('DB_PASSWORD')
            && defined('DB_OPTS')) {
            $conn = pg_connect('host=' . DB_SERVER . ' port=' . DB_PORT . ' dbname=' . DB_DATABASE . ' user=' . DB_USERNAME . ' password=' . DB_PASSWORD . ' ' . DB_OPTS);
            if (! $conn) {
                throw new DataStoreException('Could not connect to Postgresql');
            }
            $this->conn = $conn;
        } else {
            //throw new DataStoreException('Database variables not found');
        }
    }

    public function getConnection() {
        if (null == $this->conn) {
            $this->connect();
        }
        return $this->conn;
    }

    public function isReady() {
        if ($this->conn) {
            $query = 'SELECT true FROM pg_tables WHERE tablename = \'vossen\'';
            $result = pg_query($this->conn, $query);
            
            // In this case, the whole thing is messed up!
            if (! $result) {
                return false;
            }
            
            return (bool) pg_fetch_row($result);
            }
        return false;
    }
}
?>