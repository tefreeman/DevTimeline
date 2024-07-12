<?php

// includes and security
include_once('../_local_auth.inc.php');

$ip_address = trim($_REQUEST['ip_address']);
$ban_type   = $_REQUEST['ban_type'];
$ban_notes  = trim($_REQUEST['ban_notes']);

// prepare result
$result = array();
$result['error'] = false;
$result['msg']   = '';

if(strlen($ip_address) == 0)
{
    $result['error'] = true;
    $result['msg']   = adminFunctions::t("enter_the_ip_address", "Please enter the IP address.");
}
elseif (!isValidIP($ip_address))
{
    $result['error'] = true;
    $result['msg']   = adminFunctions::t("ip_address_invalid_try_again", "The format of the IP you've entered is invalid, please try again.");
}
elseif (_CONFIG_DEMO_MODE == true)
{
    $result['error'] = true;
    $result['msg']   = adminFunctions::t("no_changes_in_demo_mode");
}
else
{
    $row = $db->getRow('SELECT id FROM banned_ips WHERE ipAddress = ' . $db->quote($ip_address));
    if (is_array($row))
    {
        $result['error'] = true;
        $result['msg']   = adminFunctions::t("ip_address_already_blocked", "The IP address you've entered is already blocked.");
    }
    else
    {
        // add the banned IP
	$dbInsert = new DBObject("banned_ips", array("ipAddress", "banType", "banNotes", "dateBanned"));
	$dbInsert->ipAddress 		= $ip_address;
	$dbInsert->banType 		= $ban_type;
	$dbInsert->banNotes 		= $ban_notes;
	$dbInsert->dateBanned	 	= sqlDateTime();
	if(!$dbInsert->insert())
	{
            $result['error'] = true;
            $result['msg']   = adminFunctions::t("error_problem_record", "There was a problem banning the IP address, please try again.");
	}
        else
        {
            $result['error'] = false;
            $result['msg']   = 'IP address '.$ip_address.' has been banned.';
        }
    }
}

echo json_encode($result);
exit;
