# php-zetascan
#### Development library for the zetascan API in PHP

## Introduction
 
The [Zetascan Query Services](https://zetascan.com/) "ZQS" was created to facilitate the real-time lookup of IP and Domain threat data into various applications and services. Currently there are dozens of various domain and IP data-feeds available to developers. Many of these feeds are available free of charge and some are paid for services when minimum query levels are exceeded. In addition, there are 2 main problems with trying to incorporate multiple data feed into a solution:

* The overlap between data feed providers in the content listed (IPs & URIs), and

* The absence of normalized meta-data related to the IPs or Domains.

Because of the above, many developers asked if we could do something to reduce the complexity related to accessing and using threat data as part of their applications - MQS is our solution. We are introducing a more elegant API for developers, with an affordable pricing model to match.

To start, [signup for a developer key](https://zetascan.com/signup/?lang=en) and begin to integrate MQS into your web-apps and mobile applications.

## php-zetascan 

The php-zetascan library provides an API interface to query zetascan via HTTP and provides examples on how to integrate your web-app or mobile application to prevent abuse.

### Example domain query via HTTP

Query the zetascan service using the JSON API method. View the [developer docs](http://docs.zetascan.com/) for more information on the methods available.

See the example below for querying a "good" and "bad" domain via Zetascan, matching the result, and finding the score.

```php

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
```

### Additional examples

Coming soon.

