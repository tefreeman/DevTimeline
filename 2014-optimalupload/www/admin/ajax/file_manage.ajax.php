<?php

// includes and security
define('MIN_ACCESS_LEVEL', 10); // allow moderators
include_once('../_local_auth.inc.php');

$iDisplayLength = (int) $_REQUEST['iDisplayLength'];
$iDisplayStart  = (int) $_REQUEST['iDisplayStart'];
$sSortDir_0     = $_REQUEST['sSortDir_0'] ? $_REQUEST['sSortDir_0'] : "desc";
$filterText     = $_REQUEST['filterText'] ? $_REQUEST['filterText'] : "";
$filterByUser   = strlen($_REQUEST['filterByUser']) ? (int) $_REQUEST['filterByUser'] : false;
$filterByServer = strlen($_REQUEST['filterByServer']) ? (int) $_REQUEST['filterByServer'] : false;
$filterByStatus = strlen($_REQUEST['filterByStatus']) ? (int) $_REQUEST['filterByStatus'] : false;

// get sorting columns
$iSortCol_0     = (int) $_REQUEST['iSortCol_0'];
$sColumns       = trim($_REQUEST['sColumns']);
$arrCols        = explode(",", $sColumns);
$sortColumnName = $arrCols[$iSortCol_0];
$sort           = 'originalFilename';
switch ($sortColumnName)
{
    case 'filename':
        $sort = 'originalFilename';
        break;
    case 'filesize':
        $sort = 'fileSize';
        break;
    case 'date_uploaded':
        $sort = 'uploadedDate';
        break;
    case 'downloads':
        $sort = 'visits';
        break;
    case 'status':
        $sort = 'label';
        break;
    case 'owner':
        $sort = 'users.username';
        break;
}

$sqlClause = "WHERE 1=1 ";
if ($filterText)
{
    $filterText = $db->escape($filterText);
    $sqlClause .= "AND (file_status.label = '" . $filterText . "' OR ";
    $sqlClause .= "CONCAT('" . _CONFIG_SITE_FILE_DOMAIN . "/', file.shortUrl) LIKE '%" . $filterText . "%' OR ";
    $sqlClause .= "file.originalFilename LIKE '%" . $filterText . "%' OR ";
    $sqlClause .= "file.uploadedIP LIKE '%" . $filterText . "%' OR ";
    $sqlClause .= "file.id = '" . $filterText . "')";
}

if ($filterByUser)
{
    $sqlClause .= " AND file.userId = " . $filterByUser;
}

if ($filterByServer)
{
    $sqlClause .= " AND file.serverId = " . $filterByServer;
}

if ($filterByStatus)
{
    $sqlClause .= " AND file.statusId = " . $filterByStatus;
}

$totalRS   = $db->getValue("SELECT COUNT(file.id) AS total FROM file LEFT JOIN file_status ON file.statusId = file_status.id " . $sqlClause);
$limitedRS = $db->getRows("SELECT file.*, file_status.label, users.username FROM file LEFT JOIN file_status ON file.statusId = file_status.id LEFT JOIN users ON file.userId = users.id " . $sqlClause . " ORDER BY " . $sort . " " . $sSortDir_0 . " LIMIT " . $iDisplayStart . ", " . $iDisplayLength);

$data = array();
if (COUNT($limitedRS) > 0)
{
    foreach ($limitedRS AS $row)
    {
        $lRow = array();
        $icon = 'assets/images/icons/file_types/16px/' . $row['extension'] . '.png';
        if (!file_exists(ADMIN_ROOT . '/' . $icon))
        {
            $icon   = 'assets/images/icons/file_types/16px/_page.png';
        }
        $typeIcon = '<span style="vertical-align: middle;"><img src="' . $icon . '" width="16" height="16" title="' . $row['extension'] . '" alt="' . $row['extension'] . '" style="margin-right: 5px;"/></span>';
        
        $lRow[] = '<input type="checkbox" id="cbElement'.$row['id'].'" onClick="toggleFileIds(this);" value="'.$row['id'].'" class="checkbox"/>';
        if ($row['statusId'] == 1)
        {
            $lRow[] = $typeIcon.'<a href="' . file::getFileUrl($row['id']) . '~i" target="_blank" title="' . file::getFileUrl($row['id']) . '">' . adminFunctions::makeSafe(adminFunctions::limitStringLength($row['originalFilename'], 35)) . '</a>';
        }
        else
        {
            $lRow[] = $typeIcon.adminFunctions::makeSafe(adminFunctions::limitStringLength($row['originalFilename'], 35));
        }
        $lRow[] = dater($row['uploadedDate'], SITE_CONFIG_DATE_FORMAT);
        $lRow[] = (int) $row['fileSize'] > 0 ? formatSize($row['fileSize']) : 0;
        $lRow[] = (int) $row['visits'];
        $lRow[] = strlen($row['username'])?('<a title="IP: '.adminFunctions::makeSafe($row['uploadedIP']).'" href="'.ADMIN_WEB_ROOT.'/file_manage.php?filterByUser='.adminFunctions::makeSafe($row['userId']).'">'.adminFunctions::makeSafe($row['username']).'</a>'):'<span style="color: #aaa;" title="[no login]"><a href="'.ADMIN_WEB_ROOT.'/file_manage.php?filterText='.adminFunctions::makeSafe($row['uploadedIP']).'">'.adminFunctions::makeSafe($row['uploadedIP']).'</a></span>';
        $statusRow = '<span class="statusText'.str_replace(" ", "", adminFunctions::makeSafe(UCWords($row['label']))).'"';
        $statusRow .= '>'.$row['label'].'</span>';
        $lRow[] = $statusRow;

        $links = array();
        $links[] = '<a href="' . file::getFileStatisticsUrl($row['id']) . '" target="_blank">stats</a>';
        if ($row['statusId'] == 1)
        {
            $links[] = '<a href="#" onClick="confirmRemoveFile(' . (int) $row['id'] . '); return false;">remove</a>';
        }
        if(strlen($row['adminNotes']))
        {
            $links[] = '<a href="#" onClick="showNotes(\''.str_replace(array("\n", "\r"), "<br/>", adminFunctions::makeSafe(str_replace("'", "\"", $row['adminNotes']))).'\'); return false;">notes</a>';
        }
        if ($row['statusId'] == 1)
        {
            $links[] = '<a href="' . file::getFileUrl($row['id']) . '" target="_blank">download</a>';
        }
        $lRow[]  = implode(" | ", $links);

        $data[] = $lRow;
    }
}

$resultArr = array();
$resultArr["sEcho"]                = intval($_GET['sEcho']);
$resultArr["iTotalRecords"]        = (int) $totalRS;
$resultArr["iTotalDisplayRecords"] = $resultArr["iTotalRecords"];
$resultArr["aaData"]               = $data;

echo json_encode($resultArr);
