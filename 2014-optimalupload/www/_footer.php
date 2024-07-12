</div>
</div>

<!-- footer section -->
<div class="footerBar">
    
    <?php if(showSiteAdverts()): ?>
    <!-- footer ads -->
    <div class="footerAds">
        <?php echo SITE_CONFIG_ADVERT_SITE_FOOTER; ?>
    </div>
    <?php endif; ?>
    
    <div class="footerLinks">
        <div class="section1">
            <?php
            // footer navigation links
            $links = array();
            if ($Auth->loggedIn() == false)
            {
                $title = t('main_navigation', 'Main Navigation');
                $links['upload'] = '<a href="'.getCoreSitePath().'/index.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('upload_file', 'upload file').'</a>';
                if(SITE_CONFIG_ENABLE_USER_REGISTRATION != 'no')
                {
                    $links['register'] = '<a href="'.getCoreSitePath().'/register.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('register', 'register').'</a>';
                }
				if(enableUpgradePage() == 'yes')
				{
					$links['upgrade'] = '<a href="' . getCoreSitePath() . '/upgrade.' . SITE_CONFIG_PAGE_EXTENSION . '">' . t('premium', 'premium') . '</a>';
				}
                $links['faq'] = '<a href="'.getCoreSitePath().'/faq.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('faq', 'faq').'</a>';
                $links['login'] = '<a href="'.WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('login', 'login').'</a>';
            }
            else
            {
                $title = t('your_account', 'Your Account');
                $links['upload'] = '<a href="'.getCoreSitePath().'/index.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('upload_file', 'upload file').'</a>';
                $links['home'] = '<a href="'.getCoreSitePath().'/account_home.'.SITE_CONFIG_PAGE_EXTENSION.'">'.t('your_files', 'your files').'</a>';
                $label = t('uprade_account', 'upgrade account');
                if($Auth->hasAccessLevel(2))
                {
                    $label = t('extend_account', 'extend account');
                }
				if(enableUpgradePage() == 'yes')
				{
					$links['upgrade'] = '<a href="' . getCoreSitePath() . '/upgrade.' . SITE_CONFIG_PAGE_EXTENSION . '">' . $label . '</a>';
				}
                $links['settings'] = '<a href="' . getCoreSitePath() . '/account_edit.' . SITE_CONFIG_PAGE_EXTENSION . '">' . t('settings', 'settings') . '</a>';
                $links['faq'] = '<a href="' . getCoreSitePath() . '/faq.' . SITE_CONFIG_PAGE_EXTENSION . '">' . t('faq', 'faq') . '</a>';
                $links['logout']   = '<a href="' . getCoreSitePath() . '/logout.' . SITE_CONFIG_PAGE_EXTENSION . '">' . t('logout', 'logout') . ' (' . $Auth->username . ')</a>';
            }

            // include any plugin includes
            $links = pluginHelper::includeAppends('_footer_nav.php', $links);
            ?>

            <strong><?php echo $title; ?></strong>
            <ul>
                <?php
                // output nav
                echo '<li>' . implode('</li><li>', $links) . '</li>';
                ?>
            </ul>
            
        </div>
        <div class="section2">
            <strong><?php echo t('legal_bits', 'Legal Bits'); ?></strong>
            <ul>
                <li><a href="<?php echo getCoreSitePath(); ?>/terms.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>"><?php echo t('term_and_conditions', 'terms and conditions'); ?></a></li>
                <li><a href="<?php echo getCoreSitePath(); ?>/report_file.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?><?php echo defined('REPORT_URL')?('?file_url='.urlencode(REPORT_URL)):''; ?>"><?php echo t('report_file', 'report file'); ?></a></li>
                <li><a href="<?php echo getCoreSitePath(); ?>/contact.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>"><?php echo t('contact', 'contact'); ?></a></li>
            </ul>
        </div>
        <div class="clear"><!-- --></div>

    </div>
    <div class="clear"><!-- --></div>

    <div class="footerCopyrightText">
        <?php
        if ($Auth->loggedIn() == true)
        {
            if($Auth->hasAccessLevel(20))
            {
                echo '<strong>[ <a href="' . ADMIN_WEB_ROOT . '/" target="_blank">' . t('admin_area', 'admin area') . '</a> ]</strong><br/><br/>';
            }
            elseif($Auth->hasAccessLevel(10))
            {
                echo '<strong>[ <a href="' . ADMIN_WEB_ROOT . '/" target="_blank">' . t('moderator_area', 'moderator area') . '</a> ]</strong><br/><br/>';
            }
        }
        ?>
        <?php echo t("copyright", "copyright"); ?> &copy; <?php echo date("Y"); ?> - <?php echo SITE_CONFIG_SITE_NAME; ?>
        - <a href="http://www.yetishare.com" target="_blank">File Upload Script</a> <?php echo t("created_by", "created by "); ?> <a href="http://www.mfscripts.com" target="_blank">MFScripts.com</a>
    </div>
    <div class="clear"><!-- --></div>

    <?php
    if (SITE_CONFIG_SHOW_MULTI_LANGUAGE_SELECTOR == 'show')
    {
        $activeLanguages = $db->getRows("SELECT languageName, flag FROM language WHERE isActive = 1 ORDER BY isLocked DESC");
        if (COUNT($activeLanguages))
        {
            ?>
            <div class="footerMultiLanguageSwitcherWrapper">
                <div class="footerMultiLanguageSwitcher ui-corner-all">
                    <?php
                    foreach ($activeLanguages AS $activeLanguage)
                    {
                        echo '<a href="' . getCoreSitePath() . '/index.' . SITE_CONFIG_PAGE_EXTENSION . '?_t=' . urlencode($activeLanguage['languageName']) . '">';
                        echo '<img src="' . ADMIN_WEB_ROOT . '/assets/images/icons/flags/' . $activeLanguage['flag'] . '.png" width="16" height="11" alt="' . $activeLanguage['languageName'] . '" title="' . htmlentities(t('switch_site_language_to', 'Switch site language to') . ' ' . t($activeLanguage['languageName'], $activeLanguage['languageName'])) . '" style="border: 5px solid #';
                        if ($_SESSION['_t'] == $activeLanguage['languageName'])
                        {
                            echo 'ccc';
                        }
                        else
                        {
                            echo 'fff';
                        }
                        echo ';"/>';
                        echo '</a>';
                    }
                    ?>
                </div>
            </div>
            <div class="clear"><!-- --></div>
            <?php
        }
    }
    ?>

</div>
</div>
</body>
</html>

<?php

/*
// memory stats, load time etc
echo 'Memory: '.formatSize(memory_get_usage()).'<br/><br/>';

// query performance
$db = Database::getDatabase();
echo '<table>';
$total = 0;
foreach($db->queries AS $queryArr)
{
    echo '<tr>';
    echo '<td>'.$queryArr['total'].'</td><td>'.$queryArr['sql'].'</td>';
    echo '</tr>';
    $total = $total + $queryArr['total'];
}
echo '<tr>';
echo '<td>'.$total.'</td><td></td>';
echo '</tr>';
echo '</table>';
*/

?>