<?php
    require_once COUNTER_PATH."config.php";
    require_once COUNTER_PATH."failure.php";
    
    

    // $responseText is a string response from the clicky API that is an XML file
    // This file hopefully contains information on pageviews by ip addresses
    
    // This should return a list of integer valus corresponding to data points
    function parseClickyData($responseText, $requiredErrorInfo) {
    
        $xml = simplexml_load_string($responseText) or die("Unable to parse Clicky data");
        $visitData  = $xml->xpath("type[@type=\"visitors\"]/date/item/value") or fail("Unable to parse Clicky visitor data", 12, $requiredErrorInfo);
        $actionData = $xml->xpath("type[@type=\"actions\"]/date/item/value") or fail("Unable to parse Clicky action data", 12, $requiredErrorInfo);

        return array(intval((string)($visitData[0])),
                     intval((string)($actionData[0])));
    }
    
    function getDaysInMonth($month){
        return cal_days_in_month(CAL_GREGORIAN,
                                intval(substr($month, -2)), 
                                intval(substr($month, 0, 4)));
    }
    
    // Month is in format YYYY-MM
    function fetchClickyData($month, $ipList, $requiredErrorInfo){
        global $site_id, $site_key;
        $daysInMonth = getDaysInMonth($month);
        $range = [1, 0];
        
        // If you change the number of values we are keeping track of, change
        // the size of this array as well
        $totalValues = array(0, 0);
        
        while($range[1] < $daysInMonth) {
            $range[0] += $range[1] + 1;
            $range[1] = min($daysInMonth, $range[1] + 7);
            $dateRange = $month."-".sprintf("%02d",$range[0]).",".$month."-".sprintf("%02d",$range[1]);
            $url = "http://api.clicky.com/api/stats/4?site_id=".
                    $site_id."&sitekey=".$site_key."&type=segmentation".
                    "&segments=visitors,actions&date=".$dateRange.
                    "&output=xml&ip_address=".$ipList;
            // Note that wp_remote_get must be used as the wordpress equivelant of http_get
            $responseText = wp_remote_get($url,array('timeout'=> 10,)) or fail("Failed to load analytics", 11, $requiredErrorInfo);
            usleep(4000);
            if(!array_key_exists("response", $responseText) ||
               !array_key_exists("code",$responseText["response"]) ||
               $responseText["response"]["code"] != 200){
                fail("Failed to load analytics: ".var_export($responseText, true)."\n for request of:\n".$url, 11, $requiredErrorInfo);
            }
            $weekResults = parseClickyData($responseText["body"], $requiredErrorInfo);
            for($i = 0, $j = count($totalValues); $i < $j; $i++) {
                $totalValues[$i] += $weekResults[$i];
            }
        }
        
        return $totalValues;
    }
    
    function readCachedData($cacheFile){
        $numbers = explode(',', file_get_contents($cacheFile));
        return array_map('intval', $numbers);;
    }
    
    function findClickyData($month, $ipList, $cacheFile, $requiredErrorInfo){
        if(file_exists($cacheFile) && date('Y-m', time()) != $month){
            return readCachedData($cacheFile);
        } else {
            $data = fetchClickyData($month, $ipList, $requiredErrorInfo);
            file_put_contents($cacheFile, implode(",",$data));
            return $data;
        }
    }
?>