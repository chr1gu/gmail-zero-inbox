<?php

/*
* TODO:
* add some documentation
* add warnings
* add different formats
* show google graphic or log the thing somehow for future
* add auto-reload for web-version
* add possibility to run as cronjob
* refactor code with classes etc.
* add multi-user support
* add inbox-limit for graph warning
* add warnings? sound?
* add refresh-rate to config file for html
* throw erros nicely
*/

// user/password
$config = dirname(__FILE__) . '/config.php';
if (!is_file($config)) {
	die('Could not read config file');
}
include($config);

// init cURL
$ch = curl_init('https://mail.google.com/mail/feed/atom');

$headers = array(
"Host: mail.google.com",
"Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
"Accept-Language: en-gb,en;q=0.5",
"Accept-Encoding: text", # No gzip, it only clutters your code!
"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
"Date: ".date(DATE_RFC822)
);

// curl options
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY); // use authentication
curl_setopt($ch, CURLOPT_USERPWD,"$username:$password");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // send the headers
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // We need to fetch something from a string, so no direct output!
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // we get redirected, so follow
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1); // always stay authorised
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

// process response
$response = curl_exec($ch);
preg_match('#<fullcount>(.*)</fullcount>#', $response, $array);
$count = $array[1];

// output with correct format
$format = isset($_GET['format']) ? $_GET['format'] : null;
switch ($format) {
	case 'silent':
		break;
    case 'json':
        break;
    case 'jsonp':
        break;
	// html fallback
    default:	
		$lastcheck = date('r');
		$message = (int)$count === 0 ? "Your inbox is empty!" : "You have $count unread messages in your inbox";
		echo "<html><head><title>Inbox: $count ($username)</title><meta http-equiv=\"refresh\" content=\"10\"></head><body><h1>Zero-Inbox</h1><h2>$message</h2><p>Username: $username<br/>Last check: $lastcheck</p><body></html>";
        break;
}

curl_close($ch);
