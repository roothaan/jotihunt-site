<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireSuperAdmin();

$db = new DatabaseDriverPostgresql();
$ds = Datastore::getDatastore();

if(isset($_POST['query']) && !empty($_POST['query']) && isset($_POST['code']) && $_POST['code'] == "hathiislief") {
    $resultcustom = pg_query($ds->getConnection(), $_POST['query']);
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
    <input name="code" type="password" /><br />
    <textarea name="query" placeholder="Voer hier je query in" cols="50" rows="20"></textarea><br />
    <input type="submit" value="execute" />
</form>
<br />
<br />

<?php
$sqlQuery = "SELECT * FROM pg_catalog.pg_tables";

$result = pg_query($ds->getConnection(), $sqlQuery);

while ( $row = pg_fetch_row($result) ) {
    if(strpos($row[1], "sql_") === false && strpos($row[1], "pg_") === false) {
        $tableSqlQuery = "SELECT * FROM ".$row[1];

        $result_table = pg_query($ds->getConnection(), $tableSqlQuery);
        
        $first = true;
        echo "<h1>".$row[1]."</h1>";
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