<?php
/*
 * Does nothing other than allows for sessions to be shared cross domain
 * 
 * Should be called like this:
 * _s.php?sid={session_id}&trk={system_generated_key}
 * 
 * Use the function called 'generateSessionUrl()' to create the url.
 */

/* setup includes */
require_once('includes/master.inc.php');
