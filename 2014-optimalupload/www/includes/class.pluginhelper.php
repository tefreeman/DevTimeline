<?php

class pluginHelper
{
    private static $cssFiles = array();
    private static $jsFiles = array();
    
    static function pluginEnabled($pluginKey = '')
    {
        if((_CONFIG_DEMO_MODE == true) && (inPluginDemoMode() == false))
        {
            return false;
        }
        
        if (strlen($pluginKey) == 0)
        {
            return false;
        }

        if (!isset($_SESSION['pluginConfigs']))
        {
            $_SESSION['pluginConfigs'] = self::loadPluginConfigurationFiles();
        }

        if (!isset($_SESSION['pluginConfigs'][$pluginKey]))
        {
            return false;
        }

        if (((int) $_SESSION['pluginConfigs'][$pluginKey]['data']['plugin_enabled'] == 0) || ((int) $_SESSION['pluginConfigs'][$pluginKey]['data']['is_installed'] == 0))
        {
            return false;
        }

        return true;
    }

    static function pluginSpecificConfiguration($pluginKey = '')
    {
        if (self::pluginEnabled($pluginKey) == false)
        {
            return false;
        }

        return $_SESSION['pluginConfigs'][$pluginKey];
    }

    static function getPluginConfiguration()
    {
        return $_SESSION['pluginConfigs'];
    }

    static function loadPluginConfigurationFiles()
    {
        $rs = array();

        // get active plugins from the db
        $db      = Database::getDatabase(true);
        $plugins = $db->getRows('SELECT * FROM plugin WHERE is_installed = 1 ORDER BY load_order ASC');
        if ($plugins)
        {
            foreach ($plugins AS $plugin)
            {
                $settingsPath = PLUGIN_DIRECTORY_ROOT . $plugin['folder_name'] . '/_plugin_config.inc.php';
                if (file_exists($settingsPath))
                {
                    include($settingsPath);
                    $rs[$plugin{'folder_name'}] = array();
                    $rs[$plugin{'folder_name'}]['data']   = $plugin;
                    $rs[$plugin{'folder_name'}]['config'] = $pluginConfig;
                }
            }
        }

        return $rs;
    }
    
    static function getInstance($pluginKey)
    {
        if (self::pluginEnabled($pluginKey) == false)
        {
            return false;
        }
        
        $plugin = $_SESSION['pluginConfigs'][$pluginKey];
        
        // create plugin instance
        $classPath                              = PLUGIN_DIRECTORY_ROOT . $plugin['data']['folder_name'] . '/plugin' . UCFirst($plugin['data']['folder_name']) . '.class.php';
        $pluginClassName                        = 'Plugin' . UCFirst($plugin['data']['folder_name']);
        include_once($classPath);
        
        return new $pluginClassName();
    }

    static function outputPluginAdminNav()
    {
        // add any plugin navigation
        $pluginConfigs = self::getPluginConfiguration();
        if (COUNT($pluginConfigs))
        {
            foreach ($pluginConfigs AS $pluginConfig)
            {
                if ((self::pluginEnabled($pluginConfig['data']['folder_name']) == 1) && (isset($pluginConfig['config']['admin_settings']['top_nav'])))
                {
                    foreach ($pluginConfig['config']['admin_settings']['top_nav'] AS $navItem)
                    {
                        ?>
                        <li<?php if (ADMIN_SELECTED_PAGE == $navItem[0]['link_key']) echo ' class="active"'; ?>><a href="<?php echo $navItem[0]['link_url'] != '#' ? (PLUGIN_WEB_ROOT . '/' . $pluginConfig['config']['folder_name'] . '/' . $navItem[0]['link_url']) : ($navItem[0]['link_url']); ?>"><span><?php echo htmlentities(UCWords(strtolower(adminFunctions::t($navItem[0]['link_text'], $navItem[0]['link_text'])))); ?></span></a>
                            <?php
                            if (COUNT($navItem > 1))
                            {
                                echo '<ul>';
                                unset($navItem[0]);
                                foreach ($navItem AS $navSubItem)
                                {
                                    ?>
                                <li><a href="<?php echo PLUGIN_WEB_ROOT; ?>/<?php echo $pluginConfig['config']['folder_name']; ?>/<?php echo $navSubItem['link_url']; ?>"><span><?php echo htmlentities(UCWords(strtolower(adminFunctions::t($navSubItem['link_text'], $navSubItem['link_text'])))); ?></span></a></li>
                                <?php
                            }
                            echo '</ul>';
                        }
                        ?>
                        </li>
                        <?php
                    }
                }
            }
        }
    }
    
    static function addCssFile($filePath)
    {
        self::$cssFiles[] = $filePath;
    }

    static function outputCss()
    {
        if (!isset($_SESSION['pluginConfigs']))
        {
            $_SESSION['pluginConfigs'] = self::loadPluginConfigurationFiles();
        }

        $cssFiles = array();
        foreach ($_SESSION['pluginConfigs'] AS $pluginConfig)
        {
            if(self::pluginEnabled($pluginConfig['data']['folder_name']))
            {
                $cssFilePath = PLUGIN_DIRECTORY_ROOT . $pluginConfig['data']['folder_name'] . '/assets/css/styles.css';
                if (file_exists($cssFilePath))
                {
                    self::addCssFile(PLUGIN_WEB_ROOT . '/' . $pluginConfig['data']['folder_name'] . '/assets/css/styles.css');
                }
            }
        }

        if (COUNT(self::$cssFiles))
        {
            // merge and minify css
            //$cachedFilePath = self::mergeCssFiles(self::$cssFiles);
            //if($cachedFilePath !== false)
            //{
            //    self::$cssFiles = array($cachedFilePath);
            //}
            
            // output css
            foreach (self::$cssFiles AS $cssFile)
            {
                echo "<link rel=\"stylesheet\" href=\"" . $cssFile . "\" type=\"text/css\" charset=\"utf-8\" />\n";
            }
        }
    }
    
    static function getMergedBaseFilename($fileListing)
    {
        return MD5(implode('|', $fileListing));
    }
    
    static function mergeCssFiles($fileListing = array())
    {
        // calculate filename
        $newFileName = self::getMergedBaseFilename($fileListing);
        
        // get contents
        $fileContentStr = '';
        if(COUNT($fileListing))
        {
            foreach($fileListing AS $filePath)
            {
                // get contents
                $fileContent = file_get_contents($filePath);
                if(strlen($fileContent))
                {
                    $fileContentStr .= "/* ".$filePath." */\n";
                    $fileContentStr .= $fileContent;
                    $fileContentStr .= "\n";
                }
            }
        }
        
        // save to file
        $fullCacheFilePath = CACHE_DIRECTORY_ROOT.'/'.$newFileName.'.css';
        $rs = file_put_contents($fullCacheFilePath, $fileContentStr);
        if($rs == false)
        {
            return false;
        }
        
        return CACHE_WEB_ROOT.'/'.$newFileName.'.css';
    }
    
    static function outputAdminCss()
    {
        if (!isset($_SESSION['pluginConfigs']))
        {
            $_SESSION['pluginConfigs'] = self::loadPluginConfigurationFiles();
        }

        $cssFiles = array();
        foreach ($_SESSION['pluginConfigs'] AS $pluginConfig)
        {
            if(self::pluginEnabled($pluginConfig['data']['folder_name']))
            {
                $cssFilePath = PLUGIN_DIRECTORY_ROOT . $pluginConfig['data']['folder_name'] . '/admin/styles.css';
                if (file_exists($cssFilePath))
                {
                    $cssFiles[] = PLUGIN_WEB_ROOT . '/' . $pluginConfig['data']['folder_name'] . '/admin/styles.css';
                }
            }
        }

        if (COUNT($cssFiles))
        {
            foreach ($cssFiles AS $cssFile)
            {
                echo "<link rel=\"stylesheet\" href=\"" . $cssFile . "\" type=\"text/css\" charset=\"utf-8\" />\n";
            }
        }
    }
    
    static function addJsFile($filePath)
    {
        self::$jsFiles[] = $filePath;
    }

    static function outputJs()
    {
        if (!isset($_SESSION['pluginConfigs']))
        {
            $_SESSION['pluginConfigs'] = self::loadPluginConfigurationFiles();
        }

        $jsFiles = array();
        foreach ($_SESSION['pluginConfigs'] AS $pluginConfig)
        {
            if(self::pluginEnabled($pluginConfig['data']['folder_name']))
            {
                $jsFilePath = PLUGIN_DIRECTORY_ROOT . $pluginConfig['data']['folder_name'] . '/assets/js/plugin.js';
                if (file_exists($jsFilePath))
                {
                    self::addJsFile(PLUGIN_WEB_ROOT . '/' . $pluginConfig['data']['folder_name'] . '/assets/js/plugin.js');
                }
            }
        }

        if (COUNT(self::$jsFiles))
        {
            if(SITE_CONFIG_PERFORMANCE_JS_FILE_MINIFY == 'yes')
            {
                // merge and minify js
                $cachedFilePath = self::mergeJsFiles(self::$jsFiles);
                if($cachedFilePath !== false)
                {
                    self::$jsFiles = array($cachedFilePath);
                }
            }
            
            foreach (self::$jsFiles AS $jsFile)
            {
                echo "<script type=\"text/javascript\" src=\"" . $jsFile . "\"></script>\n";
            }
        }
    }
    
    static function mergeJsFiles($fileListing = array())
    {
        // calculate filename
        $newFileName = self::getMergedBaseFilename($fileListing);
        $fullCacheFilePath = CACHE_DIRECTORY_ROOT.'/'.$newFileName.'.js';
        $fullCacheWebPath = CACHE_WEB_ROOT.'/'.$newFileName.'.js';
        if(file_exists($fullCacheFilePath))
        {
            return $fullCacheWebPath;
        }
        
        // get contents
        $fileContentStr = '';
        if(COUNT($fileListing))
        {
            foreach($fileListing AS $filePath)
            {
                // get contents
                $fileContent = file_get_contents($filePath);
                if(strlen($fileContent))
                {
                    $fileContentStr .= "// ".$filePath."\n";
                    $fileContentStr .= $fileContent;
                    $fileContentStr .= "\n";
                }
            }
        }
        
        // minify js before saving
        $rs = self::minifyJS($fileContentStr);
        if($rs)
        {
            $fileContentStr = $rs;
        }
        
        // save to file
        $rs = file_put_contents($fullCacheFilePath, $fileContentStr);
        if($rs == false)
        {
            return false;
        }
        
        return $fullCacheWebPath;
    }
    
    static function minifyJS($fileContent)
    {
        include_once(DOC_ROOT.'/includes/jsmin/JSMin.php');
        
        return JSMin::minify($fileContent);
    }
    
    static function outputAdminJs()
    {
        if (!isset($_SESSION['pluginConfigs']))
        {
            $_SESSION['pluginConfigs'] = self::loadPluginConfigurationFiles();
        }

        $jsFiles = array();
        foreach ($_SESSION['pluginConfigs'] AS $pluginConfig)
        {
            if(self::pluginEnabled($pluginConfig['data']['folder_name']))
            {
                $jsFilePath = PLUGIN_DIRECTORY_ROOT . $pluginConfig['data']['folder_name'] . '/admin/plugin.js';
                if (file_exists($jsFilePath))
                {
                    $jsFiles[] = PLUGIN_WEB_ROOT . '/' . $pluginConfig['data']['folder_name'] . '/admin/plugin.js';
                }
            }
        }

        if (COUNT($jsFiles))
        {
            foreach ($jsFiles AS $jsFile)
            {
                echo "<script type=\"text/javascript\" src=\"" . $jsFile . "\"></script>\n";
            }
        }
    }
    
    static function includeAppends($fileName, $params = null)
    {
        $originalParams = $params;
        if (!isset($_SESSION['pluginConfigs']))
        {
            $_SESSION['pluginConfigs'] = self::loadPluginConfigurationFiles();
        }

        $includesFiles = array();
        foreach ($_SESSION['pluginConfigs'] AS $pluginConfig)
        {
            $includesFilePath = PLUGIN_DIRECTORY_ROOT . $pluginConfig['data']['folder_name'] . '/includes/_append_'.$fileName;
            if ((file_exists($includesFilePath)) && (self::pluginEnabled($pluginConfig['data']['folder_name']) == true))
            {
                $includesFiles[] = $includesFilePath;
            }
        }

        if (COUNT($includesFiles))
        {
            foreach ($includesFiles AS $includesFile)
            {
                include($includesFile);
            }
        }
        
        return $params;
    }
    
    static function outputPaymentLinks($days)
    {
        if (!isset($_SESSION['pluginConfigs']))
        {
            $_SESSION['pluginConfigs'] = self::loadPluginConfigurationFiles();
        }

        $includesFiles = array();
        foreach ($_SESSION['pluginConfigs'] AS $pluginConfig)
        {
            $includesFilePath = PLUGIN_DIRECTORY_ROOT . $pluginConfig['data']['folder_name'] . '/includes/_append_upgradeBoxes.inc.php';
            if ((file_exists($includesFilePath)) && (self::pluginEnabled($pluginConfig['data']['folder_name']) == true))
            {
                $includesFiles[] = $includesFilePath;
            }
        }

        if (COUNT($includesFiles))
        {
            foreach ($includesFiles AS $includesFile)
            {
                include($includesFile);
            }
        }
    }

}
