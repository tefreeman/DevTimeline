<?php

error_reporting(E_ALL | E_STRICT);

/* setup includes */
require_once('includes/master.inc.php');

// log
log::info('Upload request to uploadHandler.php: '.print_r($_REQUEST, true));

// no caching
header('Pragma: no-cache');
header('Cache-Control: private, no-cache');

// check we are receiving the request from the site
if (!checkReferrer())
{
    // exit
    header('HTTP/1.0 400 Bad Request');
    exit;
}

// double check user is logged in if required
$Auth = Auth::getAuth();
if (getAllowedToUpload() == false)
{
    echo createUploadError(t('unavailable', 'Unavailable.'), t('uploading_has_been_disabled', 'Uploading has been disabled.'));
    exit;
}

// check for banned ip
$bannedIP = bannedIP::getBannedType();
if (strtolower($bannedIP) == "uploading")
{
    echo createUploadError(t('unavailable', 'Unavailable.'), t('uploading_has_been_disabled', 'Uploading has been disabled.'));
    exit;
}

// check that the user has not reached their max permitted uploads
$fileRemaining = getRemainingFilesToday();
if ($fileRemaining == 0)
{
    echo createUploadError(t('max_uploads_reached', 'Max uploads reached.'), t('reached_maximum_uploads', 'You have reached the maximum permitted uploads for today.'));
    exit;
}

// check the user hasn't reached the maximum storage on their account
if(getAvailableFileStorage() <= 0)
{
    echo createUploadError(t('file_upload_space_full', 'File upload space full.'), t('file_upload_space_full_text', 'Upload storage full, please delete some active files and try again.'));
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // make sure the server meets the min upload size limits
    $uploadChunks = 5000000;
    if (isset($_REQUEST['maxChunkSize']))
    {
        $uploadChunks = (int) trim($_REQUEST['maxChunkSize']);
        if ($uploadChunks == 0)
        {
            $uploadChunks = 5000000;
        }
    }
    if (getPHPMaxUpload() < $uploadChunks)
    {
        echo createUploadError(t('file_upload_max_upload_php_limit', 'PHP Upload Limit.'), t('file_upload_max_upload_php_limit_text', 'Your PHP limits need to be set to at least [[[MAX_SIZE]]] to allow larger files to be uploaded. Contact your host to set.', array('MAX_SIZE' => formatSize($uploadChunks))));
        exit;
    }
}

// for cross domain uploads
$refDomain = getReffererDomainOnly();
if(!$refDomain)
{
    if((isset($_REQUEST['p'])) && (isset($_REQUEST['r'])))
    {
        $refDomain = $_REQUEST['p'].'://'.$_REQUEST['r'];
    }
}
else
{
    $refDomain = _CONFIG_SITE_PROTOCOL . "://" . str_replace(array("http://", "https://"), "", $refDomain);
}

header('Access-Control-Allow-Origin: ' . (($refDomain === false) ? WEB_ROOT : ($refDomain)));
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Credentials: true');

switch ($_SERVER['REQUEST_METHOD'])
{
    case 'HEAD':
    case 'GET':
        header('Content-Disposition: inline; filename="files.json"');
        $upload_handler = new uploader(
            array(
                'max_chunk_size'=>(int)$_REQUEST['maxChunkSize']
            ));
        $upload_handler->get();
        break;
    case 'POST':
        header('Content-Disposition: inline; filename="files.json"');
        $upload_handler = new uploader(
            array(
                'max_chunk_size'=>(int)$_REQUEST['maxChunkSize']
            ));
        $upload_handler->post();
        break;
    case 'OPTIONS':
        // do nothing
        break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
}
