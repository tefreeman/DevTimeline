<?php

// includes and security
define('MIN_ACCESS_LEVEL', 10); // allow moderators
include_once('../_local_auth.inc.php');

// check for script upgrade
$fileContents = geturl('http://www.mfscripts.com/_script_internal/yetishare.txt');
if(($fileContents) && (strlen($fileContents)))
{
    $lines = explode("\n", $fileContents);
    $newVersion = (float)$lines[0];
    $upgradeMessage = trim($lines[1]);
    
    // check against current version
    if($newVersion > (float)_CONFIG_SCRIPT_VERSION)
    {
        echo $upgradeMessage;
    }
}