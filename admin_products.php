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
$section = 'products';

if(isset($_GET['editor']))
{
	if(authGetUserLevel(getUserID()) < $user_level)
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
		$editor = new editor('products', $_SERVER['PHP_SELF'].'?editor=1');
		$editor->setHeading('Nytt produkt');
		$editor->setSubmitTxt(_('Add'));
	}
	else
	{
		$editor = new editor('products', $_SERVER['PHP_SELF'].'?editor=1', $id);
		$editor->setHeading('Endre produkt');
		$editor->setSubmitTxt(_('Change'));
	}
	
	$editor->setDBFieldID('product_id');
	$editor->showID (TRUE);
	
	$editor->makeNewField('product_name', 'Produktnavn', 'text');
	$editor->makeNewField('product_price', 'Pris', 'text');
	$editor->makeNewField('product_tax', 'MVA %', 'text');
	$editor->makeNewField('product_desc', 'Beskrivelse', 'textarea');
	$editor->makeNewField('area_id', _('Area belonging'), 'select',
		array('defaultValue' => $area));
	$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by `area_name`");
	$editor->addChoice('area_id', 0, 'Alle anlegg');
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('area_id', $R_area['area_id'], $R_area['area_name']);
	
	$editor->getDB();
	
	if(isset($_POST['editor_submit']))
	{
		if(isset($_POST['product_price']))
			$_POST['product_price'] = str_replace(',', '.', $_POST['product_price']);
		if(isset($_POST['product_tax']))
			$_POST['product_tax'] = str_replace(',', '.', $_POST['product_tax']);
		
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: admin_products.php');
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
	// List with products
	
	include "include/admin_middel.php";
	
	echo '<h2>Produktregister</h2>'.chr(10).chr(10);
	$Q_products = mysql_query("select * from `products` order by area_id,product_name");
	
	if(authGetUserLevel(getUserID()) >= $user_level)
		echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1">'.
		iconHTML('package_add').' Nytt produkt</a><br><br>'.chr(10);
	else
		echo 'Du har ikke tilgang til &aring; legge til nye produkter.<br><br>'.chr(10);
	
	echo '<table class="prettytable">'.chr(10).chr(10);
	echo '	<tr>'.chr(10);
	echo '		<th>'._('Area').'</th>'.chr(10);
	echo '		<th>'._('ID').'</th>'.chr(10);
	echo '		<th>Produktnavn</th>'.chr(10);
	echo '		<th>Pris</th>'.chr(10);
	echo '		<th>MVA</th>'.chr(10);
	echo '		<th>Beskrivelse</th>'.chr(10);
	echo '		<th>'._('Options').'</th>'.chr(10);
	echo '	</tr>'.chr(10).chr(10);
	while($R_product = mysql_fetch_assoc($Q_products))
	{
		echo '	<tr>'.chr(10);
		
		echo '		<td>';
		if($R_product['area_id'] == 0)
		{
				echo iconHTML('chart_organisation').' Alle anlegg';
		}
		else
		{
			$Q_area = mysql_query("select * from `mrbs_area` where id = '".$R_product['area_id']."'");
			if(!mysql_num_rows($Q_area))
				echo '<i>'._('Not found').'</i>';
			else
				echo iconHTML('house').' '.mysql_result($Q_area, 0, 'area_name');
			echo '</td>'.chr(10);
		}
		
		echo '		<td><b>'.$R_product['product_id'].'</b></td>'.chr(10);
		
		echo '		<td>'.
			iconHTML('package').' '.
			$R_product['product_name'].
		'</td>'.chr(10);
		
		echo '		<td><b>'.$R_product['product_price'].'</b></td>'.chr(10);
		
		echo '		<td><b>'.$R_product['product_tax'].' %</b></td>'.chr(10);
		
		echo '		<td>'.nl2br($R_product['product_desc']).'</td>'.chr(10);
		
		// Options
		echo '		<td>';
		if(authGetUserLevel(getUserID()) >= $user_level)
		{
			echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1&amp;id='.$R_product['product_id'].'">'.
				iconHTML('package_go').' '.
				_('Edit').'</a>';
		}
		else
		{
			echo '<i>Ikke tilgang til &aring; endre';
		}
		
		echo '</td>'.chr(10);
		
		echo '	</tr>'.chr(10).chr(10);
	}
}

?>