<?php
$_footerOptions = array();
$_footerOptions['includeHtml'] = true;
if (isset($footerOptions)) {
    $_footerOptions = array_merge($_footerOptions, $footerOptions);
}
?>
<?php
if ($_footerOptions['includeHtml']) { ?>
    <div id="footer">
        <p>Jotihunt <?php echo date('Y');?></p>
    </div>
    <!-- end #footer -->
<?php } // end of 'includeHtml'?> 
</body>
</html>
<?php
if (isset($timing) && $authMgr->isAdmin() && isset($_REQUEST ['timer'])) {
    $timing ['index.php AFTER FOOTER'] = microtime(true);
    $prevTime = 0;
    $startTime = 0;
    echo '<pre>';
    foreach ( $timing as $key => $val ) {
        if ($startTime == 0) {
            $startTime = $val;
            $prevTime = $val;
        }
        echo $key . ':' . ($val - $prevTime) . '<br />';
        $prevTime = $val;
    }
    echo 'Total time:' . ($prevTime - $startTime);
}
?>
