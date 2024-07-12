<?php

// includes and security
define('MIN_ACCESS_LEVEL', 10); // allow moderators
include_once('../_local_auth.inc.php');

$fileId = (int) $_REQUEST['fileId'];

// load file
$rs = array();
$rs['error'] = true;
$rs['msg'] = 'Failed loading file server.';
$filePath = file::getFileDomainAndPath($fileId);
if($filePath)
{
    $rs['filePath'] = $filePath;
    $rs['error'] = false;
}

echo json_encode($rs);