<?php
require_once CLASS_DIR . 'user/AuthMgr.class.php';

class DatabaseDriverPostgresql {
    private $conn;

    public function setConn($conn) {
        $this->conn = $conn;
    }
    
    public function resetDb($newAdminPw) {
        if (!$newAdminPw) {
            return false;
        }
        return $this->initDb($newAdminPw);
    }
    
    private function initDb($newAdminPw) {
        // Read file resources/db_setup.sql
        $sqlQuery = file_get_contents(ROOT_DIR . 'resources/db_setup.sql');
        pg_query($this->conn, $sqlQuery);
        $this->initAdminUserData($newAdminPw);
        return true;
    }

    private function initAdminUserData($newAdminPw) {
        // User: admin:$newAdminPw (all rights)
        $pw_hash = AuthMgr::getHash($newAdminPw);
        $sqlQuery = 'INSERT INTO _user (username, displayname, pw_hash) VALUES (\'admin\', \'Super admin\', \'' . $pw_hash . '\')';
        $result = pg_query($this->conn, $sqlQuery);

        // Group: admin
        $sqlQuery = 'INSERT INTO _group (name) VALUES (\'admin\')';
        $result = pg_query($this->conn, $sqlQuery);
        
        // Group: basic
        $sqlQuery = 'INSERT INTO _group (name) VALUES (\'basic\')';
        $result = pg_query($this->conn, $sqlQuery);
    }
}
?>