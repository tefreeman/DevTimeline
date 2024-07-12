<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo safeOutputToScreen(PAGE_NAME); ?> - <?php echo SITE_CONFIG_SITE_NAME; ?></title>
        <meta name="description" content="<?php echo safeOutputToScreen(PAGE_DESCRIPTION); ?>" />
        <meta name="keywords" content="<?php echo safeOutputToScreen(PAGE_KEYWORDS); ?>" />
        <meta name="copyright" content="Copyright &copy; <?php echo date("Y"); ?> - <?php echo SITE_CONFIG_SITE_NAME; ?>" />
        <meta name="robots" content="all" />
        <meta http-equiv="Cache-Control" content="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        <meta http-equiv="Pragma" content="no-cache" />
        <?php
        // add css files, use the htmlHelper::addCssFile() function so files can be joined/minified
        pluginHelper::addCssFile(SITE_CSS_PATH.'/jquery-ui-1.8.9.custom.css');
        pluginHelper::addCssFile(SITE_CSS_PATH.'/screen.css');
        pluginHelper::addCssFile(SITE_CSS_PATH.'/tabview-core.css');
        pluginHelper::addCssFile(SITE_CSS_PATH.'/data_table.css');
        pluginHelper::addCssFile(SITE_CSS_PATH.'/gh-buttons.css');
        
        // output css
        pluginHelper::outputCss();
        ?>
        <?php echo dbsession::crossSiteSessions(); ?>
        
        <script type="text/javascript">
            var WEB_ROOT = "<?php echo WEB_ROOT; ?>";
<?php echo translate::generateJSLanguageCode(); ?>
        </script>
        <?php
        // add js files, use the htmlHelper::addJsFile() function so files can be joined/minified
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery-1.11.0.min.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery-ui.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.dataTables.min.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.tmpl.min.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/load-image.min.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/canvas-to-blob.min.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.iframe-transport.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.fileupload.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.fileupload-process.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.fileupload-resize.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.fileupload-validate.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/jquery.fileupload-ui.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/zeroClipboard/ZeroClipboard.js');
        pluginHelper::addJsFile(WEB_ROOT.'/js/global.js');
        
        // output js
        pluginHelper::outputJs();
        ?>
    </head>

    <body>
        <?php if (_CONFIG_DEMO_MODE == true): ?>
            <div id="demoBanner">
                <span onClick="window.location='http://www.yetishare.com';">Want a copy of this site? <a href="http://www.yetishare.com">Click here</a> for more information.&nbsp;&nbsp;</span><?php echo inPluginDemoMode()?'<a href="'.WEB_ROOT.'/?_p=0" style="text-decoration: none;">[disable all plugins]</a>&nbsp;&nbsp;':'<a href="'.WEB_ROOT.'/?_p=1" style="text-decoration: none;">[enable all plugins]</a>&nbsp;&nbsp;'; ?><a href="#" onClick="$('#demoBanner').fadeOut(); return false;" style="text-decoration: none;">[close this]</a>
            </div>
        <?php endif; ?>
        <div class="globalPageWrapper">
            <!-- header section -->
            <div class="headerBar">
                <!-- extra links -->
                <div class="mainNavigation">
                    <?php
                    // main navigation links
                    $links = array();
                    if ($Auth->loggedIn() == false)
                    {
                        if(SITE_CONFIG_ENABLE_USER_REGISTRATION != 'no')
                        {
                            $links['register'] = '<a href="'.getCoreSitePath().'/register.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('register', 'register').'</a>';
                        }
						if(enableUpgradePage() == 'yes')
						{
							$links['upgrade'] = '<a href="'.getCoreSitePath().'/upgrade.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('premium', 'premium').'</a>';
						}
                        $links['faq'] = '<a href="'.getCoreSitePath().'/faq.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('faq', 'faq').'</a>';
                        $links['login'] = '<span id="loginLinkWrapper" class="loginLink">&nbsp;<a id="loginLink" href="'.WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('login', 'login').'</a>&nbsp;</span>';
                    }
                    else
                    {
                        $links['home'] = '<a href="'.getCoreSitePath().'/account_home.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('your_files', 'your files').'</a>';
                        $label = t('uprade_account', 'upgrade account');
                        if($Auth->hasAccessLevel(2))
                        {
                            $label = t('extend_account', 'extend account');
                        }
						if(enableUpgradePage() == 'yes')
						{
							$links['upgrade'] = '<a href="'.getCoreSitePath().'/upgrade.'.SITE_CONFIG_PAGE_EXTENSION.'">'.$label.'</a>';
						}
                        $links['settings'] = '<a href="'.getCoreSitePath().'/account_edit.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('settings', 'settings').'</a>';
                        $links['logout'] = '<a href="'.getCoreSitePath().'/logout.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('logout', 'logout').' ('.$Auth->username.')</a>';
                    }

                    // include any plugin includes
                    $links = pluginHelper::includeAppends('_header_nav.php', $links);

                    // output nav
                    echo implode('&nbsp;&nbsp;|&nbsp;&nbsp;', $links);
                    ?>
                </div>

                <!-- Code for Login Link -->
                <!-- xHTML Code -->
                <div class="loginWrapper">
                    <div id="loginPanel" class="loginPanel">
                        <form action="<?php echo getCoreSitePath(); ?>/login.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" method="post" AUTOCOMPLETE="off">
                            <span class="fieldWrapper">
                                <label for="loginUsername">
                                    <span class="field-name"><?php echo t("username", "username"); ?></span>
                                    <input type="text" tabindex="50" value="" id="loginUsername" name="loginUsername" style="padding:3px;"/>
                                </label>
                            </span>
                            <div class="clear"><!-- --></div>

                            <span class="fieldWrapper">
                                <label for="loginPassword">
                                    <span class="field-name"><?php echo t("password", "password"); ?></span>
                                    <input type="password" tabindex="51" value="" id="loginPassword" name="loginPassword" style="padding:3px;"/>
                                </label>
                            </span>
                            <div class="clear"><!-- --></div>

                            <?php
                            // if we're viewing the file countdown page
                            if (isset($file))
                            {
                                echo '<input name="loginShortUrl" type="hidden" value="' . urlencode($file->shortUrl) . '"/>';
                            }
                            ?>

                            <div class="submitButton">
                                <input name="submit" value="<?php echo t("login", "login"); ?>" type="submit" class="submitInput"/>
                            </div>
                            <div class="forgotPassword">
                                <a href="<?php echo getCoreSitePath(); ?>/forgot_password.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>"><?php echo t("forgot_password", "forgot password"); ?>?</a>
                            </div>
                            <div class="clear"><!-- --></div>

                            <input name="submitme" type="hidden" value="1" />
                        </form>
                        
                        <?php
                        // include any plugin includes
                        pluginHelper::includeAppends('_header_login_box.php');
                        ?>
                        
                    </div>
                </div>

                <!-- main logo -->
                <div class="mainLogo">
                    <a href="<?php echo getCoreSitePath(); ?>"><img src="<?php echo SITE_IMAGE_PATH; ?>/main_logo.jpg" height="48" alt="<?php echo SITE_CONFIG_SITE_NAME; ?>"/></a>
                </div>
            </div>

            <!-- body section -->
            <div class="bodyBarWrapper">
                <div class="bodyBar">