<?php
if (!defined('ADMIN_PAGE_TITLE'))
{
    define('ADMIN_PAGE_TITLE', adminFunctions::t("admin_area", "admin area"));
}
if (!defined('ADMIN_SELECTED_PAGE'))
{
    define('ADMIN_SELECTED_PAGE', 'dashboard');
}
$AuthUser = Auth::getAuth();
$db = Database::getDatabase();
$totalReports         = (int) $db->getValue("SELECT COUNT(id) AS total FROM file_report WHERE report_status='pending'");
?>
<html lang="en-us">

    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1" >
        <meta charset="utf-8" />

        <link rel="apple-touch-con" href="" />

        <title><?php echo adminFunctions::makeSafe(UCwords(ADMIN_PAGE_TITLE)); ?> - Admin</title>

        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">

        <!-- The Columnal Grid and mobile stylesheet -->
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/styles/columnal/columnal.css" type="text/css" media="screen" />

        <!-- Fixes for IE -->

        <!--[if lt IE 9]>
            <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/styles/columnal/ie.css" type="text/css" media="screen" />
            <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/styles/ie8.css" type="text/css" media="screen" />
            <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/IE9.js"></script>
        <![endif]-->        


        <!-- Use CDN on production server -->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/jquery.min.js"></script>
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/jquery-ui.min.js"></script>

        <!-- Now that all the grids are loaded, we can move on to the actual styles. --> 
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/jqueryui/jqueryui.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/styles/style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/styles/global.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/styles/config.css" type="text/css" media="screen" />

        <!-- Adds HTML5 Placeholder attributes to those lesser browsers (i.e. IE) -->
        <script type="text/javascript" src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/jquery.placeholder.1.2.min.shrink.js"></script>

        <!-- Sortable, searchable DataTable -->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/jquery.dataTables.min.js"></script>

        <!-- Adds HTML5 Placeholder attributes to those lesser browsers (i.e. IE) -->
        <script type="text/javascript" src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/jquery.placeholder.1.2.min.shrink.js"></script>

        <!-- Adds charts -->
        <script type="text/javascript" src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/flot/jquery.flot.min.js"></script>
        <script type="text/javascript" src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/flot/jquery.flot.pie.min.js"></script>
        <script type="text/javascript" src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/flot/jquery.flot.stack.min.js"></script>

        <!-- Form Validation Engine -->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/formvalidator/jquery.validationEngine.js"></script>
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/formvalidator/jquery.validationEngine-en.js"></script>
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/formvalidator/validationEngine.jquery.css" type="text/css" media="screen" />

        <!-- Custom Tooltips -->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/twipsy.js"></script>

        <!-- WYSIWYG Editor -->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/cleditor/jquery.cleditor.min.js"></script>
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/cleditor/jquery.cleditor.css" type="text/css" media="screen" />

        <!-- Colorbox is a lightbox alternative-->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/colorbox/jquery.colorbox-min.js"></script>
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/colorbox/colorbox.css" type="text/css" media="screen" />

        <!-- Menu -->
        <link rel="stylesheet" href="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/superfish/superfish.css" type="text/css" media="screen" />
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/superfish/superfish.js"></script>

        <!-- ddslick, for images in dropdown menus -->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/jquery.ddslick.min.js"></script>

        <!-- Js used in the theme -->
        <script src="<?php echo ADMIN_WEB_ROOT; ?>/assets/scripts/global.js"></script>

    </head>
    <body>

        <div id="wrap">
            <div id="main">
                <header class="container">
                    <div class="row clearfix">
                        <div class="left">
                            <a href="<?php echo ADMIN_WEB_ROOT; ?>/index.php" id="logo"><?php echo adminFunctions::t("admin_area", "admin area"); ?></a>
                        </div>

                        <div class="right">
                            <ul id="toolbar">
                                <li><span><?php echo adminFunctions::t("logged_in_as", "Logged in as"); ?></span> <a class="user" href="<?php echo ADMIN_WEB_ROOT; ?>/user_edit.php?id=<?php echo $AuthUser->id; ?>"><?php echo $AuthUser->username; ?></a></li>
                                <?php
                                $systemAlertErrorStr = '';

                                // output error
                                if (file_exists('../install/'))
                                {
                                    $systemAlertErrorStr = '<strong>IMPORTANT:</strong><br/><br/>Remove the /install/ folder within your webroot asap.';
                                }

                                // output error
                                if (strlen($systemAlertErrorStr))
                                {
                                    ?>
                                    <li>
                                        <a id="alertYellow" href="#" onClick="$('#alertMessage').dialog('open');
                                                return false;">Alert</a>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li><a id="search" href="#">Search</a></li>
                            </ul>
                            <div id="searchdrop">
                                <form action="<?php echo ADMIN_WEB_ROOT; ?>/file_manage.php" method="POST">
                                    <input type="text" id="searchbox" name="filterText" placeholder="Search files...">
                                </form>
                            </div>
                        </div>  
                    </div>
                </header>

                <nav class="container" style="background-color: #3B4966;">
                    <select class="mobile-only row" onchange="window.open(this.options[this.selectedIndex].value, '_top')">
                        <option value="<?php echo ADMIN_WEB_ROOT; ?>/index.php">Dashboard</option>
                    </select>

                    <ul class="sf-menu mobile-hide row clearfix">
                        <li class="<?php if (ADMIN_SELECTED_PAGE == 'dashboard') echo 'active'; ?> iconed"><a href="<?php echo ADMIN_WEB_ROOT; ?>/index.php?t=dashboard"><span><img src="<?php echo ADMIN_WEB_ROOT; ?>/assets/images/header/icon_dashboard.png" /> Dashboard</span></a></li>
                        <li<?php if ((ADMIN_SELECTED_PAGE == 'files') || (ADMIN_SELECTED_PAGE == 'downloads')) echo ' class="active"'; ?>><a href="<?php echo ADMIN_WEB_ROOT; ?>/file_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('files', 'files')))); ?></span></a>
                            <ul>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/file_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('manage_files', 'manage files')))); ?></span></a></li>
                                <?php if($AuthUser->hasAccessLevel(20)): ?>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/download_current.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('active_downloads', 'active downloads')))); ?></span></a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/file_report_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('abuse_reports', 'abuse reports')))); ?><?php if($totalReports > 0): ?> (<?php echo $totalReports; ?>)<?php endif; ?></span></a></li>
                            </ul>
                        </li>
                        
                        <?php if($AuthUser->hasAccessLevel(20)): ?>
                        <li<?php if (ADMIN_SELECTED_PAGE == 'users') echo ' class="active"'; ?>><a href="<?php echo ADMIN_WEB_ROOT; ?>/user_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('users', 'users')))); ?></span></a>
                            <ul>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/user_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('manage_users', 'manage users')))); ?></span></a></li>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/user_add.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('add_user', 'add user')))); ?></span></a></li>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/payment_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('received_payments', 'received payments')))); ?></span></a></li>
                            </ul>
                        </li>
                        <li<?php if (ADMIN_SELECTED_PAGE == 'file_servers') echo ' class="active"'; ?>><a href="<?php echo ADMIN_WEB_ROOT; ?>/server_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('file_servers', 'file servers')))); ?></span></a></li>

                        <?php
                        // add any plugin navigation
                        pluginHelper::outputPluginAdminNav();
                        ?>
                        
                        <?php endif; ?>

                        <li style="float: right;"><a href="logout.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('logout', 'logout')))); ?></span></a></li>
                        
                        <?php if($AuthUser->hasAccessLevel(20)): ?>
                        <li<?php if (ADMIN_SELECTED_PAGE == 'configuration') echo ' class="active"'; ?> style="float: right;"><a href="#"><span>Configuration</span></a>
                            <ul>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/setting_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('site_settings', 'site settings')))); ?></span></a></li>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/translation_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('translations', 'translations')))); ?></span></a></li>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/download_page_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('manage_download_pages', 'manage download pages')))); ?></span></a></li>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/banned_ip_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('banned_ips', 'banned ips')))); ?></span></a></li>
                                <li><a href=""><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('system_tools', 'system tools')))); ?></span></a>
                                    <ul>
                                        <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/log_file_viewer.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('system_logs', 'system logs')))); ?></span></a></li>
                                        <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/database_browser.php?username=&db=<?php echo _CONFIG_DB_NAME; ?>"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('database_browser', 'database browser')))); ?></span></a></li>
                                        <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/server_info.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('server_info', 'server info')))); ?></span></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        
                        <?php
                        $sQL  = "SELECT * FROM plugin WHERE is_installed = 1 ORDER BY plugin_name";
                        $pluginList = $db->getRows($sQL);
                        ?>

                        <li<?php if (ADMIN_SELECTED_PAGE == 'plugins') echo ' class="active"'; ?> style="float: right;"><a href="#"><span>Plugins</span></a>
                            <ul>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/plugin_manage.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('manage_plugins', 'manage plugins')))); ?></span></a>
                                    <?php if(COUNT($pluginList)): ?>
                                    <ul>
                                        <?php
                                        foreach($pluginList AS $k=>$pluginItem)
                                        {
                                            if($k < 10)
                                            {
                                        ?>
                                        <li><a href="<?php echo PLUGIN_WEB_ROOT; ?>/<?php echo adminFunctions::makeSafe($pluginItem['folder_name']); ?>/admin/settings.php?id=<?php echo (int)$pluginItem['id']; ?>"><span><?php echo adminFunctions::makeSafe($pluginItem['plugin_name']); ?></span></a></li>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/plugin_manage.php"><span>more....</span></a></li>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                                <li><a href="<?php echo ADMIN_WEB_ROOT; ?>/plugin_manage_add.php"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('add_plugin', 'add plugin')))); ?></span></a></li>
                                <li><a href="http://www.yetishare.com/plugins.html" target="_blank"><span><?php echo adminFunctions::makeSafe(UCWords(strtolower(adminFunctions::t('get_plugin', 'get plugins')))); ?></span></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div id="titlediv">
                    <div class="clearfix container" id="pattern">
                        <div class="row">
                            <div class="col_12">
                                <h1><?php echo adminFunctions::makeSafe(UCwords(ADMIN_PAGE_TITLE)); ?></h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container" id="actualbody">
                    <div class="notificationHeader" id="notificationHeader"></div>