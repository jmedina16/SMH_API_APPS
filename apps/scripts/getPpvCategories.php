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

$filter = new KalturaCategoryFilter();
$filter->orderBy = '-createdAt';
$pager = new KalturaFilterPager();

// PAGING
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $pager->pageSize = intval($_GET['iDisplayLength']);
    $pager->pageIndex = floor(intval($_GET['iDisplayStart']) / $pager->pageSize) + 1;
}

// ORDERING
$aColumns = array("id", "name");
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

$filteredListResult = $client->category->listAction($filter, $pager);

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
    $row = array();

    $row[] = $entry->name;
    $row[] = $entry->id;

    $output['aaData'][] = $row;
}

echo json_encode($output);
?>