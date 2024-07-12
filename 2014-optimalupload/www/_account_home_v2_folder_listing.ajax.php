<?php

/* setup includes */
require_once('includes/master.inc.php');

// require login
$Auth->requireUser(WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION);

// some initial headers
header("HTTP/1.0 200 OK");
header('Content-type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// prepare clause
$clause = 'userId = '.(int)$Auth->id.' AND ';
if((isset($_REQUEST['folder'])) && ($_REQUEST['folder'] != -1))
{
    $folder = $_REQUEST['folder'];
    $clause .= 'parentId = '.(int)$folder;
}
else
{
    $clause .= 'parentId IS NULL';
}

$rs = array();

// load folder data for user
$rows = $db->getRows('SELECT id, folderName, (SELECT COUNT(ffchild.id) AS total FROM file_folder ffchild WHERE ffchild.parentId = file_folder.id) AS childrenCount, accessPassword, (SELECT COUNT(id) AS total FROM file WHERE folderId = file_folder.id AND file.statusId = 1) AS fileCount FROM file_folder WHERE '.$clause.' ORDER BY folderName');
if($rows)
{
    foreach($rows AS $row)
    {
        $folderType = 'folder';
        if(((int)$row['fileCount'] > 0) || ((int)$row['childrenCount'] > 0))
        {
            $folderType = 'folderfull';
        }
        
        if(strlen($row['accessPassword']))
        {
            $folderType = 'folderpassword';
        }

        if((int)$row['childrenCount'] > 0)
        {
            $rs[] = array('data'=>$row['folderName'].(((int)$row['fileCount']>0)?(' ('.number_format($row['fileCount']).')'):''), 'attr'=>array('id'=>$row['id'], 'rel'=>$folderType), 'children'=> array('state'=>'closed'), 'state'=>'closed');
        }
        else
        {
            $rs[] = array('data'=>$row['folderName'].(((int)$row['fileCount']>0)?(' ('.number_format($row['fileCount']).')'):''), 'attr'=>array('id'=>$row['id'], 'rel'=>$folderType));
        }
    }
}

echo json_encode($rs);