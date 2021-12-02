<?php

//error_reporting(-1);
//ini_set('display_errors', 'On');

$baseURL = "http://".$_SERVER['HTTP_HOST']."/";
$offset = $_REQUEST["offset"];
$searchName = $_REQUEST["search_name"];
$phoneName = $_REQUEST["name"];
$output = "";
$searchPrompt = "Search Names";

$wUrl = "http://api.weatherstack.com/current";
$wKey =    "?access_key=866cbab8d1284e3ee5ce3d8123f99a2d";

function getWeather($q) {
global $wUrl, $wKey;
	$xml = file_get_contents($wUrl.$wKey."&query=".$q."&units=m");
$xml = json_decode($xml);
	
return $xml;
}
/*

object(stdClass)#2 (3) {
  ["request"]=>
  object(stdClass)#1 (4) {
    ["type"]=>
    string(4) "City"
    ["query"]=>
    string(16) "Montreal, Canada"
    ["language"]=>
    string(2) "en"
    ["unit"]=>
    string(1) "m"
  }
  ["location"]=>
  object(stdClass)#3 (9) {
    ["name"]=>
    string(8) "Montreal"
    ["country"]=>
    string(6) "Canada"
    ["region"]=>
    string(6) "Quebec"
    ["lat"]=>
    string(6) "45.500"
    ["lon"]=>
    string(7) "-73.583"
    ["timezone_id"]=>
    string(15) "America/Toronto"
    ["localtime"]=>
    string(16) "2021-12-01 13:52"
    ["localtime_epoch"]=>
    int(1638366720)
    ["utc_offset"]=>
    string(4) "-5.0"
  }
  ["current"]=>
  object(stdClass)#4 (16) {
    ["observation_time"]=>
    string(8) "06:52 PM"
    ["temperature"]=>
    int(1)
    ["weather_code"]=>
    int(113)
    ["weather_icons"]=>
    array(1) {
      [0]=>
      string(79) "https://assets.weatherstack.com/images/wsymbols01_png_64/wsymbol_0001_sunny.png"
    }
    ["weather_descriptions"]=>
    array(1) {
      [0]=>
      string(5) "Sunny"
    }
    ["wind_speed"]=>
    int(11)
    ["wind_degree"]=>
    int(260)
    ["wind_dir"]=>
    string(1) "W"
    ["pressure"]=>
    int(1017)
    ["precip"]=>
    int(0)
    ["humidity"]=>
    int(53)
    ["cloudcover"]=>
    int(0)
    ["feelslike"]=>
    int(-4)
    ["uv_index"]=>
    int(2)
    ["visibility"]=>
    int(10)
    ["is_day"]=>
    string(3) "yes"
  }
}

*/


function sendHeader() {
global $output;
header("Content-type: text/xml");
    header("Connection: close");
    header("Expires: -1");
	$output .= "<?xml version='1.0' encoding='UTF-8'?>\n";
        
return;
}

function sendTxt($txt)
{
    global $output, $weatherURL, $searchPrompt;

    
	$output .= "\n<CiscoIPPhoneText>";
  	$output .= "\n\t<Text>";
      $output .= $txt."";
	$output .= "</Text>";
    $output .= "\n</CiscoIPPhoneText>";
    return;
}

function appendImage($title, $prompt, $x, $y, $url) {
global $output;
$output .= "\n<CiscoIPPhoneImageFile>";
$output .= "\n  <Title>".$title."</Title>";
$output .= "\n  <Prompt>".$prompt."</Prompt>";
$output .= "\n  <LocationX>".$x."</LocationX>";
$output .= "\n  <LocationY>".$y."</LocationY>";
$output .= "\n  <URL>".$url."</URL>";
$output .= "\n</CiscoIPPhoneImageFile>";
}


function sendFooter() {
global $output;


   
return;
}

sendHeader();
$w = getWeather('Montreal');
sendTxt("Weather " . "\nCity: ".$w->request->query . "\nTime: " . $w->location->localtime . "\nCurrent Temperature: " . $w->current->temperature. "\nWind Speed: " . $w->current->wind_speed. "\nHumidity: " . $w->current->humidity);
//appendImage('Weather', '', 0, 0, $w->current->weather_icons[0]);
sendFooter();
print $output;
exit();

?>
