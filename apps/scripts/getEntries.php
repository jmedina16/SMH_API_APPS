<?php

error_reporting(0);
$tz = $_GET['tz'];
date_default_timezone_set($tz);
require_once('../../app/clients/php5/KalturaClient.php');

$ks = $_GET['ks'];

$partnerId = 0;
$config = new KalturaConfiguration($partnerId);
$config->serviceUrl = 'http://mediaplatform.streamingmediahosting.com/';
$client = new KalturaClient($config);
$client->setKs($ks);
$filter = new KalturaMediaEntryFilter();
$filter->orderBy = '-createdAt';
$filter->mediaTypeIn = '1,2,5,201,100,101';
$filter->statusIn = '-1,-2,0,1,2,7,4';
$filter->isRoot = KalturaNullableBoolean::NULL_VALUE;
$pager = new KalturaFilterPager();

// PAGING
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $pager->pageSize = intval($_GET['iDisplayLength']);
    $pager->pageIndex = floor(intval($_GET['iDisplayStart']) / $pager->pageSize) + 1;
}

// ORDERING
$aColumns = array("thumbnailUrl", "id", "name", "type", "createdOn", "duration", "status", "publish");
if (isset($_GET['iSortCol_0'])) {
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            $filter->orderBy = ($_GET['sSortDir_' . $i] == 'asc' ? '+' : '-') . $aColumns[intval($_GET['iSortCol_' . $i])];
            break; //Kaltura can do only order by single field currently
        }
    }
}

// FILTERING
if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
    $filter->freeText = $_GET['sSearch'];
}

//mediaTypeIn
if (isset($_GET['mediaTypeIn']) && $_GET['mediaTypeIn'] != "") {
    $filter->mediaTypeIn = $_GET['mediaTypeIn'];
}

// Status 
if (isset($_GET['statusIn']) && $_GET['statusIn'] != "") {
    $filter->statusIn = $_GET['statusIn'];
}

// Entry 
if (isset($_GET['entry']) && $_GET['entry'] != "") {
    $filter->isRoot = $_GET['entry'];
}

//Categories
if (isset($_GET['category']) && $_GET['category'] != "" && $_GET['category'] != undefined) {
    $filter->categoriesIdsMatchOr = $_GET['category'];
}

$filteredListResult = $client->baseEntry->listAction($filter, $pager);

$output = array(
    "orderBy" => $filter->orderBy,
    "iTotalRecords" => intval($filteredListResult->totalCount),
    "iTotalDisplayRecords" => intval($filteredListResult->totalCount),
    "aaData" => array()
);

if (isset($_GET['sEcho'])) {
    $output["sEcho"] = intval($_GET['sEcho']);
}

foreach ($filteredListResult->objects as $entry) {
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
    $pb = "";
    $thumb = "";
    $mediaType = "";
    $prevMedia = 'false';
    $image = 'false';
    $live_stream = 'false';
    $row = array();
    $stream_arr = "";

    if ($entry->mediaType == '1') {
        $mediaType = '<img data-placement="top" data-original-title="Video" rel="tooltip" src="/img/video.jpg" width="14px" height="15px" alt="Video" />';
    } else if ($entry->mediaType == '2') {
        $mediaType = '<img data-placement="top" data-original-title="Image" rel="tooltip" src="/img/image.jpg" width="16px" height="16px" alt="Image" />';
        $image = 'true';
    } else if ($entry->mediaType == '201' || $entry->mediaType == '202' || $entry->mediaType == '203' || $entry->mediaType == '204' || $entry->mediaType == '100' || $entry->mediaType == '101') {
        $mediaType = '<img data-placement="top" data-original-title="Live Stream" rel="tooltip" src="/img/live_flash.jpg" width="16px" height="16px" alt="Live Flash" />';
        $live_stream = 'true';
    } else if ($entry->mediaType == '5') {
        $mediaType = '<img data-placement="top" data-original-title="Audio" rel="tooltip" src="/img/audio.png" width="16px" height="16px" alt="Audio" />';
    } else {
        $mediaType = $entry->mediaType;
    }

    foreach ($entry->bitrates as $bitrate_data) {
        $stream_arr .= $bitrate_data->bitrate . "," . $bitrate_data->width . "," . $bitrate_data->height . ",";
    }

    $stream = rtrim($stream_arr, ",");

    $media_arr = $entry->id . "," . $entry->partnerId . "," . htmlspecialchars($entry->streamName, ENT_QUOTES) . "," . str_replace(",", "", htmlspecialchars($entry->name, ENT_QUOTES)) . "," . $live_stream . "," . $image;

    $time = rectime($entry->duration);

    $unixtime_to_date = date('n/j/Y H:i', $entry->createdAt);
    $newDatetime = strtotime($unixtime_to_date);
    //$minutes = ($current_time - $newDatetime) / 60;
    //re-contruct to format
    $newDatetime = date('m/d/Y h:i A', $newDatetime);


    if ($entry->status == '6') {
        $st = "Blocked";
        $pb = "<div style='width: 15px; margin-left: auto; margin-right: auto;'><a data-placement='top' data-original-title='Preview &amp; Embed' rel='tooltip' onclick='mediaPrev(\"" . $media_arr . "\",\"" . $stream . "\")'><img width='15px' src='http://mediaplatform.streamingmediahosting.com/img/Embed-Icon.png'></a></div>";
        $thumb = "<img src='http://mediaplatform.streamingmediahosting.com/p/" . $entry->partnerId . "/thumbnail/entry_id/" . $entry->id . "/version/100000/bgcolor/F7F7F7/type/2/' width='50' height='37' style='margin-left: 9px;' />";
    } else if ($entry->status == '3') {
        $st = "Deleted";
        $pb = "";
        $prevMedia = 'true';
        $thumb = "<img src='/img/thumb_loading.png' id='thumbload' width='50' height='37' style='margin-left: 9px;' />";
    } else if ($entry->status == '-1') {
        $st = 'Error';
        $pb = "";
        $prevMedia = 'true';
        $thumb = "<img src='/img/thumb_loading.png' id='thumbload' width='50' height='37' style='margin-left: 9px;' />";
    } else if ($entry->status == '-2') {
        $st = 'Error Uploading';
        $pb = "";
        $thumb = "<img src='/img/thumb_loading.png' id='thumbload' width='50' height='37' style='margin-left: 9px;'/>";
        $prevMedia = 'true';
    } else if ($entry->status == '0') {
        $st = "Uploading";
        $pb = "";
        $prevMedia = 'true';
        $thumb = "<img src='/img/thumb_loading.png' id='thumbload' width='50' height='37' style='margin-left: 9px;'/>";
    } else if ($entry->status == '5') {
        $st = "Moderate";
        $pb = "<div style='width: 15px; margin-left: auto; margin-right: auto;'><a data-placement='top' data-original-title='Preview &amp; Embed' rel='tooltip' onclick='mediaPrev(\"" . $media_arr . "\",\"" . $stream . "\")'><img width='15px' src='http://mediaplatform.streamingmediahosting.com/img/Embed-Icon.png'></a></div>";
        $thumb = "<img src='http://mediaplatform.streamingmediahosting.com/p/" . $entry->partnerId . "/thumbnail/entry_id/" . $entry->id . "/version/100000/bgcolor/F7F7F7/type/2/' width='50' height='37' style='margin-left: 9px;'/>";
    } else if ($entry->status == '7') {
        $st = "No Media";
        $pb = "";
        $thumb = "<img src='/img/thumb_loading.png' id='thumbload' width='50' height='37' style='margin-left: 9px;'/>";
    } else if ($entry->status == '4') {
        $st = "Pending";
        $pb = "<div style='width: 15px; margin-left: auto; margin-right: auto;'><a data-placement='top' data-original-title='Preview &amp; Embed' rel='tooltip' onclick='mediaPrev(\"" . $media_arr . "\",\"" . $stream . "\")'><img width='15px' src='http://mediaplatform.streamingmediahosting.com/img/Embed-Icon.png'></a></div>";
        $thumb = "<img src='http://mediaplatform.streamingmediahosting.com/p/" . $entry->partnerId . "/thumbnail/entry_id/" . $entry->id . "/version/100000/bgcolor/F7F7F7/type/2/' width='50' height='37' style='margin-left: 9px;'/>";
    } else if ($entry->status == '1') {
        $st = "Converting";
        $pb = "";
        $thumb = "<img src='http://mediaplatform.streamingmediahosting.com/p/" . $entry->partnerId . "/thumbnail/entry_id/" . $entry->id . "/version/100000/bgcolor/F7F7F7/type/2/' width='50' height='37' style='margin-left: 9px;'/>";
        $prevMedia = 'true';
    } else if ($entry->status == '2') {
        $st = "Ready";
        $pb = "<div style='width: 15px; margin-left: auto; margin-right: auto;'><a data-placement='top' data-original-title='Preview &amp; Embed' rel='tooltip' onclick='mediaPrev(\"" . $media_arr . "\",\"" . $stream . "\")'><img width='15px' src='http://mediaplatform.streamingmediahosting.com/img/Embed-Icon.png'></a></div>";
        //$thumb = "<img src='http://mediaplatform.streamingmediahosting.com/p/" . $entry->partnerId . "/thumbnail/entry_id/" . $entry->id . "/version/100000/bgcolor/F7F7F7/type/2/' width='50' height='37'/>";
        $thumb = "<img src='http://imgs.mediaplatform.streamingmediahosting.com/p/" . $entry->partnerId . "/thumbnail/entry_id/" . $entry->id . "/version/100000/bgcolor/F7F7F7/type/2/' width='50' height='37' style='margin-left: 9px;'/>";
    }

    $row[] = $thumb;
    $row[] = '<div id="entry_id">' . $entry->id . '</div>';
    $row[] = "<div id='data-name'><a id='entry_name' onclick='editMedia(\"" . $media_arr . "\",\"" . $prevMedia . "\")'>" . $entry->name . "</a></div>";
    $row[] = '<div style="width: 16px; margin-left: auto; margin-right: auto;">' .$mediaType . '</div>';
    $row[] = '<div id="data-name">' . $newDatetime . '</div>';
    $row[] = '<div id="data-name">' . $time . '</div>';
    $row[] = $st;
    $row[] = $pb;
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