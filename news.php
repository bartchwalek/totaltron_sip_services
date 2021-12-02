<?php
 $base = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
// Make sure SimplePie is included. You may need to change this to match the location of autoloader.php
// For 1.0-1.2:
 
#require_once('../simplepie.inc');
// For 1.3+:
require_once('./simplepie/autoloader.php');
 
// We'll process this feed with all of the default options.
$feed = new SimplePie();
 
// Set which feed to process.
 $feed->set_feed_url('https://globalnews.ca/montreal/feed/');
// Run SimplePie.
$feed->init();

$id = $_REQUEST['id'];
 
// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
$feed->handle_content_type();
 
// Let's begin our XHTML webpage code.  The DOCTYPE is supposed to be the very first thing, so we'll keep it on the same line as the closing-PHP tag.

	/*
	Here, we'll loop through all of the items in the feed, and $item represents the current item in the loop.
    */

    if(!isset($id)){
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><CiscoIPPhoneMenu></CiscoIPPhoneMenu>");
        $cnt = 0;
        foreach ($feed->get_items() as $item) {
        $track = $xml->addChild('MenuItem');
        $track->addChild('Name', $item->get_title());
        $track->addChild('URL', $base . "?id=" . $cnt);
        $cnt++;
    }
    } else {
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><CiscoIPPhoneText></CiscoIPPhoneText>");
        $item = $feed->get_item(($id));
        $xml->addChild('Text', $item->get_description());
    }

    
Header('Content-type: text/xml');
print($xml->asXML());
?>