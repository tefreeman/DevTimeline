<?php

error_reporting(E_ALL | E_STRICT);

class uploader
{

    const REMOTE_URL_UPLOAD_FEEDBACK_CHUNKS_BYTES = 200000; // how often to supply feedback on the url uploader

    public $options;
    public $nextFeedbackTracker;
    public $rowId;

    function __construct($options = null)
    {
        // get accepted file types
        $acceptedFileTypes = getAcceptedFileTypes();
        if (isset($options['max_chunk_size']))
        {
            $this->options['max_chunk_size'] = (int) $options['max_chunk_size'];
        }

        $this->options = array(
            'script_url'              => $_SERVER['PHP_SELF'],
            'upload_dir'              => _CONFIG_FILE_STORAGE_PATH,
            'upload_url'              => dirname($_SERVER['PHP_SELF']) . '/files/',
            'param_name'              => 'files',
            'delete_hash'             => '',
            'max_file_size'           => $this->getMaxUploadSize(),
            'min_file_size'           => 1,
            'accept_file_types'       => COUNT($acceptedFileTypes) ? ('/(\.|\/)(' . str_replace(".", "", implode("|", $acceptedFileTypes)) . ')$/i') : '/.+$/i',
            'max_number_of_files'     => null,
            'discard_aborted_uploads' => true,
            'max_chunk_size'          => 0
        );

        if ($options)
        {
            $this->options = array_replace_recursive($this->options, $options);
        }
    }

    public function getMaxUploadSize()
    {
        // ynitialize current user
        $Auth = Auth::getAuth();

        // max allowed upload size
        $maxUploadSize = (int) getMaxUploadFilesize();

        /* no longer needed
        // if php restrictions are lower than permitted, override. Ignore for chunked uploads
        if ($this->options['max_chunk_size'] == 0)
        {
            $phpMaxSize = getPHPMaxUpload();
            if ($phpMaxSize < $maxUploadSize)
            {
                $maxUploadSize = $phpMaxSize;
            }
        }
         */

        return $maxUploadSize;
    }

    public function getAvailableStorage()
    {
        // initialize current user
        $Auth = Auth::getAuth();

        // available storage
        $availableStorage = getAvailableFileStorage();

        return $availableStorage;
    }

    public function getFileObject($file_name)
    {
        $file_path = $this->options['upload_dir'] . $file_name;
        if (is_file($file_path) && $file_name[0] !== '.')
        {
            $file              = new stdClass();
            $file->name        = $file_name;
            $file->size        = filesize($file_path);
            $file->url         = $this->options['upload_url'] . rawurlencode($file->name);
            $file->delete_url  = '~d?' . $this->options['delete_hash'];
            $file->info_url    = '~i?' . $this->options['delete_hash'];
            $file->delete_type = 'DELETE';
            $file->delete_hash = $this->options['delete_hash'];

            return $file;
        }

        return null;
    }

    public function getFileObjects()
    {
        return array_values(array_filter(array_map(
                    array($this, 'getFileObject'), scandir($this->options['upload_dir'])
        )));
    }

    public function hasError($uploaded_file, $file, $error = null)
    {
        if ($error)
        {
            return $error;
        }

        if (!preg_match($this->options['accept_file_types'], $file->name))
        {
            return 'acceptFileTypes';
        }
        if ($uploaded_file && file_exists($uploaded_file))
        {
            $file_size = filesize($uploaded_file);
        }
        else
        {
            $file_size = $_SERVER['CONTENT_LENGTH'];
        }
        if ($this->options['max_file_size'] && (
            $file_size > $this->options['max_file_size'] ||
            $file->size > $this->options['max_file_size'])
        )
        {
            return 'maxFileSize';
        }
        if ($this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size'])
        {
            return 'minFileSize';
        }
        if (is_int($this->options['max_number_of_files']) && (
            count($this->getFileObjects()) >= $this->options['max_number_of_files'])
        )
        {
            return 'maxNumberOfFiles';
        }

        return null;
    }

    public function handleFileUpload($uploadedFile, $name, $size, $type, $error, $index = null, $contentRange = null, $chunkTracker = null)
    {
        $fileUpload        = new stdClass();
        $fileUpload->name  = stripslashes($name);
        $fileUpload->size  = intval($size);
        $fileUpload->type  = $type;
        $fileUpload->error = null;

        // save file locally is chunked upload
        if ($contentRange)
        {
            $localTempStore = $this->getLocalTempStorePath();
            $tmpFilename    = MD5($chunk_tracker . $fileUpload->name);
            $tmpFilePath    = $localTempStore . $tmpFilename;

            // if first chunk
            if ($contentRange[1] == 0)
            {
                // ensure the tmp file does not already exist
                if (file_exists($tmpFilePath))
                {
                    unlink($tmpFilePath);
                }

                // clean up any old chunks while we're here
                $this->cleanLeftOverChunks();
            }

            // ensure we have the chunk
            if ($uploadedFile && file_exists($uploadedFile))
            {
                // multipart/formdata uploads (POST method uploads)
                file_put_contents(
                    $tmpFilePath, fopen($uploadedFile, 'r'), FILE_APPEND
                );

                // check if this is not the last chunk
                if ($contentRange[3] != filesize($tmpFilePath))
                {
                    // exit
                    return $fileUpload;
                }

                // otherwise assume we have the whole file
                $uploadedFile     = $tmpFilePath;
                $fileUpload->size = filesize($tmpFilePath);
            }
            else
            {
                // exit
                return $fileUpload;
            }
        }

        $fileUpload->error = $this->hasError($uploadedFile, $fileUpload, $error);
        if (!$fileUpload->error)
        {
            if (strlen(trim($fileUpload->name)) == 0)
            {
                $fileUpload->error = t('classuploader_filename_not_found', 'Filename not found.');
            }
        }
        elseif (intval($size) == 0)
        {
            $fileUpload->error = t('classuploader_file_received_has_zero_size', 'File received has zero size. This is likely an issue with the maximum permitted size within PHP');
        }
        elseif (intval($size) > $this->options['max_file_size'])
        {
            $fileUpload->error = t('classuploader_file_received_larger_than_permitted', 'File received is larger than permitted. (max [[[MAX_FILESIZE]]])', array('MAX_FILESIZE' => formatSize($this->options['max_file_size'])));
        }

        if (!$fileUpload->error && $fileUpload->name)
        {
            $fileUpload = $this->moveIntoStorage($fileUpload, $uploadedFile);
        }

        // no error, add success html
        if ($fileUpload->error === null)
        {
            $fileUpload->success_result_html = self::generateSuccessHtml($fileUpload);
        }
        else
        {
            $fileUpload->error_result_html = self::generateErrorHtml($fileUpload);
        }

        return $fileUpload;
    }

    public function get()
    {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        if ($file_name)
        {
            $info = $this->getFileObject($file_name);
        }
        else
        {
            $info = $this->getFileObjects();
        }
        header('Content-type: application/json');
        echo json_encode($info);
    }

    public function post()
    {
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : array(
            'tmp_name' => null,
            'name'     => null,
            'size'     => null,
            'type'     => null,
            'error'    => null
        );

        // parse the Content-Disposition header, if available:
        $file_name = $this->getServerVar('HTTP_CONTENT_DISPOSITION') ?
            rawurldecode(preg_replace(
                    '/(^[^"]+")|("$)/', '', $this->getServerVar('HTTP_CONTENT_DISPOSITION')
            )) : null;

        // parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $content_range = $this->getServerVar('HTTP_CONTENT_RANGE') ?
            preg_split('/[^0-9]+/', $this->getServerVar('HTTP_CONTENT_RANGE')) : null;
        $size          = $content_range ? $content_range[3] : null;

        $info = array();
        if (is_array($upload['tmp_name']))
        {
            foreach ($upload['tmp_name'] as $index => $value)
            {
                $info[] = $this->handleFileUpload(
                    $upload['tmp_name'][$index], isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index], isset($_SERVER['HTTP_X_FILE_SIZE']) ? $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index], isset($_SERVER['HTTP_X_FILE_TYPE']) ? $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index], $upload['error'][$index], $index, $content_range, isset($_REQUEST['cTracker']) ? $_REQUEST['cTracker'] : null
                );
            }
        }
        else
        {
            $info[] = $this->handleFileUpload(
                $upload['tmp_name'], isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'], isset($_SERVER['HTTP_X_FILE_SIZE']) ? $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'], isset($_SERVER['HTTP_X_FILE_TYPE']) ? $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'], $upload['error'], null, $content_range, isset($_REQUEST['cTracker']) ? $_REQUEST['cTracker'] : null
            );
        }
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false))
        {
            header('Content-type: application/json');
        }
        else
        {
            header('Content-type: text/plain');
        }
        echo json_encode($info);
    }

    public function handleRemoteUrlUpload($url, $rowId = 0)
    {
        $this->rowId               = $rowId;
        $this->nextFeedbackTracker = self::REMOTE_URL_UPLOAD_FEEDBACK_CHUNKS_BYTES;

        $fileUpload   = new stdClass();
        // file name
        $realFilename = trim(end(explode('/', $url)));
        if (strlen($realFilename) == 0)
        {
            $realFilename = 'file.txt';
        }
        $fileUpload->name = $realFilename;

        $fileUpload->size  = 0;
        $fileUpload->type  = '';
        $fileUpload->error = null;
        $fileUpload->rowId = $rowId;

        $remoteFilesize = (int) $this->getRemoteFilesize($url);
        if ($remoteFilesize > $this->options['max_file_size'])
        {
            $fileUpload->error = t('classuploader_file_larger_than_permitted', 'File is larger than permitted. (max [[[MAX_FILESIZE]]])', array('MAX_FILESIZE' => formatSize($this->options['max_file_size'])));
        }
        else
        {
            // try to get the file locally
            $localFile = $this->downloadRemoteFile($url, true);

            // reconnect db if it's gone away
            $db = Database::getDatabase(true);
            $db->close();
            $db = Database::getDatabase(true);

            if ($localFile === false)
            {
                $fileUpload->error = t('classuploader_could_not_get_remote_file', 'Could not get remote file. [[[FILE_URL]]]', array('FILE_URL' => $url));
            }
            else
            {
                $size              = (int) filesize($localFile);
                $fileUpload->error = $this->hasError($localFile, $fileUpload);
                if (!$fileUpload->error)
                {
                    if (strlen(trim($fileUpload->name)) == 0)
                    {
                        $fileUpload->error = t('classuploader_filename_not_found', 'Filename not found.');
                    }
                }
                elseif (intval($size) == 0)
                {
                    $fileUpload->error = t('classuploader_file_has_zero_size', 'File received has zero size.');
                }
                elseif (intval($size) > $this->options['max_file_size'])
                {
                    $fileUpload->error = t('classuploader_file_received_larger_than_permitted', 'File received is larger than permitted. (max [[[MAX_FILESIZE]]])', array('MAX_FILESIZE' => formatSize($this->options['max_file_size'])));
                }

                if (!$fileUpload->error && $fileUpload->name)
                {
                    // filesize
                    $fileUpload->size = filesize($localFile);

                    // get mime type
                    $mimeType = mime_type($fileUpload->name, 'application/octet-stream');
                    if (($mimeType == 'application/octet-stream') && (class_exists('finfo', false)))
                    {
                        $finfo    = new finfo;
                        $mimeType = $finfo->file($localFile, FILEINFO_MIME);
                    }
                    $fileUpload->type = $mimeType;

                    // save into permanent storage
                    $fileUpload = $this->moveIntoStorage($fileUpload, $localFile);
                }
                else
                {
                    @unlink($localFile);
                }
            }
        }

        // no error, add success html
        if ($fileUpload->error === null)
        {
            $fileUpload->success_result_html = self::generateSuccessHtml($fileUpload);
        }
        else
        {
            $fileUpload->error_result_html = self::generateErrorHtml($fileUpload);
        }

        $this->remote_url_event_callback(array("done" => $fileUpload));
    }

    public function getRemoteFilesize($url)
    {
        $bytes = 0;
        if (function_exists('curl_init'))
        {
            $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

            // initialize curl with given url
            $ch      = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            $execute = curl_exec($ch);

            // check if any error occured
            if (!curl_errno($ch))
            {
                $bytes = (int) curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            }
            curl_close($ch);
        }
        else
        {
            uploader::exitWithError(t('classuploader_curl_module_not_found', 'Curl module not found. Please enable within PHP to enable remote uploads.'));
        }

        return $bytes;
    }

    private function getLocalTempStorePath()
    {
        $tmpDir = _CONFIG_FILE_STORAGE_PATH . '_tmp/';
        if (!file_exists($tmpDir))
        {
            @mkdir($tmpDir);
        }

        if (!file_exists($tmpDir))
        {
            uploader::exitWithError(t('classuploader_failed_creating_tmp_storage_folder', 'Failed creating tmp storage folder for chunked uploads. Ensure the parent folder has write permissions: [[[TMP_DIR]]]',  array('TMP_DIR' => $tmpDir)));
        }

        if (!is_writable($tmpDir))
        {
            uploader::exitWithError(t('classuploader_temp_storage_folder_for_uploads_not_writable', 'Temp storage folder for uploads is not writable. Ensure it has CHMOD 755 or 777 permissions: [[[TMP_DIR]]]', array('TMP_DIR' => $tmpDir)));
        }

        return $tmpDir;
    }

    public function downloadRemoteFile($url, $streamResponse = false)
    {
        // save locally
        $tmpDir      = $this->getLocalTempStorePath();
        $tmpName     = MD5($url . microtime());
        $tmpFullPath = $tmpDir . $tmpName;

        // use curl
        if (function_exists('curl_init'))
        {
            // get file via curl
            $fp = fopen($tmpFullPath, 'w+');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60 * 60 * 24); // 24 hours
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); // 15 seconds
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            if ($streamResponse === true)
            {
                curl_setopt($ch, CURLOPT_NOPROGRESS, false);
                curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'remoteUrlCurlProgressCallback'));
            }
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);

            // remove is no a valid status code
            if ($status === 404)
            {
                @unlink($tmpFullPath);
            }
        }
        // use file_get_contents
        else
        {
            if (function_exists('stream_context_create'))
            {
                $httpArr = array(
                    'timeout' => 15, // 15 seconds
                );

                if ($streamResponse === true)
                {
                    $httpArr['notification'] = array($this, 'remoteUrlCurlProgressCallback');
                }

                $ctx = stream_context_create(array('http' =>
                    $httpArr
                ));
            }

            // get file content
            $fileData = @file_get_contents($url);
            @file_put_contents($tmpFullPath, $fileData);
        }

        // test to see if we saved the file
        if ((file_exists($tmpFullPath)) && (filesize($tmpFullPath) > 0))
        {
            return $tmpFullPath;
        }

        // clear blank file
        if (file_exists($tmpFullPath))
        {
            @unlink($tmpFullPath);
        }

        return false;
    }

    function remote_url_event_callback($message)
    {
        echo "<script>parent.updateUrlProgress(" . json_encode($message) . ");</script>";
        ob_flush();
        flush();
    }

    function remote_url_stream_notification_callback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max)
    {
        if ($notification_code == STREAM_NOTIFY_PROGRESS)
        {
            if ($bytes_transferred)
            {
                if ($bytes_transferred > $this->nextFeedbackTracker)
                {
                    $this->remote_url_event_callback(array("progress" => array("loaded" => $bytes_transferred, "total"  => $bytes_max, "rowId"  => $this->rowId)));
                    $this->nextFeedbackTracker = $this->nextFeedbackTracker + self::REMOTE_URL_UPLOAD_FEEDBACK_CHUNKS_BYTES;
                }
            }
        }
    }

    function remoteUrlCurlProgressCallback($download_size, $downloaded_size, $upload_size, $uploaded_size)
    {
        if ($downloaded_size > $this->nextFeedbackTracker)
        {
            $this->remote_url_event_callback(array("progress" => array("loaded" => $downloaded_size, "total"  => $download_size, "rowId"  => $this->rowId)));
            $this->nextFeedbackTracker = $this->nextFeedbackTracker + self::REMOTE_URL_UPLOAD_FEEDBACK_CHUNKS_BYTES;
        }
    }

    public function moveIntoStorage($fileUpload, $tmpFile)
    {
        if ($fileUpload->name[0] === '.')
        {
            $fileUpload->name = substr($fileUpload->name, 1);
        }
        $newFilename = MD5(microtime());
        $parts       = explode(".", $fileUpload->name);
        $lastPart    = end($parts);
        $extension   = strtolower($lastPart);

        // figure out upload type
        $file_size = 0;

        // refresh db connection
        $db = Database::getDatabase(true);
        $db->close();
        $db = Database::getDatabase(true);

        // select server from pool
        $uploadServerId = getAvailableServerId();

        // get current server
        $uploadServerDetails = $db->getRow('SELECT * FROM file_server WHERE id = ' . $db->quote($uploadServerId));

        // override storage path
        if (strlen($uploadServerDetails['storagePath']))
        {
            $this->options['upload_dir'] = $uploadServerDetails['storagePath'];
            if (substr($this->options['upload_dir'], strlen($this->options['upload_dir']) - 1, 1) == '/')
            {
                $this->options['upload_dir'] = substr($this->options['upload_dir'], 0, strlen($this->options['upload_dir']) - 1);
            }
            $this->options['upload_dir'] .= '/';
        }

        // create file hash
        $fileHash = md5_file($tmpFile);

        // check if the file hash already exists
        $fileExists = false;

        $findFile = $db->getRow("SELECT * FROM file WHERE fileHash=" . $db->quote($fileHash) . " AND statusId=1 AND fileSize=" . (int) $fileUpload->size . " LIMIT 1");
        if (COUNT($findFile) > 1)
        {
            $fileExists = true;
        }

        if ($fileExists == false)
        {
            // include any plugins for other storage methods
            $params = pluginHelper::includeAppends('class_uploader_move_into_storage.inc.php', array('actioned'            => false, 'file_path'           => '', 'file'                => $file, 'uploadServerDetails' => $uploadServerDetails, 'fileUpload'          => $fileUpload, 'newFilename'         => $newFilename, 'tmpFile'             => $tmpFile, 'uploader'            => $this));
            if ($params['actioned'] == true)
            {
                $fileUpload = $params['fileUpload'];
                $file_path  = $params['file_path'];
                $file_size  = $params['file_size'];
            }
            // local, direct or ftp storage methods
            else
            {
                // move remotely via ftp
                if ($uploadServerDetails['serverType'] == 'ftp')
                {
                    // connect ftp
                    $conn_id = ftp_connect($uploadServerDetails['ipAddress'], $uploadServerDetails['ftpPort'], 30);
                    if ($conn_id === false)
                    {
                        $fileUpload->error = t('classuploader_could_not_connect_file_server', 'Could not connect to file server [[[IP_ADDRESS]]]', array('IP_ADDRESS' => $uploadServerDetails['ipAddress']));
                    }

                    // authenticate
                    if (!$fileUpload->error)
                    {
                        $login_result = ftp_login($conn_id, $uploadServerDetails['ftpUsername'], $uploadServerDetails['ftpPassword']);
                        if ($login_result === false)
                        {
                            $fileUpload->error = t('classuploader_could_not_authenticate_with_file_server', 'Could not authenticate with file server [[[IP_ADDRESS]]]', array('IP_ADDRESS' => $uploadServerDetails['ipAddress']));
                        }
                    }

                    // create the upload folder
                    if (!$fileUpload->error)
                    {
                        $uploadPathDir = $this->options['upload_dir'] . substr($newFilename, 0, 2);
                        if (!ftp_mkdir($conn_id, $uploadPathDir))
                        {
                            // Error reporting removed for now as it causes issues with existing folders. Need to add a check in before here
                            // to see if the folder exists, then create if not.
                            // $fileUpload->error = 'There was a problem creating the storage folder on '.$uploadServerDetails['ipAddress'];
                        }
                    }

                    // upload via ftp
                    if (!$fileUpload->error)
                    {
                        $file_path = $uploadPathDir . '/' . $newFilename;
                        clearstatcache();
                        if ($tmpFile)
                        {
                            // initiate ftp
                            $ret = ftp_nb_put($conn_id, $file_path, $tmpFile, FTP_BINARY, FTP_AUTORESUME);
                            while ($ret == FTP_MOREDATA)
                            {
                                // continue uploading
                                $ret = ftp_nb_continue($conn_id);
                            }

                            if ($ret != FTP_FINISHED)
                            {
                                $fileUpload->error = t('classuploader_there_was_problem_uploading_file', 'There was a problem uploading the file to [[[IP_ADDRESS]]]', array('IP_ADDRESS' => $uploadServerDetails['ipAddress']));
                            }
                            else
                            {
                                $file_size = filesize($tmpFile);
                                @unlink($tmpFile);
                            }
                        }
                    }

                    // close ftp connection
                    ftp_close($conn_id);
                }
                // move into local storage
                else
                {
                    // check the upload folder
                    if ($uploadServerDetails['serverType'] == 'direct')
                    {
                        if (!is_dir($this->options['upload_dir']))
                        {
                            $this->options['upload_dir'] = _CONFIG_SCRIPT_ROOT . '/' . $this->options['upload_dir'];
                        }
                    }

                    // create the upload folder
                    $uploadPathDir = $this->options['upload_dir'] . substr($newFilename, 0, 2);
                    @mkdir($uploadPathDir);
                    @chmod($uploadPathDir, 0777);

                    $file_path = $uploadPathDir . '/' . $newFilename;
                    clearstatcache();
                    $rs        = false;
                    if ($tmpFile)
                    {
                        $rs = rename($tmpFile, $file_path);
                        if ($rs)
                        {
                            @chmod($file_path, 0777);
                        }
                    }

                    if ($rs == false)
                    {
                        $fileUpload->error = t('classuploader_could_not_move_file_into_storage', 'Could not move the file into storage, possibly a permissions issue.');
                    }
                    $file_size = filesize($file_path);
                }
            }
        }
        else
        {
            $file_size      = $findFile['fileSize'];
            $file_path      = $this->options['upload_dir'] . $findFile['localFilePath'];
            $uploadServerId = $findFile['serverId'];
        }

        // reset the connection to the database so mysql doesn't time out
        $db->close();
        $db = Database::getDatabase(true);

        // check filesize uploaded matches tmp uploaded
        if (($file_size == $fileUpload->size) && (!$fileUpload->error))
        {
            $fileUpload->url = $this->options['upload_url'] . rawurlencode($fileUpload->name);

            // insert into the db
            $fileUpload->size        = $file_size;
            $fileUpload->delete_url  = '~d?' . $this->options['delete_hash'];
            $fileUpload->info_url    = '~i?' . $this->options['delete_hash'];
            $fileUpload->delete_type = 'DELETE';
            $fileUpload->delete_hash = $this->options['delete_hash'];

            // create delete hash, make sure it's unique
            $deleteHash = md5($fileUpload->name . getUsersIPAddress() . microtime());

            // store in db
            $db       = Database::getDatabase(true);
            $dbInsert = new DBObject("file", array("originalFilename", "shortUrl", "fileType", "extension", "fileSize", "localFilePath", "userId", "totalDownload", "uploadedIP", "uploadedDate", "statusId", "deleteHash", "serverId", "fileHash", "adminNotes"));

            $dbInsert->originalFilename = $fileUpload->name;
            $dbInsert->shortUrl         = 'temp';
            $dbInsert->fileType         = $fileUpload->type;
            $dbInsert->extension        = strtolower($extension);
            $dbInsert->fileSize         = $fileUpload->size;
            $dbInsert->localFilePath    = str_replace($this->options['upload_dir'], '', $file_path);

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
            $dbInsert->deleteHash    = $deleteHash;
            $dbInsert->serverId      = $uploadServerId;
            $dbInsert->fileHash      = $fileHash;
			$dbInsert->adminNotes      = '';

            if (!$dbInsert->insert())
            {
                $fileUpload->error = t('classuploader_failed_adding_to_database', 'Failed adding to database. [[[ERROR_MSG]]]', array('ERROR_MSG' => $dbInsert->errorMsg));
            }
            else
            {
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

                // update fileUpload with file location
                $file                    = file::loadByShortUrl($shortUrl);
                $fileUpload->url         = $file->getFullShortUrl();
                $fileUpload->delete_url  = $file->getDeleteUrl();
                $fileUpload->info_url    = $file->getInfoUrl();
                $fileUpload->stats_url   = $file->getStatisticsUrl();
                $fileUpload->delete_hash = $file->deleteHash;
                $fileUpload->short_url   = $shortUrl;

                // update storage stats
                file::updateFileServerStorageStats();

                // include any plugins
                pluginHelper::includeAppends('class_uploader_success.inc.php', array('file' => $file));
            }
        }
        else if ($this->options['discard_aborted_uploads'])
        {
            //@TODO - make ftp compatible
            @unlink($file_path);
            @unlink($tmpFile);
            if (!isset($fileUpload->error))
            {
                $fileUpload->error = t('classuploader_general_upload_error', 'General upload error, please contact support. Expected size: [[[FILE_SIZE]]]. Received size: [[[FILE_UPLOAD_SIZE]]].', array('FILE_SIZE' => $file_size, 'FILE_UPLOAD_SIZE' => $fileUpload->size));
            }
        }

        return $fileUpload;
    }

    /*
     * Removes any old files left over from failed chunked uploads
     */
    private function cleanLeftOverChunks()
    {
        // loop local tmp folder and clear any older than 3 days old
        $localTempStore = $this->getLocalTempStorePath();
        foreach (glob($localTempStore . "*") as $file)
        {
            // protect the filename
            if (filemtime($file) < time() - 60 * 60 * 24 * 3)
            {
                // double check we're in the file store
                if(substr($file, 0, strlen(_CONFIG_FILE_STORAGE_PATH)) == _CONFIG_FILE_STORAGE_PATH)
                {
                    @unlink($file);
                }
            }
        }
    }

    protected function getServerVar($id)
    {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }

    static function generateSuccessHtml($fileUpload)
    {
        // get auth for later
        $Auth = Auth::getAuth();

        // load user folders for later
        $userFolders = fileFolder::loadAllByAccount($Auth->id);

        // generate html
        $success_result_html = '';
        $success_result_html .= '<td class="cancel">';
        $success_result_html .= '   <img src="' . getCoreSitePath() . '/themes/' . SITE_CONFIG_SITE_THEME . '/images/green_tick_small.png" height="16" width="16" alt="success"/>';
        $success_result_html .= '</td>';
        $success_result_html .= '<td class="name">';
        $success_result_html .= $fileUpload->name;
        $success_result_html .= '<div class="sliderContent" style="display: none;">';
        $success_result_html .= '        <!-- popup content -->';
        $success_result_html .= '        <table width="100%">';
        $success_result_html .= '            <tr>';
        $success_result_html .= '                <td class="odd" style="width: 90px; border-top:1px solid #fff;">';
        $success_result_html .= '                    <label>' . t('download_url', 'Download Url') . ':</label>';
        $success_result_html .= '                </td>';
        $success_result_html .= '                <td class="odd" style="border-top:1px solid #fff;">';
        $success_result_html .= '                    <a href="' . $fileUpload->url . '" target="_blank">' . $fileUpload->url . '</a>';
        $success_result_html .= '                </td>';
        $success_result_html .= '            </tr>';
        $success_result_html .= '            <tr>';
        $success_result_html .= '                <td class="even">';
        $success_result_html .= '                    <label>' . t('html_code', 'HTML Code') . ':</label>';
        $success_result_html .= '                </td>';
        $success_result_html .= '                <td class="even htmlCode" onClick="return false;">';
        $success_result_html .= '                    &lt;a href=&quot;' . $fileUpload->info_url . '&quot; target=&quot;_blank&quot; title=&quot;' . t('download from', 'Download From') . ' ' . SITE_CONFIG_SITE_NAME . '&quot;&gt;' . t('download', 'Download') . ' ' . $fileUpload->name . ' ' . t('from', 'from') . ' ' . SITE_CONFIG_SITE_NAME . '&lt;/a&gt;';
        $success_result_html .= '                </td>';
        $success_result_html .= '            </tr>';
        $success_result_html .= '            <tr>';
        $success_result_html .= '                <td class="odd">';
        $success_result_html .= '                    <label>' . t('forum_code', 'Forum Code') . ':</label>';
        $success_result_html .= '                </td>';
        $success_result_html .= '                <td class="odd htmlCode">';
        $success_result_html .= '                    [url]' . $fileUpload->url . '[/url]';
        $success_result_html .= '                </td>';
        $success_result_html .= '            </tr>';
        $success_result_html .= '            <tr>';
        $success_result_html .= '                <td class="even">';
        $success_result_html .= '                    <label>' . t('stats_url', 'Stats Url') . ':</label>';
        $success_result_html .= '                </td>';
        $success_result_html .= '                <td class="even">';
        $success_result_html .= '                    <a href="' . $fileUpload->stats_url . '" target="_blank">' . $fileUpload->stats_url . '</a>';
        $success_result_html .= '                </td>';
        $success_result_html .= '            </tr>';
        $success_result_html .= '            <tr>';
        $success_result_html .= '                <td class="odd">';
        $success_result_html .= '                    <label>' . t('delete_url', 'Delete Url') . ':</label>';
        $success_result_html .= '                </td>';
        $success_result_html .= '                <td class="odd">';
        $success_result_html .= '                    <a href="' . $fileUpload->delete_url . '" target="_blank">' . $fileUpload->delete_url . '</a>';
        $success_result_html .= '                </td>';
        $success_result_html .= '            </tr>';
        $success_result_html .= '            <tr>';
        $success_result_html .= '                <td class="even">';
        $success_result_html .= '                    <label>' . t('full_info', 'Full Info') . ':</label>';
        $success_result_html .= '                </td>';
        $success_result_html .= '                <td class="even htmlCode">';
        $success_result_html .= '                    <a href="' . $fileUpload->info_url . '" target="_blank" onClick="window.open(\'' . $fileUpload->info_url . '\'); return false;">[' . t('click_here', 'click here') . ']</a>';
        $success_result_html .= '                </td>';
        $success_result_html .= '            </tr>';

        /*
          if ($Auth->loggedIn() && COUNT($userFolders))
          {
          $success_result_html .= '                <tr>';
          $success_result_html .= '                    <td class="odd">';
          $success_result_html .= '                        <label>' . t('save_to_folder',
          'Save To Folder') . ':</label>';
          $success_result_html .= '                    </td>';
          $success_result_html .= '                    <td class="odd">';
          $success_result_html .= '                        <form>';
          $success_result_html .= '                            <select name="folderId" id="folderId" class="saveToFolder" onChange="saveFileToFolder(this); return false;">';
          $success_result_html .= '                                <option value="">- ' . t('none',
          'none') . ' -</option>';
          foreach ($userFolders AS $userFolder)
          {
          $success_result_html .= '                                    <option value="' . $userFolder['id'] . '">' . htmlentities($userFolder['folderName']) . '</option>';
          }
          $success_result_html .= '                            </select>';
          $success_result_html .= '                        </form>';
          $success_result_html .= '                    </td>';
          $success_result_html .= '                </tr>';
          }
         * 
         */

        $success_result_html .= '        </table>';
        $success_result_html .= '        <input type="hidden" value="' . $fileUpload->short_url . '" name="shortUrlHidden" class="shortUrlHidden"/>';
        $success_result_html .= '    </div>';
        $success_result_html .= '</td>';
        $success_result_html .= '<td class="rightArrow"><img src="' . getCoreSitePath() . '/themes/' . SITE_CONFIG_SITE_THEME . '/images/blue_right_arrow.png" width="8" height="6" /></td>';
        $success_result_html .= '<td class="url urlOff">';
        $success_result_html .= '    <a href="' . $fileUpload->url . '" target="_blank">' . $fileUpload->url . '</a>';
        $success_result_html .= '    <div class="fileUrls hidden">' . $fileUpload->url . '</div>';
        $success_result_html .= '</td>';

        // check plugins so the resulting html can be overwritten if set
        $params              = pluginHelper::includeAppends('class_uploader_success_result_html.php', array('success_result_html' => $success_result_html, 'fileUpload'          => $fileUpload, 'userFolders'         => $userFolders));
        $success_result_html = $params['success_result_html'];

        return $success_result_html;
    }

    static function generateErrorHtml($fileUpload)
    {
        // get auth for later
        $Auth = Auth::getAuth();

        // generate html
        $error_result_html = '';
        $error_result_html .= '<td class="cancel">';
        $error_result_html .= '   <img src="' . getCoreSitePath() . '/themes/' . SITE_CONFIG_SITE_THEME . '/images/red_error_small.png" height="16" width="16" alt="error"/>';
        $error_result_html .= '</td>';

        $error_result_html .= '<td class="name">' . $fileUpload->name . '</td>';

        $error_result_html .= '<td class="error" colspan="2">'.t('classuploader_error', 'Error').': ';
        $error_result_html .= self::translateError($fileUpload->error);
        $error_result_html .= '</td>';

        // check plugins so the resulting html can be overwritten if set
        $params            = pluginHelper::includeAppends('class_uploader_error_result_html.php', array('error_result_html' => $error_result_html, 'fileUpload'        => $fileUpload));
        $error_result_html = $params['error_result_html'];

        return $error_result_html;
    }

    static function translateError($error)
    {
        switch ($error)
        {
            case 1:
                return t('file_exceeds_upload_max_filesize_php_ini_directive', 'File exceeds upload_max_filesize (php.ini directive)');
            case 2:
                return t('file_exceeds_max_file_size_html_form_directive', 'File exceeds MAX_FILE_SIZE (HTML form directive)');
            case 3:
                return t('file_was_only_partially_uploaded', 'File was only partially uploaded');
            case 4:
                return t('no_file_was_uploaded', 'No File was uploaded');
            case 5:
                return t('missing_a_temporary_folder', 'Missing a temporary folder');
            case 6:
                return t('failed_to_write_file_to_disk', 'Failed to write file to disk');
            case 7:
                return t('file_upload_stopped_by_extension', 'File upload stopped by extension');
            case 'maxFileSize':
                return t('file_is_too_big', 'File is too big');
            case 'minFileSize':
                return t('file_is_too_small', 'File is too small');
            case 'acceptFileTypes':
                return t('filetype_is_not_allowed', 'Filetype not allowed');
            case 'maxNumberOfFiles':
                return t('max_number_of_files_exceeded', 'Max number of files exceeded');
            case 'uploadedBytes':
                return t('uploaded_bytes_exceed_file_size', 'Uploaded bytes exceed file size');
            case 'emptyResult':
                return t('empty_file_upload_result', 'Empty file upload result');
            default:
                return $error;
        }
    }

    static function exitWithError($errorStr)
    {
        // log
        log::error('class.uploader.php: '.$errorStr);

        $fileUpload                    = new stdClass();
        $fileUpload->error             = $errorStr;
        $errorHtml                     = uploader::generateErrorHtml($fileUpload);
        $fileUpload->error_result_html = $errorHtml;
        echo json_encode(array($fileUpload));
        exit;
    }

}