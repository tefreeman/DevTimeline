<?php

/**
 * main file class
 */
class file
{

    public $errorMsg = null;
    const DOWNLOAD_TOKEN_VAR = 'download_token';

    function __construct()
    {
        $this->errorMsg = null;
    }

    public function download($forceDownload = true, $doPluginIncludes = true, $downloadToken = null)
    {
        // remove session
        if (isset($_SESSION['showDownload']))
        {
            $clearSession = true;

            // fixes android snag which requests files twice
            if (deviceIsAndroid())
            {
                if (!isset($_SESSION['showDownloadFirstRun']))
                {
                    $_SESSION['showDownloadFirstRun'] = true;
                    $clearSession                     = false;
                }
                else
                {
                    $_SESSION['showDownloadFirstRun'] = null;
                    unset($_SESSION['showDownloadFirstRun']);
                }
            }

            if ($clearSession == true)
            {
                // reset session variable for next time
                $_SESSION['showDownload'] = null;
                unset($_SESSION['showDownload']);
                session_write_close();
            }
        }

        // setup mode
        $mode = 'SESSION';
        if($downloadToken != null)
        {
            $mode = 'TOKEN';
        }
        
        // for session downloads
        $userLevelId = 0;
        $fileOwnerUserId = 0;
        if($mode == 'SESSION')
        {
            // get user
            $Auth = Auth::getAuth();
            
            // setup user level
            $userLevelId = $Auth->level_id;
            
            // file owner id
            $fileOwnerUserId = $Auth->id;
        }
        
        // for token downloads
        else
        {
            // get database
            $db = Database::getDatabase(true);
            
            // check token
            $tokenData = $db->getRow('SELECT id, user_id, ip_address, file_id FROM download_token WHERE file_id = '.$db->escape($this->id).' AND ip_address='.$db->quote(getUsersIPAddress()).' AND token = '.$db->quote($downloadToken).' LIMIT 1');
            if($tokenData == false)
            {
                return false;
            }
            
            // get user level
            if((int)$tokenData['user_id'] > 0)
            {
                $fileOwnerUserId = (int)$tokenData['user_id'];
                $userLevelId = (int)$db->getValue('SELECT level_id FROM users WHERE id='.(int)$fileOwnerUserId.' LIMIT 1');
            }
        }
        
        // clear any expired download trackers
        downloadTracker::clearTimedOutDownloads();
        downloadTracker::purgeDownloadData();

        // check for concurrent downloads for paid users
        $maxDownloadThreads = getMaxDownloadThreads($userLevelId);
        if ((int) $maxDownloadThreads > 0)
        {
            // get database
            $db = Database::getDatabase(true);

            $sQL  = "SELECT COUNT(download_tracker.id) AS total_threads ";
            $sQL .= "FROM download_tracker ";
            $sQL .= "WHERE download_tracker.status='downloading' AND download_tracker.ip_address = " . $db->quote(getUsersIPAddress()) . " ";
            $sQL .= "GROUP BY download_tracker.ip_address ";
            $totalThreads = (int) $db->getValue($sQL);
            if ($totalThreads >= (int) $maxDownloadThreads)
            {
                // fail
                header("HTTP/1.0 429 Too Many Requests");
                exit;
            }
        }

        // php script timeout for long downloads (2 days!)
        set_time_limit(60 * 60 * 24 * 2);

        // load the server the file is on
        $storageType         = 'local';
        $storageLocation     = _CONFIG_FILE_STORAGE_PATH;
        $uploadServerDetails = $this->loadServer();
        if ($uploadServerDetails != false)
        {
            $storageLocation = $uploadServerDetails['storagePath'];
            $storageType     = $uploadServerDetails['serverType'];

            // if no storage path set & local, use system default
            if ((strlen($storageLocation) == 0) && ($storageType == 'local'))
            {
                $storageLocation = _CONFIG_FILE_STORAGE_PATH;
            }
        }

        // get file path
        $fullPath = $this->getFullFilePath($storageLocation);

        // open file - via ftp
        if ($storageType == 'ftp')
        {
            // connect via ftp
            $conn_id = ftp_connect($uploadServerDetails['ipAddress'], $uploadServerDetails['ftpPort'], 30);
            if ($conn_id === false)
            {
                $this->errorMsg = 'Could not connect to ' . $uploadServerDetails['ipAddress'] . ' to upload file.';
                return false;
            }

            // authenticate
            $login_result = ftp_login($conn_id, $uploadServerDetails['ftpUsername'], $uploadServerDetails['ftpPassword']);
            if ($login_result === false)
            {
                $this->errorMsg = 'Could not login to ' . $uploadServerDetails['ipAddress'] . ' with supplied credentials.';
                return false;
            }

            // turn passive mode on
            //ftp_pasv($conn_id, true);
            // prepare the stream of data, unix
            $pipes = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
            if ($pipes === false)
            {
                // try different protocol
                $pipes = stream_socket_pair(STREAM_PF_INET, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
                if ($pipes === false)
                {
                    $this->errorMsg = 'Could not create stream to download file on ' . $uploadServerDetails['ipAddress'];
                    return false;
                }
            }

            stream_set_write_buffer($pipes[0], 10000);
            stream_set_timeout($pipes[1], 10);
            stream_set_blocking($pipes[1], 0);

            $fail = false;
        }
        elseif ($storageType == 'direct')
        {
            $fullPath = _CONFIG_SCRIPT_ROOT . '/' . $fullPath;
        }

        // get download speed
        $speed = (int) getMaxDownloadSpeed($userLevelId);
        if ($forceDownload == true)
        {
            // include any plugin includes
            $params = pluginHelper::includeAppends('class_file_download.php', array('speed' => $speed));
            $speed = $params['speed'];
        }
        else
        {
            $speed = 0;
        }

        // do we need to throttle the speed?
        if ($speed > 0)
        {
            // create new throttle config
            $config = new ThrottleConfig();

            // set standard transfer rate (in bytes/second)
            $config->burstLimit = $speed;
            $config->rateLimit  = $speed;

            // enable module (this is a default value)
            $config->enabled = true;

            // start throttling
            $x = new Throttle($config);
        }

        // handle where to start in the download, support for resumed downloads
        $seekStart = 0;
        $seekEnd   = $this->fileSize;
        if (isset($_SERVER['HTTP_RANGE']) || isset($HTTP_SERVER_VARS['HTTP_RANGE']))
        {
            if (isset($HTTP_SERVER_VARS['HTTP_RANGE']))
            {
                $seekRange = substr($HTTP_SERVER_VARS['HTTP_RANGE'], strlen('bytes='));
            }
            else
            {
                $seekRange = substr($_SERVER['HTTP_RANGE'], strlen('bytes='));
            }

            $range = explode('-', $seekRange);
            if ((int) $range[0] > 0)
            {
                $seekStart = intval($range[0]);
            }

            if ((int) $range[1] > 0)
            {
                $seekEnd = intval($range[1]);
            }
        }

        if ($forceDownload == true)
        {
            // output some headers
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-type: " . $this->fileType);
            header("Pragma: public");
            header("Content-Disposition: attachment; filename=\"" . str_replace("\"", "", $this->originalFilename) . "\"");
            header("Content-Description: File Transfer");

            if ($seekStart > 0)
            {
                header("HTTP/1.0 206 Partial Content");
                header("Status: 206 Partial Content");
                header('Accept-Ranges: bytes');
                header("Content-Length: " . ($seekEnd - $seekStart + 1));
                header("Content-Range: bytes ".($seekStart-$seekEnd)."/".$this->fileSize);
            }
            else
            {
                header('Accept-Ranges: bytes');
                header("Content-Length: " . $this->fileSize);
                header("Content-Range: bytes 0/".$this->fileSize);
            }
        }

        if (SITE_CONFIG_DOWNLOADS_TRACK_CURRENT_DOWNLOADS == 'yes')
        {
            // track downloads
            $downloadTracker = new downloadTracker($this);
            $downloadTracker->create($seekStart, $seekEnd);
        }

        // for returning the file contents
        if ($forceDownload == false)
        {
            $fileContent = '';
        }

        // include any plugins for other storage methods
        $params = pluginHelper::includeAppends('class_file_download_get_from_storage.inc.php', array('actioned'         => false, 'seekStart'        => $seekStart, 'seekEnd'          => $seekEnd, 'storageType'      => $storageType, 'fileContent'      => $fileContent, 'downloadTracker'  => $downloadTracker, 'forceDownload'    => $forceDownload, 'Auth'             => $Auth, 'file'             => $this, 'doPluginIncludes' => $doPluginIncludes));
        if ($params['actioned'] == true)
        {
            $fileContent = $params['fileContent'];
        }
        else
        {
            // output file - via ftp
            $timeTracker = time();
            $length      = 0;
            if ($storageType == 'ftp')
            {
                /*
                 * FOR SOME WINDOWS FTP SERVERS
                $local_file = "php://output";
				ob_start();
				ftp_get($conn_id, $local_file, $fullPath, FTP_BINARY);
				$fileContent = ob_get_contents();
				ob_end_clean();
				
				if ($forceDownload == true)
				{
					echo $fileContent;
				}
                 */
                
                $ret = ftp_nb_fget($conn_id, $pipes[0], $fullPath, FTP_BINARY, $seekStart);
                while ($ret == FTP_MOREDATA)
                {
                    $contents = stream_get_contents($pipes[1], $seekEnd);
                    if ($contents !== false)
                    {
                        if ($forceDownload == true)
                        {
                            echo $contents;
                        }
                        else
                        {
                            $fileContent .= $contents;
                        }
                        $length = $length + strlen($contents);
                        flush();
                    }

                    $ret = ftp_nb_continue($conn_id);

                    // update download status every DOWNLOAD_TRACKER_UPDATE_FREQUENCY seconds
                    if (($timeTracker + DOWNLOAD_TRACKER_UPDATE_FREQUENCY) < time())
                    {
                        $timeTracker = time();
                        if (SITE_CONFIG_DOWNLOADS_TRACK_CURRENT_DOWNLOADS == 'yes')
                        {
                            $downloadTracker->update();
                        }
                    }
                }

                fclose($pipes[0]);
                fclose($pipes[1]);
            }
            // output file - local
            else
            {
                // open file - locally
                $handle = @fopen($fullPath, "r");
                if (!$handle)
                {
                    $this->errorMsg = 'Could not open file for reading.';
                    return false;
                }

                // move to starting position
                fseek($handle, $seekStart);

                while (($buffer = fgets($handle, 4096)) !== false)
                {
                    if ($forceDownload == true)
                    {
                        echo $buffer;
                    }
                    else
                    {
                        $fileContent .= $buffer;
                    }
                    $length = $length + strlen($buffer);

                    // update download status every DOWNLOAD_TRACKER_UPDATE_FREQUENCY seconds
                    if (($timeTracker + DOWNLOAD_TRACKER_UPDATE_FREQUENCY) < time())
                    {
                        $timeTracker = time();
                        if (SITE_CONFIG_DOWNLOADS_TRACK_CURRENT_DOWNLOADS == 'yes')
                        {
                            $downloadTracker->update();
                        }
                    }
                }
                fclose($handle);
            }
        }

        if ($forceDownload == true)
        {
            // stats
            if (($fileOwnerUserId == $this->userId) || ($userLevelId == 20))
            {
                // dont update stats, this was triggered by an admin user or file owner
            }
            else
            {
                // update stats
                $rs = Stats::track($this, $this->id);
                if ($rs)
                {
                    $this->updateLastAccessed();
                }
            }
        }

        // finish off any plugins
        pluginHelper::includeAppends('class_file_download_complete.php', array('forceDownload'    => $forceDownload, 'fileOwnerUserId'=>$fileOwnerUserId, 'userLevelId'=>$userLevelId, 'Auth'             => $Auth, 'file'             => $this, 'doPluginIncludes' => $doPluginIncludes));

        if (SITE_CONFIG_DOWNLOADS_TRACK_CURRENT_DOWNLOADS == 'yes')
        {
            // close download
            $downloadTracker->finish();
        }
        
        // clear old tokens
        file::purgeDownloadTokens();

        // return file content
        if ($forceDownload == false)
        {
            return $fileContent;
        }

        exit();
    }

    public function loadServer()
    {
        // load the server the file is on
        if ((int) $this->serverId)
        {
            // load from the db
            $db                  = Database::getDatabase(true);
            $uploadServerDetails = $db->getRow('SELECT * FROM file_server WHERE id = ' . $db->quote((int) $this->serverId));
            $db->close();
            if (!$uploadServerDetails)
            {
                return false;
            }

            return $uploadServerDetails;
        }

        return false;
    }

    public function getFullFilePath($prePath = '')
    {
        if (substr($prePath, strlen($prePath) - 1, 1) == '/')
        {
            $prePath = substr($prePath, 0, strlen($prePath) - 1);
        }

        return $prePath . '/' . $this->localFilePath;
    }

    /**
     * Get full short url path
     *
     * @return string
     */
    public function getFullShortUrl($finalDownloadBasePath = false)
    {
        if (SITE_CONFIG_FILE_URL_SHOW_FILENAME == 'yes')
        {
            return $this->getFullLongUrl($finalDownloadBasePath);
        }

        return $this->getShortUrlPath($finalDownloadBasePath);
    }

    public function getShortUrlPath($finalDownloadBasePath = false)
    {
        $fileServerPath = file::getFileDomainAndPath($this->id, $this->serverId, $finalDownloadBasePath);

        return _CONFIG_SITE_PROTOCOL . '://' . $fileServerPath . '/' . $this->shortUrl;
    }

    public function getStatisticsUrl($returnAccount = false)
    {
        return $this->getShortUrlPath() . '~s' . ($returnAccount ? ('&returnAccount=1') : '');
    }

    public function getDeleteUrl($returnAccount = false, $finalDownloadBasePath = false)
    {
        return $this->getShortUrlPath($finalDownloadBasePath) . '~d?' . $this->deleteHash . ($returnAccount ? ('&returnAccount=1') : '');
    }

    public function getInfoUrl($returnAccount = false)
    {
        return $this->getShortUrlPath() . '~i?' . $this->deleteHash . ($returnAccount ? ('&returnAccount=1') : '');
    }

    public function getShortInfoUrl($returnAccount = false)
    {
        return $this->getShortUrlPath() . '~i' . ($returnAccount ? ('&returnAccount=1') : '');
    }

    /**
     * Get full long url including the original filename
     *
     * @return string
     */
    public function getFullLongUrl($finalDownloadBasePath = false)
    {
        return $this->getShortUrlPath($finalDownloadBasePath) . '/' . str_replace(array(" ", "\"", "'", ";"), "_", strip_tags($this->originalFilename));
    }

    /**
     * Method to increment visitors
     */
    public function updateVisitors()
    {
        $db = Database::getDatabase(true);
        $this->visits++;
        $db->query('UPDATE file SET visits = :visits WHERE id = :id', array('visits' => $this->visits, 'id'     => $this->id));
    }

    /**
     * Method to update last accessed
     */
    public function updateLastAccessed()
    {
        $db = Database::getDatabase(true);
        $db->query('UPDATE file SET lastAccessed = NOW() WHERE id = :id', array('id' => $this->id));
    }

    /**
     * Method to set folder
     */
    public function updateFolder($folderId = '')
    {
        $db       = Database::getDatabase(true);
        $folderId = (int) $folderId;
        if ($folderId == 0)
        {
            $folderId = '';
        }
        $db->query('UPDATE file SET folderId = :folderId WHERE id = :id', array('folderId' => $folderId, 'id'       => $this->id));
    }

    /**
     * Remove by user
     */
    public function removeByUser()
    {
        // get database
        $db = Database::getDatabase(true);

        // remove the actual file from storage
        $rs = $this->_removeFile();

        if ($rs == true)
        {
            // update db
            $rs = $db->query('UPDATE file SET statusId = 2, fileHash="" WHERE id = :id', array('id' => $this->id));
            if ($db->affectedRows() == 1)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove by system
     */
    public function removeBySystem()
    {
        // get database
        $db = Database::getDatabase(true);

        // remove the actual file from storage
        $rs = $this->_removeFile();
        if ($rs == true)
        {
            // update db
            $rs = $db->query('UPDATE file SET statusId = 5, fileHash="" WHERE id = :id', array('id' => $this->id));
            //$rs = $db->query('DELETE FROM file WHERE id = :id', array('id' => $this->id));
            if ($db->affectedRows() == 1)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the actual file, not the database entry
     */
    public function _removeFile()
    {
        // get the database
        $db = Database::getDatabase(true);

        // load the server the file is on
        $storageType         = 'local';
        $storageLocation     = _CONFIG_FILE_STORAGE_PATH;
        $uploadServerDetails = $this->loadServer();
        if ($uploadServerDetails != false)
        {
            $storageLocation = $uploadServerDetails['storagePath'];
            $storageType     = $uploadServerDetails['serverType'];
            if ((strlen($uploadServerDetails['storagePath']) == 0) && ($storageType == 'local'))
            {
                $storageLocation = _CONFIG_FILE_STORAGE_PATH;
            }
        }

        // check if the file is shared, don't remove if so
        if ($this->_fileIsShared() == false)
        {
            // file path
            $filePath = $this->getFullFilePath($storageLocation);

            // include any plugins for other storage methods
            $params = pluginHelper::includeAppends('class_file_remove_file.inc.php', array('actioned'        => false, 'filePath'        => $filePath, 'storageType'     => $storageType, 'storageLocation' => $storageLocation, 'file'            => $this));
            if ($params['actioned'] == true)
            {
                if ((isset($params['errorMsg'])) && (strlen($params['errorMsg'])))
                {
                    $this->errorMsg = $params['errorMsg'];
                    return false;
                }

                return true;
            }
            else
            {
                // remote - ftp
                if ($storageType == 'ftp')
                {
                    // connect via ftp
                    $conn_id = ftp_connect($uploadServerDetails['ipAddress'], $uploadServerDetails['ftpPort'], 30);
                    if ($conn_id === false)
                    {
                        $this->errorMsg = 'Could not connect to ' . $uploadServerDetails['ipAddress'] . ' to upload file.';
                        return false;
                    }

                    // authenticate
                    $login_result = ftp_login($conn_id, $uploadServerDetails['ftpUsername'], $uploadServerDetails['ftpPassword']);
                    if ($login_result === false)
                    {
                        $this->errorMsg = 'Could not login to ' . $uploadServerDetails['ipAddress'] . ' with supplied credentials.';
                        return false;
                    }

                    // remove file
                    if (!ftp_delete($conn_id, $filePath))
                    {
                        $this->errorMsg = 'Could not remove file on ' . $uploadServerDetails['ipAddress'];
                        return false;
                    }
                }
                // enable removal of 'direct' stored files
                elseif ($storageType == 'direct')
                {
                    // check if we're on the direct file server
                    if ($uploadServerDetails['fileServerDomainName'] == _CONFIG_SITE_HOST_URL)
                    {
                        if (!file_exists($fullPath))
                        {
                            $fullPath = _CONFIG_SCRIPT_ROOT . '/' . $fullPath;
                        }

                        unlink($filePath);
                        return true;
                    }
                }

                if (file_exists($filePath))
                {
                    // delete file from server
                    unlink($filePath);
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    private function _fileIsShared()
    {
        // get database
        $db = Database::getDatabase(true);

        // get file hash
        $fileShared = false;
        $fileHash   = $db->getValue("SELECT fileHash FROM file WHERE id=" . $this->id . " LIMIT 1");
        if (strlen($fileHash))
        {
            // check for other active files which share the stored file
            $findFile = $db->getRow("SELECT * FROM file WHERE fileHash=" . $db->quote($fileHash) . " AND statusId=1 AND id != " . $this->id . " LIMIT 1");
            if ($findFile)
            {
                $fileShared = true;
            }
        }

        return $fileShared;
    }

    public function getLargeIconPath()
    {
        $fileTypePath = DOC_ROOT . '/themes/' . SITE_CONFIG_SITE_THEME . '/images/file_icons/512px/' . strtolower($this->extension) . '.png';
        if (!file_exists($fileTypePath))
        {
            return false;
        }

        return SITE_IMAGE_PATH . '/file_icons/512px/' . strtolower($this->extension) . '.png';
    }

    public function getFilenameExcExtension()
    {
        $filename = $this->originalFilename;

        return basename($filename, '.' . $this->extension);
    }

    /**
     * Method to set password
     */
    public function updatePassword($password = '')
    {
        $db          = Database::getDatabase(true);
        $md5Password = '';
        if (strlen($password))
        {
            $md5Password = md5($password);
        }

        $db->query('UPDATE file SET accessPassword = :accessPassword WHERE id = :id', array('accessPassword' => $md5Password, 'id'             => $this->id));
    }

    public function getHtmlLinkCode()
    {
        return '&lt;a href=&quot;' . $this->getFullShortUrl() . '&quot; target=&quot;_blank&quot; title=&quot;' . t('download_from', 'Download from') . ' ' . SITE_CONFIG_SITE_NAME . '&quot;&gt;'.t('class_file_download', 'Download').' ' . safeOutputToScreen(safeOutputToScreen($this->originalFilename)) . ' '.t('class_file_from', 'from').' ' . SITE_CONFIG_SITE_NAME . '&lt;/a&gt;';
    }

    public function getForumLinkCode()
    {
        return '[url]' . safeOutputToScreen($this->getFullShortUrl()) . '[/url]';
    }

    /**
     * Create a copy of the file in the database
     */
    public function duplicateFile()
    {
        $db       = Database::getDatabase(true);
        $dbInsert = new DBObject("file", array("originalFilename", "shortUrl", "fileType", "extension", "fileSize", "localFilePath", "userId", "totalDownload", "uploadedIP", "uploadedDate", "statusId", "deleteHash", "serverId", "fileHash"));

        $dbInsert->originalFilename = $this->originalFilename;
        $dbInsert->shortUrl         = $this->shortUrl;
        $dbInsert->fileType         = $this->fileType;
        $dbInsert->extension        = $this->extension;
        $dbInsert->fileSize         = $this->fileSize;
        $dbInsert->localFilePath    = $this->localFilePath;

        // add user id if user is logged in
        $dbInsert->userId = NULL;
        $Auth             = Auth::getAuth();
        if ($Auth->loggedIn())
        {
            $dbInsert->userId = (int) $Auth->id;
        }

        $dbInsert->totalDownload = 0;
        $dbInsert->uploadedIP    = getUsersIPAddress();
        $dbInsert->uploadedDate  = sqlDateTime();
        $dbInsert->statusId      = 1;
        $deleteHash              = md5($this->originalFilename . getUsersIPAddress() . microtime());
        $dbInsert->deleteHash    = $deleteHash;
        $dbInsert->serverId      = $this->serverId;
        $dbInsert->fileHash      = $this->fileHash;

        if (!$dbInsert->insert())
        {
            return false;
        }

        // create short url
        $tracker  = 1;
        $shortUrl = file::createShortUrlPart($tracker . $dbInsert->id);
        $fileTmp  = file::loadByShortUrl($shortUrl);
        while ($fileTmp)
        {
            $shortUrl = file::createShortUrlPart($tracker . $dbInsert->id);
            $fileTmp  = file::loadByShortUrl($shortUrl);
            $tracker++;
        }

        // update short url
        file::updateShortUrl($dbInsert->id, $shortUrl);

        return file::loadByShortUrl($shortUrl);
    }

    /**
     * Remove file and any database data
     */
    public function deleteFileIncData()
    {
        // get database
        $db = Database::getDatabase(true);

        // remove the actual file from storage
        $rs = $this->_removeFile();

        // stats
        $db->query('DELETE FROM stats WHERE file_id = ' . (int) $this->id);

        // file
        $db->query('DELETE FROM file WHERE id = ' . (int) $this->id . ' LIMIT 1');

        return true;
    }
    
    public function generateDirectDownloadToken()
    {
        // get database
        $db = Database::getDatabase(true);
        
        // get auth
        $Auth = Auth::getAuth();
        
        // make sure one doesn't already exist for the file
        $checkToken = true;
        while ($checkToken != false)
        {
            // generate unique hash
            $downloadToken = hash('sha256', $this->id . microtime() . rand(1000, 9999));
            $checkToken    = $db->getValue('SELECT id FROM download_token WHERE file_id=' . $db->escape($this->id) . ' AND token=' . $db->escape($downloadToken) . ' LIMIT 1');
        }
        
        // insert token into database
        $userId = '';
        if($Auth->loggedIn())
        {
            $userId = $Auth->id;
        }
        $dbInsert             = new DBObject("download_token", array("token", "user_id", "ip_address", "file_id", "created", "expiry"));
        $dbInsert->token      = $downloadToken;
        $dbInsert->user_id    = $userId;
        $dbInsert->ip_address = getUsersIPAddress();
        $dbInsert->file_id    = $this->id;
        $dbInsert->created    = date('Y-m-d H:i:s');
        $dbInsert->expiry     = date('Y-m-d H:i:s', time()+(60*60*24));
        if (!$dbInsert->insert())
        {
            return false;
        }
        
        return $downloadToken;
    }

    /**
     * Generate a link for downloading files directly. Allows for download managers
     * and no reliance on sessions.
     */
    public function generateDirectDownloadUrl()
    {
        // get database
        $db = Database::getDatabase(true);

        // get download token
        $downloadToken = $this->generateDirectDownloadToken();
        if (!$downloadToken)
        {
            $errorMsg = 'Failed generating direct download link, please try again later.';
            return getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg);
        }

        // compile full url
        return $this->getFullShortUrl(true).'?'.file::DOWNLOAD_TOKEN_VAR.'='.$downloadToken;
    }
    
    /**
     * Whether stats data is private and can only be viewed by the account owner
     * 
     * @return boolean
     */
    public function canViewStats()
    {
        // check for admin users, they should be allowed access to all
        $Auth = Auth::getAuth();
        if($Auth->level_id == 20)
        {
            return true;
        }
        
        // if file doesn't belong to an account, assume public
        if((int)$this->userId == 0)
        {
            return true;
        }
        
        // if logged in user matches owner
        if($Auth->id == $this->userId)
        {
            return true;
        }
        
        // user not logged in or other account, load file owner and see if flagged as private
        $owner = UserPeer::loadUserById($this->userId);
        if(!$owner)
        {
            return true;
        }
        
        // check if stats are public or private on account, 0 = public
        if($owner->privateFileStatistics == 0)
        {
            return true;
        }
        
        return false;
    }

    /**
     * Hydrate file data into a file object, save reloading from database is we already have the data
     * 
     * @param type $fileDataArr
     * @return file
     */
    static function hydrate($fileDataArr)
    {
        $fileObj = new file();
        foreach ($fileDataArr AS $k => $v)
        {
            $fileObj->$k = $v;
        }

        return $fileObj;
    }

    /**
     * Load by short url
     *
     * @param string $shortUrl
     * @return file
     */
    static function loadByShortUrl($shortUrl)
    {
        $db  = Database::getDatabase(true);
        $row = $db->getRow('SELECT * FROM file WHERE shortUrl = ' . $db->quote($shortUrl));
        if (!is_array($row))
        {
            return false;
        }

        $fileObj = new file();
        foreach ($row AS $k => $v)
        {
            $fileObj->$k = $v;
        }

        return $fileObj;
    }

    /**
     * Load by full url
     *
     * @param string $fileUrl
     * @return file
     */
    static function loadByFullUrl($fileUrl)
    {
        // figure out short url part
        $fileUrl = str_replace(array('http://', 'https://'), '', strtolower($fileUrl));

        // try to match domains
        $shortUrlSection = null;
        if (substr($fileUrl, 0, strlen(_CONFIG_SITE_FULL_URL)) == _CONFIG_SITE_FULL_URL)
        {
            $shortUrlSection = str_replace(_CONFIG_SITE_FULL_URL . '/', '', $fileUrl);
        }
        else
        {
            // load direct file servers
            $db          = Database::getDatabase(true);
            $fileServers = $db->getRows('SELECT fileServerDomainName FROM file_server WHERE LENGTH(fileServerDomainName) > 0 AND serverType = \'direct\'');
            if (COUNT($fileServers))
            {
                foreach ($fileServers AS $fileServer)
                {
                    if (substr($fileUrl, 0, strlen($fileServer['fileServerDomainName'])) == $fileServer['fileServerDomainName'])
                    {
                        $shortUrlSection = str_replace($fileServer['fileServerDomainName'] . '/', '', $fileUrl);
                    }
                }
            }
        }

        if ($shortUrlSection == null)
        {
            return false;
        }

        // break apart to get actual short url
        $shortUrl = current(explode("/", $shortUrlSection));

        return self::loadByShortUrl($shortUrl);
    }

    /**
     * Load by delete hash
     *
     * @param string $deleteHash
     * @return file
     */
    static function loadByDeleteHash($deleteHash)
    {
        $db  = Database::getDatabase(true);
        $row = $db->getRow('SELECT * FROM file WHERE deleteHash = ' . $db->quote($deleteHash));
        if (!is_array($row))
        {
            return false;
        }

        $fileObj = new file();
        foreach ($row AS $k => $v)
        {
            $fileObj->$k = $v;
        }

        return $fileObj;
    }

    /**
     * Load by id
     *
     * @param integer $shortUrl
     * @return file
     */
    static function loadById($id)
    {
        $db  = Database::getDatabase(true);
        $row = $db->getRow('SELECT * FROM file WHERE id = ' . (int) $id);
        if (!is_array($row))
        {
            return false;
        }

        $fileObj = new file();
        foreach ($row AS $k => $v)
        {
            $fileObj->$k = $v;
        }

        return $fileObj;
    }

    /**
     * Create short url
     *
     * @param integer $in
     * @return string
     */
    static function createShortUrlPart($in)
    {
        // note: no need to check whether it already exists as it's handled by the code which calls this
        switch (SITE_CONFIG_GENERATE_UPLOAD_URL_TYPE)
        {
            case 'Medium Hash':
                return substr(MD5($in . microtime()), 0, 16);
                break;
            case 'Long Hash':
                return MD5($in . microtime());
                break;
        }

        // Shortest
        $codeset  = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $cbLength = strlen($codeset);
        $out      = '';
        while ((int) $in > $cbLength - 1)
        {
            $out = $codeset[fmod($in, $cbLength)] . $out;
            $in  = floor($in / $cbLength);
        }

        return $codeset[$in] . $out;
    }

    /**
     * Update short url for file
     *
     * @param integer $id
     * @param string $shortUrl
     */
    static function updateShortUrl($id, $shortUrl = '')
    {
        $db = Database::getDatabase(true);
        $db->query('UPDATE file SET shortUrl = :shorturl WHERE id = :id', array('shorturl' => $shortUrl, 'id'       => $id));
    }

    /**
     * Load all by account id
     *
     * @param integer $accountId
     * @return array
     */
    static function loadAllByAccount($accountId)
    {
        $db = Database::getDatabase(true);
        $rs = $db->getRows('SELECT * FROM file WHERE userId = ' . $db->quote($accountId) . ' ORDER BY originalFilename');
        if (!is_array($rs))
        {
            return false;
        }

        return $rs;
    }

    /**
     * Load all active by folder id
     *
     * @param integer $folderId
     * @return array
     */
    static function loadAllActiveByFolderId($folderId)
    {
        $db = Database::getDatabase(true);
        $rs = $db->getRows('SELECT * FROM file WHERE folderId = ' . $db->quote($folderId) . ' AND statusId = 1 ORDER BY originalFilename');
        if (!is_array($rs))
        {
            return false;
        }

        return $rs;
    }

    /**
     * Load all active by account id
     *
     * @param integer $accountId
     * @return array
     */
    static function loadAllActiveByAccount($accountId)
    {
        $db = Database::getDatabase(true);
        $rs = $db->getRows('SELECT * FROM file WHERE userId = ' . $db->quote($accountId) . ' AND statusId = 1 ORDER BY originalFilename');
        if (!is_array($rs))
        {
            return false;
        }

        return $rs;
    }

    /**
     * Load recent files based on account id
     *
     * @param integer $accountId
     * @return array
     */
    static function loadAllRecentByAccount($accountId, $activeOnly = false)
    {
        $db = Database::getDatabase(true);
        $rs = $db->getRows('SELECT * FROM file WHERE userId = ' . $db->quote($accountId) . ($activeOnly === true ? ' AND statusId=1' : '') . ' ORDER BY uploadedDate DESC LIMIT 10');
        if (!is_array($rs))
        {
            return false;
        }

        return $rs;
    }

    /**
     * Load recent files based on IP address
     *
     * @param string $ip
     * @return array
     */
    static function loadAllRecentByIp($ip, $activeOnly = false)
    {
        $db = Database::getDatabase(true);
        $rs = $db->getRows('SELECT * FROM file WHERE uploadedIP = ' . $db->quote($ip) . ($activeOnly === true ? ' AND statusId=1' : '') . ' AND userId IS NULL ORDER BY uploadedDate DESC LIMIT 10');
        if (!is_array($rs))
        {
            return false;
        }

        return $rs;
    }

    /**
     * Get status label
     *
     * @param integer $statusId
     * @return string
     */
    static function getStatusLabel($statusId)
    {
        $db  = Database::getDatabase(true);
        $row = $db->getRow('SELECT label FROM file_status WHERE id = ' . (int) $statusId);
        if (!is_array($row))
        {
            return 'unknown';
        }

        return $row['label'];
    }

    static function getUploadUrl()
    {
        // get available file server
        $db            = Database::getDatabase(true);
        $fileServerId  = getAvailableServerId();
        $sQL           = "SELECT serverType, fileServerDomainName, scriptPath FROM file_server WHERE id = " . (int) $fileServerId . " LIMIT 1";
        $serverDetails = $db->getRow($sQL);
        if ($serverDetails)
        {
            if ($serverDetails['serverType'] == 'direct')
            {
                $url = $serverDetails['fileServerDomainName'] . $serverDetails['scriptPath'];
                if (substr($url, strlen($url) - 1, 1) == '/')
                {
                    $url = substr($url, 0, strlen($url) - 1);
                }

                return _CONFIG_SITE_PROTOCOL . "://" . $url;
            }
        }

        return WEB_ROOT;
    }

    /*
     * Get all direct file servers
     */

    static function getDirectFileServers()
    {
        $db  = Database::getDatabase(true);
        $sQL = "SELECT id, serverType, fileServerDomainName, scriptPath FROM file_server WHERE serverType='direct' ORDER BY fileServerDomainName";

        return $db->getRows($sQL);
    }

    static function getValidReferrers($formatted = false)
    {
        $pre = '';
        if ($formatted == true)
        {
            $pre = _CONFIG_SITE_PROTOCOL . '://';
        }

        $validUrls                               = array();
        $validUrls[$pre . _CONFIG_SITE_HOST_URL] = $pre . _CONFIG_SITE_HOST_URL;
        $directFileServers                       = self::getDirectFileServers();
        if (COUNT($directFileServers))
        {
            foreach ($directFileServers AS $directFileServer)
            {
                $validUrls[$pre . $directFileServer{'fileServerDomainName'}] = $pre . $directFileServer['fileServerDomainName'];
            }
        }

        return $validUrls;
    }

    static function getFileDomainAndPath($fileId, $fileServerId = null, $finalDownloadBasePath = false)
    {
        // get database connection
        $db = Database::getDatabase(true);
        if ($fileServerId == null)
        {
            $fileServerId = $db->getValue('SELECT serverId FROM file WHERE id = ' . (int) $fileId . ' LIMIT 1');
        }

        if ((int) $fileServerId)
        {
            // use caching for better database performance
            if (!cache::cacheExists('FILE_SERVERS'))
            {
                $fileServers = $db->getRows('SELECT id, fileServerDomainName, scriptPath, routeViaMainSite FROM file_server');
                foreach ($fileServers AS $fileServer)
                {
                    $rs[$fileServer{'id'}] = $fileServer;
                }
                cache::setCache('FILE_SERVERS', $rs);
            }

            $fileServers = cache::getCache('FILE_SERVERS');
            $fileServer  = $fileServers[$fileServerId];
            if ($fileServer)
            {
                if (strlen($fileServer['fileServerDomainName']))
                {
                    // get path from file server
                    $path = $fileServer['fileServerDomainName'] . $fileServer['scriptPath'];
                    
                    // if not direct download link and file server is set to route via main site, override path to this site
                    if(($finalDownloadBasePath == false) && ($fileServer['routeViaMainSite'] == 1))
                    {
                        $path = _CONFIG_CORE_SITE_FULL_URL;
                    }

                    // tidy url
                    if (substr($path, strlen($path) - 1, 1) == '/')
                    {
                        $path = substr($path, 0, strlen($path) - 1);
                    }

                    return $path;
                }
            }
        }

        return _CONFIG_SITE_FILE_DOMAIN;
    }

    static function getFileUrl($fileId, $file = null)
    {
        if (!$file)
        {
            $file = file::loadById((int) $fileId);
        }

        if (!$file)
        {
            return false;
        }

        return $file->getFullShortUrl();
    }

    static function getFileStatisticsUrl($fileId, $file = null)
    {
        if (!$file)
        {
            $file = file::loadById((int) $fileId);
        }

        if (!$file)
        {
            return false;
        }

        return $file->getStatisticsUrl();
    }

    static function getFileDeleteUrl($fileId, $file = null)
    {
        if (!$file)
        {
            $file = file::loadById((int) $fileId);
        }

        if (!$file)
        {
            return false;
        }

        return $file->getDeleteUrl();
    }

    static function getFileInfoUrl($fileId, $file = null)
    {
        if (!$file)
        {
            $file = file::loadById((int) $fileId);
        }

        if (!$file)
        {
            return false;
        }

        return $file->getInfoUrl();
    }

    static function getFileShortInfoUrl($fileId, $file = null)
    {
        if (!$file)
        {
            $file = file::loadById((int) $fileId);
        }

        if (!$file)
        {
            return false;
        }

        return $file->getShortInfoUrl();
    }

    static function getTotalActiveFileSizeByUser($userId)
    {
        $db = Database::getDatabase();

        return $db->getValue('SELECT SUM(fileSize) AS total FROM file WHERE userId = ' . (int) $userId);
    }

    static function getIconPreviewImageUrlLarger($fileArr, $ignorePlugins = false, $css = false)
    {
        return self::getIconPreviewImageUrl($fileArr, $ignorePlugins, 160, $css, 160, 160, 'padded');
    }

    static function getIconPreviewImageUrlLarge($fileArr, $ignorePlugins = false, $css = true)
    {
        return self::getIconPreviewImageUrl($fileArr, $ignorePlugins, 48, $css);
    }

    static function getIconPreviewImageUrlMedium($fileArr, $ignorePlugins = false)
    {
        return self::getIconPreviewImageUrl($fileArr, $ignorePlugins, 24);
    }

    static function getIconPreviewImageUrlSmall($fileArr, $ignorePlugins = false)
    {
        return self::getIconPreviewImageUrl($fileArr, $ignorePlugins, 16);
    }

    static function getIconPreviewImageUrl($fileArr, $ignorePlugins = false, $size, $css = false, $width = null, $height = null, $type = 'middle')
    {
        $iconFilePath = '/file_icons/' . $size . 'px/' . $fileArr['extension'] . '.png';
        $iconUrl      = SITE_IMAGE_PATH . $iconFilePath;
        if ($css == true)
        {
            // return css class instead
            $iconUrl = 'sprite_icon_' . str_replace(array('+'), '', $fileArr['extension']);
        }
        if (!file_exists(DOC_ROOT . '/themes/' . SITE_CONFIG_SITE_THEME . '/images' . $iconFilePath))
        {
            $iconUrl = SITE_IMAGE_PATH . '/file_icons/' . $size . 'px/_page.png';
            if ($css == true)
            {
                // return css class instead
                $iconUrl = 'sprite_icon__page';
            }
        }

        // plugin previews
        if (($size > 24) && ($ignorePlugins == false))
        {
            $params  = pluginHelper::includeAppends('class_file_icon_preview_image_url.php', array('iconUrl' => $iconUrl, 'fileArr' => $fileArr, 'width'   => $width, 'height'  => $height, 'type'    => $type));
            $iconUrl = $params['iconUrl'];
        }

        return $iconUrl;
    }

    /**
     * Update used file storage stats
     */
    static function updateFileServerStorageStats($serverId = null)
    {
        $db = Database::getDatabase();

        // update stats
        if ($serverId == null)
        {
            $servers = $db->getRows('SELECT id FROM file_server');
        }
        else
        {
            $servers   = array();
            $servers[] = array('id' => $serverId);
        }

        foreach ($servers AS $server)
        {
            // server id
            $serverId = (int) $server['id'];

            // get total space used
            $totalPre  = (float) $db->getValue('SELECT SUM(file.fileSize) AS total FROM file WHERE file.statusId = 1 AND fileHash IS NULL AND file.serverId = ' . $serverId . ' GROUP BY file.serverId');
            $totalPost = (float) $db->getValue('SELECT SUM(fileSelect.fileSize) AS total FROM (SELECT * FROM file WHERE file.fileHash IS NOT NULL GROUP BY file.fileHash) AS fileSelect WHERE fileSelect.statusId = 1 AND fileSelect.fileHash IS NOT NULL AND fileSelect.serverId = ' . $serverId . ' GROUP BY fileSelect.serverId');

            // update with new totals
            $db->query('UPDATE file_server SET totalSpaceUsed = ' . (float) $db->escape($totalPre + $totalPost) . ' WHERE id = ' . $serverId);
        }
    }
    
    static function purgeDownloadTokens()
    {
        // get database
        $db = Database::getDatabase(true);
        
        // delete old token data
        $db->query('DELETE FROM download_token WHERE expiry < :expiry', array('expiry' => date('Y-m-d H:i:s')));
    }
    
    public function showDownloadPages()
    {
        // load user
        $Auth = Auth::getAuth();

        // get database
        $db = Database::getDatabase(true);
        
        // check if the user is requesting a new file
        if(isset($_SESSION['_download_page_file_id']))
        {
            if($_SESSION['_download_page_file_id'] != $this->id)
            {
                $_SESSION['_download_page_file_id'] = $this->id;
                $_SESSION['_download_page_next_page'] = 1;
                $_SESSION['_download_page_wait'] = 0;
                unset($_SESSION['_download_page_next_page']);
            }
        }

        // next page to show
        if(!isset($_SESSION['_download_page_next_page']))
        {
            $_SESSION['_download_page_next_page'] = 1;
            $_SESSION['_download_page_wait'] = 0;
        }
        
        // make sure we can actually go to the next page, because of waiting periods
        if($_SESSION['_download_page_wait'] > 0)
        {
            if ($_SESSION['_download_page_load_time'] >= (time() - (int)$_SESSION['_download_page_wait']))
            {
                $_SESSION['_download_page_next_page'] = $_SESSION['_download_page_next_page'] - 1;
                if($_SESSION['_download_page_next_page'] < 1)
                {
                    $_SESSION['_download_page_next_page'] = 1;
                }
            }
        }
        
        // log load time for this page
        $_SESSION['_download_page_load_time'] = time();
        $_SESSION['_download_page_file_id'] = $this->id;
        $_SESSION['_download_page_wait'] = 0;
        
        $nextOrder = $_SESSION['_download_page_next_page'];

        // load download pages for user level
        $downloadPage = $db->getRow('SELECT download_page, page_order, additional_javascript_code, additional_settings FROM download_page WHERE user_level_id = '.(int)$Auth->level_id.' AND page_order >= '.(int)$nextOrder.' ORDER BY page_order ASC LIMIT 1');
        if(!$downloadPage)
        {
            return true;
        }

        $filePath = _CONFIG_SCRIPT_ROOT . '/'.$downloadPage['download_page'];
        if(!file_exists($filePath))
        {
            die('Error: Download page does not exist: '.$filePath);
        }

        // load additional settings
        $additionalSettings = array();
        if(strlen($downloadPage['additional_settings']))
        {
            $additionalSettings = json_decode($downloadPage['additional_settings'], true);
        }

        // set timer wait if exists in the page config
        $_SESSION['_download_page_wait'] = 0;
        if(isset($additionalSettings['download_wait']))
        {
            $_SESSION['_download_page_wait'] = (int)$additionalSettings['download_wait'];
        }

        // reassign file object for download pages
        $file = $this;

        // header template
        require_once('_header.php');

        // download page
        include_once($filePath);

        // for page footer link
        if(!defined('REPORT_URL'))
        {
            define('REPORT_URL', $file->getFullShortUrl());
        }

        // footer template
        require_once('_footer.php');

        // increment next order
        $_SESSION['_download_page_next_page']++;
        exit();
    }

}
