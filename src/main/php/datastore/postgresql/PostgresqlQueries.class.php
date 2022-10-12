<?php

class PostgresqlQueries {
    private $conn;

    public function setConn($conn) {
        $this->conn = $conn;
    }

    public function prepare() {
        $sqlName = 'removeOpziener';
        $sqlQuery = 'DELETE FROM opzieners WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeHunt';
        $sqlQuery = 'DELETE FROM hunts WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeRider';
        $sqlQuery = 'DELETE FROM hunter WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removePhonenumber';
        $sqlQuery = 'DELETE FROM phonenumbers WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addHunt';
        $sqlQuery = 'INSERT INTO hunts(hunter_id, vossentracker_id, code) VALUES ($1, $2, $3)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'updateHunt';
        $sqlQuery = 'UPDATE hunts SET hunter_id=$2, vossentracker_id=$3, code=$4, goedgekeurd=$5 WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getLastHunt';
        $sqlQuery = 'SELECT hunts.id, hunter_id, vossentracker_id, code, goedgekeurd, time 
                    FROM hunter 
                    JOIN hunts ON (hunts.hunter_id = hunter.id)

                    JOIN vossentracker ON (hunts.vossentracker_id = vossentracker.id)
                    JOIN vossen ON (vossentracker.vossen_id = vossen.id)
                    JOIN deelgebied ON (vossen.deelgebied_id = deelgebied.id)
                    
                    WHERE vossentracker.organisation_id = $1
                    AND deelgebied.event_id = $2
                    ORDER BY time DESC 
                    LIMIT 1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'addGcm';
        $sqlQuery = '
        INSERT INTO gcm(gcm_id, hunter_id, time) 
                        VALUES ($1, $2, $3)
                        RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'updateGcm';
        $sqlQuery = '   UPDATE gcm 
                        SET gcm_id=$2, hunter_id=$3, enabled=$4, time=$5 
                        FROM hunter, user_organisation
                        WHERE gcm.hunter_id = hunter.id
                        AND user_organisation.user_id = hunter.user_id
                        AND user_organisation.organisation_id = $6
                        AND gcm.id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeGcm';
        $sqlQuery = '   DELETE FROM gcm 
                        USING hunter, user_organisation
                        WHERE gcm.hunter_id = hunter.id
                        AND user_organisation.user_id = hunter.user_id
                        AND user_organisation.organisation_id = $2
                        AND gcm.id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getGcm';
        $sqlQuery = 'SELECT gcm.id, gcm.gcm_id, gcm.hunter_id, gcm.enabled, gcm.time 
                        FROM gcm
                        JOIN hunter ON (gcm.hunter_id = hunter.id)
                        JOIN user_organisation ON (user_organisation.user_id = hunter.user_id)
                        WHERE user_organisation.organisation_id = $2
                        AND gcm.id = $1
                        ';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getGcmByGcmId';
        $sqlQuery = 'SELECT gcm.id, gcm.gcm_id, gcm.hunter_id, gcm.enabled, gcm.time 
                        FROM gcm
                        JOIN hunter ON (gcm.hunter_id = hunter.id)
                        JOIN user_organisation ON (user_organisation.user_id = hunter.user_id)
                        WHERE user_organisation.organisation_id = $3
                        AND gcm.gcm_id = $1 
                        AND gcm.hunter_id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllGcms';
        $sqlQuery = 'SELECT gcm.id, gcm.gcm_id, gcm.hunter_id, gcm.enabled, gcm.time 
                        FROM gcm
                        JOIN hunter ON (gcm.hunter_id = hunter.id)
                        JOIN user_organisation ON (user_organisation.user_id = hunter.user_id)
                        WHERE user_organisation.organisation_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllGcmsSU';
        $sqlQuery = 'SELECT id, gcm_id, hunter_id, enabled, time FROM gcm';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllActiveGcms';
        $sqlQuery = 'SELECT gcm.id, gcm.gcm_id, gcm.hunter_id, gcm.enabled, gcm.time 
                        FROM gcm 
                        JOIN hunter ON (gcm.hunter_id = hunter.id)
                        JOIN user_organisation ON (user_organisation.user_id = hunter.user_id)
                        WHERE user_organisation.organisation_id = $1
                        AND enabled';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getHunt';
        $sqlQuery = 'SELECT 
                    hunts.id, hunts.hunter_id, hunts.vossentracker_id, hunts.code, hunts.goedgekeurd,
                    hunter.user_id, 
                    vossentracker.time, vossentracker.adres, 
                    deelgebied.name AS deelgebied
                    FROM hunts
                    JOIN hunter ON hunts.hunter_id = hunter.id
                    LEFT OUTER JOIN vossentracker ON hunts.vossentracker_id = vossentracker.id
                    JOIN vossen ON vossentracker.vossen_id = vossen.id
                    JOIN deelgebied ON vossen.deelgebied_id = deelgebied.id
                    JOIN user_organisation ON user_organisation.user_id = hunter.user_id
                    JOIN events_has_organisation ON user_organisation.organisation_id = events_has_organisation.organisation_id
                    
                    WHERE vossentracker.organisation_id = $2
                    AND events_has_organisation.events_id = $3
                    AND deelgebied.event_id = $3
                    AND hunts.id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getHuntByCode';
        $sqlQuery = 'SELECT 
                    hunts.id, hunts.hunter_id, hunts.vossentracker_id, hunts.code, hunts.goedgekeurd,
                    hunter.user_id, 
                    vossentracker.time, vossentracker.adres, 
                    deelgebied.name AS deelgebied
                    FROM hunts
                    JOIN hunter ON hunts.hunter_id = hunter.id
                    LEFT OUTER JOIN vossentracker ON hunts.vossentracker_id = vossentracker.id
                    JOIN vossen ON vossentracker.vossen_id = vossen.id
                    JOIN deelgebied ON vossen.deelgebied_id = deelgebied.id
                    JOIN user_organisation ON user_organisation.user_id = hunter.user_id
                    JOIN events_has_organisation ON user_organisation.organisation_id = events_has_organisation.organisation_id
                    
                    WHERE vossentracker.organisation_id = $2
                    AND events_has_organisation.events_id = $3
                    AND deelgebied.event_id = $3
                    AND hunts.code = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllHunts';
        $sqlQuery  = 'SELECT 
                    hunts.id, hunts.hunter_id, hunts.vossentracker_id, hunts.code, hunts.goedgekeurd,
                    hunter.user_id, 
                    vossentracker.time, vossentracker.adres, 
                    deelgebied.name AS deelgebied
                    FROM hunts
                    JOIN hunter ON hunts.hunter_id = hunter.id
                    LEFT OUTER JOIN vossentracker ON hunts.vossentracker_id = vossentracker.id
                    JOIN vossen ON vossentracker.vossen_id = vossen.id
                    JOIN deelgebied ON vossen.deelgebied_id = deelgebied.id
                    JOIN user_organisation ON user_organisation.user_id = hunter.user_id
                    JOIN events_has_organisation ON user_organisation.organisation_id = events_has_organisation.organisation_id
                    
                    WHERE vossentracker.organisation_id = $1
                    AND events_has_organisation.events_id = $2
                    AND deelgebied.event_id = $2
                    ORDER BY time DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getHunterHighscore';
        $sqlQuery = 'SELECT hunter.user_id, COUNT(hunts.id) AS score 
                        FROM hunter 
                        JOIN hunts ON (hunts.hunter_id = hunter.id)

                        JOIN vossentracker ON (hunts.vossentracker_id = vossentracker.id)
                        JOIN vossen ON (vossentracker.vossen_id = vossen.id)
                        JOIN deelgebied ON (vossen.deelgebied_id = deelgebied.id)
                        
                        WHERE vossentracker.organisation_id = $1
                        AND deelgebied.event_id = $2
                        GROUP BY hunter.user_id
                        ORDER BY score DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getScoreByGroep';
        $sqlQuery = 'SELECT id, plaats, groep, woonplaats, regio, hunts, tegenhunts, 
                            opdrachten, fotoopdrachten, hints, totaal, lastupdate 
                        FROM score
                        WHERE groep = $1 
                        AND event_id = $2
                        ORDER BY lastupdate DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getScoreCollection';
        $sqlQuery = 'SELECT id, plaats, groep, woonplaats, regio, hunts, tegenhunts, opdrachten, fotoopdrachten, hints, totaal, lastupdate 
                        FROM score 
                        WHERE event_id = $1
                        ORDER BY groep ASC, lastupdate ASC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addScore';
        $sqlQuery = 'INSERT INTO score(event_id, plaats, groep, woonplaats, regio, hunts, tegenhunts, opdrachten, fotoopdrachten, hints, totaal, lastupdate) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addOpziener';
        $sqlQuery = 'INSERT INTO opzieners(user_id, deelgebied_id, type) VALUES ($1, $2, $3)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addRider';
        $sqlQuery = 'INSERT INTO hunter(user_id, deelgebied_id, van, tot, auto) VALUES ($1, $2, $3, $4, $5)';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'removeRiderViaUserId';
        $sqlQuery = 'DELETE FROM hunter WHERE user_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getOpziener';
        $sqlQuery = 'SELECT opzieners.id, _user.id AS user_id, displayname, deelgebied_id, type 
                    FROM opzieners
                    JOIN _user ON opzieners.user_id = _user.id
                    JOIN user_organisation ON (_user.id = user_organisation.user_id)
                    JOIN deelgebied ON opzieners.deelgebied_id = deelgebied.id
                    WHERE user_organisation.organisation_id = $2
                    AND deelgebied.event_id = $3
                    AND opzieners.user_id = _user.id 
                    AND opzieners.id = $1
                    ORDER BY deelgebied_id ASC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllOpzieners';
        $sqlQuery = 'SELECT opzieners.id, _user.id AS user_id, displayname, deelgebied_id, type 
                    FROM opzieners
                    JOIN _user ON opzieners.user_id = _user.id
                    JOIN user_organisation ON (_user.id = user_organisation.user_id)
                    JOIN deelgebied ON opzieners.deelgebied_id = deelgebied.id
                    WHERE user_organisation.organisation_id = $1
                    AND deelgebied.event_id = $2
                    AND opzieners.user_id = _user.id 
                    ORDER BY deelgebied_id ASC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'updateStatus';
        $sqlQuery = 'UPDATE vossen SET status=$2 WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addPhonenumber';
        $sqlQuery = 'INSERT INTO phonenumbers(user_id, phonenumber) VALUES ($1, $2)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllPhonenumbers';
        $sqlQuery = 'SELECT phonenumbers.id, phonenumbers.user_id, phonenumber 
                    FROM phonenumbers
                    JOIN _user ON (phonenumbers.user_id = _user.id)
                    JOIN user_organisation ON (_user.id = user_organisation.user_id)
                    WHERE user_organisation.organisation_id = $1
                    ';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getPhonenumbersForUserId';
        $sqlQuery = 'SELECT id, user_id, phonenumber FROM phonenumbers WHERE user_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getTotalAmountOfRiderLocations';
        $sqlQuery = 'SELECT COUNT(huntertracker.id) AS ridercount 
                        FROM huntertracker
                    INNER JOIN hunter ON (huntertracker.hunter_id = hunter.id) 
                    JOIN _user ON (hunter.user_id = _user.id)
                    JOIN user_organisation ON (_user.id = user_organisation.user_id)
                    JOIN events_has_organisation ON (user_organisation.organisation_id = events_has_organisation.organisation_id)
                    JOIN deelgebied ON hunter.deelgebied_id = deelgebied.id
                    WHERE user_organisation.organisation_id = $1
                    AND events_has_organisation.events_id = $2
                    AND deelgebied.event_id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getLastRiderLocations';
        $sqlQuery = 'SELECT DISTINCT ON (hunter_id) huntertracker.id, hunter_id, longitude, latitude, accuracy, provider, time 
                            FROM huntertracker 
                            INNER JOIN hunter ON (huntertracker.hunter_id = hunter.id) 
                            JOIN _user ON (hunter.user_id = _user.id)
                            JOIN user_organisation ON (_user.id = user_organisation.user_id)
                            JOIN events_has_organisation ON (user_organisation.organisation_id = events_has_organisation.organisation_id)
                            JOIN deelgebied ON hunter.deelgebied_id = deelgebied.id
                            WHERE user_organisation.organisation_id = $1
                            AND events_has_organisation.events_id = $2
                            AND deelgebied.event_id = $2
                            ORDER BY hunter_id, time DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getRiderLocation';
        $sqlQuery = 'SELECT id, hunter_id, longitude, latitude, accuracy, provider, time 
                        FROM huntertracker 
                        WHERE hunter_id = $1 
                        ORDER BY time DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getRiderLocationWithGcm';
        $sqlQuery = 'SELECT huntertracker.id, huntertracker.hunter_id, huntertracker.longitude, huntertracker.latitude, huntertracker.accuracy, huntertracker.provider, huntertracker.time ';
        $sqlQuery .= 'FROM huntertracker ';
        $sqlQuery .= 'INNER JOIN gcm ON (huntertracker.hunter_id = gcm.hunter_id) ';
        $sqlQuery .= 'WHERE huntertracker.hunter_id = $1 AND gcm.gcm_id = $2 ';
        $sqlQuery .= 'ORDER BY huntertracker.time DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getRiderLocationWithDateRange';
        $sqlQuery = 'SELECT id, hunter_id, longitude, latitude, accuracy, provider, time FROM huntertracker WHERE hunter_id = $1 AND time BETWEEN $2 AND $3 ORDER BY time DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getRiderLocationGraph';
        $sqlQuery = 'SELECT DISTINCT date_trunc(\'hour\', "time") AS hour_slice, count(*) OVER (ORDER BY date_trunc(\'hour\', "time")) AS running_ct FROM huntertracker WHERE hunter_id = $1 ORDER BY 1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getRiderLocationGraph2';
        $sqlQuery = 'WITH x AS (SELECT date_trunc(\'hour\', "time") AS hour_slice FROM huntertracker WHERE hunter_id = $1) ';
        $sqlQuery .= 'SELECT DISTINCT ';
        $sqlQuery .= '    m.hour_slice, count(x.hour_slice) OVER (ORDER BY m.hour_slice) AS running_ct ';
        $sqlQuery .= 'FROM (SELECT generate_series(   min(hour_slice) ';
        $sqlQuery .= '                               ,max(hour_slice), \'1h\') AS hour_slice FROM x) m ';
        $sqlQuery .= 'LEFT JOIN x USING (hour_slice) ';
        $sqlQuery .= 'ORDER  BY 1;';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addRiderLocation';
        $sqlQuery = 'INSERT INTO huntertracker(hunter_id, longitude, latitude, accuracy, provider, time) VALUES ($1, $2, $3, $4, $5, $6)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getBericht';
        $sqlQuery = 'SELECT bericht.id, event_id, bericht_id, titel, datum, eindtijd, maxpunten, inhoud, lastupdate, type 
                        FROM bericht JOIN events ON bericht.event_id = events.id
                        WHERE bericht_id = $1 
                        AND events.id = $2
                        ORDER BY lastupdate DESC LIMIT 1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getLastBericht';
        $sqlQuery = 'SELECT id, event_id, bericht_id, titel, datum, 
                            eindtijd, maxpunten, inhoud, lastupdate, type 
                        FROM bericht
                        WHERE bericht.event_id = $1
                        ORDER BY datum DESC LIMIT 1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getLastBerichtByType';
        $sqlQuery = 'SELECT bericht.id, event_id, bericht_id, titel, datum, eindtijd, maxpunten, inhoud, lastupdate, type 
                        FROM bericht JOIN events ON bericht.event_id = events.id
                        WHERE type = $1 
                        AND events.id = $2
                        ORDER BY datum DESC LIMIT 1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addBericht';
        $sqlQuery = 'INSERT INTO bericht (bericht_id, event_id, titel, datum, eindtijd, maxpunten, inhoud, lastupdate, type) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getBerichtGeschiedenis';
        $sqlQuery = 'SELECT bericht.id, event_id, bericht_id, titel, datum, eindtijd, maxpunten, inhoud, lastupdate, type 
                        FROM bericht JOIN events ON bericht.event_id = events.id
                        WHERE bericht_id = $1 
                        AND events.id = $2
                        ORDER BY lastupdate DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getBerichtCollectionByType';
        $sqlQuery = 'SELECT DISTINCT ON (bericht_id) bericht.id, event_id, bericht_id, titel, datum, eindtijd, maxpunten, inhoud, lastupdate, type 
                        FROM bericht JOIN events ON bericht.event_id = events.id
                        WHERE type = $1
                        AND events.id = $2
                        ORDER BY bericht_id DESC, lastupdate DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getBerichtCollection';
        $sqlQuery = 'SELECT bericht.id, event_id, bericht_id, titel, datum, eindtijd, maxpunten, inhoud, lastupdate, type 
                        FROM bericht JOIN events ON bericht.event_id = events.id
                        WHERE events.id = $1
                        ORDER BY lastupdate DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllRidersSu';
        $sqlQuery = 'SELECT hunter.id, hunter.user_id, hunter.deelgebied_id, hunter.van, hunter.tot, hunter.auto 
                    FROM hunter
                    ORDER BY hunter.deelgebied_id ASC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllRiders';
        $sqlQuery = 'SELECT hunter.id, hunter.user_id, hunter.deelgebied_id, hunter.van, hunter.tot, hunter.auto 
                    FROM hunter 
                    JOIN _user ON (hunter.user_id = _user.id)
                    JOIN user_organisation ON (_user.id = user_organisation.user_id)
                    JOIN events_has_organisation ON (user_organisation.organisation_id = events_has_organisation.organisation_id)
                    JOIN deelgebied ON hunter.deelgebied_id = deelgebied.id
                    WHERE user_organisation.organisation_id = $1
                    AND events_has_organisation.events_id = $2
                    AND deelgebied.event_id = $2
                    ORDER BY deelgebied_id ASC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getActiveRiders';
        $sqlQuery = 'SELECT hunter.id, hunter.user_id, hunter.deelgebied_id, hunter.van, hunter.tot, hunter.auto 
                        FROM hunter
                        JOIN _user ON (hunter.user_id = _user.id)
                        JOIN user_organisation ON (_user.id = user_organisation.user_id)
                        JOIN events_has_organisation ON (user_organisation.organisation_id = events_has_organisation.organisation_id)
                    WHERE user_organisation.organisation_id = $2
                        AND deelgebied_id = $1 AND van <= \''.date("Y-m-d H:i:s").'\' AND tot >= \''.date("Y-m-d H:i:s").'\'
                    AND events_has_organisation.events_id = $3
                        ORDER BY van ASC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getRider';
        $sqlQuery = 'SELECT hunter.id, hunter.user_id, hunter.deelgebied_id, hunter.van, hunter.tot, hunter.auto 
                    FROM hunter 
                    JOIN _user ON (hunter.user_id = _user.id)
                    JOIN user_organisation ON (_user.id = user_organisation.user_id)
                    WHERE user_organisation.organisation_id = $2
                    AND hunter.id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getRiderByName';
        $sqlQuery = 'SELECT hunter.id, hunter.user_id, deelgebied_id, van, tot, auto 
                        FROM hunter 
                        INNER JOIN _user ON (hunter.user_id = _user.id) 
                        JOIN user_organisation ON (_user.id = user_organisation.user_id)
                        WHERE _user.username = $1
                        AND user_organisation.organisation_id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getRiderByName2';
        $sqlQuery = 'SELECT hunter.id, hunter.user_id, deelgebied_id, van, tot, auto 
                        FROM hunter
                            JOIN _user ON (hunter.user_id = _user.id)
                            JOIN user_organisation ON (_user.id = user_organisation.user_id)
                            JOIN events_has_organisation ON (user_organisation.organisation_id = events_has_organisation.organisation_id)
                            JOIN deelgebied ON hunter.deelgebied_id = deelgebied.id
                        WHERE _user.username = $1
                        AND user_organisation.organisation_id = $2
                        AND events_has_organisation.events_id = $3
                        AND deelgebied.event_id = $3';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getTotalAmountOfVossenLocations';
        $sqlQuery = 'SELECT COUNT(vossentracker.id) as vossencount 
                        FROM vossentracker
                        JOIN vossen ON vossentracker.vossen_id = vossen.id
                        JOIN deelgebied ON vossen.deelgebied_id = deelgebied.id
                        WHERE vossentracker.organisation_id = $1 AND deelgebied.event_id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'updateRider';
        $sqlQuery = 'UPDATE hunter 
                        SET deelgebied_id=$2,
                        user_id=$3, 
                        van=$4, 
                        tot=$5, 
                        auto=$6 
                        WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllTeams';
        $sqlQuery = 'SELECT id, deelgebied_id, speelhelft_id, name, status FROM vossen';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllTeamsCount';
        $sqlQuery = 'SELECT COUNT(id) as teamcount FROM vossen';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getMyTeams';
        $sqlQuery = 'SELECT 
                        vossen.id, vossen.deelgebied_id, vossen.speelhelft_id, vossen.name, vossen.status 
                        FROM vossen 
                        JOIN deelgebied ON vossen.deelgebied_id = deelgebied.id
                        WHERE deelgebied.event_id = $1
                        ORDER BY vossen.name';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getTeamByName';
        $sqlQuery = 'SELECT vossen.id, vossen.deelgebied_id, vossen.speelhelft_id, vossen.name, vossen.status 
                    FROM vossen 
                    JOIN deelgebied ON vossen.deelgebied_id = deelgebied.id
                    WHERE vossen.name = $1
                    AND deelgebied.event_id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getTeamById';
        $sqlQuery = 'SELECT id, deelgebied_id, speelhelft_id, name, status FROM vossen WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getTeamByDeelgebiedId';
        $sqlQuery  = 'SELECT name ';
        $sqlQuery .= 'FROM vossen ';
        $sqlQuery .= 'WHERE deelgebied_id = $1 ';
        $sqlQuery .= 'ORDER BY id DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addTeam';
        $sqlQuery = 'INSERT INTO vossen(deelgebied_id, speelhelft_id, name, status) VALUES($1, $2, $3, $4) RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'updateTeam';
        $sqlQuery = 'UPDATE vossen SET name = $2, deelgebied_id = $3, status = $4, speelhelft_id = $5 WHERE id = $1 ';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeTeam';
        $sqlQuery = 'DELETE FROM vossen WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getLocations';
        $sqlQuery = 'SELECT vossentracker.id, vossen_id, longitude, latitude, x, y,
                            time, hunts.hunter_id, type, adres, counterhuntrondje_id
                        FROM vossentracker 
                        JOIN vossen ON vossentracker.vossen_id = vossen.id
                        JOIN deelgebied ON vossen.deelgebied_id = deelgebied.id
                        LEFT OUTER JOIN hunts ON (vossentracker.id = hunts.vossentracker_id)
                        WHERE vossen_id = $1
                        AND organisation_id = $2
                        AND deelgebied.event_id = $3
                        ORDER BY time DESC';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getLocation';
        $sqlQuery = 'SELECT vossentracker.id, vossen_id, longitude, latitude, x, y, time, hunts.hunter_id, type, adres, counterhuntrondje_id 
                        FROM vossentracker LEFT OUTER JOIN hunts ON (vossentracker.id = hunts.vossentracker_id), vossen 
                        WHERE vossentracker.id = $1 AND vossentracker.vossen_id = vossen.id';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addLocation';
        $sqlQuery = 'INSERT INTO vossentracker(vossen_id, x, y, longitude, latitude, adres, type, time, organisation_id, counterhuntrondje_id) 
                        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10) 
                        RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeVossenLocation';
        $sqlQuery = 'DELETE FROM vossentracker WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        // Auth
        $sqlName = 'addUser';
        $sqlQuery = 'INSERT INTO _user(username, displayname, pw_hash) VALUES ($1, $2, $3) RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'updateUser';
        $sqlQuery = 'UPDATE _user set displayname=$2, pw_hash=$3 WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'removeUser';
        $sqlQuery = 'DELETE FROM _user WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'removeAllGroupsOfUser';
        $sqlQuery = 'DELETE FROM user_has_group WHERE user_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'authAllUsers';
        $sqlQuery = 'SELECT id, username, displayname, pw_hash 
                    FROM _user
                    JOIN user_organisation ON (_user.id = user_organisation.user_id)
                    WHERE user_organisation.organisation_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'authAllUsersSU';
        $sqlQuery = 'SELECT id, username, displayname, pw_hash 
                    FROM _user';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        
        $sqlName = 'authUserBySession';
        $sqlQuery = 'SELECT id, username, displayname, pw_hash 
                        FROM _user 
                        INNER JOIN session ON (_user.id = session.user_id) 
                        WHERE session_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'authUserById';
        $sqlQuery = 'SELECT id, username, displayname, pw_hash 
                        FROM _user 
                        WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'authLogin';
        $sqlQuery = 'SELECT id, username, displayname, pw_hash from _user WHERE username = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'authGetSessionInformation';
        $sqlQuery = 'SELECT session_id, user_id, organisation_id, event_id FROM session WHERE session_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        
        $sqlName = 'authAddSessionId';
        $sqlQuery = 'INSERT INTO session(session_id, user_id, organisation_id, event_id) VALUES ($1, $2, $3, $4)';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'authUpdateSession';
        $sqlQuery = 'UPDATE session SET event_id=$2 WHERE session_id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'authRemoveAllSessionIdsForUserId';
        $sqlQuery = 'DELETE FROM session WHERE user_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'authRemoveSessionId';
        $sqlQuery = 'DELETE FROM session WHERE session_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'authGroupsByUserId';
        $sqlQuery = 'SELECT id, name FROM _group INNER JOIN user_has_group ON (_group.id = user_has_group.group_id) WHERE user_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addUserToGroup';
        $sqlQuery = 'INSERT INTO user_has_group (user_id, group_id) VALUES ($1, $2)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addUserToOrganisation';
        $sqlQuery = 'INSERT INTO user_organisation (user_id, organisation_id) VALUES ($1, $2)';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'removeUserFromAllOrganisations';
        $sqlQuery = 'DELETE FROM user_organisation WHERE user_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllOrganisationsCount';
        $sqlQuery = 'SELECT COUNT(id) AS organisationscount FROM organisation';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllOrganisations';
        $sqlQuery = 'SELECT id, name FROM organisation';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getOrganisationById';
        $sqlQuery = 'SELECT id, name FROM organisation WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addOrganisation';
        $sqlQuery = 'INSERT INTO organisation(name) VALUES ($1) RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'updateOrganisation';
        $sqlQuery = 'UPDATE organisation SET name=$2 WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeOrganisation';
        $sqlQuery = 'DELETE FROM organisation WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getOrganisationForUser';
        $sqlQuery = 'SELECT id, name FROM organisation INNER JOIN user_organisation ON organisation.id = user_organisation.organisation_id WHERE user_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        // End auth
        
        $sqlName = 'addDeelgebied';
        $sqlQuery = 'INSERT INTO deelgebied(event_id, name, linecolor, polycolor) VALUES ($1, $2, $3, $4) RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getDeelgebiedByName';
        $sqlQuery = 'SELECT deelgebied.id, event_id, deelgebied.name, deelgebied.linecolor, deelgebied.polycolor 
                        FROM deelgebied
                        JOIN events ON (deelgebied.event_id = events.id)
                        WHERE deelgebied.name = $1 
                        AND events.id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getDeelgebiedById';
        $sqlQuery = 'SELECT deelgebied.id, event_id, deelgebied.name, deelgebied.linecolor, deelgebied.polycolor FROM deelgebied, events WHERE deelgebied.event_id = events.id AND deelgebied.id = $1 AND events.id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getDeelgebiedByIdSu';
        $sqlQuery = 'SELECT deelgebied.id, event_id, deelgebied.name, deelgebied.linecolor, deelgebied.polycolor 
                    FROM deelgebied
                    WHERE deelgebied.id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllDeelgebiedenCount';
        $sqlQuery = 'SELECT COUNT(id) AS deelgebiedcount FROM deelgebied';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllDeelgebieden';
        $sqlQuery = 'SELECT deelgebied.id, event_id, deelgebied.name, deelgebied.linecolor, deelgebied.polycolor 
                    FROM deelgebied, events 
                    WHERE deelgebied.event_id = events.id 
                    AND events.id = $1
                    ORDER BY deelgebied.name';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeDeelgebied';
        $sqlQuery = 'DELETE FROM deelgebied WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addCoordinate';
        $sqlQuery = 'INSERT INTO deelgebied_coord(deelgebied_id, longitude, latitude, order_id) VALUES ($1, $2, $3, $4) RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        
        $sqlName = 'getAllCoordinatesForDeelgebied';
        $sqlQuery = 'SELECT id, deelgebied_id, longitude, latitude, order_id
                        FROM deelgebied_coord
                        WHERE deelgebied_id = $1
                        ORDER BY order_id';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllEventsCount';
        $sqlQuery = 'SELECT COUNT(id) AS eventcount FROM events';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllEvents';
        $sqlQuery = 'SELECT id, name, public, starttime, endtime FROM events';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getMyEvents';
        $sqlQuery = 'SELECT id, name, public, starttime, endtime 
                        FROM events
                        JOIN events_has_organisation ON id = events_id 
                        WHERE organisation_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getEventById';
        $sqlQuery = 'SELECT id, name, public, starttime, endtime FROM events WHERE ID = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addEvent';
        $sqlQuery = 'INSERT INTO events(name, public, starttime, endtime) VALUES ($1, $2, $3, $4) RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'updateEvent';
        $sqlQuery = 'UPDATE events SET name=$2, public=$3, starttime=$4, endtime=$5 WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeEvent';
        $sqlQuery = 'DELETE FROM events WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addOrganisationToEvent';
        $sqlQuery = 'INSERT INTO events_has_organisation(events_id, organisation_id) VALUES ($1, $2)';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeOrganisationFromEvent';
        $sqlQuery = 'DELETE FROM events_has_organisation WHERE events_id = $1 AND organisation_id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllSpeelhelften';
        $sqlQuery = 'SELECT id, event_id, starttime, endtime FROM speelhelft';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllSpeelhelftenCount';
        $sqlQuery = 'SELECT COUNT(id) AS speelhelftcount FROM speelhelft';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllSpeelhelftenForEvent';
        $sqlQuery = 'SELECT id, event_id, starttime, endtime FROM speelhelft WHERE event_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getSpeelhelftById';
        $sqlQuery = 'SELECT id, event_id, starttime, endtime FROM speelhelft WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addSpeelhelft';
        $sqlQuery = 'INSERT INTO speelhelft(event_id, starttime, endtime) VALUES ($1, $2, $3) RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeSpeelhelft';
        $sqlQuery = 'DELETE FROM speelhelft WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'updateSpeelhelft';
        $sqlQuery = 'UPDATE speelhelft SET event_id=$2, starttime=$3, endtime=$4 WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);

        // POIs
        $sqlName = 'addPoi';
        $sqlQuery = 'INSERT INTO poi(event_id, name, data, latitude, longitude, type)
                        VALUES($1, $2, $3, $4, $5, $6)
                        RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'updatePoi';
        $sqlQuery = 'UPDATE poi SET 
                    event_id=$2,
                    name=$3,
                    data=$4,
                    latitude=$5,
                    longitude=$6,
                    type=$7
                    WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'removePoi';
        $sqlQuery = 'DELETE FROM poi WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllPois';
        $sqlQuery = 'SELECT id, event_id, name, data, latitude, longitude, type
                    FROM poi
                    WHERE event_id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllPoisSu';
        $sqlQuery = 'SELECT id, event_id, name, data, latitude, longitude, type
                    FROM poi';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getPoiById';
        $sqlQuery = 'SELECT id, event_id, name, data, latitude, longitude, type
                    FROM poi
                    WHERE event_id = $1
                    AND id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);
        // End POIs

        // POITypess
        $sqlName = 'addPoiType';
        $sqlQuery = 'INSERT INTO poitype(event_id, organisation_id, name, onmap, onapp, image)
                        VALUES($1, $2, $3, $4, $5, $6)
                        RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'updatePoiType';
        $sqlQuery = 'UPDATE poitype SET 
                    event_id=$2,
                    name=$3,
                    onmap=$4,
                    onapp=$5,
                    image=$6
                    WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'removePoiType';
        $sqlQuery = 'DELETE FROM poitype WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllPoiTypes';
        $sqlQuery = 'SELECT id, event_id, organisation_id, name, onmap, onapp, image
                    FROM poitype
                    WHERE event_id = $1
                    AND organisation_id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllPoiTypesSu';
        $sqlQuery = 'SELECT id, event_id, organisation_id, name, onmap, onapp, image
                    FROM poitype';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getPoiTypeById';
        $sqlQuery = 'SELECT id, event_id, organisation_id, name, onmap, onapp, image
                    FROM poitype
                    WHERE event_id = $1
                    AND id = $2';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getPoiTypeByName';
        $sqlQuery = 'SELECT id, event_id, organisation_id, name, onmap, onapp, image
                    FROM poitype
                    WHERE event_id = $1
                    AND name = $2';
        $this->prepareInternal($sqlName, $sqlQuery);
        // End POITypes
        
        $sqlName = 'imageGetBySha';
        $sqlQuery = 'SELECT * FROM image WHERE sha1 = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addImage';
        $sqlQuery = 'INSERT INTO image (data, name, extension, sha1, file_size, last_modified)
                        VALUES($1, $2, $3, $4, $5, $6)
                        RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'updateImage';
        $sqlQuery = 'UPDATE image SET 
                    data=$2,
                    name=$3,
                    extension=$4,
                    sha1=$5,
                    file_size=$6,
                    last_modified=$7
                    WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getImageById';
        $sqlQuery = 'SELECT id, data, name, extension, sha1, file_size, last_modified
                    FROM image
                    WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getAllCounterhuntrondjesCount';
        $sqlQuery = 'SELECT COUNT(id) AS count FROM counterhunt_rondjes';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getAllCounterhuntrondjesSu';
        $sqlQuery = 'SELECT id, deelgebied_id, organisation_id, name, active
                    FROM counterhunt_rondjes
                    ORDER BY organisation_id, deelgebied_id';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getCounterhuntrondjeByIdSu';
        $sqlQuery = 'SELECT c.id, c.deelgebied_id, c.organisation_id, c.name, c.active
                    FROM counterhunt_rondjes AS c
                    WHERE c.id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'addCounterhuntrondje';
        $sqlQuery = 'INSERT INTO counterhunt_rondjes(deelgebied_id, organisation_id, name, active)
                        VALUES($1, $2, $3, $4)
                        RETURNING id';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'updateCounterhuntrondje';
        $sqlQuery = 'UPDATE counterhunt_rondjes SET 
                    deelgebied_id=$2,
                    organisation_id=$3,
                    name=$4,
                    active=$5
                    WHERE id=$1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'removeCounterhuntrondje';
        $sqlQuery = 'DELETE FROM counterhunt_rondjes WHERE id = $1';
        $this->prepareInternal($sqlName, $sqlQuery);
        
        $sqlName = 'getActiveCounterhuntRondjeByDeelgebiedName';
        $sqlQuery = 'SELECT c.id, c.deelgebied_id, c.organisation_id, c.name, c.active
                    FROM counterhunt_rondjes AS c
                    JOIN deelgebied AS d ON (c.deelgebied_id = d.id)
                    JOIN events ON (d.event_id = events.id)
                    WHERE d.name = $1
                    AND c.organisation_id = $2
                    AND events.id = $3
                    AND c.active = True';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'getCounterhuntrondjeForDeelgebiedByName';
        $sqlQuery = 'SELECT c.id, c.deelgebied_id, c.organisation_id, c.name, c.active
                    FROM counterhunt_rondjes AS c
                    JOIN deelgebied AS d ON (c.deelgebied_id = d.id)
                    JOIN events ON (d.event_id = events.id)
                    WHERE d.name = $1
                    AND c.organisation_id = $2
                    AND events.id = $3
                    ORDER BY c.name';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'setAllCounterhuntrondjesAsInactive';
        $sqlQuery = 'UPDATE counterhunt_rondjes AS c
                    SET active=False
                    FROM deelgebied d
                    JOIN events ON (d.event_id = events.id)
                    WHERE c.deelgebied_id = d.id
                    AND c.organisation_id = $2
                    AND events.id = $3
                    AND d.name = $1';
        $this->prepareInternal($sqlName, $sqlQuery);

        $sqlName = 'setActiveCounterhuntrondjeId';
        $sqlQuery = 'UPDATE counterhunt_rondjes AS c
                    SET active=True
                    FROM deelgebied d
                    JOIN events ON (d.event_id = events.id)
                    WHERE c.deelgebied_id = d.id
                    AND c.id=$1
                    AND d.name = $2
                    AND c.organisation_id = $3
                    AND events.id = $4';
        $this->prepareInternal($sqlName, $sqlQuery);
    }

    private function prepareInternal($sqlName, $sqlQuery) {
        $prepStatement = pg_prepare($this->conn, $sqlName, $sqlQuery);
    }
}