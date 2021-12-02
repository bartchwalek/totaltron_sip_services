<?php

$baseURL = "http://".$_SERVER['HTTP_HOST']."/";
$offset = $_REQUEST["offset"];
$searchName = $_REQUEST["search_name"];
$phoneName = $_REQUEST["name"];
$output = "";
$searchPrompt = "Search Names";


function sendHeader() {
global $output;
header("Content-type: text/xml");
    header("Connection: close");
    header("Expires: -1");

	$output .= "<CiscoIPPhoneMenu>\n";
    $output .= "\t<Title>Services</Title>\n";
      $output .= "\t<Prompt>Services</Prompt>\n";
return;
}

function sendMenu($title, $prompt)
{
    global $output, $weatherURL, $searchPrompt;

    
	
  
      $output .= "\t<MenuItem>\n";
         $output .= "\t\t<Name>".$title."</Name>\n";
         $output .= "\t\t<URL>" . $prompt ."</URL>\n";
      $output .= "\t</MenuItem>\n";

    
    return;
}


function sendFooter() {

global $output;
   $output .= "</CiscoIPPhoneMenu>\n";
return;
}

sendHeader();
sendMenu('Weather', $baseURL."weather.php");
sendMenu('SMS', $baseURL."sms.php");
sendMenu('News', $baseURL."news.php");
sendFooter();
print $output;
exit();

?>
