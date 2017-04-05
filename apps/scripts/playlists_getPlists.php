<?php

error_reporting(0);
$tz = $_GET['tz'];
date_default_timezone_set($tz);
require_once('../../app/clients/php5/KalturaClient.php');

$ks = $_GET['ks'];
$entryId = $_GET['id'];

$partnerId = 0;
$config = new KalturaConfiguration($partnerId);
$config->serviceUrl = 'http://mediaplatform.streamingmediahosting.com/';
$client = new KalturaClient($config);
$client->setKs($ks);

// ORDERING
$aColumns = array("name", "type", "duration", "status");
if (isset($_GET['iSortCol_0'])) {
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            $filter->orderBy = ($_GET['sSortDir_' . $i] == 'asc' ? '+' : '-') . $aColumns[intval($_GET['iSortCol_' . $i])];
            break; //Kaltura can do only order by single field currently
        }
    }
}
$detailed = null;
$filteredListResult = $client->playlist->execute($entryId, $detailed);

$output = array(
    "aaData" => array()
);

if (isset($_GET['sEcho'])) {
    $output["sEcho"] = intval($_GET['sEcho']);
}

foreach ($filteredListResult as $entry) {
    $current_time = strtotime(date('n/j/Y H:i'));

//    $createdAt = date('n/j/Y H:i', $entry->createdAt);
//    $newtime = strtotime($createdAt);
//    $min = ($current_time - $newtime) / 60;
//    if ($entry->mediaType == '1' || $entry->mediaType == '5') {
//        $filter2 = new KalturaAssetFilter();
//        $entry2 = (String) $entry->id;
//
//        $filter2->entryIdIn = $entry2;
//        $pager2 = null;
//        $flavor = $client->flavorAsset->listAction($filter2, $pager2);
//        $flavor_arr = array();
//        foreach ($flavor->objects as $flavors) {
//            $updatedAt = date('n/j/Y H:i', $flavors->updatedAt);
//            $newtime = strtotime($updatedAt);
//            $mins = ($current_time - $newtime) / 60;
//            $flavor_arr[] = $mins;
//        }
//        $min = min($flavor_arr);
//    }

    $st = "";
    $mediaType = "";
    $prevMedia = 'false';
    $live_stream = 'false';
    $image = 'false';
    $row = array();

    if ($entry->mediaType == '1') {
        $mediaType = '<img src="/img/video.jpg" width="14px" height="15px" alt="Video" />';
    } else if ($entry->mediaType == '2') {
        $mediaType = '<img src="/img/image.jpg" width="16px" height="16px" alt="Image" />';
        $image = 'true';
    } else if ($entry->mediaType == '201' || $entry->mediaType == '202' || $entry->mediaType == '203' || $entry->mediaType == '204' || $entry->mediaType == '100' || $entry->mediaType == '101') {
        $mediaType = '<img src="/img/live_flash.jpg" width="16px" height="16px" alt="Live Flash" />';
        $live_stream = 'true';
    } else if ($entry->mediaType == '5') {
        $mediaType = '<img src="/img/audio.png" width="16px" height="16px" alt="Audio" />';
    } else {
        $mediaType = $entry->mediaType;
    }

    $media_arr = $entry->id . "," . $entry->partnerId . "," . htmlspecialchars($entry->streamName, ENT_QUOTES) . "," . htmlspecialchars($entry->name, ENT_QUOTES) . "," . $live_stream . "," . $image;

    $time = rectime($entry->duration);

    $unixtime_to_date = date('n/j/Y H:i', $entry->createdAt);
    $newDatetime = strtotime($unixtime_to_date);
    //$minutes = ($current_time - $newDatetime) / 60;
    //re-contruct to format
    $newDatetime = date('m/d/Y h:i A', $newDatetime);


    if ($entry->status == '6') {
        $st = "Blocked";
    } else if ($entry->status == '3') {
        $st = "Deleted";
        $prevMedia = 'true';
    } else if ($entry->status == '-1') {
        $st = 'Error';
        $prevMedia = 'true';
    } else if ($entry->status == '-2') {
        $st = 'Error Uploading';
        $prevMedia = 'true';
    } else if ($entry->status == '0') {
        $st = "Uploading";
        $prevMedia = 'true';
    } else if ($entry->status == '5') {
        $st = "Moderate";
    } else if ($entry->status == '7') {
        $st = "No Media";
    } else if ($entry->status == '4') {
        $st = "Pending";
    } else if ($entry->status == '1') {
        $st = "Converting";
        $prevMedia = 'true';
    } else if ($entry->status == '2') {
        $st = "Ready";
    }

    $row[] = "<div id='data-name'><a id='" . $entry->id . "' href='#'>" . $entry->name . "</a></div>";
    $row[] = $mediaType;
    $row[] = $time;
    $row[] = $st;
    $row[] = $entry->duration;
    $output['aaData'][] = $row;
}

echo json_encode($output);

function rectime($secs) {
    $hr = floor($secs / 3600);
    $min = floor(($secs - ($hr * 3600)) / 60);
    $sec = $secs - ($hr * 3600) - ($min * 60);

    if ($hr < 10) {
        $hr = "0" . $hr;
    }
    if ($min < 10) {
        $min = "0" . $min;
    }
    if ($sec < 10) {
        $sec = "0" . $sec;
    }
    return $hr . ':' . $min . ':' . $sec;
}

?>
