<?php

require_once('../../../includes/master.inc.php');

// load plugin details
$pluginConfig   = pluginHelper::pluginSpecificConfiguration('paypal');
$pluginSettings = $pluginConfig['data']['plugin_settings'];
$paypalEmail    = '';
if (strlen($pluginSettings))
{
    $pluginSettingsArr = json_decode($pluginSettings, true);
    $paypalEmail       = $pluginSettingsArr['paypal_email'];
}

if (!isset($_REQUEST['days']))
{
    redirect(WEB_ROOT . '/index.html');
}

// require login
if (!isset($_REQUEST['i']))
{
    $Auth->requireUser(WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION);
    $userId    = $Auth->id;
    $username  = $Auth->username;
    $userEmail = $Auth->email;
}
else
{
    $user = UserPeer::loadUserByIdentifier($_REQUEST['i']);
    if (!$user)
    {
        die('User not found!');
    }

    $userId    = $user->id;
    $username  = $user->username;
    $userEmail = $user->email;
}

$days = (int) (trim($_REQUEST['days']));

$fileId = null;
if (isset($_REQUEST['f']))
{
    $file = file::loadByShortUrl($_REQUEST['f']);
    if ($file)
    {
        $fileId = $file->id;
    }
}

// create order entry
$orderHash = MD5(time() . $userId);
$amount    = number_format(constant('SITE_CONFIG_COST_FOR_' . $days . '_DAYS_PREMIUM'), 2);
$order     = OrderPeer::create($userId, $orderHash, $days, $amount, $fileId);
if ($order)
{    
    // redirect to the payment gateway
    $desc      = $days . ' days extension for ' . $username;
    $paypalUrl = 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&notify_url=' . urlencode(PLUGIN_WEB_ROOT . '/' . $pluginConfig['data']['folder_name'] . '/site/_payment_ipn.php') . '&email=' . urlencode($userEmail) . '&return=' . urlencode(WEB_ROOT . '/payment_complete.' . SITE_CONFIG_PAGE_EXTENSION) . '&business=' . urlencode($paypalEmail) . '&item_name=' . urlencode($desc) . '&item_number=1&amount=' . urlencode($amount) . '&no_shipping=2&no_note=1&currency_code=' . SITE_CONFIG_COST_CURRENCY_CODE . '&lc=' . substr(SITE_CONFIG_COST_CURRENCY_CODE, 0, 2) . '&bn=PP%2dBuyNowBF&charset=UTF%2d8&custom=' . $orderHash;
    redirect($paypalUrl);
    
}