<?php
require_once '../config.inc.php';
if (! defined('DEV_MODE')) {
    die('Not in dev mode');
}

require_once 'authTest.php';
?>
<html>
<head>
<title>Run tests</title>
</head>
<body>
    <h1>Tests</h1>
    <a href="<?= TEST_URL ?>?runTest=auth">Auth tests</a>
    <hr />
    <?php
    
    if (isset($_GET ['runTest']) && $_GET ['runTest'] == 'auth') {
        $authTests = new AuthTest();
        $authTests->runTests();
    }
    ?>
</body>

</html>