<?php
require_once CLASS_DIR . 'datastore/postgresql/PostgresqlDatastore.class.php';
require_once CLASS_DIR . 'datastore/postgresql/PostgresqlQueries.class.php';
require_once CLASS_DIR . 'datastore/postgresql/DatabaseDriver.class.php';
require_once CLASS_DIR . 'datastore/postgresql/SiteDriver.postgresql.class.php';

abstract class Datastore {
    private static $datastore;
    private static $siteDriver;
    private static $databaseDriver;

    public static function getDatastore() {
        if (null == self::$datastore) {
            switch (DB_TYPE) {
                case 'postgresql' :
                    $datastore = new PostgresqlDatastore();
                    $datastore->connect();
                    
                    $ready = $datastore->isReady();
                    if ($ready) {
                        $queries = new PostgresqlQueries();
                        $queries->setConn($datastore->getConnection());
                        $queries->prepare();
                    }
                    
                    self::$datastore = $datastore;
                break;
            }
        }
        return self::$datastore;
    }

    public static function getSiteDriver() {
        if (null == self::$siteDriver) {
            switch (DB_TYPE) {
                case 'postgresql' :
                    $siteDriver = new SiteDriverPostgresql();
                    self::$siteDriver = $siteDriver;
                break;
            }
        }
        return self::$siteDriver;
    }

    public static function getDatabaseDriver() {
        if (null == self::$databaseDriver) {
            switch (DB_TYPE) {
                case 'postgresql' :
                    $databaseDriver = new DatabaseDriverPostgresql();
                    $databaseDriver->setConn(Datastore::getDatastore()->getConnection());
                    self::$databaseDriver = $databaseDriver;
                break;
            }
        }
        return self::$databaseDriver;
    }
}
?>