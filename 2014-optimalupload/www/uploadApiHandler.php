<?php

// note: An API is required for this script to work.

error_reporting(E_ALL | E_STRICT);

/* setup includes */
require_once('includes/master.inc.php');

// validate the api key
$apiKey = $_REQUEST['api_key'];
if (strlen($apiKey) == 0)
{
    $rs    = array();
    $rs[0] = array('error' => 'Please set the users API Key.');
    die(json_encode($rs));
}
else
{
    // check API key in db
    $userId = $db->getValue("SELECT id FROM users WHERE apikey = " . $db->quote($apiKey) . " LIMIT 1");
    if (!$userId)
    {
        $rs    = array();
        $rs[0] = array('error' => 'Invalid API Key.');
        die(json_encode($rs));
    }
}

// setup uploader
$upload_handler = new uploader();

// setup auth for current user
$Auth = Auth::getAuth();
$Auth->impersonate($userId);

// headers
// for cross domain uploads
$refDomain = getReffererDomainOnly();
if (!$refDomain)
{
    if ((isset($_REQUEST['p'])) && (isset($_REQUEST['r'])))
    {
        $refDomain = $_REQUEST['p'] . '://' . $_REQUEST['r'];
    }
}
else
{
    $refDomain = _CONFIG_SITE_PROTOCOL . "://" . str_replace(array("http://", "https://"), "", $refDomain);
}

header('Access-Control-Allow-Origin: ' . (($refDomain === false) ? WEB_ROOT : ($refDomain)));
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Credentials: true');
header('Content-Disposition: inline; filename="files.json"');

switch ($_SERVER['REQUEST_METHOD'])
{
    case 'POST':
        $upload_handler->post();
        break;
    case 'OPTIONS':
        // do nothing
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
}
