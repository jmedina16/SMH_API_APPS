<?php

/*
 * This is a PayPal IPN (Instant Payment Notification) broadcaster
 * Since PayPal does not provide any straightforward way to add
 * multiple IPN listeners we'll have to create a central IPN
 * listener that will broadcast (or filter and dispatch) Instant
 * Payment Notifications to different destinations (IPN listeners)
 *
 * Destination IPN listeners must not panic and recognize IPNs sent
 * by this central broadcast as valid ones in terms of source IP
 * and any other fingerprints. Any IP filtering must add this host,
 * other adjustments made as necessary.
 *
 * IPNs are logged into files for debugging and maintenance purposes
 *
 * this code comes with absolutely no warranty
 * http://codeseekah.com
 */

ini_set('max_execution_time', 0); /* Do not abort with timeouts */
ini_set('display_errors', 'Off'); /* Do not display any errors to anyone */
$urls = array(); /* The broadcast session queue */

/* List of IPN listener points */
$ipns = array(
    'streamingforthesoul' => 'https://www.streamingforthesoul.tv/__ipn.php',
    'mediaplatform' => 'http://mediaplatform.streamingmediahosting.com/apps/ppv/v1.0/ipn.php'
);

/* Fingerprints */

if (strpos($_POST['receiver_email'], 'streamingforthesoul@gmail.com') == 0){
    $urls [] = $ipns['streamingforthesoul'];
} else {
    
}

/* Broadcast */

if (!sizeof($urls))
    $urls = $ipns; /* No URLs have been matched */
$urls = array_unique($urls); /* Unique, just in case */

/* Broadcast (excluding IPNs from the list according to filter is possible */
foreach ($urls as $url)
    broadcast($url);

header('HTTP/1.1 200 OK', true, 200);
exit(); /* Thank you, bye */

/* Perform a simple cURL-powered proxy request to broadcast */

function broadcast($url) {

    /* Format POST data accordingly */
    $data = array();
    foreach ($_POST as $key => $value)
        $data [] = urlencode($key) . '=' . urlencode($value);
    $data = implode('&', $data);

    /* Log the broadcast */
    file_put_contents('_logs/' . time() . '.' . reverse_lookup($url) . '-' . rand(1, 100), $data);

    $ch = curl_init(); /* Initialize */

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch); /* Execute HTTP request */

    curl_close($ch); /* Close */
}

function reverse_lookup($url) {
    global $ipns;
    foreach ($ipns as $tag => $_url) {
        if ($url == $_url)
            return $tag;
    }
    return 'unknown';
}

?>