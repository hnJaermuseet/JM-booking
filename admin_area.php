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

include "include/admin_top.php";
require "libs/editor.class.php";
$section = 'area';

if(isset($_GET['editor']))
{
	if(!$login['user_access_areaadmin'])
	{
		showAccessDenied($day, $month, $year, $area, true);
		exit ();
	}
	
	$id = 0;
	if(isset($_GET['id']) && is_numeric($_GET['id']))
		$id = (int)$_GET['id'];
	if(isset($_POST['id']) && is_numeric($_POST['id']))
		$id = (int)$_POST['id'];
	
	if($id <= 0)
	{
		$editor = new editor('mrbs_area', $_SERVER['PHP_SELF'].'?editor=1');
		$editor->setHeading(_('New area'));
		$editor->setSubmitTxt(_('Add'));
	}
	else
	{
		$editor = new editor('mrbs_area', $_SERVER['PHP_SELF'].'?editor=1', $id);
		$editor->setHeading(_('Change area'));
		$editor->setSubmitTxt(_('Change'));
	}
	
	$editor->setDBFieldID('id');
	$editor->showID (TRUE);
	
	$editor->makeNewField('area_name', _('Area name'), 'text');
	
	$editor->makeNewField('area_group', 'Standard brukergruppe', 'select');
	$editor->addChoice('area_group', 0, 'Ingen');
	$Q_groups = mysql_query("select group_id, group_name from `groups` order by `group_name`");
	while($R = mysql_fetch_assoc($Q_groups))
		$editor->addChoice('area_group', $R['group_id'], $R['group_name']);
	
	
	$editor->makeNewField('importdatanova_shop_id', _('Datanova import').' - '._('Shop id'), 'text');
	$editor->makeNewField('importdatanova_alert_email', _('Datanova import').' - '._('Alert email(s)').'*', 'text');
	
	$editor->getDB();
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: admin_area.php');
				exit();
			}
			else
			{
				echo 'Error occured while performing query on database:<br>'.chr(10),
				//echo '<b>Error:</b> '.$editor->error();
				exit();
			}
		}
	}
	
	include "include/admin_middel.php";
	$editor->printEditor();
	
	echo '<br /><br />*'.
		_('Email(s) receiving alerts when new goods are detected for this shop.').'<br />'.
		_('Can be multiple emails seperated by space, comma or semicolon.');
}
else
{
	// List
	
	include "include/admin_middel.php";
	
	echo '<h2>'._('Area').'</h2>'.chr(10).chr(10);
	$QUERY = mysql_query("select * from `mrbs_area` order by area_name");
	
	if($login['user_access_areaadmin'])
		echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1">'.iconHTML('house_add').' '._('New area').'</a><br><br>'.chr(10);
	
	echo '<table class="prettytable">'.chr(10).chr(10);
	echo '	<tr>'.chr(10);
	echo '		<th><b>'._('ID').'</b></th>'.chr(10);
	echo '		<th><b>'._('Area').'</b></th>'.chr(10);
	if($login['user_access_areaadmin'])
		echo '		<th><b>'._('Options').'</b></th>'.chr(10);
	echo '	</tr>'.chr(10).chr(10);
	while($ROW = mysql_fetch_assoc($QUERY))
	{
		echo '	<tr>'.chr(10);
		echo '		<td><b>'.$ROW['id'].'</b></td>'.chr(10);
		echo '		<td>'.iconHTML('house').' '.$ROW['area_name'].'</td>'.chr(10);
		echo '		<td>'.
		'<a href="admin_room.php?area_id='.$ROW['id'].'">'.
		iconHTML('shape_square').' '._('Show rooms').'</a>';
		if($login['user_access_areaadmin'])
			echo ' -:- '.
			'<a href="'.$_SERVER['PHP_SELF'].'?editor=1&amp;id='.$ROW['id'].'">'.
			iconHTML('house_go').' '._('Edit');
		echo '</td>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
	}
}

?>