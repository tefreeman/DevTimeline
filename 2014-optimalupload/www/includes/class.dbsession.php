<?php

class DBSession
{

    public static function register()
    {
        ini_set('session.save_handler', 'user');
        session_set_save_handler(array('DBSession', 'open'), array('DBSession', 'close'), array('DBSession', 'read'), array('DBSession', 'write'), array('DBSession', 'destroy'), array('DBSession', 'gc'));
        
        // the following prevents unexpected effects when using objects as save handlers
		register_shutdown_function('session_write_close');
    }

    public static function open()
    {
        $db = Database::getDatabase(true);
        return $db->isConnected();
    }

    public static function close()
    {
        return true;
    }

    public static function read($id)
    {
        $db = Database::getDatabase(true);
        $db->query('SELECT `data` FROM `sessions` WHERE `id` = :id', array('id' => $id));

        return $db->hasRows() ? $db->getValue() : '';
    }

    public static function write($id, $data)
    {
        // load user id if the user is logged in
        $user_id = NULL;
        $Auth = Auth::getAuth();
        if($Auth->loggedIn())
        {
            $user_id = $Auth->id;
        }
        
        $db = Database::getDatabase(true);
        $db->query('INSERT INTO `sessions` (`id`, `data`, `updated_on`, `user_id`) values (:id, :data, :updated_on, :user_id) ON DUPLICATE KEY UPDATE data=:data, updated_on=:updated_on, user_id=:user_id', array('id'         => $id, 'data'       => $data, 'updated_on' => time(), 'user_id' => $user_id));

        return ($db->affectedRows() == 1);
    }

    public static function destroy($id)
    {
        $db = Database::getDatabase(true);
        $db->query('DELETE FROM `sessions` WHERE `id` = :id', array('id' => $id));
        return ($db->affectedRows() == 1);
    }

    /*
     * $max set in php.ini with session.gc-maxlifetime
     */
    public static function gc($max)
    {
        $db = Database::getDatabase(true);
        $db->query('DELETE FROM `sessions` WHERE `updated_on` < :updated_on', array('updated_on' => time() - $max));
        return true;
    }

    public static function crossSiteSessions()
    {
        if (isMainSite() == false)
        {
            return;
        }

        // clear any old tokens
        self::clearCrossSiteTokens();

        // database connection
        $db   = Database::getDatabase(true);
        $urls = array();

        // get all direct servers
        $rows = $db->getRows('SELECT fileServerDomainName, scriptPath FROM file_server WHERE serverType = \'direct\' AND statusId != 1');
        if ($rows)
        {
            foreach ($rows AS $row)
            {
                $urls[] = '<link rel="stylesheet" href="' . generateSessionUrl(_CONFIG_SITE_PROTOCOL . '://' . $row['fileServerDomainName'] . $row['scriptPath']) . '" type="text/css" charset="utf-8" />';
            }
        }

        $str = '';
        if (COUNT($urls))
        {
            $str = implode("\n", $urls);
        }

        return $str;
    }

    public static function clearCrossSiteTokens()
    {
        // allow only 1 month for the cross site transfers
        $db = Database::getDatabase(true);
        $db->query('DELETE FROM `session_transfer` WHERE UNIX_TIMESTAMP(date_added) < (now() - INTERVAL 1 MONTH)');
    }

    public static function validateCrossSiteSession($sid, $trk)
    {
        // validate session
        $db     = Database::getDatabase(true);
        $db->query('SELECT id FROM session_transfer WHERE transfer_key = :transfer_key AND session_id = :session_id', array('transfer_key' => $trk, 'session_id'   => $sid));
        $result = $db->getValue();
        if ($result)
        {
            $db->query('DELETE FROM session_transfer WHERE transfer_key = :transfer_key AND session_id = :session_id', array('transfer_key' => $trk, 'session_id'   => $sid));
            
            // clear any old tokens
            self::clearCrossSiteTokens();
        
            return true;
        }
        
        // clear any old tokens
        self::clearCrossSiteTokens();

        return false;
    }

}