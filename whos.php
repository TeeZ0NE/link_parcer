<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require(__DIR__ . '/vendor/autoload.php');
}

$file = fopen("download/urls_list.txt", "r");
$output_file = "download/whois.md";
$log = "download/whois-logs.htm";

# create outpu file if doesn't exist
if (!file_exists($output_file)) {
	$string = "## Список адресів\n";
	$string .= "Назва|Зевчення\n:-|:-:\n";
	file_put_contents($output_file, $string, FILE_APPEND | LOCK_EX);
}

function get_urls($file, $output_file, $log){
	$whois = new Whois();
	while(!feof($file)) {
		preg_match("/.*/",fgets($file),$testing_url);
		$result = $whois->lookup($testing_url[0], true);
		get_data($result, $testing_url[0], $output_file, $log);
		$sleep = rand(60,120);
		sleep($sleep);
		file_put_contents($log,"<p>sleep $sleep</p>",FILE_APPEND | LOCK_EX);
	}
}

function get_data($result, $query, $output_file, $log)
{
	$ns_arr = $ip_arr = array();
	$created = $expires = $registrar = $city = $country = "-";
	if (isset($result["rawdata"]) AND $result["regrinfo"]["registered"]) {
		foreach ($result["rawdata"] as $key => $value) {
			# ns servers
			preg_match("/nserver:\s*([-\w\d\.]*)/i", $value, $op_arr);
			if (!empty($op_arr)) array_push($ns_arr, $op_arr[1]);
			# created
			preg_match("/crea[\w]*:\s*([-\w\d\.]+)/i", $value, $op_cr_arr);
			if (!empty($op_cr_arr)) $created = $op_cr_arr[1];
			# expires
			preg_match("/exp[\w]*:\s*([-\w\d\.]+)/i", $value, $op_exp_arr);
			if (!empty($op_exp_arr)) $expires = $op_exp_arr[1];
			# ip adresses
			preg_match("/ip[-\w]*:\s*([-\w\d\.]+)/i", $value, $op_ip_arr);
			if (!empty($op_ip_arr)) array_push($ip_arr, $op_ip_arr[1]);
			# registrar
			preg_match("/exp[\w]*:\s*([-\w\d\.]+)/i", $value, $op_registr_arr);
			if (!empty($op_registr_arr)) $registrar = $op_registr_arr[1];
			# city
			preg_match("/city[-_\s\w]*:\s*([-\w\d\.]+)/i", $value, $op_city_arr);
			if (!empty($op_city_arr)) $city = $op_city_arr[1];
			# country
			preg_match("/cou[\w]*:\s*([-\w\d\.]+)/i", $value, $op_country_arr);
			if (!empty($op_country_arr)) $country = $op_country_arr[1];
		}
		write_data2file($query, $created, $expires, $registrar, $city, $country, $ns_arr, $ip_arr, $output_file, $log);
	}
}

function write_data2file($query, $created, $expires, $registrar, $city, $country, $ns_arr, $ip_arr, $output_file, $log)
{
	$string = "url|$query\n";
	$string .= "Створено|$created\n";
	$string .= "Закінч.|$expires\n";
	$string .= "Регістр.|$registrar\n";
	$string .= "Город|$city\n";
	$string .= "Країна|$country\n";
	if (count($ns_arr)) {
		for ($i = 0; $i < count($ns_arr); $i++) {
			$string .= "ns|$ns_arr[$i]\n";
		}
	} else $string .= "ns|-\n";
	if (count($ip_arr)) {
		for ($i = 0; $i < count($ip_arr); $i++) {
			$string .= "IP|$ip_arr[$i]\n";
		}
	} else $string .= "IP|-\n";
	if (file_put_contents($output_file, $string, FILE_APPEND | LOCK_EX))
		file_put_contents($log, "<p><b>$query</b> записан</p>", FILE_APPEND | LOCK_EX);
	else file_put_contents($log, "<p><b>$query</b> не записан</p>", FILE_APPEND | LOCK_EX);
}

get_urls($file,$output_file,$log);
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
