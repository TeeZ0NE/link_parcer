<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require(__DIR__ . '/vendor/autoload.php');
}
$query = 'energy.mk.ua';

$whois = new Whois();
$result = $whois->lookup($query, true);
echo "<pre>";
print_r($result);
echo "</pre>";
print_r($result["regrinfo"]["registered"]);
$op_arr = array();
foreach ($result["rawdata"] as $key => $value){
	echo  $value."<br>";
	preg_match("/nserver:\s+([\w\d\.]*)/i", $value, $op_arr);
	if(count($op_arr)) echo "found $output_array[1] <br>";
}
print_r($op_arr);
/*
# Reading file line by line
$file = fopen ("download/urls_list.txt","r");
$output_file = "download/test.md";
header('Content-Type: text/plain');
if(!file_exists($output_file)){
	$string = "## List of files\n";
	$string.="param|value\n-:|:-:\n";
	file_put_contents($output_file,$string,FILE_APPEND | LOCK_EX);
}
while(!feof($file)) {
	$testing_url = fgets($file);
//	echo "testing string: " . $testing_url;
	//preg_match("/.",$testing_url,$output);
	$result = $whois->lookup($testing_url, true);
	$string = "site|$output[0]\n";
	file_put_contents($output_file,$string,FILE_APPEND | LOCK_EX);
}
echo "well done";

# sorting and writing file
/*
$contents = file_get_contents("download/urls_list.txt");
//print_r($contents);
	$result = preg_replace("/https?:\/\/(?:www.)?([-\w\d\.]*)/", "$1", $contents);
if (file_put_contents("download/sorted_urls.txt",$result)){
	echo "well done<br>";
} else echo "error occur!<br>";
*/
