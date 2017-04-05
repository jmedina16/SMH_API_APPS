<?php

error_reporting(0);
$tz = $_GET['tz'];
date_default_timezone_set($tz);
require_once('../../app/clients/php5/KalturaClient.php');

$ks = $_GET['ks'];

$partnerId = $_GET['pid'];
$config = new KalturaConfiguration($partnerId);
$config->serviceUrl = 'http://mediaplatform.streamingmediahosting.com/';
$client = new KalturaClient($config);
$client->setKs($ks);

$filter = new KalturaUiConfFilter();

if ($partnerId !== '10585') {
    $filter->orderBy = '-createdAt';
}

$filter->tagsMultiLikeOr = 'kdp3,player,playlist';
$pager = new KalturaFilterPager();

// PAGING
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    if ($_GET['iDisplayLength'] == '10' && $partnerId == '10561') {
        $pager->pageSize = 9;
    } else {
        $pager->pageSize = intval($_GET['iDisplayLength']);
    }
    $pager->pageIndex = floor(intval($_GET['iDisplayStart']) / $pager->pageSize) + 1;
}

// ORDERING
$aColumns = array("id", "name", "createdOn", "size", "type", "actions");
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
    $filter->nameLike = $_GET['sSearch'];
}

$filteredListResult = $client->uiConf->listAction($filter, $pager);

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
    if ($entry->confFile != null && $entry->confFile != '' && $entry->confFileFeatures != null && $entry->confFileFeatures != '') {
        $xml_string = $entry->confFile;
        $name = $entry->name;
        $uiconf_id = $entry->id;
        $width = $entry->width;
        $height = $entry->height;
        $dimensions = $width . "x" . $height;
        $p_id = $entry->partnerId;

        $xml = simplexml_load_string($xml_string);
        if ($xml->xpath(sprintf('/layout[@isPlaylist="%s"]', 'true'))) {
            $player_type = 'Playlist';
        } else if ($xml->xpath(sprintf('/layout[@isPlaylist="%s"]', 'multi'))) {
            $player_type = 'Channel Playlist';
        } else {
            $player_type = 'Player';
        }

        $unixtime_to_date = date('n/j/Y H:i', $entry->createdAt);
        $newDatetime = strtotime($unixtime_to_date);
        $newDatetime = date('m/d/Y h:i A', $newDatetime);

        $actions = "<a data-placement='top' data-original-title='Edit' rel='tooltip' onclick='editPlayer(\"" . $uiconf_id . "\")'><img width='15px' src='http://mediaplatform.streamingmediahosting.com/img/ppv_edit.png'></a> &nbsp;&nbsp;<a data-placement='top' data-original-title='Duplicate' rel='tooltip' onclick='createDuplicate(\"" . $uiconf_id . "\")'><img width='15px' src='http://mediaplatform.streamingmediahosting.com/img/duplicate.png'></a>";
        if ($player_type == 'Channel Playlist') {
            $actions .= " &nbsp;&nbsp;<a data-placement='top' data-original-title='Preview & Embed' rel='tooltip' onclick='playlistPrev(\"" . $uiconf_id . "," . $p_id . "," . str_replace(",", "", htmlspecialchars($name, ENT_QUOTES)) . "\")'><img width='15px' src='http://mediaplatform.streamingmediahosting.com/img/Embed-Icon.png'></a>";
        }

        $row = array();
        $row[] = $uiconf_id;
        $row[] = "<div id='data-name'>" . $name . "</div>";
        $row[] = "<div id='data-name'>" . $newDatetime . "</div>";
        $row[] = $dimensions;
        $row[] = $player_type;
        $row[] = $actions;
        $output['aaData'][] = $row;
    }
}

echo json_encode($output);
?>
