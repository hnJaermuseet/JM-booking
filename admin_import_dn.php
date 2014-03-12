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

$area_okey = false;
if(isset($_GET['area_id']))
{
	$area = getArea($_GET['area_id']);
	if(count($area))
	{
		if($area['importdatanova_shop_id'] == '0')
		{
			echo 'Ingen butikknr satt for dette omr&aring;det ('.$area['area_id'].' - '.$area['area_name'].')';
			exit;
		}
		$area_okey = true;
	}
}

if(isset($_GET['action']))
	$action = $_GET['action'];
else
	$action = '';

if($action == 'kat_list')
{
	// List
	
	include "include/admin_middel.php";
	
	echo '<h1>Innstillinger for import fra kasseapparat</h1>'.chr(10).chr(10);
	
	echo '<h2>Kategorier (felles for hele J&aerlig;rmuseet)</h2>'.chr(10);
	
	echo '- <a href="'.$_SERVER['PHP_SELF'].'">Tilbake</a><br /><br />'.chr(10);
	
	
	if($login['user_access_importdn'])
		echo '- <a href="'.$_SERVER['PHP_SELF'].'?action=editor_kat">'.
			'Ny kategori</a><br />'.chr(10);
	
	$QUERY = mysql_query('select * from `import_dn_kategori` '.
		//"where area_id = '".$area['area_id']."' ".
		'order by kat_navn');
	$kategorier = array();
	if(mysql_num_rows($QUERY))
	{
		echo '<table class="prettytable">'.chr(10).chr(10);
		echo '	<tr>'.chr(10);
		echo '		<th>'.__('ID').'</th>'.chr(10);
		echo '		<th>Kategorinavn</th>'.chr(10);
		//echo '		<th>'._('Area').'</th>'.chr(10);
		if($login['user_access_importdn'])
			echo '		<th>'.__('Options').'</th>'.chr(10);
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
				echo '		<td><a href="'.$_SERVER['PHP_SELF'].'?action=editor_kat&amp;id='.$ROW['kat_id'].'">'.__('Edit').'</td>'.chr(10);
			echo '	</tr>'.chr(10).chr(10);
		}
		echo '</table>';
	}
	else
		echo '<div class="notice" style="width: 600px;">Ingen import-kategorier laget</div>';
}
elseif($action == 'editor_kat')
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
		$editor = new editor('import_dn_kategori', $_SERVER['PHP_SELF'].'?action=editor_kat');
		$editor->setHeading('Ny kategori'.
			//' for '.$area['area_name']
			'');
		$editor->setSubmitTxt(__('Add'));
	}
	else
	{
		$editor = new editor('import_dn_kategori', $_SERVER['PHP_SELF'].'?action=editor_kat', $id);
		$editor->setHeading('Endre kategori');
		$editor->setSubmitTxt(__('Change'));
	}
	
	$editor->setDBFieldID('kat_id');
	$editor->showID (TRUE);
	
	$editor->makeNewField('kat_navn', 'Navn p&aring; kategori', 'text');
	
	/*
	$editor->makeNewField('area_id', _('Area belonging'), 'select', array('defaultValue' => $area['area_id']));
	$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by `area_name`");
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('area_id', $R_area['area_id'], $R_area['area_name']);
	*/
	
	if(!$editor->getDB()) {
		echo 'Finner ikke det du &oslash;nsker &aring; endre.';
		exit();
	}
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: '.$_SERVER['PHP_SELF'].'?action=kat_list');
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
elseif($action == 'notimported_list')
{
	include "include/admin_middel.php";
	
	echo '<h1>Innstillinger for import fra kasseapparat</h1>'.chr(10).chr(10);
	
	if($area_okey)
	{
		echo '<h2>Ikke-importerte varer fra '.$area['area_name'].' (butikknr '.$area['importdatanova_shop_id'].')</h2>'.chr(10);
		$redirect = 'notimported';
		$Q_notimported = mysql_query("select * from `import_dn_tall_ikkeimportert` where shop_id = '".$area['importdatanova_shop_id']."' order by `vare_nr`");
	}
	else
	{
		echo '<h2>Ikke-importerte varer fra alle butikker</h2>'.chr(10);
		$redirect = 'notimported_all';
		$Q_notimported = mysql_query("select * from `import_dn_tall_ikkeimportert` order by `vare_nr`");
	}
	
	echo '- <a href="'.$_SERVER['PHP_SELF'].'">Tilbake</a><br /><br />'.chr(10);
	
	$Q_areas_with_shop = mysql_query("select id as area_id, area_name, importdatanova_shop_id from `mrbs_area` where importdatanova_shop_id != 0");
	$areas = array();
	while($R_area = mysql_fetch_assoc($Q_areas_with_shop))
	{
		$areas[$R_area['importdatanova_shop_id']] = $R_area['area_id'];
		if(!$area_okey || $R_area['area_id'] == $area['area_id'])
			echo 'Butikknr '.$R_area['importdatanova_shop_id'].' = '.$R_area['area_name'].'<br />';
	}
	
	$Q_varer = mysql_query("select varereg.*, kat.kat_navn as kat_navn
	from import_dn_vareregister varereg left join import_dn_kategori kat
	on varereg.kat_id = kat.kat_id
	");
	$areavarer = array(); // "area_id"_"vare_nr" => array()
	while($R_vare = mysql_fetch_assoc($Q_varer))
	{
		$areavarer[$R_vare['area_id'].'_'.$R_vare['vare_nr']] = $R_vare;
	}
	
	$set_shop_id = false;
	$vare_antall_tot = 0;
	$vare_dager  = 0;
	$vare_antall = 0;
	echo '<table class="prettytable">';
	echo '<tr>'.
		'<th>Varenr</th>'.
		'<th>Navn fra kasseapparat</th>'.
		'<th>Butikknr</th>'.
		'<th>Antall dager</th>'.
		'<th>Antall bes&oslash;kende</th>'.
		'<th>Valg for &aring; legge inn varenr</th>'.
		'</tr>'.chr(10);
	while($vare = mysql_fetch_assoc($Q_notimported))
	{
		if(!isset($areas[$vare['shop_id']]))
		{
			$link_nyvare = '*';
			$set_shop_id = true;
		}
		else
		{
			// Check if it will be imported next time
			$vare_id_primary = $vare['area_id'].'_'.$vare['vare_nr'];
			if(isset($areavarer[$vare_id_primary]))
			{
				if($areavarer[$vare_id_primary]['kat_id'] == 0)
					$link_nyvare = 'Skal ignoreres ved neste import.';
				else
					$link_nyvare = 'Vil importes som '.$areavarer[$vare_id_primary]['kat_navn'].' ('.$areavarer[$vare_id_primary]['kat_id'].') ved neste import';
			}
			else
			{
				$link_nyvare = '<a href="admin_import_dn.php?area_id='.$areas[$vare['shop_id']].'&amp;'.
						'action=editor_varereg&amp;redirect='.$redirect.'&amp;vare_nr='.urlencode($vare['vare_nr']).
						'&amp;vare_navn='.urlencode($vare['vare_navn']).
						'">'.
						'Legg inn vare</a>';
			}
		}
		
		echo '<tr>'.
			'<td>'.$vare['vare_nr'].'</td>'.
			'<td>'.$vare['vare_navn'].'</td>'.
			'<td>'.$vare['shop_id'].'</td>'.
			'<td>'.$vare['vare_dager'].'</td>'.
			'<td>'.$vare['vare_antall'].'</td>'.
			'<td>'.$link_nyvare.'</td>'.
			'</tr>'.chr(10);
		
		$vare_antall_tot++;
		$vare_dager  += $vare['vare_dager'];
		$vare_antall += $vare['vare_antall'];
	}
	echo '<tr>'.
		'<td>&nbsp;</td>'.
		'<td>'.$vare_antall_tot.' varer totalt</td>'.
		'<td>&nbsp;</td>'.
		'<td>'.$vare_dager.'</td>'.
		'<td>'.$vare_antall.'</td>'.
		'<td>&nbsp;</td>'.
		'</tr>'.chr(10);
	echo '</table>';
	
	if($set_shop_id)
	{
		echo '* Butikknr m&aring; settes p&aring; det anlegget det skal kobles sammen med. Endre anlegget som butikknret fra Datanova skal kobles til (se "Anlegg" under administrasjon)';
	}
}
elseif($action == 'editor_varereg' && $area_okey)
{
	if(!$login['user_access_importdn'])
	{
		showAccessDenied($day, $month, $year, $area, true);
		exit ();
	}
	
	/*
	
	varereg_list + area_id
	varereg_list_all (Default)
	notimported + area_id
	notimported_all
	
	*/
	if(isset($_GET['redirect']) && $_GET['redirect'] == 'notimported_all')
	{
		$redirect      = '&action=notimported_list';
		$redirect2     = '&amp;redirect='.$_GET['redirect'];
		$redirect_fil  = $_SERVER['PHP_SELF'];
	}
	elseif(isset($_GET['redirect']) && $_GET['redirect'] == 'notimported')
	{
		$redirect      = '&action=notimported_list&area_id='.$area['area_id'];
		$redirect2     = '&amp;redirect='.$_GET['redirect'];
		$redirect_fil  = $_SERVER['PHP_SELF'];
	}
	elseif(isset($_GET['redirect']) && $_GET['redirect'] == 'varereg_list')
	{
		$redirect      = '&action=varereg_list&area_id='.$area['area_id'];
		$redirect2     = '&amp;redirect='.$_GET['redirect'];
		$redirect_fil  = $_SERVER['PHP_SELF'];
	}
	else
	{
		$redirect      = '&action=varereg_list';
		$redirect2     = '';
		$redirect_fil  = $_SERVER['PHP_SELF'];
	}
	
	
	
	$id = 0;
	if(isset($_GET['id']) && is_numeric($_GET['id']))
		$id = slashes(htmlspecialchars($_GET['id'],ENT_QUOTES));
	if(isset($_POST['id']) && is_numeric($_POST['id']))
		$id = slashes(htmlspecialchars($_POST['id'],ENT_QUOTES));
	
	if($id <= 0)
	{
		$editor = new editor('import_dn_vareregister', 
		$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;action=editor_varereg'.
		$redirect2);
		$editor->setHeading('Ny vare for '.$area['area_name']);
		$editor->setSubmitTxt(__('Add'));
	}
	else
	{
		$editor = new editor('import_dn_vareregister', 
		$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;action=editor_varereg&amp;id='.$id.
		$redirect2, 
		array('vare_nr' => $id, 'area_id' => $area['area_id']));
		$editor->setHeading('Endre vare for '.$area['area_name']);
		$editor->setSubmitTxt(__('Change'));
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
	
	$editor->makeNewField('area_id', __('Area'), 'hidden', array('defaultValue' => $area['area_id']));
	
	$editor->makeNewField('navn', 'Navn p&aring; vare i Datanova kasseapparat', 'text',
		array('defaultValue' => $vare_navn));

	$editor->makeNewField('kat_id', 'Import-kategori', 'select', array('defaultValue' => 0));
	$Q_area = mysql_query("select kat_id, kat_navn from `import_dn_kategori` order by `kat_navn`");
	$editor->addChoice('kat_id', 0, 'Ignorer (varen f&aring;r ikke advarsel lenger)');
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('kat_id', $R_area['kat_id'], $R_area['kat_navn']);
	
	$editor->makeNewField('barn', 'Barn/Voksen', 'select', array('defaultValue' => 0));
	$editor->addChoice('barn', 1, 'Barn');
	$editor->addChoice('barn', 0, 'Voksen');
	
	if(!$editor->getDB()) {
		echo 'Finner ikke det du &oslash;nsker &aring; endre.';
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
					'?'.$redirect);
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
	
	echo '<h1>Innstillinger for import fra kasseapparat</h1>'.chr(10).chr(10);
	$editor->printEditor();
}
elseif($action == 'varereg_list' && $area_okey)
{
	// List
	
	include "include/admin_middel.php";
	
	echo '<h1>Innstillinger for import fra kasseapparat - '.$area['area_name'].'</h1>'.chr(10).chr(10);
	echo '<h2>Vareregister</h2>'.chr(10);
	
	echo '- <a href="'.$_SERVER['PHP_SELF'].'">Tilbake</a><br /><br />'.chr(10);
	
	echo 'Dette er varer fra kasseapparatet (Datanova) som bookingsystemet kjenner til og som blir koblet med en valgt kategori for anlegget. '.
		'Hvis en svare ikke har noen kategori for dette anlegget, s&aring; blir den ignorert og varsling(er) sendt ut.<br /><br />';
	
	if($login['user_access_importdn'])
		echo '- <a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;action=editor_varereg">'.
			'Ny vare</a><br /><br />'.chr(10);
	
	$QUERY = mysql_query('select * from `import_dn_kategori` '.
		'order by kat_navn');
	$kategorier = array();
	while($ROW = mysql_fetch_assoc($QUERY))
	{
		$kategorier[$ROW['kat_id']] = $ROW['kat_navn'];
	}
	
	$QUERY = mysql_query("select * from `import_dn_vareregister` where area_id = '".$area['area_id']."' order by vare_nr");
	if(mysql_num_rows($QUERY))
	{
		echo '<table class="prettytable">'.chr(10).chr(10);
		echo '	<tr>'.chr(10);
		echo '		<th>Varenr</th>'.chr(10);
		echo '		<th>Navn*</th>'.chr(10);
		echo '		<th>'.__('Area').'</th>'.chr(10);
		echo '		<th>Importert til kategori</th>'.chr(10);
		echo '		<th>B / V</th>'.chr(10);
		if($login['user_access_importdn'])
			echo '		<th>'.__('Options').'</th>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
		while($ROW = mysql_fetch_assoc($QUERY))
		{
			echo '	<tr>'.chr(10);
			echo '		<td><b>'.$ROW['vare_nr'].'</b></td>'.chr(10);
			echo '		<td>'.$ROW['navn'].'</td>'.chr(10);
			
			echo '		<td>';
			$Q_area = mysql_query("select * from `mrbs_area` where id = '".$ROW['area_id']."'");
			if(!mysql_num_rows($Q_area))
				echo '<i>'.__('Not found').'</i>';
			else
				echo mysql_result($Q_area, 0, 'area_name');
			echo '</td>'.chr(10);
			
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
			
			if($login['user_access_importdn'])
				echo '		<td><a href="'.$_SERVER['PHP_SELF'].'?area_id='.$area['area_id'].'&amp;action=editor_varereg&amp;id='.$ROW['vare_nr'].'">'.__('Edit').'</td>'.chr(10);
			echo '	</tr>'.chr(10).chr(10);
		}
		echo '</table>';
		echo '* = Navn fra database i Backoffice / Datanova';
	}
	else
		echo '<div class="notice" style="width: 600px;">Ingen varer registert</div>';
}
else
{
	include "include/admin_middel.php";
	
	echo '<h1>Innstillinger for import fra kasseapparat</h1>'.chr(10).chr(10);
	
	echo '- <a href="'.$_SERVER['PHP_SELF'].'?action=kat_list">Kategori-oversikt</a> (felles kategorier for hele J&aerlig;rmuseet)<br />';
	echo '- <a href="'.$_SERVER['PHP_SELF'].'?action=notimported_list">Alle ikke-importerte varenr</a><br />';
	
	$Q_areas_with_shop = mysql_query("select id as area_id, area_name, importdatanova_shop_id, importdatanova_alert_email from `mrbs_area` where importdatanova_shop_id != 0");
	$areas = array();
	while($R_area = mysql_fetch_assoc($Q_areas_with_shop))
	{
		$areas[$R_area['importdatanova_shop_id']] = $R_area;
	}
	
	function printout_shop ($shop_id, $shop_name)
	{
		global $areas;
		
		if(isset($areas[$shop_id]))
		{
			$area_id = 'area_id='.$areas[$shop_id]['area_id'];
			$area_name = $areas[$shop_id]['area_name'];
		}
		else
		{
			$area_id = 'shop_id='.$shop_id;
			$area_name = '<i>Ikke valgt</i>';
		}
		
		$Q_notimported = mysql_query("select * from `import_dn_tall_ikkeimportert` where shop_id = '".$shop_id."'");
		$Q_imported = mysql_query("select * from `import_dn_tall` where shop_id = '".$shop_id."'");
		$visits = 0; $visit_first = null; $visit_last = null; $days = array();
		while($R = mysql_fetch_assoc($Q_imported)) {
			$visits += (int)($R['antall_barn'] + $R['antall_voksne']);
			if(is_null($visit_last) || $R['dag'] > $visit_last)
				$visit_last = $R['dag'];
			if(is_null($visit_first) || $R['dag'] < $visit_first)
				$visit_first = $R['dag'];
			$days[$R['dag']] = null;
		}
		if(is_null($visit_first))
			$visit_first = '-';
		else
			$visit_first = date('d.m.Y', $visit_first);
		if(is_null($visit_last))
			$visit_last = '-';
		else
			$visit_last = date('d.m.Y', $visit_last);
		
		echo 
			'	<tr>'.chr(10).
			'		<td>'.$shop_id.'</td>'.chr(10).
			'		<td>'.$shop_name.'</td>'.chr(10).
			'		<td>'.$area_name.'</td>'.chr(10).
			'		<td><a href="'.$_SERVER['PHP_SELF'].'?'.$area_id.'&amp;action=notimported_list">'.mysql_num_rows($Q_notimported).'</td>'.chr(10).
			'		<td style="text-align: right;">'.$visits.'</td>'.chr(10).
			'		<td style="text-align: right;">'.count($days).'</td>'.chr(10).
			'		<td>'.$visit_first.'</td>'.chr(10).
			'		<td>'.$visit_last.'</td>'.chr(10).
			'		<td><a href="'.$_SERVER['PHP_SELF'].'?'.$area_id.'&amp;action=varereg_list">Vis vare-kategori-koblinger</a></td>'.chr(10).
			'	</tr>'.chr(10).chr(10);
	}
	
	
	echo '<table class="prettytable">'.chr(10);
	echo 
		'	<tr>'.chr(10).
		'		<th>Butikknr.</th>'.chr(10).
		'		<th>Butikknavn.</th>'.chr(10).
		'		<th>'.__('Area').' (bookingsystem)</th>'.chr(10).
		'		<th>Ikke-importerte varenr</th>'.chr(10).
		'		<th colspan="2">Importerte bes&oslash;kende / dager</th>'.chr(10).
		'		<th colspan="2">F&oslash;rste / siste tall</th>'.chr(10).
		'		<th></th>'.chr(10).
		'	</tr>'.chr(10).chr(10);
	$Q_shops = mysql_query("select * from `import_dn_shops`");
	while($R = mysql_fetch_assoc($Q_shops))
	{
		printout_shop ($R['shop_id'], $R['shop_name']);
	}
	echo '</table>';
	
	echo '<h2>E-post-varsling ved nye varer for anlegget</h2>';
	echo 'F&oslash;lgende f&aring;r e-post hver gang serveren kj&oslash;rer import hvis det finnes varer som er solgt som den ikke klarer &aring; importere.';
	echo '<table class="prettytable">';
	echo '<tr><th>Anlegg</th><th>E-post-adresser</th></tr>';
	foreach($areas as $shop_id => $area)
	{
		$emails = splittEmails($area['importdatanova_alert_email']);
		
		echo '<tr><td>'.$area['area_name'].'</td><td>'.implode('<br />', $emails).'</td></tr>';
	}
	echo '</table>';
}