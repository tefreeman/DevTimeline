<?php

class adminFunctions
{

    public static $errors = array();
    public static $success = array();

    public static function setError($error)
    {
        self::$errors[] = $error;
    }

    public static function isErrors()
    {
        return self::getErrors() > 0;
    }

    public static function getErrors()
    {
        if (COUNT(self::$errors) == 0)
        {
            return false;
        }

        return self::$errors;
    }

    public static function compileErrorHtml()
    {
        $html = '';
        if (self::getErrors())
        {
            $html .= '<span class="notification undone">';
            foreach (self::getErrors() AS $error)
            {
                $html .= $error . '<br/>';
            }
            $html .= '</span>';
        }

        return $html;
    }

    public static function setSuccess($success)
    {
        self::$success[] = $success;
    }

    public static function isSuccess()
    {
        return self::getSuccess() > 0;
    }

    public static function getSuccess()
    {
        if (COUNT(self::$success) == 0)
        {
            return false;
        }

        return self::$success;
    }

    public static function compileSuccessHtml()
    {
        $html = '';
        if (self::getSuccess())
        {
            $html .= '<span class="notification done">';
            foreach (self::getSuccess() AS $success)
            {
                $html .= $success . '<br/>';
            }
            $html .= '</span>';
        }

        return $html;
    }

    public static function compileNotifications()
    {
        $html = self::compileErrorHtml();
        $html .= self::compileSuccessHtml();

        return $html;
    }

    public static function makeSafe($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
    }

    public static function redirect($path)
    {
        header('location: ' . $path);
        exit;
    }

    public static function getDirectoryList($directory, $extFilter = null, $recurr = false)
    {
        $rs = array();
		if ($handle = opendir($directory))
		{
			// iterate over the directory entries
			while (false !== ($entry = readdir($handle)))
			{
                if($recurr == true)
                {
                    if ($entry != '.' && $entry != '..')
                    {
                        $rs[] = $directory . '/' . $entry;
                        if (is_dir($directory . '/' . $entry))
                        {

                            $rs2 = self::getDirectoryList($directory . '/' . $entry, $extFilter, $recurr);
                            if (COUNT($rs2))
                            {
                                $rs = array_merge($rs, $rs2);
                            }
                        }
                    }
                }
                else
                {
                    $rs[] = str_replace($directory, '', $entry);
                }
			}

			// close the directory
			closedir($handle);
		}

        return $rs;
    }
	
    public static function formatSize($bytes, $decimals = 0)
    {
        $size = $bytes / 1024;
        if ($size < 1024)
        {
            $size = number_format($size, $decimals);
            $size .= ' KB';
        }
        else
        {
            if ($size / 1024 < 1024)
            {
                $size = number_format($size / 1024, $decimals);
                $size .= ' MB';
            }
            else if ($size / 1024 / 1024 < 1024)
            {
                $size = number_format($size / 1024 / 1024, $decimals);
                $size .= ' GB';
            }
            else if ($size / 1024 / 1024 / 1024 < 1024)
            {
                $size = number_format($size / 1024 / 1024 / 1024, $decimals);
                $size .= ' TB';
            }
        }
        // remove unneccessary zeros
        $size = str_replace(".00 ", " ", $size);

        return $size;
    }

    public static function t($key, $defaultContent = '', $replacements = array())
    {
        return translate::getTranslation($key, $defaultContent, 1, $replacements);
    }

    public static function registerPlugins()
    {
        // get database connection
        $db = Database::getDatabase();

        // scan plugin directory and make sure they are all listed within the database
        $pluginDirectory = PLUGIN_DIRECTORY_ROOT;
        $directories     = adminFunctions::getDirectoryList($pluginDirectory);
        if (COUNT($directories))
        {
            foreach ($directories AS $directory)
            {
                // check the database to see if it already exists
                $found = $db->getValue("SELECT id FROM plugin WHERE folder_name = " . $db->quote($directory));
                if ($found)
                {
                    continue;
                }

                // not found in the db, we probably need to add it
                $pluginPath      = $pluginDirectory . $directory . '/';
                $pluginClassFile = $pluginPath . 'plugin' . UCFirst(strtolower($directory)) . '.class.php';
                $pluginClassName = 'Plugin' . UCFirst(strtolower($directory));

                // make sure we have the main class file
                if (!file_exists($pluginClassFile))
                {
                    continue;
                }

                try
                {
                    // try to create an instance of the class
                    include_once($pluginClassFile);
                    if (!class_exists($pluginClassName))
                    {
                        continue;
                    }

                    $instance = new $pluginClassName();
                    if (!$instance)
                    {
                        continue;
                    }

                    // get plugin details
                    $pluginDetails = $instance->getPluginDetails();

                    // insert new plugin into db
                    if ($pluginDetails)
                    {
                        $dbInsert = new DBObject("plugin", array("plugin_name", "folder_name", "plugin_description", "is_installed"));
                        $dbInsert->plugin_name = $pluginDetails['plugin_name'];
                        $dbInsert->folder_name = $pluginDetails['folder_name'];
                        $dbInsert->plugin_description = $pluginDetails['plugin_description'];
                        $dbInsert->is_installed = 0;
                        $dbInsert->insert();
                    }
                }
                catch (Exception $e)
                {
                    continue;
                }
            }
        }

        return true;
    }

    public static function recursiveDelete($str)
    {
        // failsafe, make sure it's only in the plugin directory
        if(substr($str, 0, strlen(PLUGIN_DIRECTORY_ROOT)) != PLUGIN_DIRECTORY_ROOT)
        {
            return false;
        }
        
        if (is_file($str))
        {
            return @unlink($str);
        }
        elseif (is_dir($str))
        {
            // look for .htaccess files
            $scan = glob(rtrim($str, '/') . '/.*');
            foreach ($scan as $index => $path)
            {
                @unlink($path);
            }
            
            // handle directories
            $scan = glob(rtrim($str, '/') . '/*');
            foreach ($scan as $index => $path)
            {
                self::recursiveDelete($path);
            }
            
            return @rmdir($str);
        }
    }
    
    public static function limitStringLength($string, $length = 100)
    {
        if(strlen($string) < $length)
        {
            return $string;
        }
        
        return substr($string, 0, $length).'...';
    }

}