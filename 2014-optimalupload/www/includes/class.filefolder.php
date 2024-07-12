<?php

class fileFolder
{

    static function getFoldersByUser($userId)
    {
        $db   = Database::getDatabase(true);
        $rows = $db->getRows('SELECT * FROM file_folder WHERE userId = ' . $db->quote($userId) . ' ORDER BY folderName ASC');

        return $rows;
    }

    static function loadById($id)
    {
        $db  = Database::getDatabase(true);
        $row = $db->getRow('SELECT * FROM file_folder WHERE id = ' . (int) $id);
        if (!is_array($row))
        {
            return false;
        }

        $folderObj = new fileFolder();
        foreach ($row AS $k => $v)
        {
            $folderObj->$k = $v;
        }

        return $folderObj;
    }

    /**
     * Remove by user
     */
    public function removeByUser()
    {
        // get db
        $db = Database::getDatabase(true);
        
        // get owner
        $accountId = $db->getValue('SELECT userId FROM file_folder WHERE id = '.(int)$this->id);
        if(!(int)$accountId)
        {
            return false;
        }

        // get all child ids
        return fileFolder::deleteFolder($this->id, $accountId);
    }
    
    static function deleteFolder($folderId, $accountId)
    {
        // get db
        $db = Database::getDatabase(true);

        // load children
        $subFolders = $db->getRows('SELECT id FROM file_folder WHERE parentId = '.(int)$folderId.' AND userId = '.(int)$accountId);
        if($subFolders)
        {
            foreach($subFolders AS $subFolder)
            {
                self::deleteFolder($subFolder['id'], $accountId);
            }
        }
        
        $db->query('UPDATE file SET folderId = 0 WHERE folderId = '.(int)$folderId);
        $db->query('DELETE FROM file_folder WHERE id = '.(int)$folderId);
        
        return true;
    }

    static function loadAllByAccount($accountId)
    {
        $db = Database::getDatabase(true);
        $rs = $db->getRows('SELECT * FROM file_folder WHERE userId = ' . $db->quote($accountId) . ' ORDER BY folderName');
        if (!is_array($rs))
        {
            return false;
        }

        return $rs;
    }
    
    static function loadAllForSelect($accountId)
    {
        $rs = array();
        $folders = self::loadAllByAccount($accountId);
        if($folders)
        {
            // first prepare local array for easy lookups
            $lookupArr = array();
            foreach($folders AS $folder)
            {
                $lookupArr[$folder{'id'}] = array('l'=>$folder['folderName'], 'p'=>$folder['parentId']);
            }
            
            // populate data
            foreach($folders AS $folder)
            {
                $folderLabelArr = array();
                $folderLabelArr[] = $folder['folderName'];
                $failSafe = 0;
                $parentId = $folder['parentId'];
                while(($parentId != NULL) && ($failSafe < 30))
                {
                    $failSafe++;
                    $folderLabelArr[] = $lookupArr[$parentId]['l'];
                    $parentId = $lookupArr[$parentId]['p'];
                }
                
                $folderLabelArr = array_reverse($folderLabelArr);
                $rs[$folder{'id'}] = implode('/', $folderLabelArr);
            }
        }
        
        // make pretty
        asort($rs);
        
        return $rs;
    }
    
    static function loadAllPublicChildren($parentFolderId = null)
    {
        $db  = Database::getDatabase(true);
        $row = $db->getRows('SELECT * FROM file_folder WHERE parentId = ' . (int) $parentFolderId .' AND isPublic = 1 ORDER BY folderName');
        if (!is_array($row))
        {
            return false;
        }

        return $row;
    }

}
