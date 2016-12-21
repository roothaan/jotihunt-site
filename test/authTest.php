<?php
require_once '../config.inc.php';
if (! defined('DEV_MODE')) {
    die('Not in dev mode');
}
require_once TEST_CLASS_DIR . 'tests/TestFramework.class.php';

require_once CLASS_DIR . 'user/AuthMgr.class.php';

class AuthTest extends TestFramework {
    
    // Implemented from TestFramework
    public function runTests() {
        $this->testAdmin();
        $this->testAdminSessionId();
        $this->testAdminFail();
        
        $this->testGetGroup();
        $this->testGetGroupFail();
        
        $this->getAllUsers();
        $this->testAdminLogout();
        
        $this->end();
    }
    private $adminSessionId;

    private function testAdmin() {
        echo 'start test <b>testAdmin</b><br />';
        $mgr = new AuthMgr();
        $sessionId = $mgr->login('admin', 'BaloeDeBeer');
        if ($sessionId !== false) {
            echo 'got sessionID:<code>' . $sessionId . '</code><br />';
            $this->adminSessionId = $sessionId;
            return $this->pass();
        }
        return $this->fail('sessionId === false: ' . $sessionId);
    }

    private function testAdminSessionId() {
        echo 'start test <b>testAdminSessionId</b><br />';
        if (null === $this->adminSessionId) {
            return $this->fail('adminSessionId has to be set first');
        }
        
        $mgr = new AuthMgr();
        $user = $mgr->getUser($this->adminSessionId);
        if ($user) {
            var_dump($user);
            return $this->pass();
        } else {
            return $this->fail('user not found via SessionId');
        }
    }

    private function testAdminFail() {
        echo 'start test <b>testAdminFail</b><br />';
        $mgr = new AuthMgr();
        $sessionId = $mgr->login('admin', 'wrongPassword!');
        if ($sessionId === false) {
            echo 'got sessionID:' . $sessionId . ' (false is expected)<br />';
            return $this->pass();
        }
        return $this->fail('sessionId should have been false at this point: ' . $sessionId);
    }

    private function getAllUsers() {
        echo 'start test <b>getAllUsers</b><br />';
        $siteDriver = DataStore::getSiteDriver();
        $allUsers = $siteDriver->getAllUsers();
        if (is_array($allUsers) && sizeof($allUsers) > 0) {
            echo 'found users:';
            var_dump($allUsers);
            return $this->pass();
        }
        return $this->fail('There should be at least 1 users (or result wasn\'t an array)');
    }

    private function testGetGroup() {
        echo 'start test <b>testGetGroups</b><br />';
        $mgr = new AuthMgr();
        if (null === $this->adminSessionId) {
            return $this->fail('adminSessionId has to be set first');
        }
        $mgr->setSessionId($this->adminSessionId);
        $success = $mgr->hasGroup('admin');
        if ($success) {
            return $this->pass();
        }
        return $this->fail('admin should be part of admin group');
    }

    private function testGetGroupFail() {
        echo 'start test <b>testGetGroupFail</b><br />';
        $mgr = new AuthMgr();
        if (null === $this->adminSessionId) {
            return $this->fail('adminSessionId has to be set first');
        }
        $mgr->setSessionId($this->adminSessionId);
        $success = $mgr->hasGroup('unknownGroup');
        if ($success) {
            return $this->fail('admin cannot be part of "unknownGroup" group');
        }
        return $this->pass();
    }

    private function testAdminLogout() {
        echo 'start test <b>testAdminLogout</b><br />';
        $mgr = new AuthMgr();
        $user = $mgr->getUser($this->adminSessionId);
        if ($user) {
            $mgr->setUser($user);
            $mgr->setSessionId($this->adminSessionId);
            $loggedIn = $mgr->isLoggedIn();
            if (! $loggedIn) {
                return $this->fail('isLoggedIn should have returned true here, but didn\'t');
            }
            // The real test :)
            $mgr->logout();
            
            $loggedIn = $mgr->isLoggedIn();
            if ($loggedIn) {
                return $this->fail('isLoggedIn should have returned false here, but didn\'t');
            }
            return $this->pass();
        } else {
            return $this->fail('missing session ID to work, test aborted');
        }
    }
}
?>