<?php
require_once '../init.php';
$authMgr->requireSuperAdmin();

$db = new DatabaseDriverPostgresql();
$ds = Datastore::getDatastore();


//$sqlQuery = "DROP TABLE user_group";
//pg_query($ds->getConnection(), $sqlQuery);

/*$sqlQuery = "ALTER TABLE hunts ADD goedgekeurd INT";
pg_query($ds->getConnection(), $sqlQuery);*/

// $sqlQuery = "ALTER TABLE bericht DROP COLUMN datum";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = "ALTER TABLE bericht DROP COLUMN eindtijd";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = "ALTER TABLE bericht DROP COLUMN lastupdate";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = "ALTER TABLE bericht ADD COLUMN datum timestamp";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = "ALTER TABLE bericht ADD COLUMN eindtijd timestamp";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = "ALTER TABLE bericht ADD COLUMN lastupdate timestamp";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = "ALTER TABLE bericht DROP COLUMN organisation_id";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = "ALTER TABLE vossentracker ADD COLUMN organisation_id int4";
// pg_query($ds->getConnection(), $sqlQuery);

// $sqlQuery = 'ALTER TABLE "vossentracker" ADD CONSTRAINT "Ref_vossentracker_to_organisation" FOREIGN KEY ("organisation_id")
// 	REFERENCES "organisation"("id")
// 	MATCH SIMPLE
// 	ON DELETE NO ACTION
// 	ON UPDATE NO ACTION
// 	NOT DEFERRABLE;';
// pg_query($ds->getConnection(), $sqlQuery);

//$sqlQuery = "INSERT INTO events_has_organisation(events_id, organisation_id) VALUES (8, 2)";
//pg_query($ds->getConnection(), $sqlQuery);

echo 'Done';