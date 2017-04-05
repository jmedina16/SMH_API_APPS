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

$client->startMultiRequest();

$client->partner->getinfo();

$filter = new KalturaUserFilter();
$filter->orderBy = '+createdAt';
$filter->statusIn = '1,0';
$filter->isAdminEqual = KalturaNullableBoolean::TRUE_VALUE;
$filter->loginEnabledEqual = KalturaNullableBoolean::TRUE_VALUE;
$pager = new KalturaFilterPager();

// PAGING
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $pager->pageSize = intval($_GET['iDisplayLength']);
    $pager->pageIndex = floor(intval($_GET['iDisplayStart']) / $pager->pageSize) + 1;
}

// ORDERING
$aColumns = array("status", "fullName", "id", "email", "roleIds", "lastLoginTime", "actions");
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
    $filter->firstNameOrLastNameStartsWith = $_GET['sSearch'];
}

$client->user->listAction($filter, $pager);

$result = $client->doMultiRequest();

$myData = $result[1]->objects;
$you = $_GET['id'];
$account_limit = $result[0]->adminLoginUsersQuota;
$i = 0;

$output = array(
    "orderBy" => $filter->orderBy,
    "iTotalRecords" => intval($result[1]->totalCount),
    "iTotalDisplayRecords" => intval($result[1]->totalCount),
    "aaData" => array(),
    "usersInUse" => 0,
    "usersAvail" => 0
);

$delete_data = '';
$block_data = '';

foreach ($myData as $data) {
    $oy;
    $select = '<select id="action" style="width: 115px !important;" onChange="action($(this).find(\'option:selected\').text(), $(this).val()); $(this).prop(\'selectedIndex\',0);">';
    if ($data->isAccountOwner && $you == $data->email) {
        $name = $data->fullName . " (You, Account Owner)";
        $oy = 'true';
        $status = '<div style="margin-left: 10px;"><img width="17px" src="http://mediaplatform.streamingmediahosting.com/img/unblock-icon.png" rel="tooltip" data-original-title="Active" data-placement="top"></div>';
    } else if ($data->isAccountOwner && $you != $data->email) {
        $name = $data->fullName . " (Account Owner)";
        $oy = 'true';
        $status = '<div style="margin-left: 10px;"><img width="17px" src="http://mediaplatform.streamingmediahosting.com/img/unblock-icon.png" rel="tooltip" data-original-title="Active" data-placement="top"></div>';
    } else if (!$data->isAccountOwner && $you == $data->email) {
        $name = $data->fullName . " (You)";
        $oy = 'true';
        $status = '<div style="margin-left: 10px;"><img width="17px" src="http://mediaplatform.streamingmediahosting.com/img/unblock-icon.png" rel="tooltip" data-original-title="Active" data-placement="top"></div>';
    } else {
        $name = $data->fullName;
        $owner_arr = array();
        $block_arr = array();
        $oy = 'false';
        $block_arr = $data->id . ',' . $data->status . ',' . $data->fullName;
        if ($data->status == 0) {
            $status = '<div style="margin-left: 10px;"><img width="17px" src="http://mediaplatform.streamingmediahosting.com/img/block-icon.png" rel="tooltip" data-original-title="Blocked" data-placement="top"></div>';
            $block_data = '<a href="#" rel="tooltip" data-original-title="Activate" data-placement="top" onclick="statusUser(\'' . $block_arr . '\');"><img width="15px" src="http://mediaplatform.streamingmediahosting.com/img/unblock-small-icon.png"></a>';
        } else {
            $status = '<div style="margin-left: 10px;"><img width="17px" src="http://mediaplatform.streamingmediahosting.com/img/unblock-icon.png" rel="tooltip" data-original-title="Active" data-placement="top"></div>';
            $block_data = '<a href="#" rel="tooltip" data-original-title="Block" data-placement="top" onclick="statusUser(\'' . $block_arr . '\');"><img width="15px" src="http://mediaplatform.streamingmediahosting.com/img/block-small-icon.png"></a>';
        }
        $delete_data = '<a href="#" rel="tooltip" data-original-title="Delete" data-placement="top" onclick="deleteUser(\'' . $block_arr . '\');"><img width="15px" src="http://mediaplatform.streamingmediahosting.com/img/ppv-remove.png"></a>';
    }

    $owner_arr = $data->id . ',' . $data->email . ',' . $data->screenName . ',' . $data->firstName . ',' . $data->lastName . ',' . $data->fullName . ',' . $data->roleNames . ',' . $data->isAdmin . ',' . $data->roleIds . ',' . $oy;

    if ($data->lastLoginTime === null) {
        $unixtime_to_date = date('n/j/Y H:i', 1008276300);
        $newDatetime = strtotime($unixtime_to_date);
        $newDatetime = date('m/d/Y h:i A', $newDatetime);
    } else {
        $unixtime_to_date = date('n/j/Y H:i', $data->lastLoginTime);
        $newDatetime = strtotime($unixtime_to_date);
        $newDatetime = date('m/d/Y h:i A', $newDatetime);
    }

//    if ($data->status === 1) {
//        $status = 'Active';
//    } else if ($data->status === 0) {
//        $status = 'Blocked';
//    } else if ($data->status === 2) {
//        $status = 'Deleted';
//    }
    $row = array();
    $row[] = $status;
    $row[] = "<div id='data-name'>" . $name . "</div>";
    $row[] = "<div id='data-name'>" . $data->id . "</div>";
    $row[] = "<div id='data-name'>" . $data->email . "</div>";
    $row[] = "<div id='data-name'>" . $data->roleNames . "</div>";
    $row[] = "<div id='data-name'>" . $newDatetime . "</div>";
    $row[] = '<a href="#" rel="tooltip" data-original-title="Edit" data-placement="top" onclick="editUser(\'' . $owner_arr . '\');"><img width="15px" src="http://mediaplatform.streamingmediahosting.com/img/ppv_edit.png"></a> &nbsp;&nbsp;' . $delete_data . '&nbsp;&nbsp;' . $block_data;
    $output['aaData'][] = $row;
    $i++;
}

$output['usersInUse'] = $i;

$available = $account_limit - $i;
$output['usersAvail'] = $available;

if (isset($_GET['sEcho'])) {
    $output["sEcho"] = intval($_GET['sEcho']);
}
echo json_encode($output);
?>
