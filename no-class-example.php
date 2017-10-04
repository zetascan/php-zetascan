<?
/*
// HTTP method
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.zetascan.com/v2/check/http/baddomain.org?key=YOURAPIKEY');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Request headers only
curl_setopt($ch, CURLOPT_HEADER, true); 
curl_setopt($ch, CURLOPT_NOBODY, true);

$response = curl_exec($ch);

// Lookup the HTTP status code
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check status codes for a match
if($status == 204) {
    echo "No match in Zetascan blacklist or white-list";

} else if($status == 403)   {
    echo "Request forbidden, check API-key or IP registered";

} else if($status == 200) {

    $headers = array();    
    $data = explode("\n",$response);
    
    $headers['status'] = $data[0];
    
    // Take the HTTP response out, first element
    array_shift($data);
    
    // Build an array for our headers
    foreach($data as $part){
        $middle=explode(":",$part);
        $headers[trim($middle[0])] = trim($middle[1]);
    }

	// Display the score if blacklisted, otherwise if a whitelist
    if( $headers[x-zetascan-score] > 0 || $headers[x-zetascan-webscore] > 0)    {
        echo "Item blacklisted, score " . $headers[x-zetascan-score] . " webscore " . $headers[x-zetascan-webscore];
    } else {
        echo "Item white-listed";
    }
}

// Text method
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.zetascan.com/v2/check/text/baddomain.org?key=d9e7fccc96280b799df429b71c8c508e');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

// Lookup the HTTP status code
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check status codes for a match
if($status == 204) {
    echo "No match in Zetascan blacklist or white-list";

} else if($status == 403)   {
    echo "Request forbidden, check API-key or IP registered";

} else if($status == 200) {

        // Read the body and split from the specified API formatting
        $head = explode(":", $response);

        if($head[0] == "error") {

            echo "An error occurred " . $head[1];

        } else {

            $str = explode(",", $head[1]);
            
            // $str will contain each field as specified below
            echo "Blacklist hit => " . $str[0] . "\n";
            echo "Whitelist hit => " . $str[1] . "\n";
            echo "Whitelist data => " . $str[2] . "\n";
            echo "Score => " . $str[3] . "\n";
            echo "WebScore => " . $str[4] . "\n";
    
            echo "Sources => ";
    
            // List all sources, comma seperated
            for($i = 5; $i < count($str); $i++) {
                echo $str[$i] . ",";
            }

        }
 
        
}

// JSON method
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.zetascan.com/v2/check/json/baddomain.org,okdomain.org,badquery?key=d9e7fccc96280b799df429b71c8c508e');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

// Lookup the HTTP status code
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check status codes for a match
if($status == 403)   {
    echo "Request forbidden, check API-key or IP registered";

} else if($status == 200) {

    $json = json_decode($response, true);

    // Check for any error messages, auth failure, API-key incorrect, etc.
    if( !empty($json["error"]["message"])) {
        echo "An error occurred " . $json["error"]["message"];
    } 

    // Loop through each response ( multiple queries can be delimitated by a comma)
    for($i = 0; $i < count($json["results"]); $i++) {
        
        echo "Query => " . $json["results"][$i]["item"] . "\n";

        // Check for an error message
        if($json["results"][$i]["error"]) {
            echo "An error occurred " . $json["results"][$i]["error"]["message"] . "\n";
            continue;
        }
        
        // Print information on the record
        if( empty( $json["results"][$i]["wl"] ) && !empty($json["results"][$i]["found"]) ) {
            echo "Blacklist hit\n";
        } else {
            echo "Whitelist hit\n";
            echo "Whitelist data => " . $json["results"][$i]["wldata"] . "\n";
        }

        echo "Score => " . $json["results"][$i]["score"] . "\n";
        echo "WebScore => " . $json["results"][$i]["webscore"]. "\n";

        echo "\n";

    }
    
}

*/

// Simple JSON method
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.zetascan.com/v2/check/json/baddomain.org,okdomain.org,badquery?key=d9e7fccc96280b799df429b71c8c508e');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

// Lookup the HTTP status code
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check status codes for a match
if($status == 403)   {
    echo "Request forbidden, check API-key or IP registered";

} else if($status == 200) {

    $json = json_decode($response, true);

    // Check for any error messages, auth failure, API-key incorrect, etc.
    if( !empty($json["error"]["message"])) {
        echo "An error occurred " . $json["error"]["message"];
    } 

    // Loop through each response ( multiple queries can be delimitated by a comma)
    for($i = 0; $i < count($json["results"]); $i++) {
        
        echo "Query => " . $json["results"][$i]["item"] . "\n";

        // Check for an error message
        if($json["results"][$i]["error"]) {
            echo "An error occurred " . $json["results"][$i]["error"]["message"] . "\n";
            continue;
        }
        
        print_r($json["results"][$i]);

    }
    
}


// JSONx method
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.zetascan.com/v2/check/jsonx/127.9.9.1?key=d9e7fccc96280b799df429b71c8c508e');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

// Lookup the HTTP status code
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check status codes for a match
if($status == 403)   {
    echo "Request forbidden, check API-key or IP registered";

} else if($status == 200) {

    $json = json_decode($response, true);

    // Display the array
    print_r($json);

    // Display extended information
    print_r($json["results"][0]["extended"]);
    
}

?>