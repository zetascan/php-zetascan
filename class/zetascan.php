<?php

// Define the API end point and connection details
defined('ZS_apiURL') or define('ZS_apiURL', 'api.zetascan.com');
defined('ZS_apiProtocol') or define('ZS_apiProtocol', 'https'); // SSL on by default
defined('ZS_apiMethod') or define('ZS_apiMethod', 'http'); // Method
defined('ZS_apiVersion') or define('ZS_apiVersion', 'v2'); // Version

// Create the Zetascan class
if (!class_exists('zetascan')) {
    class zetascan {

        // Setup the environment and validate
        // TODO: Improve setting other fields such as apiURL, etc.
        function __construct($apiKey, $ipCheck) {

            // API key specified?
            if( !empty($apiKey) ) {
                $this->apiKey = $apiKey;
            }

            // Check if https required
            if (ZS_apiProtocol == "http" && empty( $this->apiKey ) && empty($ipCheck)) {
                throw new Exception("https required if using API key without ip check");
            }

            // TODO: improve
            $this->apiURL = ZS_apiURL;
            $this->apiProtocol = ZS_apiProtocol;
            $this->apiMethod = ZS_apiMethod;
            $this->apiVersion = ZS_apiVersion;
            

        }


        // Return a URL for the API end-point.
        function getUrl($domain) {

            $str = $this->apiProtocol . "://" . $this->apiURL . "/" . $this->apiVersion . "/check/" . $this->apiMethod . "/" . $domain;

            if( !empty($this->apiKey) ) {
                $str = $str . "?key=" . urlencode($this->apiKey);
            }

            // Return our URL for the API end-point with the correct query + arguments
            return $str;
        }
            
 
        // Query function ( for all methods, in one wrapper )
        function Query($query) {

            // TODO: Implement PHP DNS method
            if($this->apiMethod == "dns") {

            } else {

                // Generic HTTP methods
                $ch = $this->Get( $this->getUrl($query) );

                // Execute the query, fetch the result
                $res = curl_exec($ch);

                // Lookup the HTTP status code
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if($statusCode == "404") {
                    throw new Exception("Invalid request, check URL not malformed: " . $this->getUrl($query) );
                } else if($statusCode == "403") {
                    throw new Exception("Request forbidden, check API key or IP for authorization: " . $this->getUrl($query) );                    
                } else {
                    // Status 200
                    $result = $this->parseResult($ch, $res);           
                }

            }

            return $result;

        }

        function Get($query) {

            $ch = curl_init();
            $this->headers = [];
            curl_setopt($ch, CURLOPT_URL, $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            // this function is called by curl for each header received, required to parse custom headers in parseResult()
            curl_setopt($ch, CURLOPT_HEADERFUNCTION,
              function($curl, $header) use (&$headers)
              {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                  return $len;
            
                $name = strtolower(trim($header[0]));
                if (!array_key_exists($name, $this->headers))
                  $this->headers[$name] = [trim($header[1])];
                else
                  $this->headers[$name][] = trim($header[1]);
            
                return $len;
              }
            );
            
            //$res = curl_exec($ch);

            return $ch;

        }


        // Check if the response is listed
        function isMatch($data) {

            if($data["results"][0]["Found"] == true) {
                return true;
            }

            return false;

        }
            
        // Check if the response is whitelisted
        function IsWhiteList($data) {

            if($data["results"][0]["Wl"] == true || $data["results"][0]["Score"] < 0) {
                return true;
            }

            return false;

        } 
            
        // Check if the response is blacklisted
        function IsBlackList($data) {

            if($data["results"][0]["Found"] == true && $data["results"][0]["Wl"] == false) {
                return true;
            }

            return false;

        }

        function Score($data)   {

            if($data["results"][0]["Found"] == true || $data["results"][0]["Wl"] == true) {
                return $data["results"][0]["Score"];
            }
        }

        // Parseresult ( from HTTP methods or DNS, a single format is returned )
        function parseResult($ch, $res)  {

            // Our 3D array, to parse the result and return
            $data = array(
                "Item" => "",
                "Found" => "",
                "Score" => "",
                "FromSubnet" => "",
                "Sources" => "",
                "Wl" => "",
                "Wldata" => "",

                "Extended" => array(
                    "ASNum" => "",
                    "Route" => "",
                    "Country" => "",
                    "Domain" => "",
                    "State" => "",
                    "Time" => "",

                    "Reason" => array(
                        "Class" => "",
                        "Rule" => "",
                        "Type" => "",
                        "Name" => "",
                        "Source" => "",
                        "Port" => "",
                        "SourcePort" => "",
                        "Destination" => ""                   
                    )
                )

            );
               
            switch ($this->apiMethod) {

                case "http":

                    // If using the HTTP method, retrieve and build our response based on the HTTP status + related headers
                    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if($statusCode == "204") {
                        $data["Found"] = false;
                    } else {
                        $data["Found"] = true;
                    }

                    //var_dump($this->headers);

                    $data["Score"] = $this->headers["x-zetascan-score"][0];

                    // Split multiple sources into an array
                    $data["Sources"] = explode(";", $this->headers["x-zetascan-sources"][0]);
                    
                    $wl = $this->headers["x-zetascan-wl"][0];

                    // TODO: Validate correct compared to JSON feed
                    if($wl == "null") {
                        $data["Wl"] = false;
                    } else {
                        $data["Wl"] = true;
                        
                    }
                    
                    $data["Status"] = $this->headers["x-zetascan-status"][0];

                break;

                case "text":

                    // Read the body and split from the specified API formatting
                    $head = explode(":", $bodyString);
                    $str = explode(",", $head[1]);

                    /*
                        http://docs.zetascan.io/?php#http-format
                        item:bool,bool,wldata,score,source

                        Where:

                        the first bool is true, if found in any black list,
                        the second bool is true, if found in any white list,
                        wldata contains the data from the white list, and
                        score is followed by the list of sources where the item was found.
                    */
                    
                    if ($str[0] == "true") {
                        $data["Found"] = true;
                    } else {
                        $data["Found"] = false;
                    }

                    $data["Score"] = $str[3];

                break;

                case "json":
                case "jsonx":
                    echo $res;
                    // Matches our existing $data array
                    $data = json_decode($res, true);

                break;


            }

            $obj = array(
                "results" => array()
            );

            $obj["results"][] = $data;
            //array_push($obj["results"], $data);

            return $obj;

            }

        }
   
}