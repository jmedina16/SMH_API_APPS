<?php

error_reporting(0);
$tz = $_GET['tz'];
date_default_timezone_set($tz);
require_once('../../smh_application/lib/KalturaClient.php');

$ks = $_GET['ks'];

$partnerId = 0;
$config = new KalturaConfiguration($partnerId);
$config->serviceUrl = 'http://mediaplatform.streamingmediahosting.com/';
$client = new KalturaClient($config);
$client->setKs($ks);

$filter = new KalturaCuePointFilter();
$filter->tagsLike = 'chaptering';
$pager = new KalturaFilterPager();

// PAGING
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $pager->pageSize = intval($_GET['iDisplayLength']);
    $pager->pageIndex = floor(intval($_GET['iDisplayStart']) / $pager->pageSize) + 1;
}

// ORDERING
$aColumns = array("id", "name", "playlistType", "createdOn", "status", "publish");
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

$filteredListResult = $client->cuePoint->listAction($filter, $pager);

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
    $pt = "";
    if ($entry->playlistType) {
        $pt = $entry->playlistType;
    }

    $plist_arr = $entry->id . "," . $entry->partnerId . "," . htmlspecialchars($entry->streamName, ENT_QUOTES) . "," . htmlspecialchars($entry->name, ENT_QUOTES) . "," . $pt . "," . $entry->description;

    $current_time = strtotime(date('n/j/Y H:i'));
    $createdAt = date('n/j/Y H:i', $entry->createdAt);
    $newtime = strtotime($createdAt);
    $min = ($current_time - $newtime) / 60;

    $st = "";
    $pb = "";
    $status = "";
    $row = array();

    if ($entry->status) {
        $status = $entry->status;
    } else {
        $status = 2;
    }

    $playlistType = "";
    if ($entry->playlistType == '10') {
        $playlistType = 'Rule Based';
    } else if ($entry->playlistType == '3') {
        $playlistType = 'Manual';
    }

    $unixtime_to_date = date('n/j/Y H:i', $entry->createdAt);
    $newDatetime = strtotime($unixtime_to_date);
    $newDatetime = date('m/d/Y h:i A', $newDatetime);

    if ($status == '6') {
        $st = 'Blocked';
        $pb = "<a onclick='playlistPrev(\"" . $plist_arr . "\")'>Preview & Embed</a>";
    } else if ($status == '3') {
        $st = 'Deleted';
        $pb = '';
    } else if ($status == '-1') {
        $st = 'Error';
        $pb = "";
    } else if ($status == '-2') {
        $st = 'Error Uploading';
        $pb = "";
    } else if ($status == '0') {
        $st = 'Uploading';
        $pb = '';
    } else if ($status == '5') {
        $st = 'Moderate';
        $pb = "<a onclick='playlistPrev(\"" . $plist_arr . "\")'>Preview & Embed</a>";
    } else if ($status == '7') {
        $st = 'No Media';
        $pb = '';
    } else if ($status == '4') {
        $st = 'Pending';
        $pb = "<a onclick='playlistPrev(\"" . $plist_arr . "\")'>Preview & Embed</a>";
    } else if ($status == '1') {
        $st = 'Converting';
        $pb = '';
    } else if ($status == '2') {
//        if ($min <= 10) {
//            $st = 'Syncing';
//            $pb = '';
//        } else {
            $st = 'Ready';
            $pb = "<a onclick='playlistPrev(\"" . $plist_arr . "\")'>Preview & Embed</a>";
//        }
    }

    $row[] = $entry->id;
    $row[] = "<a onclick='editPlaylist(\"" . $plist_arr . "\")'>" . $entry->name . "</a>";
    $row[] = $playlistType;
    $row[] = $newDatetime;
    $row[] = $st;
    $row[] = $pb;
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>