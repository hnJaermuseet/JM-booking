<?php

/*
JM-booking
Copyright (C) 2007-2010  Jaermuseet <http://www.jaermuseet.no>
Contact: <hn@jaermuseet.no> 
Project: <http://github.com/hnJaermuseet/JM-booking>

Based on ARBS, Advanced Resource Booking System, copyright (C) 2005-2007 
ITMC der TU Dortmund <http://sourceforge.net/projects/arbs/>. ARBS is based 
on MRBS by Daniel Gardner <http://mrbs.sourceforge.net/>.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/*

IMPORTING VISITING NUMBER FROM DATANOVA BACK OFFICE/WEBREPORT
Hallvard Nygård <hn@jaermuseet> for Jærmuseet, 
a Norwegian science center.

Jærmuseet
http://jaermuseet.no/

*/

$alert_admin = false;
$require_login = false;
$path_site_config = '../config/site.config.php';

set_time_limit(600);

require_once dirname(__FILE__).'/../functions/importdatanova.php';

function printout ($txt)
{
	global $year;
	if(isset($year))
		$year_txt = ' ['.$year.']';
	else
		$year_txt = '';
	
	if(php_sapi_name() == 'cli') // Command line
	{
		echo date('Y-m-d H:i:s').$year_txt.' '.$txt."\r\n";
	}
	else
	{
		echo str_replace(' ', '&nbsp;', 
			date('Y-m-d H:i:s').$year_txt.' '.$txt).'<br />'.chr(10);
	}
}
function printout_mysqlerror ()
{
	if(mysql_error())
		printout(mysql_error());
}

// MySQL and other stuff, using the same as the rest of the system
require_once dirname(__FILE__).'/../glob_inc.inc.php';

if(php_sapi_name() != 'cli' && $importdatanova_from_clionly)
{
	echo 'Only accessable from command line.';
	alertAdmin(array($_SERVER['REMOTE_ADDR'].' tried to access importdatanova from web.'));
	exit;
}



try
{
	// Checking config
	if(!isset($importdatanova_baseurl))
		throw new Exception('Missing in config: $importdatanova_baseurl not set. Please put this in site config (see default.config.php for example)');
	if(!isset($importdatanova_login))
		throw new Exception('Missing in config: $importdatanova_login not set. Please put this in site config (see default.config.php for example)');
	if(
		!isset($importdatanova_login['username']) ||
		!isset($importdatanova_login['password']) ||
		!isset($importdatanova_login['shop'])
	)
		throw new Exception('Config failed: $importdatanova_login is not correct. Please correct this in site config (see default.config.php for example)');
	
	$data_rows = array();
	for($year = 2008; $year <= date('Y'); $year++)
	{
		// Get HTML and analyze
		printout('Retriving webreport from baseurl '.$importdatanova_baseurl);
		$webreport =
			datanova_webreport_getreport (
				$importdatanova_baseurl, 
				$importdatanova_login['username'], 
				$importdatanova_login['password'], 
				$importdatanova_login['shop'],
				$year);
		printout('Webreport retrived.');

		printout('Parsing retrived HTML.');
		$data_rows_year = datanova_webreport_parser($webreport);
		
		$data_rows = array_merge_recursive($data_rows, $data_rows_year);
		printout('Datarows parsed: '.count($data_rows_year) .' (total: '.count($data_rows).')');
	}
	
	unset($year); // Not year spesific any more, this fixes printout
	
	// Clean database
	printout('Cleaning database tables (import_dn_tall and import_dn_tall_ikkeimportert)');
	mysql_query("TRUNCATE TABLE `import_dn_tall`");
	if(mysql_error())
		throw new Exception('MySQL error: '.mysql_error());
	printout('- import_dn_tall cleaned');
	mysql_query("TRUNCATE TABLE `import_dn_tall_ikkeimportert`");
	if(mysql_error())
		throw new Exception('MySQL error: '.mysql_error());
	printout('- import_dn_tall_ikkeimportert cleaned');
	
	// Getting shops => we are using all areas with shop_id set
	$shops = array();
	$areas = array();
	$Q = mysql_query("SELECT id as area_id, area_name, importdatanova_shop_id as shop_id FROM `mrbs_area` where importdatanova_shop_id != 0 && importdatanova_shop_id != ''");
	printout('Shops being imported (areas that has shop_id set):');
	while($R = mysql_fetch_assoc($Q))
	{
		$shops[$R['shop_id']] = $R['area_id'];
		$areas[$R['area_id']] = $R;
		printout('- Shop id '.$R['shop_id'].', '.$R['area_name'].' (area id '.$R['area_id'].')');
	}
	
	// Analyze data
	printout('Analyzing data rows');
	$data_analyzed  = datanova_analyze_data ($data_rows, $shops);
	printout('Putting new data in database');
	$numbers_new    = datanova_databaseinsert($data_analyzed['numbers_new']);
	printout('New numbers imported: '.$numbers_new);
	
	printout('Updating data in database');
	$numbers_updated = datanova_databaseupdate($data_analyzed['numbers_update']);
	printout('Updated numbers (delete+insert): '.$numbers_updated);
	
	printout('Putting unknown goods (goods id & shop id combinations) in database for review');
	$numbers_updated_notimported = datanova_databaseupdate_notimported($data_analyzed['unknowns']);
	printout('Numbers of unknown goods: '.$numbers_updated_notimported);
	
	printout('Unknown goods ignored (goods id, shop id and day combinations): '.count($data_analyzed['numbers_ignored_notreged']));
	printout('Ignored: '.count($data_analyzed['numbers_ignored_reged']));
	printout('Already imported: '.count($data_analyzed['numbers_alreadyimported']));

	if(count($data_analyzed['unknowns']))
	{
		printout('');
		printout('Unknown goods:');
		$unknowns_per_shop = array();
		foreach($data_analyzed['unknowns'] as $unknown)
		{
			$values = array();
			foreach($unknown as $key => $val) {
				$values[] = $key.'='.$val; 
			}
			printout(implode(', ', $values));
			
			if(!isset($unknowns_per_shop[$unknown['shop_id']]))
				$unknowns_per_shop[$unknown['shop_id']] = array();
			$unknowns_per_shop[$unknown['shop_id']][] = $unknown;
		}
		
		printout('');
		printout('Unknown goods per shop:');
		foreach($unknowns_per_shop as $shop_id => $unknowns)
		{
			$Q_shop = mysql_query("select * from `import_dn_shops` where shop_id='".$shop_id."'");
			if(mysql_num_rows($Q_shop))
			{
				$shop = mysql_result($Q_shop, 0, 'shop_name').' (shop id '.$shop_id.')';
			}
			else
				$shop = 'Unknown shop (shop id '.$shop_id.')';
			
			printout($shop.': '.count($unknowns).' unknowns');
			
			// TODO: alert people!
		}
	}
}
catch(Exception $e)
{
	printout('Exception: '.$e->getMessage());
	$alert_admin = true;
	$alerts[]   = 'Exception: '.$e->getMessage();
}

if($alert_admin)
	alertAdmin($alerts);

function alertAdmin($alerts = array())
{
	emailSendAdmin ('Problems in importdatanova', 
		'Please see log around '. date('H:i d-m-Y').chr(10).
		'Alerts:'.chr(10).implode(chr(10), $alerts));
}