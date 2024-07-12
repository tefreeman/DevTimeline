<?php

// Determine our absolute document root
define('DOC_ROOT', realpath(dirname(__FILE__) . '/../'));

// set timezone if not set, change to whatever timezone you want
if(!ini_get('date.timezone'))
{
    date_default_timezone_set('GMT');
}

// global include files
require_once DOC_ROOT . '/includes/functions.inc.php'; // __autoload() is contained in this file
require_once DOC_ROOT . '/includes/class.dbobject.php';
require_once DOC_ROOT . '/includes/class.objects.php';

// fix magic quotes
if (get_magic_quotes_gpc())
{
    $_POST    = fix_slashes($_POST);
    $_GET     = fix_slashes($_GET);
    $_REQUEST = fix_slashes($_REQUEST);
    $_COOKIE  = fix_slashes($_COOKIE);
}

// load our config settings
$config = Config::getConfig();

// load db config settings into constants
Config::initConfigIntoMemory();

// make sure the url exactly matches the one set in _config.inc.php
checkDomain();

// setup database connection
$db = Database::getDatabase();

// setup error handler
log::initErrorHandler();

// store session info in the database?
if ($config->useDBSessions === true)
{
    DBSession::register();
}

// initialize our session
session_name($config->sessionName);

// how long to keep sessions active before expiring
session_set_cookie_params((int) SITE_CONFIG_SESSION_EXPIRY);

// rejoin sessions, used for cors
$sessionStarted = false;
if (isset($_REQUEST['_sid']))
{
    // check we have a matching trk in the database for security purposes
    if (isset($_REQUEST['_trk']))
    {
        $isValidSession = dbsession::validateCrossSiteSession($_REQUEST['_sid'], $_REQUEST['_trk']);
        if ($isValidSession)
        {
            $sid = $_REQUEST['_sid'];
            session_id($sid);

            // get session un/pw from db
            $db->query('SELECT data FROM sessions WHERE id=:id', array('id' => $sid));
            $sessionData = $db->getValue();
            if ($sessionData)
            {
                session_start();
                $sessionStarted = true;

                // reapply session
                session_decode($sessionData);
            }
        }
    }
}

// start session
if ($sessionStarted == false)
{
    session_start();
}

// pick up any requests to change the site language
if (isset($_REQUEST['_t']))
{
    // make sure the one passed is an active language
    $isValidLanguage = $db->getRow("SELECT languageName, flag FROM language WHERE isActive = 1 AND languageName = '" . $db->escape(trim($_REQUEST['_t'])) . "' LIMIT 1");
    if ($isValidLanguage)
    {
        $_SESSION['_t'] = trim($_REQUEST['_t']);
    }
    else
    {
        $_SESSION['_t'] = SITE_CONFIG_SITE_LANGUAGE;
    }
}
elseif (!isset($_SESSION['_t']))
{
    $_SESSION['_t'] = SITE_CONFIG_SITE_LANGUAGE;
}

// Initialize current user
$Auth = Auth::getAuth();

// check for maintenance mode
if((SITE_CONFIG_MAINTENANCE_MODE == 'yes') && ($Auth->level_id <= 2))
{
    showMaintenancePage();
}

// Object for tracking and displaying error messages
$Error = Error::getError();

// whether to use language specific images
$languageImagePath = '';
if (SITE_CONFIG_LANGUAGE_SEPARATE_LANGUAGE_IMAGES == 'yes')
{
    $languageFlag = $db->getValue("SELECT flag FROM language WHERE isActive = 1 AND languageName = '" . $db->escape($_SESSION['_t']) . "' LIMIT 1");
    if ($languageFlag)
    {
        $languageImagePath = $languageFlag . '/';
    }
}
define("SITE_THEME_PATH", WEB_ROOT . "/themes/" . SITE_CONFIG_SITE_THEME);
define("SITE_IMAGE_PATH", SITE_THEME_PATH . "/" . $languageImagePath . "images");
define("SITE_CSS_PATH", SITE_THEME_PATH . "/" . $languageImagePath . "styles");
define("SITE_JS_PATH", SITE_THEME_PATH . "/" . $languageImagePath . "js");

// how often to update the download tracker in seconds.
define('DOWNLOAD_TRACKER_UPDATE_FREQUENCY', 15);

// how long to keep the download tracker data, in days
define('DOWNLOAD_TRACKER_PURGE_PERIOD', 7);

// the root plugin directory
define('PLUGIN_DIRECTORY_NAME', 'plugins');
define('PLUGIN_DIRECTORY_ROOT', DOC_ROOT . '/' . PLUGIN_DIRECTORY_NAME . '/');
define('PLUGIN_WEB_ROOT', WEB_ROOT . '/' . PLUGIN_DIRECTORY_NAME);

// admin paths
define('ADMIN_FOLDER_NAME', 'admin');
define('ADMIN_WEB_ROOT', WEB_ROOT . '/' . ADMIN_FOLDER_NAME);

// cache store
define('CACHE_DIRECTORY_NAME', 'cache');
define('CACHE_DIRECTORY_ROOT', DOC_ROOT . '/' . CACHE_DIRECTORY_NAME);
define('CACHE_WEB_ROOT', WEB_ROOT . '/' . CACHE_DIRECTORY_NAME);

/* check for banned ip */
$bannedIP = bannedIP::getBannedType();
if (strtolower($bannedIP) == "whole site")
{
    header('HTTP/1.1 404 Not Found');
    die();
}

// setup demo mode
if (_CONFIG_DEMO_MODE == true)
{
    if (isset($_REQUEST['_p']))
    {
        $_SESSION['_plugins'] = false;
        if ((int) $_REQUEST['_p'] == 1)
        {
            $_SESSION['_plugins'] = true;
        }
        unset($_SESSION['pluginConfigs']);
    }

    if (!isset($_SESSION['_plugins']))
    {
        $_SESSION['_plugins'] = true;
        unset($_SESSION['pluginConfigs']);
    }
}

// load plugin configuration into the session
if (!isset($_SESSION['pluginConfigs']))
{
    $_SESSION['pluginConfigs'] = pluginHelper::loadPluginConfigurationFiles();
}

// append any plugin includes
pluginHelper::includeAppends('master.inc.php');
