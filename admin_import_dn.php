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
$section = 'import_dn';

function no_areaid_selected () {
	$Q = mysql_query("SELECT id as area_id FROM `mrbs_area` where area_name = 'Vitenfabrikken' limit 1");
	if(mysql_num_rows($Q))
		$area_id_vitenfabrikken = mysql_result($Q, 0, 'area_id');
	else
		$area_id_vitenfabrikken = -1;
	
	$Q = mysql_query("SELECT id as area_id FROM `mrbs_area` where area_name = 'Vitengarden' limit 1");
	if(mysql_num_rows($Q))
		$area_id_vitengarden = mysql_result($Q, 0, 'area_id');
	else
		$area_id_vitengarden = -1;
	
	
	echo '<h1>Velg hvilket anlegg du vil endre innstillinger for</h1>';
	echo '<div style="font-size: 1.2em">';
	echo '- <a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area_id_vitenfabrikken.'">'.
		'Vitenfabrikken</a><br />';
	echo '- <a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area_id_vitengarden.'">'.
		'Vitengarden</a><br />';
	echo '</div>';
	exit();
}

// Checking area_id
if(!isset($_GET['area_id']))
{
	include "include/admin_middel.php";
	no_areaid_selected ();
}

$area = getArea($_GET['area_id']);
if(!count($area))
{
	include "include/admin_middel.php";
	no_areaid_selected ();
}

if(isset($_GET['editor_kat']))
{
	if(!$login['user_access_importdn'])
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
		$editor = new editor('import_dn_kategori', $_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_kat=1');
		$editor->setHeading('Ny kategori'.
			//' for '.$area['area_name']
			'');
		$editor->setSubmitTxt(_('Add'));
	}
	else
	{
		$editor = new editor('import_dn_kategori', $_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_kat=1', $id);
		$editor->setHeading('Endre kategori');
		$editor->setSubmitTxt(_('Change'));
	}
	
	$editor->setDBFieldID('kat_id');
	$editor->showID (TRUE);
	
	$editor->makeNewField('kat_navn', 'Navn på kategori', 'text');
	
	/*
	$editor->makeNewField('area_id', _('Area belonging'), 'select', array('defaultValue' => $area['area_id']));
	$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by `area_name`");
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('area_id', $R_area['area_id'], $R_area['area_name']);
	*/
	
	if(!$editor->getDB()) {
		echo 'Finner ikke det du ønsker å endre.';
		exit();
	}
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: '.$_SERVER['PHP_SELF'].'?area_id='.$editor->vars['area_id']['value']);
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
elseif(isset($_GET['editor_varereg']))
{
	if(!$login['user_access_importdn'])
	{
		showAccessDenied($day, $month, $year, $area, true);
		exit ();
	}
	
	
	if(isset($_GET['importerfil']))
	{
		$importerfil_redirect   = '&importerfil='.slashes(htmlspecialchars($_GET['importerfil'],ENT_QUOTES));
		$importerfil_redirect2  = '&amp;importerfil='.slashes(htmlspecialchars($_GET['importerfil'],ENT_QUOTES));
		$redirect_fil           = 'import-datanova.php';
	}
	else
	{
		$importerfil_redirect   = '';
		$importerfil_redirect2  = '';
		$redirect_fil           = $_SERVER['PHP_SELF'];
	}
	
	
	$id = 0;
	if(isset($_GET['id']) && is_numeric($_GET['id']))
		$id = slashes(htmlspecialchars($_GET['id'],ENT_QUOTES));
	if(isset($_POST['id']) && is_numeric($_POST['id']))
		$id = slashes(htmlspecialchars($_POST['id'],ENT_QUOTES));
	
	if($id <= 0)
	{
		$editor = new editor('import_dn_vareregister', 
		$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_varereg=1'.
		$importerfil_redirect2);
		$editor->setHeading('Ny vare for '.$area['area_name']);
		$editor->setSubmitTxt(_('Add'));
	}
	else
	{
		$editor = new editor('import_dn_vareregister', 
		$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_varereg=1&amp;id='.$id.
		$importerfil_redirect2, 
		array('vare_nr' => $id, 'area_id' => $area['area_id']));
		$editor->setHeading('Endre vare for '.$area['area_name']);
		$editor->setSubmitTxt(_('Change'));
	}
	
	$editor->setDBFieldID(array('vare_nr', 'area_id'));
	$editor->showID (TRUE);
	
	if(isset($_GET['vare_nr']))
		$vare_nr = slashes(htmlspecialchars($_GET['vare_nr'],ENT_QUOTES));
	else
		$vare_nr = '';
	if(isset($_GET['vare_navn']))
		$vare_navn = slashes(htmlspecialchars($_GET['vare_navn'],ENT_QUOTES));
	else
		$vare_navn = '';
	
	
	if($id <= 0)
	{
		$editor->makeNewField('vare_nr', 'Varenr i Datanova kasseapparat', 'text',
		array('defaultValue' => $vare_nr));
	}
	
	$editor->makeNewField('area_id', _('Area'), 'hidden', array('defaultValue' => $area['area_id']));
	
	$editor->makeNewField('navn', 'Navn på vare i Datanova kasseapparat', 'text',
		array('defaultValue' => $vare_navn));
	/*
	$editor->makeNewField('area_id', _('Area belonging'), 'select', array('defaultValue' => $area['area_id']));
	$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by `area_name`");
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('area_id', $R_area['area_id'], $R_area['area_name']);
	
	if(!$editor->getDB()) {
		echo 'Finner ikke det du ønsker å endre.';
		exit();
	}*/
	
	$editor->makeNewField('kat_id', 'Import-kategori', 'select', array('defaultValue' => 0));
	$Q_area = mysql_query("select kat_id, kat_navn from `import_dn_kategori` order by `kat_navn`");
	$editor->addChoice('kat_id', 0, 'Ignorer (varen får ikke advarsel lenger)');
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('kat_id', $R_area['kat_id'], $R_area['kat_navn']);
	
	$editor->makeNewField('barn', 'Barn/Voksen', 'select', array('defaultValue' => 0));
	$editor->addChoice('barn', 1, 'Barn');
	$editor->addChoice('barn', 0, 'Voksen');
	
	if(!$editor->getDB()) {
		echo 'Finner ikke det du ønsker å endre.';
		exit();
	}
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: '.$redirect_fil.
					'?area_id='.$area['area_id'].$importerfil_redirect);
				exit();
			}
			else
			{
				if(strpos($editor->error(), 'Duplicate entry') !== FALSE) {
					include "include/admin_middel.php";
					echo '<h1>Feil ved import</h1>';
					echo '<div class="error">'.
						'Vare med dette varenret for dettee anlegget eksiterer allerede.'.
						'</div>';
				}
				else
				{
					echo 'Error occured while performing query on database:<br>'.chr(10);
					echo '<b>Error:</b> '.$editor->error();
					echo '<br><br>Please forward this message to the system administrator.';
				}
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
	
	echo '<h1>Innstillinger for import fra kasseapparat - '.$area['area_name'].'</h1>'.chr(10).chr(10);
	
	echo '- <a href="import-datanova.php?area='.$area['area_id'].'">Tilbake til importering</a><br /><br />'.chr(10);
	
	echo 'Se artikkelen '.
		'<a href="'.wikiLink('Bookingsystemet/Import_fra_kasseapparat').'">'.
		'import fra kasseapparat'.
		'</a> på wikien for informasjon om import fra Datanova kasseapparat.';
	
	echo '<h3>Kategorier (felles for hele Jærmuseet)</h3>'.chr(10);
	if($login['user_access_importdn'])
		echo '- <a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_kat=1">'.
			'Ny kategori</a><br />'.chr(10);
	
	$QUERY = mysql_query('select * from `import_dn_kategori` '.
		//"where area_id = '".$area['area_id']."' ".
		'order by kat_navn');
	$kategorier = array();
	if(mysql_num_rows($QUERY))
	{
		echo '<table class="prettytable">'.chr(10).chr(10);
		echo '	<tr>'.chr(10);
		echo '		<th>'._('ID').'</th>'.chr(10);
		echo '		<th>Kategorinavn</th>'.chr(10);
		//echo '		<th>'._('Area').'</th>'.chr(10);
		if($login['user_access_importdn'])
			echo '		<th>'._('Options').'</th>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
		while($ROW = mysql_fetch_assoc($QUERY))
		{
			$kategorier[$ROW['kat_id']] = $ROW['kat_navn'];
			echo '	<tr>'.chr(10);
			echo '		<td><b>'.$ROW['kat_id'].'</b></td>'.chr(10);
			echo '		<td>'.$ROW['kat_navn'].'</td>'.chr(10);
			//echo '		<td>';
			//$Q_area = mysql_query("select * from `mrbs_area` where id = '".$ROW['area_id']."'");
			//if(!mysql_num_rows($Q_area))
			//	echo '<i>'._('Not found').'</i>';
			//else
			//	echo mysql_result($Q_area, 0, 'area_name');
			//echo '</td>'.chr(10);
			if($login['user_access_importdn'])
				echo '		<td><a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_kat=1&amp;id='.$ROW['kat_id'].'">'._('Edit').'</td>'.chr(10);
			echo '	</tr>'.chr(10).chr(10);
		}
		echo '</table>';
	}
	else
		echo '<div class="notice" style="width: 600px;">Ingen import-kategorier laget</div>';
	
	echo '<h3>Vareregister</h3>'.chr(10);
	echo 'Dette er varer fra kasseapparatet (Datanova) som bookingsystemet kjenner til. '.
		'Hvis det ikke står noe i kategori, så blir kommer det advarsel på varen ved import.<br />';
	
	if($login['user_access_importdn'])
		echo '- <a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_varereg=1">'.
			'Ny vare</a>'.chr(10);
	
	$QUERY = mysql_query("select * from `import_dn_vareregister` where area_id = '".$area['area_id']."' order by vare_nr");
	if(mysql_num_rows($QUERY))
	{
		echo '<table class="prettytable">'.chr(10).chr(10);
		echo '	<tr>'.chr(10);
		echo '		<th>Varenr</th>'.chr(10);
		echo '		<th>Navn*</th>'.chr(10);
		echo '		<th>Kategori</th>'.chr(10);
		echo '		<th>B / V</th>'.chr(10);
		echo '		<th>'._('Area').'</th>'.chr(10);
		if($login['user_access_importdn'])
			echo '		<th>'._('Options').'</th>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
		while($ROW = mysql_fetch_assoc($QUERY))
		{
			echo '	<tr>'.chr(10);
			echo '		<td><b>'.$ROW['vare_nr'].'</b></td>'.chr(10);
			echo '		<td>'.$ROW['navn'].'</td>'.chr(10);
			
			if(isset($kategorier[$ROW['kat_id']]))
				echo '		<td>'.$kategorier[$ROW['kat_id']].'</td>'.chr(10);
			elseif($ROW['kat_id'] == 0)
				echo '		<td><i>Ignoreres</i></td>'.chr(10);
			else
				echo '		<td><i>Ikke funnet i database</i><!-- '.$ROW['kat_id'].' --></td>'.chr(10);
			
			
			if($ROW['barn'] == '1')
				echo '		<td>Barn</td>'.chr(10);
			else
				echo '		<td>Voksen</td>'.chr(10);
				
			echo '		<td>';
			$Q_area = mysql_query("select * from `mrbs_area` where id = '".$ROW['area_id']."'");
			if(!mysql_num_rows($Q_area))
				echo '<i>'._('Not found').'</i>';
			else
				echo mysql_result($Q_area, 0, 'area_name');
			echo '</td>'.chr(10);
			if($login['user_access_importdn'])
				echo '		<td><a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;editor_varereg=1&amp;id='.$ROW['vare_nr'].'">'._('Edit').'</td>'.chr(10);
			echo '	</tr>'.chr(10).chr(10);
		}
		echo '</table>';
		echo '* = Navn fra database i Backoffice / Datanova';
	}
	else
		echo '<div class="notice" style="width: 600px;">Ingen varer registert</div>';
}

?>