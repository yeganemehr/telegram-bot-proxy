<?php

$allowedBots = explode(",", getenv("ALLOWED_BOTS") ?: "");

if ($allowedBots and (!preg_match("/^\/bot([^\/]+)\//", $_SERVER['REQUEST_URI'], $matches) or !in_array($matches[1], $allowedBots))) {
	header("HTTP/1.1 403 Forbiden");
	echo "Access denied";
	return;
}

$client = curl_init("https://api.telegram.org" . $_SERVER['REQUEST_URI']);
curl_setopt($client, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
curl_setopt($client, CURLOPT_HEADER, false);
curl_setopt($client, CURLOPT_HEADERFUNCTION, function($client, $header) {
	$len = strlen($header);
	if (str_ends_with($header, "\r\n")) {
		$header = substr($header, 0, -2);
	}
	$pass = !empty($header);
	$colon = strpos($header, ":");
	if ($colon) {
		$key = substr($header, 0, $colon);
		$key = strtolower($key);
		if (in_array($key, ["date", "server"])) {
			$pass = false;
		}
	}
	if ($pass) {
		header($header);
	}

	return $len;
});
$result = curl_exec($client);
$info = curl_getinfo($client);
if (!$info["http_code"]) {
	header("HTTP/1.1 500");
	echo "Cannot connect to upstream";
	return;
}
echo $result;

