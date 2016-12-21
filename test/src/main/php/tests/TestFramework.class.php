<?php

abstract class TestFramework {
    private $pass = 0;
    private $fail = 0;
    
    abstract function runTests();
    
    protected function pass() {
        echo '<span style="color:green"><b>PASS</b></span><hr />';
        $this->pass = $this->pass + 1;
    }
    
    protected function fail($msg) {
        $this->fail = $this->fail + 1;
        echo '<span style="color:red"><b>FAIL</b></span>: ' . $msg . '<hr />';
    }
    
    protected function end() {
        if ($this->fail == 0) {
            echo 'ALL ' . $this->pass . ' PASSED';
        } else {
            echo 'FAILURES: ' . $this->fail . ' out of ' . ($this->pass + $this->fail);
        }
    }
    
}
?>