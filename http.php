<?php
include 'includes/getUA.php';
//include 'includes/getVersion.php';

$url = "http://blog.tuinslak.org";

// prepare HTTP request
$request_options = array(
			referer => "http://irail.be/", 
			timeout => "30",
			useragent => $irailAgent, 
		);

$data = "?iRailTest";

echo $irailVersion;

$post = http_post_data($url, $data, $request_options) or die("<br />NMBS/SNCB website timeout. Please <a href='..'>refresh</a>.");

?>
