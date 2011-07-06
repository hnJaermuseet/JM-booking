<?php

// Settings relevant to AuthPluginJaermuseet

## Database settings
// This mysql user must have access to the jm-booking database
$wgDBtype           = "mysql";
$wgDBserver         = "localhost";
$wgDBname           = "";
$wgDBuser           = "";
$wgDBpassword       = "";

// Restrict wiki to a set of users, optional
/*
$authpluginjmTillatteBrukere = array
	(
		'username-jm-booking', 
		'another user',
	);
*/

/* Authentication */
require_once("extensions/AuthPluginJaermuseet.php");
$wgAuth = new AuthPluginJaermuseet();
