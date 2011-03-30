<?php

/*
Get XML
Parse out values
Save values
- append to existing XML
*/

// variables
$url = $_GET["url"];

$filename = "data.xml";

$urlxml = simplexml_load_file($url);

$xmlstr = $xmlstr = <<<XML
<?xml version='1.0' standalone='yes'?>
<root />
XML;

if (file_exists($filename)){
    $newxml = simplexml_load_file($filename);
    $numentries=0;
    foreach ($newxml->xpath('entry') as $blah) {
        $numentries++;
    }
}
else {
    $newxml = new SimpleXMLElement($xmlstr);
    $numentries=0;
}

// append new child entry
$entry = $newxml->addChild('entry');

// xpath for names and population
$xpathname = "//tr/td[1]/p";
$xpathpop = "//tr/td[4]/p";

// arrays to hold names and population values
$names = array();
$pops = array();

// load xpath results into arrays
foreach ($urlxml->xpath($xpathname) as $object) {
    array_push($names,$object."");
}
echo count($names);
print_r($names);


foreach ($urlxml->xpath($xpathpop) as $object) { array_push($pops, $object*1); }
echo count($pops);
print_r($pops);


// append time node
$time = $newxml->entry[$numentries]->addChild("Time",time());

// append city pop nodes
for ($i=0;$i<count($names);$i++) { $entry = $newxml->entry[$numentries]->addChild(str_replace(" ", "_", $names[$i]),$pops[$i]); }

// format output
$dom = dom_import_simplexml($newxml)->ownerDocument;
$dom->formatOutput = true;
echo $dom->saveXML(); 

// write XML file
$fh = fopen($filename, 'w') or die("can't open file");
fwrite($fh, $dom->saveXML());
fclose($fh);

// echo $newxml->asXML();

?>
