<?php

error_reporting(0);
$tz = $_GET['tz'];
date_default_timezone_set($tz);
require_once('../../smh_application/lib/KalturaClient.php');

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

        $select = '<select id="action" style="width: 115px;" onChange="action($(this).find(\'option:selected\').text(), $(this).val()); $(this).prop(\'selectedIndex\',0);">';
        $select .= "<option>Select Action</option><option value='" . $uiconf_id . "'>Edit</option>";
        $select .= "<option value='" . $uiconf_id . "'>Duplicate</option>";
        if ($player_type == 'Channel Playlist') {
            $select .= "<option value='" . $uiconf_id . "," . $p_id . "," . str_replace(",", "", htmlspecialchars($name, ENT_QUOTES)) . "'>Preview & Embed</option>";
        }

        $row = array();
        $row[] = $uiconf_id;
        $row[] = $name;
        $row[] = $newDatetime;
        $row[] = $dimensions;
        $row[] = $player_type;
        $row[] = $select;
        $output['aaData'][] = $row;
    }
}

echo json_encode($output);
?>