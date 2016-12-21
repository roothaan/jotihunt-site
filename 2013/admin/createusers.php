<?php
require_once '../init.php';
$authMgr->requireAdmin();
require_once BASE_DIR . 'header.php'; 

require_once CLASS_DIR . 'datastore/mail/Email.class.php';
require_once CLASS_DIR . 'datastore/mail/Mailer.class.php';

function createUser($username, $password, $displayName, $groupId) {
    global $driver, $authMgr;
    
    $organisationId = $authMgr->getMyOrganisationId();
    
    // Create a hash out of the password, this is what gets stored in the DB
    $pw_hash = AuthMgr::getHash($password);
    
    // Add user
    $user = new User(null, $username, $displayName, $pw_hash);
    $newUser = $driver->addUser($user);

    if (!$newUser) {
        echo "<div class='user notCreated'>".$username."</div>";
        return false;
    }
    
    $success = $driver->addUserToGroup($newUser->getId(), $groupId);
    $success = $driver->addUserToOrganisation($newUser->getId(), $organisationId);
    
    return $newUser;
}

function createHunter($userId, $eventId, $starttime, $endtime) {
    $driver = DataStore::getSiteDriver();
    
    $rider = new Rider();
    $rider->setUserId($userId);
    $rider->setDeelgebied($eventId);
    $rider->setVan(strtotime($starttime));
    $rider->setTot(strtotime($endtime));
    $rider->setAuto(getRandomCar());
    $driver->addRider($rider);
    return $rider;
}

function getRandomCar() {
    global $cars;
    if (sizeof($cars) === 0) {
        return null;
    }
    
    $carId = array_rand($cars);
    $car = $cars[$carId];
    unset($cars[$carId]);
    
    return $car;
}

function mailDetails($emailaddress, $username, $password, $displayName) {
    $email = new Email();
    
    // subject
    $email->setSubject('Account aangemaakt voor Jotihunt '.date('Y'));

    // message
    $email->setHtml('<html>
    <head>
      <title>'.$email->getSubject().'</title>
    </head>
    <body>
      <h1>'.$email->getSubject().'</h1>
      <p>Hallo '.$displayName.',</p>
      <p>Omdat je je hebt aangemeld voor de Jotihunt '
      . date('Y')
      . ' ontvang je bij deze je gebruikersnaam en wachtwoord.
      Hiermee kun je inloggen op de website
      (<a href="http:'.WEBSITE_URL.'" 
          target="_blank" 
          title="Jotihunt Website">http:'.WEBSITE_URL.'</a>
      ) en de <a href="https://play.google.com/store/apps/details?id=org.roothaan.jotihunt&hl=nl" 
                 target="_blank" 
                 title="Jotihunt App">app</a>.</p>
      <table>
        <tr>
          <td>Gebruikersnaam</td><td>'.$username.'</td>
        </tr>
        <tr>
          <td>Wachtwoord</td><td>'.$password.'</td>
        </tr>
      </table>
      <p>Groet,<br />Het Jotihunt IT Team</p>
    </body>
    </html>');
    
    $email->setTo($displayName.' <'.$emailaddress.'>');
    if (defined('MAILGUN_FROM_EMAIL')) {
        $email->setFrom('Jotihunt IT Team <'.MAILGUN_FROM_EMAIL.'>');
    }

    // Mail it
    if ($emailaddress) {
        $mailer = new Mailer();
        $mailer->send($email);
    }
}

function parseExcelFile() {
    if(isset($_FILES['user_excel']) && isset($_FILES['user_excel']["tmp_name"])) {
        if (!is_file($_FILES['user_excel']["tmp_name"])) {
            echo '<p class="errorMsg2">Vergeet niet de CSV file ook te selecteren.</p>';
            return;
        }
        if (($handle = fopen($_FILES['user_excel']["tmp_name"], "r")) !== FALSE) {
            $cars = scandir(BASE_DIR.'images/cars/');
            unset($cars[0]); unset($cars[1]);
            
            $firstLine = true;
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Skip the first line
                if ($firstLine) {
                    echo '<h1>Creating users...</h1>';
                    $firstLine = false;
                    continue;
                }
                
                $groupId = $_POST['groupId'];
                $newUser = createUser($data[1],$data[2],$data[0], $groupId);
                if ($newUser) {
                    $eventId = $_POST['eventId'];
                    $van = $_POST['rider_van'];
                    $tot = $_POST['rider_tot'];
                    $newRider = createHunter($newUser->getId(), $eventId, $van, $tot);
                    mailDetails($data[3],$data[1],$data[2],$data[0]);
                    echo '<p>Created user: <strong>' . $newUser->getDisplayName() . '</strong></p>';
                } else {
                    echo '<p class="errorMsg2">Could NOT create user with data:<br/>';
                    echo 'Name:' . $data[0] . '<br/>';
                    echo 'Username:' . $data[1] . '<br/>';
                    echo 'Password:' . $data[2] . '<br/>';
                    echo 'Email:' . $data[3];
                    echo '</p>';
                }
            }
            fclose($handle);
            echo "<p>Users succesvol aangemaakt.</p>";
        }
    }
} ?>
<div id="page">
    <div id="content">
        <?php parseExcelFile(); ?>
        <h1>Create Users</h1>
        <form method="post" enctype="multipart/form-data">
            <p>Upload hier de <code>.csv</code> file met daarin: Volledige naam, username, password, e-mailadres</p>
            <p><strong>Let op: De eerste kolom wordt genegeerd (dit zijn de headers).</strong></p>
            <table>
                <tr>
                    <th>Group ID</th>
                    <td><input type="text" name="groupId" value="2"/> (2 = Users)</td>
                </tr>
                <tr>
                    <th>Event ID</th>
                    <td><input type="text" name="eventId" value="4"/> (4 = Alpha)</td>
                </tr>
                <tr>
                    <th>Van</th>
                    <td><input type="text" name="rider_van" value="2016-10-01 00:00:00" /></td>
                </tr>
                <tr>
                    <th>Tot</th>
                    <td><input type="text" name="rider_tot" value="2016-12-31 23:55:00"/></td>
                </tr>
        
                <tr>
                    <th>CSV file</th>
                    <td><input type="file" name="user_excel" accept=".csv" /></td>
                </tr>
            </table>
            <input type="submit" value="Create users" />
        </form>
    </div>
</div>


<?php
require_once BASE_DIR . 'footer.php';