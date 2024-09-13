<?php

$allowedBots = trim(getenv("ALLOWED_BOTS") ?: "");
$allowedBots = $allowedBots ? explode(",", $allowedBots) : [];

if ($allowedBots and (!preg_match("/^\/bot([^\/]+)\//", $_SERVER['REQUEST_URI'], $matches) or !in_array($matches[1], $allowedBots))) {
	header("{$_SERVER['SERVER_PROTOCOL']} 403 Forbiden");
	echo "Access denied";
	return;
}

$client = curl_init("https://api.telegram.org" . $_SERVER['REQUEST_URI']);
curl_setopt($client, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
curl_setopt($client, CURLOPT_HEADER, false);
curl_setopt($client, CURLOPT_HEADERFUNCTION, function ($client, $header) {
	$len = strlen($header);
	if (str_ends_with($header, "\r\n")) {
		$header = substr($header, 0, -2);
	}
	if (empty($header)) {
		return $len;
	}
	$colon = strpos($header, ":");
	if (!$colon) {
		if (!preg_match("/^HTTP\/[\d\.]+ (\d+)/", $header, $matches)) {
			return $len;
		}
		$header =  "{$_SERVER['SERVER_PROTOCOL']} {$matches[1]}";
	}
	if ($colon) {
		$key = substr($header, 0, $colon);
		$key = strtolower($key);
		if (in_array($key, ["date", "server"])) {
			return $len;
		}
	}
	header($header);

	return $len;
});
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	curl_setopt($client, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
	if (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
		curl_setopt($client, CURLOPT_HTTPHEADER, [
			"Content-Type: " . $_SERVER['HTTP_CONTENT_TYPE'],
		]);
	}
}
$result = curl_exec($client);
$info = curl_getinfo($client);
if (!$info["http_code"]) {
	header("{$_SERVER['SERVER_PROTOCOL']} 500");
	echo "Cannot connect to upstream";
	return;
}
echo $result;
