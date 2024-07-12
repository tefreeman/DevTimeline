<?php
// Determine our absolute document root
define('ADMIN_ROOT', realpath(dirname(dirname(__FILE__))));

// ignore maintenance mode
define('IGNORE_MAINTENANCE_MODE', true);

// global includes
require_once(ADMIN_ROOT.'/../includes/master.inc.php');
require_once(ADMIN_ROOT.'/_admin_functions.inc.php');

// prepare result
$result = array();
$result['error'] = false;
$result['msg']   = '';

// security, pulled locally so we can provide useful error messages over ajax
if($Auth->hasAccessLevel(10) == false)
{
    $rs['msg'] = 'Could not authenticate user.';
    echo json_encode($rs);
    die();
}

// pick up file ids
$fileIds     = $_REQUEST['fileIds'];
$deleteData = false;
if(isset($_REQUEST['deleteData']))
{
    $deleteData = $_REQUEST['deleteData']=='false'?false:true;
}

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
            // load file and process if active
            $file = file::loadById($fileId);
            if ($file)
            {
                $rs = false;
                if($deleteData == true)
                {
                    // delete
                    $rs = $file->deleteFileIncData();
                }
                elseif($file->statusId == 1)
                {
                    // remove
                    $rs = $file->removeBySystem();
                }

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
