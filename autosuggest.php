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
 * - Autosuggest
 * 
 * Returns a JSON-formated result.
 * Also see http://www.brandspankingnew.net/specials/ajax_autosuggest/ajax_autosuggest_autocomplete.html
 */

include_once("glob_inc.inc.php");


if(isset($_GET['limit']))
	$limit = (int)$_GET['limit'];
else
	$limit = 0;
if($limit > 0)
{
	$sql_limit = ' limit '.$limit;
} else
	$sql_limit = '';

$aResults = array();
$dynamicPrint = false;
if(isset($_GET['customer_name']))
{
	//$customer_name = slashes(preg_replace('/%([0-9a-f]{2})/ie', 'chr(hexdec($1))', (string) $_GET['customer_name']));
	$customer_name = slashes(utf8_decode($_GET['customer_name']));
	$sql = db()->prepare("select customer_id, customer_name from `customer` where customer_name like '$customer_name%' and slettet = '0' order by `customer_name`$sql_limit");
    $sql->execute();
	//$customer_name = unicode_encode($customer_name, 'ISO-8859-1');
	//$customer_name = unichr()
	//echo strlen($customer_name);
	//for ($i = 0; $i < strlen($customer_name); $i++)
	//	echo $customer_name{$i}.chr(10);
	//$aResults[] = array(
	//		'id'	=> 0,
	//		'value'	=> $customer_name,
	//		'info'	=> '');
	while($row = $sql->fetch())
	{
		$aResults[] = array(
			'id'	=> $row['customer_id'],
			'value'	=> htmlentities($row['customer_name']),
			'info'	=> '');
	}
	//print_r($aResults);
	//exit();
}
elseif(isset($_GET['municipal_name']))
{
	require "libs/municipals_norway.php";
	foreach($municipals as $mun_num => $mun)
	{
		if(strtolower(substr($mun,0, strlen($_GET['municipal_name']))) == strtolower($_GET['municipal_name']))
			$aResults[] = array(
				'id'	=> $mun_num,
				'value'	=> htmlentities($mun),
				'info'	=> '');
	}
}
elseif(isset($_GET['postal_place']))
{
	// TODO: Fix bugs
	$postfil = file('libs/postnr');
	$aResults = array();
	foreach($postfil as $line)
	{
		//echo trim(substr($line,4,strlen($_GET['postal_place'])+1)).' <b>----</b> '.$_GET['postal_place'].'<br>';
		if(strtolower(trim(substr($line,4,strlen($_GET['postal_place'])))) == strtolower($_GET['postal_place']))
		{
			$poststed = trim(substr($line,4, 31));
			$postnum = trim(substr($line, 0, 4));
			$aResults[] = array(
				'id'	=> $postnum,
				'value'	=> $poststed.' ('.$postnum.')',
				'info'	=> $poststed); // Returnerer poststedet
		}
	}
}
elseif(isset($_GET['postal_num']))
{
	if(strlen($_GET['postal_num']) == 4 && postalNumber($_GET['postal_num']))
	{
		// Fixing some norwegian characters, making utf-8
		header('Content-Type: text/html; charset=utf-8'); 
		echo utf8_encode(postalNumber($_GET['postal_num']));
	}
	exit();
}
elseif(isset($_GET['customer_id']))
{
	$customer = getCustomer($_GET['customer_id']);
	if(count($customer))
	{
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header ("Pragma: no-cache"); // HTTP/1.0
		
		header("Content-Type: application/json");
		
		//echo "{\"customer\": [";
		echo "{";
		$arr = array();
		foreach ($customer as $var => $value)
		{
			if(!is_array($value))
				$arr[] = "\"$var\": \"".htmlentities($value)."\"";
		}
		echo implode(", ", $arr);
		echo "}";
		//echo "]}";
	}
	exit();
}
elseif(isset($_GET['address_id']))
{
	if(is_numeric($_GET['address_id']))
	{
		$Q = db()->prepare("select * from `customer_address` where address_id = ::address_id");
        $Q->bindValue(':address_id', ((int)$_GET['address_id']), PDO::PARAM_INT);
		if($Q->rowCount() > 0)
		{
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header ("Pragma: no-cache"); // HTTP/1.0

            $row = $Q->fetch();
			if(!isset($_GET['address_format']) || $_GET['address_format'] != '2') {
                echo htmlentities($row['address_full'], ENT_QUOTES, 'ISO-8859-1', false);
            }
			else
			{
				// address_format = 2
				// => are using all the lines
				echo htmlentities($row['address_line_1'],ENT_QUOTES, 'ISO-8859-1', false).chr(10);
				echo htmlentities($row['address_line_2'],ENT_QUOTES, 'ISO-8859-1', false).chr(10);
				echo htmlentities($row['address_line_3'],ENT_QUOTES, 'ISO-8859-1', false).chr(10);
				echo htmlentities($row['address_line_4'],ENT_QUOTES, 'ISO-8859-1', false).chr(10);
				echo htmlentities($row['address_line_5'],ENT_QUOTES, 'ISO-8859-1', false).chr(10);
				echo htmlentities($row['address_line_6'],ENT_QUOTES, 'ISO-8859-1', false).chr(10);
				echo htmlentities($row['address_line_7'],ENT_QUOTES, 'ISO-8859-1', false).chr(10);
			}
		}
	}
	exit();
}
elseif(isset($_GET['template_id']))
{
	if(is_numeric($_GET['template_id']))
	{
		$Q_tpl = db()->prepare("select * from `template` where template_id = '".((int)$_GET['template_id'])."'");
		$Q_tpl->execute();
		if($Q_tpl->rowCount() > 0)
		{
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header ("Pragma: no-cache"); // HTTP/1.0
			
			//header("Content-Type: application/json");
			
			echo htmlentities($Q_tpl->fetch()['template'],ENT_QUOTES, 'ISO-8859-1', false);
		}
	}
	exit();
}
elseif(isset($_GET['attSearch']))
{
	// Searching for an attachment
	$Q_att = db()->prepare("select * from `entry_confirm_attachment` where att_filename_orig like '%".addslashes($_GET['attSearch'])."%' order by `att_filename_orig`");
	$Q_att->execute();
	$dynamicPrint = true;
	while($R_att = $Q_att->fetch())
	{
		$aResults[] = array(
				'att_id'			=> $R_att['att_id'],
				'att_displayname'	=> $R_att['att_filename_orig'].' ('.smarty_modifier_file_size($R_att['att_filesize']).')',
				'att_filetype_icon'	=> iconFiletypeFilename($R_att['att_filetype']).'.gif'
			);
	}
}
else
{
	exit(); // DIE DIE DIE MY DARLING!
}

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header ("Pragma: no-cache"); // HTTP/1.0

header("Content-Type: application/json");
/* */

echo "{\"results\": [";
$arr = array();
for ($i=0;$i<count($aResults);$i++)
{
	if($dynamicPrint)
	{
		$arr[$i] = "{";
		$c = 0;
		foreach($aResults[$i] as $key => $value)
		{
			$c++;
			$arr[$i] .= //"\t\t".
				'"'.$key.'": "'.$value.'"';
			if($c != count($aResults[$i]))
				$arr[$i] .= ',';//.chr(10);
		}
		$arr[$i] .= '}';//.chr(10).chr(10);
	}
	else
		$arr[] = "{\"id\": \"".$aResults[$i]['id']
		."\", \"value\": \"".$aResults[$i]['value']
		."\", \"info\": \"".$aResults[$i]['info']
		."\"}";
}
echo implode(", ", $arr);
echo "]}";
