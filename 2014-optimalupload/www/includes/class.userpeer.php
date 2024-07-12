<?php

class UserPeer
{

    // Singleton object. Leave $me alone.
    private static $me;

    static function create($username, $password, $email, $title, $firstname, $lastname, $accType = 'user')
    {
        $dbInsert                 = new DBObject("users", array("username", "password", "email",
            "title", "firstname", "lastname", "datecreated",
            "createdip", "status", "level_id", "paymentTracker", "identifier")
        );
        $dbInsert->username       = $username;
        $dbInsert->password       = MD5($password);
        $dbInsert->email          = $email;
        $dbInsert->title          = $title;
        $dbInsert->firstname      = $firstname;
        $dbInsert->lastname       = $lastname;
        $dbInsert->datecreated    = sqlDateTime();
        $dbInsert->createdip      = getUsersIPAddress();
        $dbInsert->status         = 'active';
        $dbInsert->level_id       = 1;
        $dbInsert->paymentTracker = MD5(time() . $username);
        $dbInsert->identifier     = MD5(time() . $username . $password);
        if ($dbInsert->insert())
        {
            return $dbInsert;
        }

        return false;
    }

    static function createPasswordResetHash($userId)
    {
        $user = true;

        // make sure it doesn't already exist on an account
        while ($user != false)
        {
            // create hash
            $hash = MD5(microtime() . $userId);

            // lookup by hash
            $user = self::loadUserByPasswordResetHash($hash);
        }

        // update user with hash
        $db = Database::getDatabase(true);
        $db->query('UPDATE users SET passwordResetHash = :passwordResetHash WHERE id = :id', array('passwordResetHash' => $hash, 'id'                => $userId));

        return $hash;
    }

    static function loadUserById($id)
    {
        $userObj = new User();
        $userObj->select($id, 'id');
        if (!$userObj->ok())
        {
            return false;
        }

        return $userObj;
    }

    static function loadUserByUsername($username)
    {
        $userObj = new User();
        $userObj->select($username, 'username');
        if (!$userObj->ok())
        {
            return false;
        }

        return $userObj;
    }

    static function loadUserByPaymentTracker($paymentTracker)
    {
        $userObj = new User();
        $userObj->select($paymentTracker, 'paymentTracker');
        if (!$userObj->ok())
        {
            return false;
        }

        return $userObj;
    }

    static function loadUserByEmailAddress($email)
    {
        $userObj = new User();
        $userObj->select($email, 'email');
        if (!$userObj->ok())
        {
            return false;
        }

        return $userObj;
    }

    static function loadUserByPasswordResetHash($hash)
    {
        $userObj = new User();
        $userObj->select($hash, 'passwordResetHash');
        if (!$userObj->ok())
        {
            return false;
        }

        return $userObj;
    }

    static function loadUserByIdentifier($identifier)
    {
        $userObj = new User();
        $userObj->select($identifier, 'identifier');
        if (!$userObj->ok())
        {
            return false;
        }

        return $userObj;
    }

    static function upgradeUser($userId, $days)
    {
        // load user
        $user          = UserPeer::loadUserById($userId);
        
        // upgrade user
        $newExpiryDate = strtotime('+' . $days . ' days');
        
        // exntend user
        if ($user->level_id >= 2)
        {
            // add onto existing period
            $existingExpiryDate = strtotime($user->paidExpiryDate);

            // if less than today just revert to now
            if ($existingExpiryDate < time())
            {
                $existingExpiryDate = time();
            }

            $newExpiryDate = (int) $existingExpiryDate + (int) ($days * (60 * 60 * 24));
        }

        // figure out new account type
        $newUserType = 2;
        if ($user->level_id > 2)
        {
            $newUserType = $user->level_id;
        }

        // update user account to premium
        $dbUpdate                 = new DBObject("users", array("level_id", "lastPayment", "paidExpiryDate"), 'id');
        $dbUpdate->level_id       = $newUserType;
        $dbUpdate->lastPayment    = date("Y-m-d H:i:s", time());
        $dbUpdate->paidExpiryDate = date("Y-m-d H:i:s", $newExpiryDate);
        $dbUpdate->id             = $userId;
        $effectedRows             = $dbUpdate->update();
        if ($effectedRows === false)
        {
            // failed to update user
            return false;
        }

        return true;
    }
    
    static function downgradeExpiredAccounts()
    {
        // connect db
        $db = Database::getDatabase(true);

        // downgrade paid accounts
        $sQL = 'UPDATE users SET level_id = 1 WHERE level_id = 2 AND UNIX_TIMESTAMP(paidExpiryDate) < ' . time();
        $rs  = $db->query($sQL);
    }
    
    static function getLevelLabel($levelId)
    {
        // connect db
        $db = Database::getDatabase(true);
        
        return $db->getValue('SELECT label FROM user_level WHERE level_id = '.(int)$levelId.' LIMIT 1');
    }

}
