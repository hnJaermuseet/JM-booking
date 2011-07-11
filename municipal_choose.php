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
 * JM-booking
 * - Edit or add of a customer
 */

include_once("glob_inc.inc.php");

if(!isset($_GET['id']) || !isset($_GET['name']))
{
	exit();
}

if($_GET['id'] == '' || $_GET['name'] == '')
{
	exit();
}

$id = slashes(htmlspecialchars($_GET['id'],ENT_QUOTES));
$name = slashes(htmlspecialchars($_GET['name'],ENT_QUOTES));

// Form...

echo '<HTML>
<HEAD>
<TITLE>JM-booking</TITLE><LINK REL="stylesheet" href="css/jm-booking.css" type="text/css">
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp-municipal.js"></script>
</HEAD>

<body>
';
echo '<script language="javascript">

function choose_municipal (id, name)
{
	if (top.opener && !top.opener.closed)
	{
		thisid = top.opener.document.getElementById(\''.$id.'\');
		thisid.value = id;
		
		thisname = top.opener.document.getElementById(\''.$name.'\');
		thisname.value = name;
		';
		
	if(isset($_GET['two']) || isset($_GET['id2']))
	{
		echo '
		thisid = top.opener.document.getElementById(\''.$id.'2\');
		thisid.value = id;
		';
	}
	if(isset($_GET['two']))
	{
		echo '
		thisname = top.opener.document.getElementById(\''.$name.'2\');
		thisname.value = name;
		';
	}
		echo '
		top.close();
	}
}
</script>';

require "libs/municipals_norway.php";

echo '<table width="100%" height="100%" style="border: 1px solid black;">'.chr(10);
echo '<tr><td align="center" height="40">'.chr(10);
echo '<h1>'._('Choose municipal').'</h1>'.chr(10);
echo '<b>'._('Choose one or search at the bottom.').'</b>'.chr(10);
echo '</td></tr>';

echo '<tr><td>'.chr(10);

echo '<table width="100%">'.chr(10);
if(defined('COUNTY'))
{
	$municipals2 = array();
	foreach ($county[COUNTY] as $mun_num)
		$municipals2[$mun_num] = $municipals[$mun_num];
	asort($municipals2);
	$i = 0;
	foreach ($municipals2 as $mun_num => $mun)
	{
		$i++;
		if($i == 1)
		{
			echo '<tr>'.chr(10);
		}
		echo '<td><input type="radio" onclick="choose_municipal(\''.$mun_num.'\', \''.$mun.'\');">&nbsp;'.$mun_num.'&nbsp;'.str_replace(' ', '&nbsp;', $mun).'</td>';
		if($i == 2)
		{
			echo '</tr>'.chr(10);
			$i = 0;
		}
	}
}

echo '<tr><td><br></td><td><br></td></tr>'.chr(10);

// Search
echo '<tr><td><b>'._('Search for others').'</b><br><input type="text" name="municipal_search" id="municipal_search"></td>'.chr(10);
echo '<td><br><input type="button" onclick="choose_municipal(\'\', \'\');" value="'._('Select none').'"></td></tr>'.chr(10);
echo '</table>'.chr(10);

echo '</td></tr>'.chr(10);
echo '</table>'.chr(10);

echo '<script language="javascript">'.chr(10);
echo 'var options = {
	script: "autosuggest.php?",
	varname: "municipal_name",
	json: true,
	maxresults: 35,
	shownoresults: false
};
var as = new bsn.AutoSuggest(\'municipal_search\', options);
';
echo '</script>'.chr(10);

echo '</body></html>';
?>