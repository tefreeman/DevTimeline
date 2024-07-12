<?php

// includes and security
define('MIN_ACCESS_LEVEL', 10); // allow moderators
include_once('../_local_auth.inc.php');

$fileId     = (int) $_REQUEST['fileId'];
$statusId   = (int) $_REQUEST['statusId'];
$adminNotes = isset($_REQUEST['adminNotes']) ? trim($_REQUEST['adminNotes']) : '';

// prepare result
$result = array();
$result['error'] = false;
$result['msg']   = '';

if (_CONFIG_DEMO_MODE == true)
{
    $result['error'] = true;
    $result['msg']   = adminFunctions::t("no_changes_in_demo_mode");
}
else
{
    // check for removal
    if (($statusId == 3) || ($statusId == 4))
    {
        // load file
        $file = file::loadById($fileId);
        if (!$file)
        {
            $result['error'] = true;
            $result['msg']   = 'Could not locate the file.';
            echo json_encode($result);
            exit;
        }

        // remove
        $file->removeBySystem();
    }

    $db->query('UPDATE file SET statusId = :statusId, adminNotes = :adminNotes WHERE id = :id', array('statusId'   => $statusId, 'adminNotes' => $adminNotes, 'id'         => $fileId));
    if ($db->affectedRows() == 1)
    {
        $result['error'] = false;
        $result['msg']   = 'File \'' . $file->originalFilename . '\' removed.';
    }
    else
    {
        $result['error'] = true;
        $result['msg']   = 'Could not update the status of the file.';
    }
}

echo json_encode($result);
exit;
