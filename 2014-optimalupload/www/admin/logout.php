<?php
require_once('_local_auth.inc.php');
$Auth->logout();
header("location: login.php");
exit;
