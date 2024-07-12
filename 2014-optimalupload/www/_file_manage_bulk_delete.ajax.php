<?php

/* setup includes */
require_once('includes/master.inc.php');

// require login
$Auth->requireUser(WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION);

// prepare result
$result = array();
$result['error'] = false;
$result['msg']   = '';

// pick up file ids
$fileIds     = $_REQUEST['fileIds'];

if (_CONFIG_DEMO_MODE == true)
{
    $result['error'] = true;
    $result['msg']   = adminFunctions::t("no_changes_in_demo_mode");
}
else
{
    $totalRemoved = 0;
    
    // load files
    if(COUNT($fileIds))
    {
        foreach($fileIds AS $fileId)
        {
            // load file and process if active and belongs to the currently logged in user
            $file = file::loadById($fileId);
            if (($file) && ($file->statusId == 1) && ($file->userId == $Auth->id))
            {
                // remove
                $rs = $file->removeBySystem();
                if($rs)
                {
                    $totalRemoved++;
                }
            }
        }
    }
}

$result['msg'] = 'Removed '.$totalRemoved.' file'.($totalRemoved!=1?'s':'').'.';

echo json_encode($result);
exit;
