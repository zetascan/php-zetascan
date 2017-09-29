<?php

require "class/zetascan.php";

$zs = new zetascan("", false);

// Test a bad domain
echo "Testing baddomain.org\n";
var_dump($zs->Query("baddomain.org"));

echo "\n\n";

// Test a good IP
echo "Testing okdomain.org\n";
var_dump($zs->Query("okdomain.org"));

//var_dump($zs);

?>
