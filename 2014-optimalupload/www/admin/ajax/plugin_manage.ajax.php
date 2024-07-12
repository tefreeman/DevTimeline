<?php

// includes and security
include_once('../_local_auth.inc.php');

// import any new plugins as uninstalled
adminFunctions::registerPlugins();

$sQL  = "SELECT * ";
$sQL .= "FROM plugin ";
$sQL .= "ORDER BY plugin_name ";
$totalRS = $db->getRows($sQL);
$limitedRS = $db->getRows($sQL);

$data = array();
if (COUNT($limitedRS) > 0)
{
    foreach ($limitedRS AS $row)
    {
        $lRow = array();

        $icon = 'local';
        $lRow[] = '<img src="'.WEB_ROOT.'/plugins/'.$row['folder_name'].'/assets/img/icons/16px.png" width="16" height="16" title="' . $row['plugin_name'] . '" alt="' . $row['plugin_name'] . '"/>';
        $lRow[] = adminFunctions::makeSafe($row['plugin_name']);
        $lRow[] = adminFunctions::makeSafe($row['plugin_description']);
        $lRow[] = '/'.adminFunctions::makeSafe($row['folder_name']);
        $lRow[] = '<span class="statusText'.(($row['is_installed']==1)?'Yes':'No').'">'.(($row['is_installed']==1)?'Yes':'No').'</span>';

        $links = array();
        if ($row['is_installed']==1)
        {
            $settingsPath = PLUGIN_DIRECTORY_ROOT . $row['folder_name'] . '/admin/settings.php';
            if(file_exists($settingsPath))
            {
                $links[] = '<a href="'.PLUGIN_WEB_ROOT.'/'.$row['folder_name'] . '/admin/settings.php?id='.$row['id'].'">settings</a>';
            }
            $links[] = '<a href="#" onClick="confirmUninstallPlugin(' . (int) $row['id'] . '); return false;">uninstall</a>';
        }
        else
        {
            $links[] = '<a href="#" onClick="confirmInstallPlugin(' . (int) $row['id'] . '); return false;">install</a>';
        }
        $lRow[]  = implode(" | ", $links);

        $data[] = $lRow;
    }
}

$resultArr = array();
$resultArr["sEcho"]                = intval($_GET['sEcho']);
$resultArr["iTotalRecords"]        = (int) COUNT($totalRS);
$resultArr["iTotalDisplayRecords"] = $resultArr["iTotalRecords"];
$resultArr["aaData"]               = $data;

echo json_encode($resultArr);