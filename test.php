<?php

require "class/zetascan.php";

$zs = new zetascan("YOURAPIKEY", false);

// Choose from http, text, json or jsonx
$zs->apiMethod = "http";

// Test a bad domain
echo "Testing baddomain.org\n";

$response = $zs->Query("baddomain.org");

if($zs->IsBlackList($response)) {
    echo "baddomain.org matches a black-list!\n";
    echo "Score => " . $response["results"][0]["Score"];
}

echo "\n\n";

// Test a good IP
echo "Testing okdomain.org\n";

$response = $zs->Query("okdomain.org");

if($zs->isWhiteList($response)) {
    echo "okdomain.org matches a white-list!\n";
}

//var_dump($zs);

?>
