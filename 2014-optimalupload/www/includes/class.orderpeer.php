<?php

class OrderPeer
{

    // Singleton object. Leave $me alone.
    private static $me;

    static function create($user_id, $payment_hash, $days, $amount, $upgradeFileId)
    {
        $upgradeUserId = null;
        if ((isset($_SESSION['plugin_rewards_aff_user_id'])) && ($user_id != (int) $_SESSION['plugin_rewards_aff_user_id']))
        {
            $upgradeUserId = (int)$_SESSION['plugin_rewards_aff_user_id'];
        }

        $dbInsert = new DBObject("premium_order",
                        array("user_id", "payment_hash", "days",
                            "amount", "order_status", "date_created", "upgrade_file_id", "upgrade_user_id"));
        $dbInsert->user_id = $user_id;
        $dbInsert->payment_hash = $payment_hash;
        $dbInsert->days = $days;
        $dbInsert->amount = $amount;
        $dbInsert->order_status = 'pending';
        $dbInsert->date_created = date("Y-m-d H:i:s", time());
        $dbInsert->upgrade_file_id = $upgradeFileId;
        if((int)$upgradeFileId)
        {
            // lookup user
            $db   = Database::getDatabase();
            $upgradeUserId = (int)$db->getValue('SELECT userId FROM file WHERE id='.(int)$upgradeFileId.' LIMIT 1');
        }
        
        $dbInsert->upgrade_user_id = $upgradeUserId;
        if ($dbInsert->insert())
        {
            return $dbInsert;
        }

        return false;
    }

    static function loadByPaymentTracker($paymentHash)
    {
        $orderObj = new Order();
        $orderObj->select($paymentHash, 'payment_hash');
        if (!$orderObj->ok())
        {
            return false;
        }

        return $orderObj;
    }

}
