<?php
// Determine our absolute document root
define('ADMIN_ROOT', realpath(dirname(__FILE__)));

// ignore maintenance mode
define('IGNORE_MAINTENANCE_MODE', true);

// global includes
require_once(ADMIN_ROOT.'/../includes/master.inc.php');
require_once(ADMIN_ROOT.'/_admin_functions.inc.php');

if(!defined('ADMIN_IGNORE_LOGIN'))
{
    if(defined('MIN_ACCESS_LEVEL'))
    {
        $Auth->requireAccessLevel(MIN_ACCESS_LEVEL, ADMIN_WEB_ROOT."/login.php");
    }
    else
    {
        $Auth->requireAdmin();
    }
    $userObj = $Auth->getAuth();
}

// setup database
$db = Database::getDatabase();

// for cross domain uploads
$refDomain = getReffererDomainOnly();
if (!$refDomain)
{
    $refDomain = _CONFIG_SITE_PROTOCOL.'://' . _CONFIG_CORE_SITE_HOST_URL;
}
else
{
    $refDomain = _CONFIG_SITE_PROTOCOL . "://" . str_replace(array("http://", "https://"), "", $refDomain);
}

header('Access-Control-Allow-Origin: ' . (($refDomain === false) ? WEB_ROOT : ($refDomain)));
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Credentials: true');
header('Content-Disposition: inline; filename="files.json"');
