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

// Checking area_id
if(!isset($_GET['area_id']))
{
	echo 'Area must be selected.';
	exit();
}

$area = getArea($_GET['area_id']);
if(!count($area))
{
	echo 'Area must be selected.';
	exit();
}

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
		$editor = new editor('mrbs_room', $_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor=1');
		$editor->setHeading(_('New room for').' '.$area['area_name']);
		$editor->setSubmitTxt(_('Add'));
	}
	else
	{
		$editor = new editor('mrbs_room', $_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor=1', $id);
		$editor->setHeading(_('Change room'));
		$editor->setSubmitTxt(_('Change'));
	}
	
	$editor->setDBFieldID('id');
	$editor->showID (TRUE);
	
	$editor->makeNewField('room_name', _('Area name'), 'text');
	$editor->makeNewField('area_id', _('Area belonging'), 'select', array('defaultValue' => $area['area_id']));
	$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by `area_name`");
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('area_id', $R_area['area_id'], $R_area['area_name']);
	
	$editor->getDB();
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: admin_room.php?area_id='.$editor->vars['area_id']['value']);
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
}
else
{
	// List
	
	include "include/admin_middel.php";
	
	echo '<h2>'._('Rooms for').' '.$area['area_name'].'</h2>'.chr(10).chr(10);
	$QUERY = mysql_query("select * from `mrbs_room` where area_id = '".$area['area_id']."' order by room_name");
	
	if($login['user_access_areaadmin'])
		echo '- <a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor=1">'._('New room').'</a><br><br>'.chr(10);
	
	echo '<table class="prettytable">'.chr(10).chr(10);
	echo '	<tr>'.chr(10);
	echo '		<th>'._('ID').'</th>'.chr(10);
	echo '		<th>'._('Room name').'</th>'.chr(10);
	echo '		<th>'._('Area').'</th>'.chr(10);
	if($login['user_access_areaadmin'])
		echo '		<th>'._('Options').'</th>'.chr(10);
	echo '	</tr>'.chr(10).chr(10);
	while($ROW = mysql_fetch_assoc($QUERY))
	{
		echo '	<tr>'.chr(10);
		echo '		<td><b>'.$ROW['id'].'</b></td>'.chr(10);
		echo '		<td>'.$ROW['room_name'].'</td>'.chr(10);
		echo '		<td>';
		$Q_area = mysql_query("select * from `mrbs_area` where id = '".$ROW['area_id']."'");
		if(!mysql_num_rows($Q_area))
			echo '<i>'._('Not found').'</i>';
		else
			echo mysql_result($Q_area, 0, 'area_name');
		echo '</td>'.chr(10);
		if($login['user_access_areaadmin'])
			echo '		<td><a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor=1&amp;id='.$ROW['id'].'">'._('Edit').'</td>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
	}
}

?>