<?php

$require_login = false;
$path_site_config = '../config/site.config.php';

// MySQL and other stuff, using the same as the rest of the system
require_once dirname(__FILE__).'/../glob_inc.inc.php';

echo 'DB fix for database changes named 01.07.2011, committed 04.07.2011';
echo '<br><br>';

$Q_exchangesync = mysql_query("select * from `entry_exchangesync`");
echo mysql_error();
while($R_sync = mysql_fetch_assoc($Q_exchangesync))
{
	echo 'Entry '.$R_sync['entry_id'];
	$entry = getEntry($R_sync['entry_id']);
	
	if($entry['rev_num'] != $R_sync['entry_rev'])
		echo ' - Unable to fix! Diff rev_num';
	else
	{
		echo ' - Fixing sync_from and sync_to';
		mysql_query("
			UPDATE `entry_exchangesync` 
			SET 
				`sync_from` = '".$entry['time_start']."',
				`sync_to` = '".$entry['time_end']."' 
			WHERE 
				`user_id` = '".$R_sync['user_id']."' AND 
				`entry_id` = '".$entry['entry_id']."' AND 
				`entry_rev` = '".$R_sync['entry_rev']."'
			LIMIT 1 ;");
	}
	
	echo '<br>'.chr(10);
}