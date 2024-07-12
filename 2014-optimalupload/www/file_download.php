<?php

require_once('includes/master.inc.php');

// try to load the file object
$file = null;
if (isset($_REQUEST['u']))
{
    // only keep the initial part if there's a forward slash
    $shortUrl = current(explode("/", $_REQUEST['u']));
    $file     = file::loadByShortUrl($shortUrl);
}

// could not load the file
if (!$file)
{
    output404();
    //redirect(getCoreSitePath() . "/index." . SITE_CONFIG_PAGE_EXTENSION);
}

// download file
if(isset($_REQUEST[file::DOWNLOAD_TOKEN_VAR]))
{
    $downloadToken = $_REQUEST[file::DOWNLOAD_TOKEN_VAR];
    $rs = $file->download(true, true, $downloadToken);
    if (!$rs)
    {
        $errorMsg = t("error_can_not_locate_file", "File can not be located, please try again later.");
        if ($file->errorMsg != null)
        {
            $errorMsg = t("file_download_error", "Error").': ' . $file->errorMsg;
        }
        redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
    }
}

// clear any expired download trackers
downloadTracker::clearTimedOutDownloads();
downloadTracker::purgeDownloadData();

/* setup page */
define("PAGE_NAME", $file->originalFilename);
define("PAGE_DESCRIPTION", t("file_download_description", "Download file"));
define("PAGE_KEYWORDS", t("file_download_keywords", "download, file, upload, mp3, avi, zip"));

// has the file been removed
if ($file->statusId == 2)
{
    $errorMsg = t("error_file_has_been_removed_by_user", "File has been removed.");
    redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
}
elseif ($file->statusId == 3)
{
    $errorMsg = t("error_file_has_been_removed_by_admin", "File has been removed by the site administrator.");
    redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
}
elseif ($file->statusId == 4)
{
    $errorMsg = t("error_file_has_been_removed_due_to_copyright", "File has been removed due to copyright issues.");
    redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
}
elseif ($file->statusId == 5)
{
    $errorMsg = t("error_file_has_expired", "File has been removed due to inactivity.");
    redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
}

// initial variables
$skipCountdown = false;

// include any plugin includes
$params = pluginHelper::includeAppends('file_download_top.php', array('skipCountdown'=>$skipCountdown));
$skipCountdown = $params['skipCountdown'];

// if the user is not logged in but we have http username/password. (for download managers)
if ($Auth->loggedIn() === false)
{
    if ((isset($_SERVER['PHP_AUTH_USER'])) && (isset($_SERVER['PHP_AUTH_PW'])))
    {
        $Auth->attemptLogin(trim($_SERVER['PHP_AUTH_USER']), MD5(trim($_SERVER['PHP_AUTH_PW'])), false);
        if ($Auth->loggedIn() === false)
        {
            header('WWW-Authenticate: Basic realm="Please enter a valid username and password"');
            header('HTTP/1.0 401 Unauthorized');
            header('status: 401 Unauthorized');
            exit;
        }
        else
        {
            // assume download manager
            $skipCountdown = true;
        }
    }
}

// whether to allow downloads or not if the user is not logged in
if ((!$Auth->loggedIn()) && (SITE_CONFIG_REQUIRE_USER_ACCOUNT_DOWNLOAD == 'yes'))
{
    redirect(getCoreSitePath() . "/register." . SITE_CONFIG_PAGE_EXTENSION);
}

// if we need to request the password
if (strlen($file->accessPassword) && (($Auth->id != $file->userId) || ($Auth->id == '')))
{
    if (!isset($_SESSION['allowAccess' . $file->id]))
    {
        $_SESSION['allowAccess' . $file->id] = false;
    }

    // make sure they've not already set it
    if ($_SESSION['allowAccess' . $file->id] === false)
    {
        redirect(getCoreSitePath() . "/file_password." . SITE_CONFIG_PAGE_EXTENSION . '?file=' . $shortUrl);
    }
}

// free or non logged in users
if ($Auth->level_id <= 1)
{
    // make sure the user is permitted to download files of this size
    if ((int) getMaxDownloadSize() > 0)
    {
        if ((int) getMaxDownloadSize() < $file->fileSize)
        {
            $errorMsg = t("error_you_must_register_for_a_premium_account_for_filesize", "You must register for a premium account to download files of this size. Please use the links above to register or login.");
            redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
        }
    }

    // check if the user has reached the max permitted concurrent downloads
    if ((int) SITE_CONFIG_FREE_USER_MAX_DOWNLOAD_THREADS > 0)
    {
        $sQL          = "SELECT COUNT(download_tracker.id) AS total_threads ";
        $sQL .= "FROM download_tracker ";
        $sQL .= "WHERE download_tracker.status='downloading' AND download_tracker.ip_address = " . $db->quote(getUsersIPAddress()) . " ";
        $sQL .= "GROUP BY download_tracker.ip_address ";
        $totalThreads = (int) $db->getValue($sQL);
        if ($totalThreads >= (int) SITE_CONFIG_FREE_USER_MAX_DOWNLOAD_THREADS)
        {
            $errorMsg = t("error_you_have_reached_the_max_permitted_downloads", "You have reached the maximum concurrent downloads. Please wait for your existing downloads to complete or register for a premium account above.");
            redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
        }
    }

    // make sure the user is permitted to download
    if ((int) getWaitTimeBetweenDownloads() > 0)
    {
        $sQL            = "SELECT (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(date_updated)) AS seconds ";
        $sQL .= "FROM download_tracker ";
        $sQL .= "WHERE download_tracker.status='finished' AND download_tracker.ip_address = " . $db->quote(getUsersIPAddress()) . " ";
        $sQL .= "ORDER BY download_tracker.date_updated DESC ";
        $longAgoSeconds = (int) $db->getValue($sQL);
        if (($longAgoSeconds > 0) && ($longAgoSeconds < (int) getWaitTimeBetweenDownloads()))
        {
            $errorMsg = t("error_you_must_wait_between_downloads", "You must wait [[[WAITING_TIME_LABEL]]] between downloads. Please try again later or register for a premium account above to remove the restriction.", array('WAITING_TIME_LABEL' => secsToHumanReadable(getWaitTimeBetweenDownloads())));
            redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
        }
    }

    // make sure the user is permitted to download files of this size
    if ((int) getMaxDailyDownloads() > 0)
    {
        // get total user downloads today
        $sQL            = "SELECT COUNT(id) AS total ";
        $sQL .= "FROM download_tracker ";
        $sQL .= "WHERE download_tracker.status='finished' AND download_tracker.ip_address = " . $db->quote(getUsersIPAddress()) . " ";
        $sQL .= "AND UNIX_TIMESTAMP(date_updated) >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 day))";
        $totalDownloads24Hour = (int) $db->getValue($sQL);
        if ((int) getMaxDailyDownloads() < $totalDownloads24Hour)
        {
            $errorMsg = t("error_you_have_reached_the_maximum_permitted_downloads_in_the_last_24_hours", "You have reached the maximum permitted downloads in the last 24 hours.");
            redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
        }
    }
}

// show the download pages
if($skipCountdown == false)
{
    $file->showDownloadPages();
}

// do we need to display the captcha?
if (showDownloadCaptcha() == true)
{
    /* do we require captcha validation? */
    $showCaptcha = false;
    if (!isset($_REQUEST['recaptcha_response_field']))
    {
        $showCaptcha = true;
    }

    /* check captcha */
    if (isset($_REQUEST['recaptcha_response_field']))
    {
        $rs = captchaCheck($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
        if (!$rs)
        {
            setError(t("invalid_captcha", "Captcha confirmation text is invalid."));
            $showCaptcha = true;
        }
    }

    if ($showCaptcha == true)
    {
        include_once(_CONFIG_SCRIPT_ROOT . '/_download_page_captcha.inc.php');
        exit();
    }
}

// include any plugin includes
pluginHelper::includeAppends('file_download_bottom.php');

// close database so we don't cause locks during the download
$db = Database::getDatabase();
$db->close();

// clear session tracker
$_SESSION['_download_page_next_page'] = 1;

// generate unique download url
$downloadUrl = $file->generateDirectDownloadUrl();
redirect($downloadUrl);