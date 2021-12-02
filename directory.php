<?php

$directoryTitle = "Directory";
$directoryURL = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$directoryPrompt = "Select an entry to dial";
$welcomePrompt = "Search or tap Submit to browse";
$searchPrompt = "Search Names";
$vcardfile = "./contacts.vcf";


$offset = $_REQUEST["offset"];
$searchName = $_REQUEST["search_name"];
$phoneName = $_REQUEST["name"];
$output = "";

if (!preg_match("/^[0-9]+$/", $offset)) {
    $offset = 0;
}
$maxEntry = $offset + 32; # display maximum of 32 entries
$myEntry = 0;

### Read vCard file
$lines = file($vcardfile);
if (!$lines) {
     exit("Can't read the vCard file: $vcardfile");
}


if (!empty($searchName) || empty($phoneName)) {

  addDirectoryHeader($directoryTitle,$directoryPrompt);

  addStaticEntries();
  
  # Loop through each directory entry
  foreach ($lines as $line) 
  {

     # Once a full page is ready and we have more, send refresh header
     if ($myEntry >= $maxEntry) {
        header("Refresh: 0; url=" . $directoryURL . "?offset=$maxEntry\n");
        break;
     }

     if (preg_match("/^END/",$line)) {
        unset($name);
        unset($uri);
        next;
     }
     if (preg_match("/^FN:(.+)$/",$line,$matches)) {
        $name = trim($matches[1]);
        next;
     }
     if (preg_match("/^TEL.+:(.+)$/",$line,$matches) && (!stristr($line,"FAX"))) {
        # We don't want fax numbers
        $uri = trim($matches[1]);
        if (!empty($name) && !empty($uri)) {
               # Rudimentary labeling system for entries with multiple phone numbers
               if (stristr($line,"WORK")) { $label = $name . " (Work)"; }
               else if (stristr($line,"WORK")) { $label = $name . " (Work)"; }
               else if (stristr($line,"HOME")) { $label = $name . " (Home)"; }
               else if (stristr($line,"CELL")) { $label = $name . " (Mobile)"; }
               else if (stristr($line,"MAIN")) { $label = $name . " (Main)"; }
               else if (stristr($line,"IPPHONE")) { $label = $name . " (ip)"; }
               addEntry($label, $uri);
               unset($uri);
               unset($label);
        }
        next;
     }
  }
  addDirectoryFooter();
} 
else
{
  sendSearchMenu($directoryTitle,$welcomePrompt);
}

print $output;
exit();

### --------------------------------------------------------------------------
### Helper functions only beyond this point
### --------------------------------------------------------------------------

function addDirectoryHeader($title, $prompt)
{
    global $output;

    header("Content-type: text/xml");
    header("Connection: close");
    header("Expires: -1");

    $output = "<CiscoIPPhoneDirectory>\n";
    $output .= "\t<Title>" . $title . "</Title>\n";
    $output .= "\t<Prompt>" . $prompt . "</Prompt>\n";
    return;
}

function addDirectoryFooter()
{
    global $output;

    $output .= "</CiscoIPPhoneDirectory>\n";
    return;
}

function sendSearchMenu($title, $prompt)
{
    global $output, $directoryURL, $searchPrompt;

    header("Content-type: text/xml");
    header("Connection: close");
    header("Expires: -1");

	$output .= "<CiscoIPPhoneMenu>\n";
      $output .= "\t<Title>Directories</Title>\n";
      $output .= "\t<Prompt>Directories</Prompt>\n";
      $output .= "\t<MenuItem>\n";
         $output .= "\t\t<Name>Bart Directory</Name>\n";
         $output .= "\t\t<URL>" . $directoryURL ."</URL>\n";
      $output .= "\t</MenuItem>\n";
   $output .= "</CiscoIPPhoneMenu>\n";

    
    return;
}

function addEntry($name, $uri)
{
    global $output, $myEntry, $maxEntry, $offset, $searchName;

    # Don't add name to output if it doesn't match the search criteria
    if (!empty($searchName) && !preg_match("/$searchName/i",$name)) return;

    $myEntry++;
    if ($myEntry < $offset) return;
    if ($myEntry >= $maxEntry) return;

    # Shorten name if necessary
    if (strlen($name) > 25) {
       $name = substr($name,0,23) . "..";
    }

    # Clean up phone number
    $uri = normalizePhone($uri);

    $output .= "\t<DirectoryEntry>\n";
    $output .= "\t\t<Name>";
    $output .= htmlspecialchars($name, ENT_NOQUOTES);
    $output .= "</Name>\n";
    $output .= "\t\t<Telephone>";
    $output .= $uri;
    $output .= "</Telephone>\n";
    $output .= "\t</DirectoryEntry>\n";
    return;
}

function normalizePhone($number) 
{
    if (preg_match("/\(*(\d{3})\)*[\s\-\.]+(\d{3})\s*[\.\-]\s*(\d{4})/", $number, $matches)) {
           $number = $matches[1] . $matches[2] . $matches[3];
    }

    # Remove any whitespace
    $number = preg_replace('/[\s+]/','',$number);

    return $number;
}

function addStaticEntries() 
{
    # Define extra numbers here that are not contained in the vCard file
    addEntry("Example One (ip)","test@example.com");
    addEntry("Example Two (Work)","8005551212");
    addEntry("Example Three (Home)","(800) 555-1212");
}

?> 
