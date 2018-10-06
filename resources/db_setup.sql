-- =============================================================================
-- Diagram Name: Noname1
-- Created on: 19-7-2016 17:08:21
-- Diagram Version: 
-- =============================================================================

DROP TABLE IF EXISTS "_group" CASCADE;

CREATE TABLE "_group" (
	"id" SERIAL NOT NULL,
	"name" varchar(254),
	CONSTRAINT "_group_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "_user" CASCADE;

CREATE TABLE "_user" (
	"id" SERIAL NOT NULL,
	"username" varchar(254) NOT NULL,
	"displayname" varchar(254),
	"pw_hash" varchar(254),
	CONSTRAINT "_user_pkey" PRIMARY KEY("id"),
	CONSTRAINT "_username_unique" UNIQUE("username")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "bericht" CASCADE;

CREATE TABLE "bericht" (
	"id" SERIAL NOT NULL,
	"event_id" int4,
	"bericht_id" varchar(50),
	"titel" text,
	"datum" timestamp,
	"eindtijd" timestamp,
	"maxpunten" int4,
	"inhoud" text,
	"lastupdate" timestamp,
	"type" varchar(20),
	CONSTRAINT "bericht_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "opzieners" CASCADE;

CREATE TABLE "opzieners" (
	"id" SERIAL NOT NULL,
	"user_id" int4,
	"deelgebied_id" int4,
	"type" int4,
	CONSTRAINT "opzieners_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "gcm" CASCADE;

CREATE TABLE "gcm" (
	"id" SERIAL NOT NULL,
	"gcm_id" text,
	"hunter_id" int4,
	"enabled" bool DEFAULT true,
	"time" timestamp,
	CONSTRAINT "gcm_pkey" PRIMARY KEY("id"),
	CONSTRAINT "gcm_gcm_id_hunter_id_key" UNIQUE("gcm_id","hunter_id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "hunter" CASCADE;

CREATE TABLE "hunter" (
	"id" SERIAL NOT NULL,
	"user_id" int4,
	"deelgebied_id" int4,
	"van" timestamp,
	"tot" timestamp,
	"auto" varchar(254),
	CONSTRAINT "hunter_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "huntertracker" CASCADE;

CREATE TABLE "huntertracker" (
	"id" SERIAL NOT NULL,
	"hunter_id" int4 NOT NULL,
	"longitude" float8,
	"latitude" float8,
	"time" timestamp,
	"accuracy" float8,
	"provider" text,
	CONSTRAINT "huntertracker_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "hunts" CASCADE;

CREATE TABLE "hunts" (
	"id" SERIAL NOT NULL,
	"hunter_id" int4,
	"vossentracker_id" int4 NOT NULL,
	"code" varchar(200),
	"goedgekeurd" int4,
	CONSTRAINT "hunts_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "organisation" CASCADE;

CREATE TABLE "organisation" (
	"id" SERIAL NOT NULL,
	"name" varchar(254),
	CONSTRAINT "organisation_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "phonenumbers" CASCADE;

CREATE TABLE "phonenumbers" (
	"id" SERIAL NOT NULL,
	"user_id" int4,
	"phonenumber" text,
	CONSTRAINT "phonenumbers_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "score" CASCADE;

CREATE TABLE "score" (
	"id" SERIAL NOT NULL,
	"event_id" int4,
	"plaats" int4,
	"groep" varchar(254),
	"woonplaats" varchar(254),
	"regio" varchar(1),
	"hunts" int4,
	"tegenhunts" int4,
	"opdrachten" int4,
	"fotoopdrachten" int4,
	"hints" int4,
	"totaal" int4,
	"lastupdate" int4,
	CONSTRAINT "score_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "session" CASCADE;

CREATE TABLE "session" (
	"session_id" varchar(254) NOT NULL,
	"user_id" int4 NOT NULL,
	"organisation_id" int4,
	"event_id" int4
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "user_organisation" CASCADE;

CREATE TABLE "user_organisation" (
	"user_id" int4,
	"organisation_id" int4
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "vossen" CASCADE;

CREATE TABLE "vossen" (
	"id" SERIAL NOT NULL,
	"deelgebied_id" int4,
	"speelhelft_id" int4,
	"name" text,
	"status" text,
	CONSTRAINT "vossen_pkey" PRIMARY KEY("id"),
	CONSTRAINT "vossen_name_key" UNIQUE("name","deelgebied_id","speelhelft_id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "vossentracker" CASCADE;

CREATE TABLE "vossentracker" (
	"id" SERIAL NOT NULL,
	"vossen_id" int4 NOT NULL,
	"organisation_id" int4,
	"longitude" float8,
	"latitude" float8,
	"x" int4,
	"y" int4,
	"adres" text,
	"type" int4 DEFAULT 1,
	"time" timestamp,
	"counterhuntrondje_id" int4,
	CONSTRAINT "vossentracker_pkey" PRIMARY KEY("id")
)
WITH (
	OIDS = False
);

DROP TABLE IF EXISTS "events" CASCADE;

CREATE TABLE "events" (
	"id" SERIAL NOT NULL,
	"name" varchar(254),
	"public" bool DEFAULT False,
	"starttime" timestamp,
	"endtime" timestamp,
	PRIMARY KEY("id")
);

DROP TABLE IF EXISTS "events_has_organisation" CASCADE;

CREATE TABLE "events_has_organisation" (
	"NMID" SERIAL NOT NULL,
	"events_id" int4 NOT NULL,
	"organisation_id" int4 NOT NULL
);

DROP TABLE IF EXISTS "user_has_group" CASCADE;

CREATE TABLE "user_has_group" (
	"NMID" SERIAL NOT NULL,
	"user_id" int4 NOT NULL,
	"group_id" int4 NOT NULL
);

DROP TABLE IF EXISTS "deelgebied" CASCADE;

CREATE TABLE "deelgebied" (
	"id" SERIAL NOT NULL,
	"event_id" int4,
	"name" varchar(254),
	"linecolor" varchar(50),
	"polycolor" varchar(50),
	PRIMARY KEY("id")
);

DROP TABLE IF EXISTS "deelgebied_coord" CASCADE;

CREATE TABLE "deelgebied_coord" (
	"id" SERIAL NOT NULL,
	"deelgebied_id" int4,
	"longitude" float8 NOT NULL,
	"latitude" float8 NOT NULL,
	"order_id" int4 NOT NULL,
	PRIMARY KEY("id")
);

DROP TABLE IF EXISTS "speelhelft" CASCADE;

CREATE TABLE "speelhelft" (
	"id" SERIAL NOT NULL,
	"event_id" int4,
	"starttime" timestamp,
	"endtime" timestamp,
	PRIMARY KEY("id")
);

DROP TABLE IF EXISTS "poi" CASCADE;

CREATE TABLE "poi" (
	"id" SERIAL NOT NULL,
	"event_id" int4,
	"name" varchar(254),
	"data" text,
	"longitude" float8 NOT NULL,
	"latitude" float8 NOT NULL,
	"type" varchar(254),
	PRIMARY KEY("id")
);


DROP TABLE IF EXISTS "poitype" CASCADE;

CREATE TABLE "poitype" (
	"id" SERIAL NOT NULL,
	"event_id" int4,
	"organisation_id" int4,
	"name" varchar(254),
	"onmap" bool DEFAULT True,
	"onapp" bool DEFAULT True,
	"image" text,
	PRIMARY KEY("id")
);

DROP TABLE IF EXISTS "image" CASCADE;

CREATE TABLE "image" (
	"id" SERIAL NOT NULL,
	"data" bytea NOT NULL,
	"name" varchar(254),
	"extension" varchar(5),
	"sha1" varchar(40) NOT NULL,
	"file_size" int4,
	"last_modified" int4,
	PRIMARY KEY("id")
);

DROP TABLE IF EXISTS "counterhunt_rondjes" CASCADE;

CREATE TABLE "counterhunt_rondjes" (
	"id" SERIAL NOT NULL,
	"deelgebied_id" int4,
	"organisation_id" int4,
	"name" varchar(254),
	"active" bool DEFAULT False,
	CONSTRAINT "counterhunt_rondjes_pkey" PRIMARY KEY("id")
);

ALTER TABLE "counterhunt_rondjes" ADD CONSTRAINT "Ref_deelgebied_id_to_deelgebied" FOREIGN KEY ("deelgebied_id")
	REFERENCES "deelgebied"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "counterhunt_rondjes" ADD CONSTRAINT "Ref_organisation_id_to_organisation" FOREIGN KEY ("organisation_id")
	REFERENCES "organisation"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "poi" ADD CONSTRAINT "Ref_event_id_to_event" FOREIGN KEY ("event_id")
	REFERENCES "events"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "bericht" ADD CONSTRAINT "Ref_bericht_to_events" FOREIGN KEY ("event_id")
	REFERENCES "events"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "opzieners" ADD CONSTRAINT "Ref_opzieners_to__user" FOREIGN KEY ("user_id")
	REFERENCES "_user"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "opzieners" ADD CONSTRAINT "Ref_opzieners_to_deelgebied" FOREIGN KEY ("deelgebied_id")
	REFERENCES "deelgebied"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "gcm" ADD CONSTRAINT "gcm_hunter_id_fkey" FOREIGN KEY ("hunter_id")
	REFERENCES "hunter"("id")
	MATCH SIMPLE
	ON DELETE CASCADE
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "hunter" ADD CONSTRAINT "hunter_user_id_fkey" FOREIGN KEY ("user_id")
	REFERENCES "_user"("id")
	MATCH SIMPLE
	ON DELETE RESTRICT
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "hunter" ADD CONSTRAINT "Ref_hunter_to_deelgebied" FOREIGN KEY ("deelgebied_id")
	REFERENCES "deelgebied"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "huntertracker" ADD CONSTRAINT "huntertracker_hunter_id_fkey" FOREIGN KEY ("hunter_id")
	REFERENCES "hunter"("id")
	MATCH SIMPLE
	ON DELETE CASCADE
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "hunts" ADD CONSTRAINT "hunts_vossentracker_id_fkey" FOREIGN KEY ("vossentracker_id")
	REFERENCES "vossentracker"("id")
	MATCH SIMPLE
	ON DELETE SET NULL
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "hunts" ADD CONSTRAINT "hunts_hunter_id_fkey" FOREIGN KEY ("hunter_id")
	REFERENCES "hunter"("id")
	MATCH SIMPLE
	ON DELETE SET NULL
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "phonenumbers" ADD CONSTRAINT "Ref_phonenumbers_to__user" FOREIGN KEY ("user_id")
	REFERENCES "_user"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "score" ADD CONSTRAINT "Ref_score_to_events" FOREIGN KEY ("event_id")
	REFERENCES "events"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "session" ADD CONSTRAINT "session_user_id_fkey" FOREIGN KEY ("user_id")
	REFERENCES "_user"("id")
	MATCH SIMPLE
	ON DELETE RESTRICT
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "session" ADD CONSTRAINT "Ref_session_to_organisation" FOREIGN KEY ("organisation_id")
	REFERENCES "organisation"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "session" ADD CONSTRAINT "Ref_session_to_events" FOREIGN KEY ("event_id")
	REFERENCES "events"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "user_organisation" ADD CONSTRAINT "user_organisation_user_id_fkey" FOREIGN KEY ("user_id")
	REFERENCES "_user"("id")
	MATCH SIMPLE
	ON DELETE RESTRICT
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "user_organisation" ADD CONSTRAINT "user_organisation_organisation_id_fkey" FOREIGN KEY ("organisation_id")
	REFERENCES "organisation"("id")
	MATCH SIMPLE
	ON DELETE RESTRICT
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "vossen" ADD CONSTRAINT "Ref_vossen_to_deelgebied" FOREIGN KEY ("deelgebied_id")
	REFERENCES "deelgebied"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "vossen" ADD CONSTRAINT "Ref_vossen_to_speelhelft" FOREIGN KEY ("speelhelft_id")
	REFERENCES "speelhelft"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "vossentracker" ADD CONSTRAINT "vossentracker_vossen_id_fkey" FOREIGN KEY ("vossen_id")
	REFERENCES "vossen"("id")
	MATCH SIMPLE
	ON DELETE CASCADE
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "vossentracker" ADD CONSTRAINT "Ref_vossentracker_to_organisation" FOREIGN KEY ("organisation_id")
	REFERENCES "organisation"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "events_has_organisation" ADD CONSTRAINT "Ref_events_has_organisation_to_events" FOREIGN KEY ("events_id")
	REFERENCES "events"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "events_has_organisation" ADD CONSTRAINT "Ref_events_has_organisation_to_organisation" FOREIGN KEY ("organisation_id")
	REFERENCES "organisation"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "user_has_group" ADD CONSTRAINT "Ref__user_has__group_to__user" FOREIGN KEY ("user_id")
	REFERENCES "_user"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "user_has_group" ADD CONSTRAINT "Ref__user_has__group_to__group" FOREIGN KEY ("group_id")
	REFERENCES "_group"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "deelgebied" ADD CONSTRAINT "Ref_deelgebied_to_events" FOREIGN KEY ("event_id")
	REFERENCES "events"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;

ALTER TABLE "deelgebied_coord" ADD CONSTRAINT "Ref_deelgebied_coord_to_deelgebied" FOREIGN KEY ("deelgebied_id")
	REFERENCES "deelgebied"("id")
	MATCH SIMPLE
	ON DELETE CASCADE
	ON UPDATE CASCADE
	NOT DEFERRABLE;

ALTER TABLE "speelhelft" ADD CONSTRAINT "Ref_speelhelft_to_events" FOREIGN KEY ("event_id")
	REFERENCES "events"("id")
	MATCH SIMPLE
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	NOT DEFERRABLE;


