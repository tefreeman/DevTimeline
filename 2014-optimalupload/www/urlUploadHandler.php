<?php

error_reporting(E_ALL | E_STRICT);

/* setup includes */
require_once('includes/master.inc.php');

// log
log::info('Remote upload request to urlUploadHandler.php: '.print_r($_REQUEST, true));

// no caching
header('Pragma: no-cache');
header('Cache-Control: private, no-cache');

// get url
$url = !empty($_REQUEST["url"]) && preg_match("|^http(s)?://.+$|", stripslashes($_REQUEST["url"])) ? stripslashes($_REQUEST["url"]) : null;
$rowId = (int) $_REQUEST['rowId'];

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

// include plugin code
$params = pluginHelper::includeAppends('url_upload_handler.php', array('url' => $url));
$url    = $params['url'];

// 1KB of initial data, required by Webkit browsers
echo "<span>" . str_repeat("0", 1000) . "</span>";

// allow sub-domains for remote file servers
echo "<script>document.domain = '"._CONFIG_CORE_SITE_HOST_URL."';</script>";

$upload_handler = new uploader();
$upload_handler->handleRemoteUrlUpload($url, $rowId);
