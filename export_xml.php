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

include_once("glob_inc.inc.php");


filterMakeAlternatives();

if(!isset($_GET['filters']))
	$_GET['filters'] = '';

$filters = filterGetFromSerialized($_GET['filters']);
if(!$filters)
	$filters = array();

$SQL = genSQLFromFilters($filters, 'entry_id');
$SQL .= " order by `time_start`";
$Q = mysql_query($SQL);

/*
$xml->addAttribute('area', 6); // $area['area_id']);
$xml->addAttribute('area_name', 'Vitenfabrikken'); // $area['area_name']);

$varer = $xml->addChild('varer');

while($entry = mysql_fetch_assoc($Q))
{
	if($entry['num_person_child'] > 0)
	{
		$vare = $varer->addChild($entry['entry_id'].'_barn');
		$vare->addChild('vare_id', '5');
		$vare->addChild('tid', $entry['time_start']);
		$vare->addChild('antall', $entry['num_person_child']);
	}
	if($entry['num_person_adult'] > 0)
	{
		$vare = $varer->addChild($entry['entry_id'].'_voksen');
		$vare->addChild('vare_id', '6');
		$vare->addChild('tid', $entry['time_start']);
		$vare->addChild('antall', $entry['num_person_adult']);
	}
}*/

$xml = '<?xml version="1.0" standalone="yes"?>'.chr(10);

$xml .= '<varer area="6" area_name="Vitenfabrikken">'.chr(10);

while($R = mysql_fetch_assoc($Q))
{
	$entry = getEntry($R['entry_id']);
	if($entry['num_person_child'] > 0)
	{
		$xml .= '	<vare from="'.$entry['entry_id'].'_barn">'.chr(10);
		$xml .= '		<vareid>5</vareid>'.chr(10);
		$xml .= '		<tid>'.$entry['time_start'].'</tid>'.chr(10);
		$xml .= '		<antall>'.$entry['num_person_child'].'</antall>'.chr(10);
		$xml .= '	</vare>'.chr(10);
	}
	if($entry['num_person_adult'] > 0)
	{
		$xml .= '	<vare from="'.$entry['entry_id'].'_voksen">'.chr(10);
		$xml .= '		<vareid>6</vareid>'.chr(10);
		$xml .= '		<tid>'.$entry['time_start'].'</tid>'.chr(10);
		$xml .= '		<antall>'.$entry['num_person_adult'].'</antall>'.chr(10);
		$xml .= '	</vare>'.chr(10);
	}
}
$xml .= '</varer>';

$xml = new SimpleXMLElement($xml);

header ("Content-type: text/xml");
echo $xml->asXML();

?>