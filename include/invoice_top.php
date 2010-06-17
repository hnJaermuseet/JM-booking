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
	Invoice-includes
*/

include_once("glob_inc.inc.php");

if(!$login['user_invoice'])
{
	print_header($day, $month, $year, $area);
	echo '<h1>'._('Invoice').'</h1>'.chr(10).chr(10);
	echo _('No access');
	
	exit();
}

require "libs/invoice.class.php";

filterMakeAlternatives();
$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '1');
$filters = addFilter($filters, 'time_start', 'current', '>');
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_soon = mysql_num_rows(mysql_query($SQL));

$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '1');
$filters = addFilter($filters, 'time_start', 'current', '<');
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_tobemade = mysql_num_rows(mysql_query($SQL));

$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '2');
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_tobemade_ready = mysql_num_rows(mysql_query($SQL));

$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '3');
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_exported = mysql_num_rows(mysql_query($SQL));

unset($SQL, $filters);

/* Functions for printing of bookinglists */

function entrylist_invoice_soon ($SQL)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!mysql_num_rows($Q))
	{
		echo _('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '._('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Area').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black; text-align: right;"><b>Sum</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		$entry_ids = array();
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			$entry_ids[] = $entry['entry_id'];
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a></td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Invoice
			echo '  <td style="border: 1px solid black;">kr '.smarty_modifier_commify($entry['faktura_belop_sum'],2,","," ").'</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}

function entrylist_invoice_tobemade_ready ($SQL)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!mysql_num_rows($Q))
	{
		echo _('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '._('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Area').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Sum</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>&nbsp;</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Satt faktureringsklar av</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		$entry_ids = array();
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			$entry_ids[] = $entry['entry_id'];
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a></td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Invoice
			echo '  <td style="border: 1px solid black; text-align: right;">kr '.smarty_modifier_commify($entry['faktura_belop_sum'],2,","," ").'</td>'.chr(10);
			
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="entry_invoice.php?entry_id='.$entry['entry_id'].'">';
			echo iconHTML('coins').' ';
			echo 'Vis fakturagrunnlag</a>';
			echo '</td>'.chr(10); 
			
			// Searching for who did set the entry ready for invoice
			$Q_user = mysql_query("SELECT * FROM `entry_log`
				WHERE `log_action` = 'edit' AND `log_action2` = 'invoice_readyfor' AND `entry_id` = '".$entry['entry_id']."'
				ORDER BY `log_time` DESC LIMIT 1");
			$user = array();
			if(mysql_num_rows($Q_user))
				$user = getUser(mysql_result($Q_user, '0', 'user_id'));
			echo '  <td style="border: 1px solid black;">';
			if(count($user))
				echo '<a href="user.php?user_id='.$user['user_id'].'">'.$user['user_name'].'</a>';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10); 
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
		
		echo '<div style="font-size: 1.6em; margin-top: 20px; margin-left: 10px;">'.
		'<a href="invoice_export.php?';
		$counter = 0;
		foreach($entry_ids as $id)
		{
			$counter++;
			echo 'entry_id[]='.$id;
			if($counter != count($entry_ids))
				echo '&amp;';
		}
		
		echo '">'.
		'<img src="img/Crystal_Clear_action_db_comit.png" style="border: 0px solid black;" height="32"> '.
		'Eksporter til Komfakt'.
		'</a></div>';
	}
}

function entrylist_invoice_tobemade ($SQL, $area_spesific = false)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!mysql_num_rows($Q))
	{
		echo _('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '._('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Area').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Sum</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>&nbsp;</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a></td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Invoice
			echo '  <td style="border: 1px solid black; text-align: right;">kr '.smarty_modifier_commify($entry['faktura_belop_sum'],2,","," ").'</td>'.chr(10);
			
			// Set ready
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="invoice_setready.php?entry_id='.$entry['entry_id'];
			if($area_spesific)
				echo '&amp;return=invoice_tobemade_area';
			else
				echo '&amp;return=invoice_tobemade';
			echo '">'.
			' Sett faktureringsklar '.iconHTML ('arrow_right').'</a>';
			echo '</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}

function entrylist_invoice_exported ($SQL)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!mysql_num_rows($Q))
	{
		echo _('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '._('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Area').'</b></td>'.chr(10);
		//echo '  <td style="border: 1px solid black;"><b>&nbsp;</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a></td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Set ready
			/*echo '  <td style="border: 1px solid black;">';
			echo '<a href="invoice_setready.php?entry_id='.$entry['entry_id'].'">'.
			' Sett faktureringsklar '.iconHTML ('arrow_right').'</a>';
			echo '</td>'.chr(10);
			*/
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}