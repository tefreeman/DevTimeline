<?php

/**
 * API class for remote file management.
 * 
 * See /api/index.php for full usage details.
 */
class api
{

    public $apiKey = '';
    public $userName = '';
    public $userId = '';

    function __construct($apiKey, $userName)
    {
        $this->apiKey = $apiKey;
        $this->userName = $userName;
    }
    
    /*
     * Validate API access.
     */
    public function validateAccess()
    {
        // make sure username and key is valid
        $db = Database::getDatabase();
        $userId = $db->getValue('SELECT id FROM users WHERE apikey = '.$db->quote($this->apiKey).' AND username = '.$db->quote($this->userName).' AND status = \'active\' LIMIT 1');
        $this->userId = $userId;
        
        return $userId;
    }
    
    /*
     * List all files within the account.
     */
    public function apiList($params)
    {
        // get all files for the current account
        $db = Database::getDatabase();
        $items = $db->getRows('SELECT * FROM file WHERE userId = '.(int)$this->userId.' ORDER BY uploadedDate DESC');
        $rs = array();
        foreach($items AS $item)
        {
            $file = file::hydrate($item);
            $rs[$file->id] = $file->getFullShortUrl();
        }

        return self::produceSuccess(array('total'=>COUNT($rs), 'data'=>$rs));
    }
    
    /*
     * Get detailed information for a specific file.
     */
    public function apiInfo($params)
    {
        // make sure we have the file_id
        $file_id = null;
        if(isset($params['file_id']))
        {
            $file_id = (int)$params['file_id'];
        }
        
        if((int)$file_id == 0)
        {
            return self::produceError('Error: Method requires a valid file_id within this account.');
        }
        
        // get all files for the current account
        $db = Database::getDatabase();
        $rs = $db->getRow('SELECT file.id AS file_id, originalFileName AS file_name, shortUrl AS short_url, fileType AS file_type, extension, fileSize AS file_size, visits AS total_downloads, uploadedDate AS uploaded_date, file_status.label AS status, folderId AS folder_id, fileHash AS file_hash FROM file LEFT JOIN file_status ON file.statusId = file_status.id WHERE userId = '.(int)$this->userId.' AND file.id='.(int)$file_id.' ORDER BY uploadedDate DESC');
        if(!$rs)
        {
            return self::produceError('Error: Method requires a valid file_id within this account.');
        }

        // only keep kvp
        $data = array();
        foreach($rs AS $k=>$v)
        {
            if(is_int($k))
            {
                continue;
            }
            $data[$k] = $v;
        }
        
        // add on full url, stats url and delete url
        $file = file::loadById($file_id);
        $data['full_url'] = $file->getFullShortUrl();
        $data['stats_url'] = $file->getStatisticsUrl();
        $data['delete_url'] = $file->getDeleteUrl();
        $data['info_url'] = $file->getShortInfoUrl();
        
        return self::produceSuccess($data);
    }
    
    /*
     * Delete a file.
     */
    public function apiDelete($params)
    {
        // make sure we have the file_id
        $file_id = null;
        if(isset($params['file_id']))
        {
            $file_id = (int)$params['file_id'];
        }
        
        if((int)$file_id == 0)
        {
            return self::produceError('Error: Method requires a valid file_id within this account.');
        }
        
        // load file
        $file = file::loadById($file_id);
        
        // ensure the current user owns the file
        if($file->userId != $this->userId)
        {
            return self::produceError('Error: Method requires a valid file_id within this account.');
        }
        
        // make sure the file is active
        if($file->statusId != 1)
        {
            return self::produceError('Error: File is not active.');
        }
        
        // remove file
        $rs = $file->removeByUser();
        if(!$rs)
        {
            return self::produceError('Error: There was a problem removing the file.');
        }
        
        $data = 'File deleted.';
        return self::produceSuccess($data);
    }
    
    /*
     * Create success output.
     */
    public static function produceSuccess($dataArr)
    {
        $rs = array();
        $rs['success'] = true;
        $rs['response_time'] = time();
        $rs['result'] = $dataArr;
        
        return json_encode($rs);
    }
    
    public static function outputSuccess($dataArr)
    {
        $successStr = self::produceSuccess($dataArr);
        echo $successStr;
        exit;
    }
    
    /*
     * Create error output.
     */
    public static function produceError($errorMsg)
    {
        $rs = array();
        $rs['error'] = true;
        $rs['error_time'] = time();
        $rs['error_msg'] = $errorMsg;
        
        return json_encode($rs);
    }
    
    public static function outputError($errorMsg)
    {
        $errorStr = self::produceError($errorMsg);
        echo $errorStr;
        exit;
    }
}
