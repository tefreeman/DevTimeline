<?php
die(); // DISABLED - ENABLE AS REQUIRED FOR TESTING

// setup includes
require_once('includes/master.inc.php');

// prepare the variables
$email = "you@youremail.com";
$subject = "Test email from email_test.php";
$plainMsg = "Test email content";

// send the email
send_html_mail($email, $subject, $plainMsg, SITE_CONFIG_REPORT_ABUSE_EMAIL, $plainMsg, true);
