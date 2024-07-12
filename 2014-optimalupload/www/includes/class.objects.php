<?php

// Stick your DBOjbect subclasses in here (to help keep things tidy).

class User extends DBObject
{

    public function __construct($id = null)
    {
        parent::__construct('users', array('username', 'password', 'level_id', 'email', 'paidExpiryDate', 'firstname', 'lastname', 'title', 'languageId', 'storageLimitOverride', 'privateFileStatistics'), $id);
    }
    
    public function deleteUserData()
    {
        // connect db
        $db = Database::getDatabase(true);
        
        // remove database file records, this will not delete files, assume this is already done
        if((int)$this->id > 0)
        {
            // stats
            $db->query('DELETE FROM stats WHERE file_id IN (SELECT id FROM file WHERE userId = '.(int)$this->id.')');
            
            // files
            $db->query('DELETE FROM file WHERE userId = '.(int)$this->id);
        }
        
        // remove folders
        $db->query('DELETE FROM file_folder WHERE userId = '.(int)$this->id);
        
        // remove sessions
        $db->query('DELETE FROM sessions WHERE user_id = '.(int)$this->id);
        
        // user record
        $db->query('DELETE FROM users WHERE id = '.(int)$this->id);
        
        return true;
    }

}

class Order extends DBObject
{

    public function __construct($id = null)
    {
        parent::__construct('premium_order', array('user_id', 'payment_hash', 'days', 'amount', 'order_status', 'upgrade_file_id', 'upgrade_user_id'), $id);
    }

}
