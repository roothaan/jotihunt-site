<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireSuperAdmin();

$db = new DatabaseDriverPostgresql();
$ds = Datastore::getDatastore();

$secretCode = 'hathiislief';
$customQueryUsed = false;
$customQuery = '';

if(isset($_POST['query']) && !empty($_POST['query']) && isset($_POST['code']) && $_POST['code'] == $secretCode) {
    $customQueryUsed = true;
    $customQuery = $_POST['query'];
    $resultcustom = pg_query($ds->getConnection(), $customQuery);
    $first = true;
    echo "<h1>Custom</h1>";
    echo "<table border='1'>";
    while ( $rowcustom = pg_fetch_assoc($resultcustom) ) {
        if($first) {
            echo "<tr style='font-weight:bold;'>";
            foreach($rowcustom as $key => $value) {
                echo "<td>".$key."</td>";
            }
            echo "</tr>";
            $first = false;
        }
        
        echo "<tr>";
        foreach($rowcustom as $key => $value) {
            echo "<td>".$value."</td>";
        }
        echo "</tr>";
        
    }
    echo "</table><br /><br /><br />";
} ?>

<form method="post">
    Denk aan de geheime code!<br />
    <input name="code" type="password" value="<?= $customQueryUsed ? $secretCode : '' ?>" /><br />
    <textarea name="query" placeholder="Voer hier je query in" cols="50" rows="20"><?= $customQuery ?></textarea><br />
    <input type="submit" value="execute" />
</form>
<br />
<br />

<?php
function printTable($tableName, $ds, $expand) {
    if(strpos($tableName, "sql_") === false && strpos($tableName, "pg_") === false) {
        
        echo "<h1>".$tableName."</h1>";

        if ($expand) {
        $first = true;
        $tableSqlQuery = "SELECT * FROM ".$tableName;
        $result_table = pg_query($ds->getConnection(), $tableSqlQuery);
        while ( $row_table = pg_fetch_assoc($result_table) ) {
            
            if($first) {
                echo "<table border='1'>";
                echo "<tr style='font-weight:bold;'>";
                foreach($row_table as $key => $value) {
                    echo "<td>".$key."</td>";
                }
                echo "</tr>";
                $first = false;
            }
            
            echo "<tr>";
            foreach($row_table as $key => $value) {
                echo "<td>".substr($value, 0, 1000)."</td>";
            }
            echo "</tr>";
        }
        // If there are no results, $first is still true...
        if (!$first) {
            echo "</table><br /><br /><br />";
        } else {
            echo '<em>No data in table</em>';
        }
        }
    }
}
if (!$customQueryUsed) {
    $sqlQuery = "SELECT * FROM pg_catalog.pg_tables";
    $customTable = null;
    $expand = false;
    if (isset($_GET['table'])) {
        $customTable = $_GET['table'];
    }
    if (isset($_GET['expand'])) {
        $expand = true;
    }
    
    if ($customTable) {
        printTable($customTable, $ds, true);
    } else {
        $result = pg_query($ds->getConnection(), $sqlQuery);
        
        while ( $row = pg_fetch_row($result) ) {
            $tableName = $row[1];
            printTable($tableName, $ds, $expand);
        }
    }
}