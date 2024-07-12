<?php

/* setup includes */
require_once('includes/master.inc.php');

// require login
$Auth->requireUser(WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION);

// prepare result
$rs = array();
$rs['error'] = true;
$rs['msg'] = 'Failed loading file server(s) for selected files, please try again later or remove individually.';

// get variables
$fileIds = $_REQUEST['fileIds'];

// loop file ids and get paths
$filePaths = array();
if(COUNT($fileIds))
{
    foreach($fileIds AS $fileId)
    {
        $filePath = file::getFileDomainAndPath($fileId, null, true);
        if($filePath)
        {
            if(!is_array($filePaths[$filePath]))
            {
                $filePaths[$filePath] = array();
            }
            $filePaths[$filePath][] = $fileId;
        }
    }
}

if(COUNT($filePaths))
{
    $rs['filePaths'] = $filePaths;
    $rs['error'] = false;
    $rs['msg'] = '';
}

echo json_encode($rs);