<?php

/* main configuration file for script */
define("_CONFIG_SITE_HOST_URL",         "yoursite.com");  /* site url host without the http:// and no trailing forward slash - i.e. www.mydomain.com or links.mydomain.com */
define("_CONFIG_SITE_FULL_URL",         _CONFIG_SITE_HOST_URL);  /* full site url without the http:// and no trailing forward slash - i.e. www.mydomain.com/links or the same as the _CONFIG_SITE_HOST_URL */

/* database connection details */
define("_CONFIG_DB_HOST",               "localhost");  /* database host name */
define("_CONFIG_DB_NAME",               "");    /* database name */
define("_CONFIG_DB_USER",               "");    /* database username */
define("_CONFIG_DB_PASS",               "");    /* database password */

/* set these to the main site host if you're using direct web server uploads/downloads to remote servers */
define("_CONFIG_CORE_SITE_HOST_URL",    _CONFIG_SITE_HOST_URL);  /* site url host without the http:// and no trailing forward slash - i.e. www.mydomain.com or links.mydomain.com */
define("_CONFIG_CORE_SITE_FULL_URL",    _CONFIG_SITE_FULL_URL);  /* full site url without the http:// and no trailing forward slash - i.e. www.mydomain.com/links or the same as the _CONFIG_SITE_HOST_URL */

/* script version */
define("_CONFIG_SCRIPT_VERSION",        "3.5");    /* script version */

/* show database degug information on fail */
define("_CONFIG_DB_DEBUG",              true);    /* this will display debug information when something fails in the DB - leave this as true if you're not sure */

/* server paths */
define("_CONFIG_SCRIPT_ROOT",           dirname(__FILE__));
define("_CONFIG_FILE_STORAGE_PATH",     _CONFIG_SCRIPT_ROOT . '/files/');     /* location on your server to store file uploads */

/* the url of the domain to download files from, only change if you plan on using a different domain to link to your files */
define("_CONFIG_SITE_FILE_DOMAIN",      _CONFIG_SITE_FULL_URL);  /* url without the http:// and no trailing forward slash */

/* run the site as http or https, requires ssl certificate - functionality in beta */
define("_CONFIG_SITE_PROTOCOL",      	'http');  /* http or https */

/* toggle demo mode */
define("_CONFIG_DEMO_MODE",             false);    /* always leave this as false */
