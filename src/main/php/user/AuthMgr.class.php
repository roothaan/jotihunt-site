<?php
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'datastore/postgresql/SiteDriver.postgresql.class.php';

require_once CLASS_DIR . 'user/User.class.php';
require_once CLASS_DIR . 'user/Group.class.php';
require_once CLASS_DIR . 'user/Session.class.php';

class AuthMgr {
    private $siteDriver;
    private $sessionLength = 40;
    private $cryptoStrong = true;
    private $sessionId;
    private $user;

    public function __construct() {
        $this->siteDriver = Datastore::getSiteDriver();
    }

    public function loginViaPost() {
        if (! isset($_POST ["submit"]) || ! isset($_POST ["username"]) || empty($_POST ["username"]) || ! isset($_POST ["password"]) || empty($_POST ["password"])) {
            return '<p style="color:red;">Gebruik je gebruikersnaam en wachtwoord om in te loggen.</p>';
        }
        
        $sessionId = $this->login($_POST ["username"], $_POST ["password"]);
        if ($sessionId !== false) {
            $days = 30;
            $thirtyDays = time() + 60 * 60 * 24 * $days;
            setcookie('spelsitehash', $sessionId, $thirtyDays);
            header('Location: '.WEBSITE_URL);
            die();
        } else {
            return '<p style="color:red;">Ongeldige combinatie van gebruikersnaam en wachtwoord.</p>';
        }
    }

    /**
     *
     * @return the sessionID if valid, null otherwise
     */
    public function loginViaAPI($request) {
        $un = null;
        $pw = null;
        if (isset($_SERVER ['authenticationUsername']) && isset($_SERVER ['authenticationPassword'])) {
            $un = $_SERVER ['authenticationUsername'];
            $pw = $_SERVER ['authenticationPassword'];
            error_log("[AuthMgr->loginViaAPI] INFO Found " . $un . " via lowercase version");
        }
        if (isset($_SERVER ['HTTP_AUTHENTICATIONUSERNAME']) && isset($_SERVER ['HTTP_AUTHENTICATIONPASSWORD'])) {
            $un = $_SERVER ['HTTP_AUTHENTICATIONUSERNAME'];
            $pw = $_SERVER ['HTTP_AUTHENTICATIONPASSWORD'];
            error_log("[AuthMgr->loginViaAPI] INFO Found " . $un . " via UPPERCASE version");
        }
        
        if (isset($un) && isset($pw)) {
            error_log("[AuthMgr->loginViaAPI] INFO Logging in " . $un);
            $sessionId = $this->login($un, $pw);
            if ($sessionId !== false) {
                error_log("[AuthMgr->loginViaAPI] INFO Retrieved sessionId for " . $un);
                $this->setSessionId($sessionId);
                return $sessionId;
            }
            error_log("[AuthMgr->loginViaAPI] ERROR Could NOT authenticate this person: " . $un);
        }
        error_log("[AuthMgr->loginViaAPI] FATAL Could NOT authenticate, no un/pw found");
        return null;
    }

    /**
     * <p>This is authentication via the ANDROID APP (or API)!</p>
     *
     * <p>Authenticate via authenticationToken (old style, plain-text code).</p>
     *
     * @param Request $request            
     * @return boolean true is user token is valid, false otherwise
     */
    public function attemptAuthViaAPI($request) {
        $token = null;
        if (isset($_SERVER ['authenticationToken'])) {
            $token = $_SERVER ['authenticationToken'];
            error_log("[AuthMgr->attemptAuthViaAPI] INFO login attempt via lowercase Token: ". $token);
        }
        
        // Something it's all upper case I guess?
        if (isset($_SERVER ['HTTP_AUTHENTICATIONTOKEN'])) {
            $token = $_SERVER ['HTTP_AUTHENTICATIONTOKEN'];
            error_log("[AuthMgr->attemptAuthViaAPI] INFO login attempt via UPPERCASE Token: ". $token);
        }

        $this->user = $this->getUser($token);
        if ($this->isRealUser()) {
            error_log("[AuthMgr->attemptAuthViaAPI] INFO Real user found!");
            $request->setAuthCode($token);
            $this->setSessionId($token);
            return true;
        }
        
        if (defined('DEV_MODE') && DEV_MODE == true) {
            if ($this->attemptAuth()) {
                return true;
            }
        }
        
        error_log("[AuthMgr->attemptAuthViaAPI] FATAL NO real user found!");
        return false;
    }

    public function attemptAuth() {
        // Get it from the cookie!
        $token = $this->getCookieValue();
        if (null == $token) {
            return;
        }
        $this->user = $this->getUser($token);
        if ($this->isRealUser()) {
            $this->setSessionId($token);
            return true;
        }
        return false;
    }

    private function getCookieValue() {
        if (isset($_COOKIE ['spelsitehash'])) {
            return $_COOKIE ['spelsitehash'];
        }
        return null;
    }

    /**
     * <p>Invalidates (removes) sessionId, remove cookie value.</p>
     * <p>It also removes the current user/sessionId, so they cannot be reused.</p>
     */
    public function logout() {
        if (isset($this->sessionId)) {
            $this->siteDriver->removeSessionId($this->sessionId);
            setcookie('spelsitehash', '', 0);
        }
        $this->sessionId = null;
        $this->user = null;
    }

    /**
     * <p>Logs you in, returns a hash if success, false otherwise.</p>
     */
    public function login($username, $password) {
        $user = $this->siteDriver->login($username, $password);
        if ($user !== false) {
            $sessionId = base64_encode(openssl_random_pseudo_bytes($this->sessionLength, $this->cryptoStrong));
            // Get Organisation (if >1, 0)
            $org = $this->siteDriver->getOrganisationForUser($user);
            
            // Get Event (if >1, 0)
            
            $success = $this->siteDriver->addSessionId($sessionId, $user, $org);
            if ($success) {
                error_log("[AuthMgr->login] INFO successful login (got sessionId, added to DB) for " . $username);
                return $sessionId;
            }
            error_log("[AuthMgr->login] ERROR failed login (got sessionId, NOT added to DB) for " . $username);
        } else {
            error_log("[AuthMgr->login] ERROR failed login (could not find a user) for " . $username);
        }
        return false;
    }

    /**
     *
     * @param string $sessionId            
     * @return Ambigous <boolean, User>
     */
    public function getUser($sessionId) {
        if ($this->siteDriver->isReady()) {
            return $this->siteDriver->getUser($sessionId);
        }
        return false;
    }

    public function setUser($user) {
        if ($user instanceof User) {
            $this->user = $user;
        } else {
            throw Exception('user not instanceof User');
        }
    }

    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;
    }
    
    public function getSessionId() {
        return $this->sessionId;
    }

    public function isRealUser() {
        if ($this->user instanceof User) {
            return true;
        }
        return false;
    }
    
    // Only use these!
    public function isLoggedIn() {
        if (null === $this->sessionId) {
            return false;
        }
        return $this->isRealUser();
    }

    public function getMe() {
        return $this->user;
    }
    
    
    public function getSessionInformation() {
        if (null === $this->sessionId) {
            return false;
        }
        $session = $this->siteDriver->getSessionInformation($this->sessionId);
        return $session;
    }

    public function getMyEventId() {
        if (null === $this->sessionId || null == $this->getSessionInformation()) {
            return false;
        }
        return $this->getSessionInformation()->getEventId();
    }

    public function getMyOrganisationId() {
        if (null === $this->sessionId || !$this->getSessionInformation()) {
            return false;
        }
        return $this->getSessionInformation()->getOrganisationId();
    }

    public function isAdmin() {
        if (!$this->isSuperAdmin()) {
            return $this->isLoggedIn() && $this->hasGroup('admin');
        }
        return true;
    }

    public function isSuperAdmin() {
        return $this->isLoggedIn() && $this->getMe()->getId() === 1;
    }

    public function requireAdmin() {
        if (!$this->isSuperAdmin()) {
            $this->requireGroup('admin');
        }
    }

    public function requireSuperAdmin() {
        $this->requireSuperAdminId();
    }

    public function hasGroup($groupname) {
        if (null == $this->sessionId) {
            return false;
        }
        $groups = $this->siteDriver->getGroupsOfUser($this->sessionId);
        
        if (is_array($groups) && sizeof($groups) > 0) {
            foreach ( $groups as $group ) {
                if ($group->getName() === $groupname) {
                    return true;
                }
            }
        }
        return false;
    }

    public function requireGroup($group) {
        if (null == $this->sessionId) {
            header('HTTP/1.0 401 Unauthorized');
            die('You\'re not logged in! [no sessionId]');
        }
        if (! $this->hasGroup($group)) {
            header('HTTP/1.0 403 Forbidden');
            die('You\'re not logged in! [not part of the required group ('.$group.')]');
        }
        return true;
    }
    
    public function requireSuperAdminId() {
        if (!$this->getMe() || $this->getMe()->getId() !== 1) {
            header('403 Forbidden');
            die('You\'re not the SuperAdmin');
        }
        return true;
    }

    public static function getHash($password) {
        if (function_exists('password_hash')) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            error_log("[AuthMgr::hash] INFO via password_hash: " . $hash);
            return $hash;
        } else {
            throw Exception('The password_hash method does not exist, consider upgrading your PHP version to PHP 5 >= 5.5.0 or PHP 7');
        }
    }
}