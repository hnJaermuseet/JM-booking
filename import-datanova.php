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
 * JM-booking - Import av statistikk fra Datanova-system
 */


include "include/admin_top.php";
require "libs/editor.class.php";
$section = 'import_dn';


/* Init area stuff */
$areaArray = getArea($area);
$Q = mysql_query("SELECT id as area_id FROM `mrbs_area` where area_name = 'Vitenfabrikken' limit 1");
if(mysql_num_rows($Q))
	$area_id_vitenfabrikken = mysql_result($Q, 0, 'area_id');
else
	$area_id_vitenfabrikken = -1;
unset($Q);

if(count($areaArray) && $areaArray['area_name'] == 'Vitengarden') {
	$area_name = 'Vitengarden';
	
	$other = $area_id_vitenfabrikken;
} else {
	$area_name = 'Vitenfabrikken';
	
	$Q = mysql_query("SELECT id as area_id FROM `mrbs_area` where area_name = 'Vitengarden' limit 1");
	if(mysql_num_rows($Q))
		$other = mysql_result($Q, 0, 'area_id');
	unset($Q);
	
	$areaArray = getArea($area_id_vitenfabrikken);
	if(!count($areaArray))
	{
		die('Vitenfabrikken not found. System error.');
	}
}
$area = $areaArray['area_id'];
$path = './import/datanova-'.strtolower($area_name);


/* The real thing */
if(isset($_GET['importerfil']))
{
	// Import of a singel file
	$importerfil = str_replace('/', '', $_GET['importerfil']);
	$importerfil = trim(str_replace('\\', '', $_GET['importerfil']));
	
	
	if(isset($_GET['go']))
		$go = true;
	else
		$go = false;
	
	
	$importerfil_full = $path.'/'.$importerfil;
	
	if(!file_exists($importerfil_full)) {
		die('Finner ikke filen '.$importerfil);
	}
	$xml = file_get_contents($importerfil_full);
	
	/* Cleanup */
	$xml = str_replace('Trans.- dato', 'Transdato', $xml);
	$xml = str_replace('Ant. solgt', 'Antsolgt', $xml);
	$xml = str_replace('Innkjøps- beløp', 'Innkjøpsbelop', $xml);
	$xml = str_replace('Oms. etter rab.', 'Omsetterrab', $xml);
	$xml = str_replace('Oms. u/ mva', 'Omsumva', $xml);
	$xml = str_replace('mva beløp', 'mvabelop', $xml);
	$xml = str_replace('Kamp. salg', 'Kampsalg', $xml);
	$xml = str_replace('Kreditt salg', 'Kredittsalg', $xml);
	$xml = str_replace('Brutto fortj. kr.', 'Bruttofortjkr', $xml);
	$xml = str_replace('Bruttooms. fortj. %', 'Bruttoomsfortjprosent', $xml);
	$xml = str_replace('Bud. bto. fortj. %', 'Budbtofortjprosent', $xml);
	$xml = str_replace('Uke nr.', 'Ukenr', $xml);
	$xml = str_replace('Lev. nr.', 'Levnr', $xml);
	
	
	
	$arr = my_xml2array($xml);
	//print_r(get_value_by_path($arr, 'Report')); 
	
	
	
	/*print_r($arr); exit(); /**/
	
	// Test
	if(isset($_GET['xml']))
	{
		header ("content-type: text/xml");
		echo $xml;
		exit();
	}
	
	/*
	$varer_som_tasmed = array(
		'3002',
		'200000002961',
		'200000000486',
		'200000002664',
		'200000002756',
		'3010',
		'200000000462',
		'200000000455',
		'200000002718',
		'3001',
		'200000002749',
		'200000002695',
		'200000002688',
		'3000',
		'200000002657',
		'200000002640',
		'3005',
		'200000002701',
		'200000000431',
		'200000003012',
		'200000003036',
		'200000002947',
		'200000002725',
		'200000003029',
		'200000003043',
		'200000002732',
	
	);
	
	$gjorom_vare = array (
		'200000002725' => 'Sponsor',
		'200000002947' => 'Sponsor',
		'200000003036' => 'Sponsor',
		'200000003029' => 'Sponsor',
		'200000003043' => 'Sponsor',
	);*/
	
	$Q_varer = mysql_query("select varereg.*, kat.kat_navn as kat_navn
	from import_dn_vareregister varereg left join import_dn_kategori kat
	on varereg.kat_id = kat.kat_id
	where varereg.area_id = '$area';");
	$varer = array(); // vare_nr => array()
	while($R_vare = mysql_fetch_assoc($Q_varer))
		$varer[$R_vare['vare_nr']] = $R_vare;
	
	//print_r($varer); exit;
	
	include "include/admin_middel.php";
	
	/*
	echo '<table class="prettytable">';
	echo '	<tr>';
	echo '		<th>Varenr</th>';
	echo '		<th>Varenavn</th>';
	echo '		<th>Dato</th>';
	echo '		<th>Antsolgt</th>';
	echo '	</tr>'.chr(10);
	*/
	
	$unknowns       = array();
	$found          = array();
	$tall_nye       = array();
	$tall_update    = array();
	$tall_ignore    = array();
	$tall_ignore2   = array();
	$tall_allerede  = array();
	$varer_nye      = array();
	$varer_update   = array();
	foreach($arr[0] as $key => $val)
	{
		if(is_array($val) && $val['name'] == 'ReportLine')
		{
			$strike = '';
			/*
			if(in_array($val[2]['value'], $varer_som_tasmed))
				$strike = '';
			else
				$strike = ' style="text-decoration: line-through;"';
			
			if(array_key_exists($val[2]['value'], $gjorom_vare))
				$navn = $gjorom_vare[$val[2]['value']];
			else
				$navn = $val[1]['value'];
			*/
			$vare = array();
			$vare['vare_nr']      = slashes(htmlspecialchars($val[2]['value'],ENT_QUOTES));
			$vare['vare_navn']    = slashes(htmlspecialchars($val[1]['value'],ENT_QUOTES));
			$vare['vare_antall']  = (int)$val[5]['value'];
			if(strlen($val[4]['value']) != strlen('11.06.2008')) {
				die('Problemer med tolking av filen. Dato er ikke i rett format for '.$vare['vare_nr'].' (dato: '.$val[4]['value'].')');
			} else {
				$vare['dag']     = getTime($val[4]['value'], array('d', 'm', 'y'));
				if($vare['dag'] == 0)
					die('Problemer med tolking av filen. Dato er ikke i rett format for '.$vare['vare_nr'].' (dato: '.$val[4]['value'].')');
			}
			
			
			/* Determine import */
			if(!isset($varer[$vare['vare_nr']])) {
				if(!isset($unknowns[$vare['vare_nr']])) {
					$unknowns[$vare['vare_nr']] = $vare;
					unset($unknowns[$vare['vare_nr']]['dag']);
					$unknowns[$vare['vare_nr']]['vare_dager'] = 1;
				}
				else
				{
					$unknowns[$vare['vare_nr']]['vare_antall'] += $vare['vare_antall'];
					$unknowns[$vare['vare_nr']]['vare_dager'] += 1;
				}
				$tall_ignore[] = $vare;
			}
			else
			{
				// Varer funnet
				if(!isset($found[$vare['vare_nr']])) {
					$found[$vare['vare_nr']] = $vare;
					unset($found[$vare['vare_nr']]['dag']);
					$found[$vare['vare_nr']]['vare_dager'] = 1;
				}
				else
				{
					$found[$vare['vare_nr']]['vare_antall'] += $vare['vare_antall'];
					$found[$vare['vare_nr']]['vare_dager'] += 1;
				}
				
				
				$vare_med_kat = $varer[$vare['vare_nr']];
				$vare['kat_id'] = $varer[$vare['vare_nr']]['kat_id'];
				if($varer[$vare['vare_nr']]['barn'] == 0)
				{
					$vare['antall_barn']     = 0;
					$vare['antall_voksne']   = $vare['vare_antall'];
				}
				else
				{
					$vare['antall_barn']     = $vare['vare_antall'];
					$vare['antall_voksne']   = 0;
				}
				//unset($vare['vare_antall']);
				
				if($vare_med_kat['kat_id'] == 0) {
					$tall_ignore2[] = $vare;
				}
				else
				{
					// Sjekker mot database
					$Q_dbsjekk = mysql_query("select * from `import_dn_tall` where
						vare_nr = '".$vare['vare_nr']."' AND
						area_id = '".$area."' AND
						dag = '".$vare['dag']."'
						limit 1;");
					if(!mysql_num_rows($Q_dbsjekk)) {
						$tall_nye[] = $vare;
						
						// Nye varer
						if(!isset($varer_nye[$vare['vare_nr']])) {
							$varer_nye[$vare['vare_nr']] = $vare;
							unset($varer_nye[$vare['vare_nr']]['dag']);
							$varer_nye[$vare['vare_nr']]['vare_dager'] = 1;
						}
						else
						{
							$varer_nye[$vare['vare_nr']]['vare_antall'] += $vare['vare_antall'];
							$varer_nye[$vare['vare_nr']]['vare_dager'] += 1;
						}
					}
					else
					{
						$tall = mysql_fetch_assoc($Q_dbsjekk);
						if (
							$tall['kat_id']      != $vare['kat_id'] ||
							$tall['antall_barn'] != $vare['antall_barn'] ||
							$tall['antall_voksne']  != $vare['antall_voksne']
						)
						{
							$tall_update[]    = $vare;
							
							// Update av varer
							if(!isset($varer_update[$vare['vare_nr']])) {
								$varer_update[$vare['vare_nr']] = $vare;
								unset($varer_update[$vare['vare_nr']]['dag']);
								$varer_update[$vare['vare_nr']]['vare_dager'] = 1;
							}
							else
							{
								$varer_update[$vare['vare_nr']]['vare_antall'] += $vare['vare_antall'];
								$varer_update[$vare['vare_nr']]['vare_dager'] += 1;
							}
						}
						else
							$tall_allerede[]  = $vare;
					}
				}
			}
			
			
			/*
			echo '	<tr>'.chr(10);
			echo '		<td><span'.$strike.'>'.$vare['vare_nr'].'</span></td>'.chr(10);
			echo '		<td><span'.$strike.'>'.$vare['vare_navn'].'</span></td>'.chr(10);
			//echo '		<td><span'.$strike.'>'.$val[3]['value'].'</span></td>'.chr(10);
			echo '		<td><span'.$strike.'>'.date('d.m.Y', $vare['vare_tid']).'</span></td>'.chr(10);
			echo '		<td><span'.$strike.'>'.$vare['vare_antall'].'</span></td>'.chr(10);
			*/
			/*
			if(in_array($val[2]['value'], $varer_som_tasmed)) {
				echo '	<td style="color: green;">Blir importert</td>'.chr(10);
			} else {
				echo '	<td style="color: red;">Importeres ikke</td>'.chr(10);
			}*/
			
			/*echo '	</tr>'.chr(10).chr(10);*/
		}
	}
	/*echo '</table>';*/
	
	echo '<h1>Import fra Datanova kasseapparat på '.$area_name.'</h1>'.chr(10);
	
	if(!$go && count($unknowns))
	{
		echo '<div class="error" style="width: 700px;">Det var <b>'.count($unknowns).' ukjente varenr</b> i filen du vil importere. '.
			'Vennligst legg inn disse rett (enten at de importeres eller at de skal ignoreres). '.
			'Hvis du ikke gjør noe, så blir de ignorert. Du bør legge de inn som varer.</div>';
		echo '<table class="prettytable">';
		echo '<tr>'.
			'<th>Varenr</th>'.
			'<th>Navn fra kasseapparat</th>'.
			'<th>Antall dager</th>'.
			'<th>Antall besøkende</th>'.
			'<th>Valg for å legge inn varenr</th>'.
			'</tr>'.chr(10);
		foreach($unknowns as $vare) {
			echo '<tr>'.
				'<td>'.$vare['vare_nr'].'</td>'.
				'<td>'.$vare['vare_navn'].'</td>'.
				'<td>'.$vare['vare_dager'].'</td>'.
				'<td>'.$vare['vare_antall'].'</td>'.
				'<td><a href="admin_import_dn.php?area_id='.$area.'&amp;'.
					'editor_varereg=1&amp;vare_nr='.urlencode($vare['vare_nr']).
					'&amp;vare_navn='.urlencode($vare['vare_navn']).
					'&amp;importerfil='.$importerfil.'">'.
					'Legg inn vare</a></td>'.
				'</tr>'.chr(10);
		}
		echo '</table>';
	}
	
	if($go)
	{
		if(count($tall_nye))
		{
			$tall_nye2 = array();
			foreach($tall_nye as $vare) {
				// Insert
				$tall_nye2[] =
					'\''.$vare['vare_nr'].'\','.
					'\''.$area.'\','.
					'\''.$vare['dag'].'\','.
					'\''.$vare['kat_id'].'\','.
					'\''.$vare['antall_barn'].'\','.
					'\''.$vare['antall_voksne'].'\'';
			}
			mysql_query('insert into `import_dn_tall` (
				`vare_nr`,
				`area_id`,
				`dag`,
				`kat_id`,
				`antall_barn`,
				`antall_voksne`
			) VALUES ('.implode('),(', $tall_nye2).');');
			
			if(mysql_error())
			{
				echo '<div style="width: 700px;" class="error"><b>Import - nye:</b> Feil ved import av nye tall:<br>'.mysql_error().'</div>';
				exit;
			}
			else
			{
				echo '<div style="width: 700px;" class="success"><b>Import - nye:</b> '.count($tall_nye2).' nye tall importert.</div>';
			}
		}
		else {
			echo '<div style="width: 700px;" class="notice"><b>Import - nye:</b> Ingen nye tall å importere</div>';
		}
		
		if(count($tall_update))
		{
			foreach($tall_update as $vare)
			{
				// TODO: update
				mysql_query("UPDATE `import_dn_tall` SET
					`antall_barn` = '".$vare['antall_barn']."',
					`antall_voksne` = '".$vare['antall_voksne']."',
					`kat_id` = '".$vare['kat_id']."'
					WHERE
						`vare_nr`   = '".$vare['vare_nr']."' AND
						`area_id`   = '".$area."' AND
						`dag`       = '".$vare['dag']."'");
				if(mysql_error())
				{
					echo '<div style="width: 700px;" class="error"><b>Import - oppdatering:</b> Feil ved oppdatering av tall:<br>'.mysql_error().'</div>';
					exit;
				}
			}
			echo '<div style="width: 700px;" class="success"><b>Import - oppdatering:</b> '.count($tall_update).' tall oppdatert.</div>';
		}
	}
	
	echo '<h2>Oppsummert - importerte tall</h2>';
	echo '<span style="font-size: 1.3em;'.
		((!count($tall_nye)) ? ' color:gray;' : '').
		'>"><b>Nye:</b> '.count($tall_nye).'</span><br />';
	echo '<span'.((!(count($tall_ignore)+count($tall_ignore2))) ? ' style="color:gray;"' : '').
		'><b>Ignoreres:</b> '.(count($tall_ignore)+count($tall_ignore2)).'</span><br>'.
		'(<span'.((!count($tall_ignore)) ? ' style="color:gray;"' : '').
		'> '.(count($tall_ignore)).' pga. vare ikke registert,</span>'.
		'<span'.((!count($tall_ignore2)) ? ' style="color:gray;"' : '').
		'> '.(count($tall_ignore2)).' pga. vare skal ignoreres</span>)'.
		'<br />';
	echo '<span'.((!count($tall_update)) ? ' style="color:gray;"' : '').
		'><b>Trenger oppdatering:</b> '.count($tall_update).'</span><br />';
	echo '<span'.((!count($tall_allerede)) ? ' style="color:gray;"' : '').
		'><b>Allerede importert:</b> '.count($tall_allerede).'</span><br />';
	
	echo '<br />';
	echo '<h2>Nye tall</h2>';
	if(!count($varer_nye)) {
		echo '<div style="width: 700px;" class="notice">Ingen nye tall å importere.</div>';
	} else {
		echo '<table class="prettytable">';
		echo '<tr>'.
			'<th>Varenr og navn fra DN</th>'.
			'<th>Importeres til</th>'.
			'<th>Antall nye dager</th>'.
			'<th>Antall nye besøkende</th>'.
			'</tr>'.chr(10);
		foreach($varer_nye as $vare) {
			if($varer[$vare['vare_nr']]['kat_id'] == 0) {
				$strike =  ' style="text-decoration: line-through;"';
				$kat = 'Importeres ikke';
			}
			else
			{
				$strike = '';
				$kat = $varer[$vare['vare_nr']]['kat_navn'].' ('.
						(($varer[$vare['vare_nr']]['barn'] == 1) ? "barn" : "voksen").')';
			}
			echo '<tr>'.
				'<td'.$strike.'>'.$vare['vare_nr'].' - '.$vare['vare_navn'].'</td>'.
				'<td>'.$kat.'</td>'.
				'<td'.$strike.'>'.$vare['vare_dager'].'</td>'.
				'<td'.$strike.'>'.$vare['vare_antall'].'</td>'.
				'</tr>'.chr(10);
		}
		echo '</table>';
	}
	echo '<h2>Tall som oppdateres</h2>';
	if(!count($varer_update))
	{
		echo '<div style="width: 700px;" class="notice">Ingen tall som trenger oppdatering.</div>';
	}
	else
	{
		echo '<div style="width: 700px;" class="notice">Noen varer hadde antall som ikke stemte med databasen. Sikkert fordi tidligere tall har inneholdt besøk for en halv dag. Jeg retter det for deg.</div>';
		echo '<table class="prettytable">';
		echo '<tr>'.
			'<th>Varenr og navn fra DN</th>'.
			'<th>Importeres til</th>'.
			'<th>Antall dager som oppdateres</th>'.
			'<th>Antall besøkende (etter oppdatering)</th>'.
			'</tr>'.chr(10);
		foreach($varer_update as $vare) {
			if($varer[$vare['vare_nr']]['kat_id'] == 0) {
				$strike =  ' style="text-decoration: line-through;"';
				$kat = 'Importeres ikke';
			}
			else
			{
				$strike = '';
				$kat = $varer[$vare['vare_nr']]['kat_navn'].' ('.
						(($varer[$vare['vare_nr']]['barn'] == 1) ? "barn" : "voksen").')';
			}
			echo '<tr>'.
				'<td'.$strike.'>'.$vare['vare_nr'].' - '.$vare['vare_navn'].'</td>'.
				'<td>'.$kat.'</td>'.
				'<td'.$strike.'>'.$vare['vare_dager'].'</td>'.
				'<td'.$strike.'>'.$vare['vare_antall'].'</td>'.
				'</tr>'.chr(10);
		}
		echo '</table>';
	}
	
	if(!$go)
	{
		echo '<br /><br />';
		echo '<form method="get">';
		echo '<input type="hidden" name="area" value="'.$area.'">';
		echo '<input type="hidden" name="importerfil" value="'.$importerfil.'">';
		echo '<input type="hidden" name="go" values="jada">';
		echo '<input class="ui-button ui-state-default ui-corner-all" '.
			'type="submit" style="font-size: 18px;" value="Importer til database"/>';
		echo '</form>';
		echo '<br /><br /><br /><br />';
	}
	
}
else
{
	// Directory listing
	
	$dir_handle = @opendir($path) or die("Unable to open $path");
	include "include/admin_middel.php";
	
	echo '<h1>Import fra Datanova kasseapparat på '.$area_name.'</h1>'.chr(10);
	if($area_name == 'Vitenfabrikken' && isset($other)) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?area='.$other.'">Importer fra Vitengardens kasseapparat heller</a><br />';
	} else if(isset($other)) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?area='.$other.'">Importer fra Vitenfabrikkens kasseapparat heller</a><br />';
	}
	echo '<a href="admin_import_dn.php?area_id='.$area.'">Endre innstillinger</a><br />';
	echo '<br /><br />';
	$files = array();
	while ($file = readdir($dir_handle)) 
	{
		if($file != '.' && $file != '..')
		{
			$files[] = $file;
		}
	}
	
	if(!count($files))
	{
		echo '<div class="notice" style="width: 600px;">Ingen filer som ikke er importert.<br /><br />'.
			'For guide om hvordan du kan laste inn fil, se artikkelen '.
			'<a href="'.wikiLink('Bookingsystemet/Import_fra_kasseapparat').'">'.
			'import fra kasseapparat'.
			'</a> på wikien';
	}
	else
	{
		echo '<div style="font-size: 1.2em;">';
		echo '<b>Velg fil å importere:</b><br />';
		echo '<ul>';
		foreach($files as $file)
		{
			echo '<li style="font-size: 1.2em;">'.
			'<a href="'.$_SERVER['PHP_SELF'].'?area='.$areaArray['area_id'].'&amp;importerfil='.$file.'">'.
			$file.'</a></li>';
		}
		echo '</ul></div>';
	}
	
	//closing the directory
	closedir($dir_handle);
	
}


function my_xml2array($contents)
{
    $xml_values = array();
    $parser = xml_parser_create('');
    if(!$parser)
        return false;

    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'ISO-8859-1');
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
        return array();
   
    $xml_array = array();
    $last_tag_ar =& $xml_array;
    $parents = array();
    $last_counter_in_tag = array(1=>0);
    foreach ($xml_values as $data)
    {
        switch($data['type'])
        {
            case 'open':
                $last_counter_in_tag[$data['level']+1] = 0;
                $new_tag = array('name' => $data['tag']);
                if(isset($data['attributes']))
                    $new_tag['attributes'] = $data['attributes'];
                if(isset($data['value']) && trim($data['value']))
                    $new_tag['value'] = trim($data['value']);
                $last_tag_ar[$last_counter_in_tag[$data['level']]] = $new_tag;
                $parents[$data['level']] =& $last_tag_ar;
                $last_tag_ar =& $last_tag_ar[$last_counter_in_tag[$data['level']]++];
                break;
            case 'complete':
                $new_tag = array('name' => $data['tag']);
                if(isset($data['attributes']))
                    $new_tag['attributes'] = $data['attributes'];
                if(isset($data['value']) && trim($data['value']))
                    $new_tag['value'] = trim($data['value']);

                $last_count = count($last_tag_ar)-1;
                $last_tag_ar[$last_counter_in_tag[$data['level']]++] = $new_tag;
                break;
            case 'close':
                $last_tag_ar =& $parents[$data['level']];
                break;
            default:
                break;
        };
    }
    return $xml_array;
}

//
// use this to get node of tree by path with '/' terminator
//
function get_value_by_path($__xml_tree, $__tag_path)
{
    $tmp_arr =& $__xml_tree;
    $tag_path = explode('/', $__tag_path);
    foreach($tag_path as $tag_name)
    {
        $res = false;
        foreach($tmp_arr as $key => $node)
        {
            if(is_int($key) && $node['name'] == $tag_name)
            {
                $tmp_arr = $node;
                $res = true;
                break;
            }
        }
        if(!$res)
            return false;
    }
    return $tmp_arr;
}

?>