<?php

require_once('kaltura/KalturaClient.php');

$adminSecret = 'e7a6f68e9a5ab47b6172caffae25b4f3';
$userId = null;
$sessionType = KalturaSessionType::ADMIN;
$partnerId = 12773;

$config = new KalturaConfiguration($partnerId);
$config->serviceUrl = 'https://mediaplatform.streamingmediahosting.com';
$client = new KalturaClient($config);
$ks = $client->generateSession($adminSecret, $userId, $sessionType, $partnerId);
$client->setKs($ks);
$entry = new KalturaMediaEntry();
$entry->name = 'Test';
$entry->mediaType = KalturaMediaType::VIDEO;
$result = $client->media->add($entry);

?>