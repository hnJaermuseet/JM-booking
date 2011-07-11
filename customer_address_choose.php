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

if(!isset($_GET['id']) || !isset($_GET['name']))
{
	exit();
}

if($_GET['id'] == '' || $_GET['name'] == '')
{
	exit();
}
if(!isset($_GET['customer_id']))
{
	echo 'Finner ikke kunde / ingen kunde valgt. (1)';
	exit();
}

$customer = getCustomer($_GET['customer_id']);
if(!count($customer))
{
	echo 'Finner ikke kunde / ingen kunde valgt.';
	exit();
}

$id = slashes(htmlspecialchars($_GET['id'],ENT_QUOTES));
$name = slashes(htmlspecialchars($_GET['name'],ENT_QUOTES));


$string = 'id='.$id.'&amp;name='.$name.'&amp;customer_id='.$customer['customer_id'];
if(isset($_GET['callSelectAddress']))
	$string .= '&amp;callSelectAddress=1';
if(isset($_GET['id2']))
	$string .= '&amp;id2=1';
if(isset($_GET['two']))
	$string .= '&amp;two=1';

if(isset($_GET['address_id']))
{
	if($_GET['address_id'] != 0)
		$address = getAddress($_GET['address_id']);
	else
		$address = array(
			'address_id' => 0,
			'address_info' => '',
			'customer_id' => $customer['customer_id'],
			'address_line_1' => $customer['customer_name'].chr(10),
			'address_line_2' => '',
			'address_line_3' => '',
			'address_line_4' => '',
			'address_line_5' => '',
			'address_line_6' => '',
			'address_line_7' => '',
			'address_postalnum' => '',
			'address_full');
	
	if(!count($address))
	{
		echo 'Finner ikke adressen du vil endre.';
		exit();
	}
	
	// Behandle adressedata
	if(isset($_POST['address_info']))
	{
		$thisone = array();
		$thisone['address_id']		= $address['address_id'];
		$thisone['address_info']	= '';
		$thisone['address_line_1']	= '';
		$thisone['address_line_2']	= '';
		$thisone['address_line_3']	= '';
		$thisone['address_line_4']	= '';
		$thisone['address_line_5']	= '';
		$thisone['address_line_6']	= ''; // Postalnumber + place
		$thisone['address_line_7']	= ''; // Country, if any
		$thisone['address_postalnum']	= ''; // Postalnumber
		
		if(isset($_POST['address_info']))		$thisone['address_info']	= $_POST['address_info'];
		if(isset($_POST['address_lines']))
		{
			$i = 0;
			foreach(explode("\n", $_POST['address_lines'], 5) as $line)
			{
				$i++;
				if($i != 5)
					$thisone['address_line_'.$i] = trim(slashes(htmlspecialchars($line,ENT_QUOTES)));
				else
					$thisone['address_line_'.$i] = str_replace("\r", '', str_replace("\n", ', ', trim(slashes(htmlspecialchars($line,ENT_QUOTES)))));
			}
		}
		if(isset($_POST['address_postalnum']))
		{
			if(postalNumber($_POST['address_postalnum']))
			{
				$thisone['address_line_6']		= $_POST['address_postalnum'].' '.slashes(htmlspecialchars(postalNumber($_POST['address_postalnum']),ENT_QUOTES));
				$thisone['address_postalnum']	= $_POST['address_postalnum'];
			}
		}
		if(isset($_POST['address_country']))
			$thisone['address_line_7'] = slashes(htmlspecialchars($_POST['address_country'],ENT_QUOTES));	
		
		// Generate address_full
		$addrline = array();
		if($thisone['address_line_1'])
			$addrline[] = $thisone['address_line_1'];
		if($thisone['address_line_2'])
			$addrline[] = $thisone['address_line_2'];
		if($thisone['address_line_3'])
			$addrline[] = $thisone['address_line_3'];
		if($thisone['address_line_4'])
			$addrline[] = $thisone['address_line_4'];
		if($thisone['address_line_5'])
			$addrline[] = $thisone['address_line_5'];
		if($thisone['address_line_6'])
			$addrline[] = $thisone['address_line_6'];
		if($thisone['address_line_7'])
			$addrline[] = $thisone['address_line_7'];
		$thisone['address_full'] = implode(chr(10),$addrline);
		
		$default_invoice_address_is_set = false;
		if($thisone['address_id'] == 0)
		{
			mysql_query("INSERT INTO `customer_address` (
				`address_id` , `customer_id` , `address_info` , 
					`address_line_1`,
					`address_line_2`,
					`address_line_3`,
					`address_line_4`,
					`address_line_5`,
					`address_line_6`,
					`address_line_7`,
					`address_full`,
					`address_postalnum`
					) 
				VALUES (NULL , '".$customer['customer_id']."', '".$thisone['address_info']."', 
					'".$thisone['address_line_1']."',
					'".$thisone['address_line_2']."',
					'".$thisone['address_line_3']."',
					'".$thisone['address_line_4']."',
					'".$thisone['address_line_5']."',
					'".$thisone['address_line_6']."',
					'".$thisone['address_line_7']."',
					'".$thisone['address_full']."',
					'".$thisone['address_postalnum']."'
					);");
			$address['address_id'] = mysql_insert_id();
		}
		else
		{
			mysql_query("UPDATE `customer_address` SET 
					`address_info` = '".$thisone['address_info']."',
					`address_line_1` = '".$thisone['address_line_1']."',
					`address_line_2` = '".$thisone['address_line_2']."',
					`address_line_3` = '".$thisone['address_line_3']."',
					`address_line_4` = '".$thisone['address_line_4']."',
					`address_line_5` = '".$thisone['address_line_5']."',
					`address_line_6` = '".$thisone['address_line_6']."',
					`address_line_7` = '".$thisone['address_line_7']."',
					`address_full` = '".$thisone['address_full']."',
					`address_postalnum` = '".$thisone['address_postalnum']."'
				WHERE `address_id` = ".$thisone['address_id']." LIMIT 1 ;");
			if($customer['customer_address_id_invoice'] == $address['address_id'])
			{
				$default_invoice_address_is_set = true;
			}
		}
		
		if(isset($_POST['default_invoice']))
		{
			if($_POST['default_invoice'] == '1')
			{
				// Update customer_address_id_invoice in customer
				mysql_query("UPDATE `customer` SET 
				`customer_address_id_invoice` = '".$address['address_id']."'
					WHERE `customer_id` = ".$customer['customer_id']." LIMIT 1 ;");
			}
			else
			{
				if($default_invoice_address_is_set)
				{
					// Set to zero => no default address
					mysql_query("UPDATE `customer` SET 
					`customer_address_id_invoice` = '0'
						WHERE `customer_id` = ".$customer['customer_id']." LIMIT 1 ;");
				}
			}
		}
		
		header('Location: '.$_SERVER['PHP_SELF'].'?'.str_replace('&amp;', '&', $string));
		exit();
	}
	
	echo '<HTML>
<HEAD>
<TITLE>JM-booking</TITLE><LINK REL="stylesheet" href="css/jm-booking.css" type="text/css">
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">

'.
//'<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp-postal.js"></script>'.
'
</HEAD>

<body>
';
	
	echo '<script type="text/javascript">'.chr(10);
	/*echo 'var autos_options_post = {
		script: "autosuggest.php?",
		varname: "postal_place",
		json: true,
		maxresults: 35,
		shownoresults: false
	};';*/
	echo '
	
	function selectPostalNumber (postfield_name, postalnum)
{
	var xmlHttp=null; // Defines that xmlHttp is a new variable.
	// Try to get the right object for different browser
	try {
		// Firefox, Opera 8.0+, Safari, IE7+
		xmlHttp = new XMLHttpRequest(); // xmlHttp is now a XMLHttpRequest.
	} catch (e) {
		// Internet Explorer
		try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4)
			try { // In some instances, status cannot be retrieved and will produce 
			      // an error (e.g. Port is not responsive)
				if (xmlHttp.status == 200) {
					var postal_place=xmlHttp.responseText;
					if(postal_place != "undefined")
						document.getElementById(postfield_name).value = postal_place;
				}
			} catch (e) {
				//document.getElementById("ajax_output").innerHTML 
				//= "Error on Ajax return call : " + e.description;
			}
	}
	xmlHttp.open("get","autosuggest.php?postal_num="+postalnum); // .open(RequestType, Source);
	xmlHttp.send(null); // Since there is no supplied form, null takes its place 
	                    // as a new form.
}
	';
	echo '</script>'.chr(10);
	
	echo '<table width="100%" height="100%" style="border: 1px solid black;">'.chr(10);
	echo '<tr><td align="center" height="40">'.chr(10);
	echo '<h1>Endre adresse</h1>'.chr(10);
	echo '</td></tr>';
	
	echo '<tr><td>';
	
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?address_id='.$address['address_id'].'&amp;'.$string.'">'.chr(10);
	
	echo iconHTML('group').' <b>Kunde:</b><br>';
	echo '<input type="text" disabled="disabled" size="2" value="'.$customer['customer_id'].'">&nbsp;'.
	'<input type="text" disabled="disabled" size="20" value="'.$customer['customer_name'].'">&nbsp;';
	
	echo '<br><br>';
	
	echo iconHTML('email').' <b>Adressenavn:</b><br>
	<input type="text" size="20" name="address_info" id="address_info" value="'.$address['address_info'].'">'.chr(10);
	
	echo '<br><br>';
	
	echo '<b>Adresse:</b><br>
	<textarea rows="4" cols="25" name="address_lines" id="address_lines">';
	$lines = array();
	if($address['address_line_1'] != '')
		$lines[] = $address['address_line_1'];
	if($address['address_line_2'] != '')
		$lines[] = $address['address_line_2'];
	if($address['address_line_3'] != '')
		$lines[] = $address['address_line_3'];
	if($address['address_line_4'] != '')
		$lines[] = $address['address_line_4'];
	if($address['address_line_5'] != '')
		$lines[] = $address['address_line_5'];
	echo implode(chr(10),$lines);
	echo '</textarea>'.chr(10);
	
	echo '<br><br>';
	
	echo iconHTML('house').' <b>Postnummer/-sted:</b><br>';
	echo '<input type="text" size="5" name="address_postalnum" id="address_postalnum" '.
	'value="'.$address['address_postalnum'].'" '.
	'onkeyup="selectPostalNumber (\'address_postalplace\', document.getElementById(\'address_postalnum\').value);" '.
	'autocomplete="off">'.
	'<input type="text" size="12" name="address_postalplace" id="address_postalplace" value="'.postalNumber($address['address_postalnum']).'">'.chr(10);
	
	echo '<br><br>';
	
	echo '<b>Land:</b><br>';
	echo '<input type="text" size="15" name="address_country" value="'.$address['address_line_7'].'">'.chr(10);
	
	echo '<br><br>';
	$yes_checked = '';
	$no_checked = '';
	if($customer['customer_address_id_invoice'] == $address['address_id'])
		$yes_checked = ' checked="checked"';
	else
		$no_checked = ' checked="checked"';
	echo iconHTML('email_link').' <b>Standard fakturaadresse:</b><br>';
	echo '<input type="radio" name="default_invoice" value="1"'.$yes_checked.'> Ja<br>'.
	'<input type="radio" name="default_invoice" value="0"'.$no_checked.'> Nei';
	
	echo '<br><br>';
	if($address['address_id'] == 0)
		echo '<input type="submit" value="Legg til">';
	else
		echo '<input type="submit" value="Endre">';
	
	echo '</form>';
	
	echo '<script langauge="javascript">
	as = new bsn.AutoSuggest(\'address_postalplace\', autos_options_post, \'address_postalnum\', \'\');'.chr(10);
	echo '</script>'.chr(10);
	echo '</td></tr>';
	echo '</table>';
	echo '</body></html>';
	exit();
}

// Form...

echo '<HTML>
<HEAD>
<TITLE>JM-booking</TITLE><LINK REL="stylesheet" href="css/jm-booking.css" type="text/css">
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp-municipal.js"></script>
</HEAD>

<body>
';

if(!isset($_GET['callSelectAddress']))
{
	echo '<script language="javascript">
	
	function choose_address (id)
	{
		address = document.getElementById(\'address_full\' + id).value;
		if (top.opener && !top.opener.closed)
		{
			thisid = top.opener.document.getElementById(\''.$id.'\');
			thisid.value = id;
			
			thisname = top.opener.document.getElementById(\''.$name.'\');
			thisname.value = address;
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
			$string .= '&amp;two=1';
			echo '
			thisname = top.opener.document.getElementById(\''.$name.'2\');
			thisname.value = address;
			';
		}
			echo '
			top.close();
		}
	}
	</script>';
}
else
{
	echo '<script language="javascript">
	
	function choose_address (id)
	{
		address = document.getElementById(\'address_full\' + id).value;
		if (top.opener && !top.opener.closed)
		{
			thisid = top.opener.document.getElementById(\''.$id.'\');
			thisid.value = id;
			
			top.opener.selectAddress(id);
			';
			
		if(isset($_GET['two']) || isset($_GET['id2']))
		{
			echo '
			thisid = top.opener.document.getElementById(\''.$id.'2\');
			thisid.value = id;
			';
		}
			echo '
			top.close();
		}
	}
	</script>';
}

echo '<table width="100%" height="100%" style="border: 1px solid black;">'.chr(10);
echo '<tr><td align="center" height="40">'.chr(10);
echo '<h1>'._('Choose address from').' '.$customer['customer_name'].'</h1>'.chr(10);
echo 'En kunde kan i systemet v&aring;rt kan ha mange adresser koblet til seg.';
echo '</td></tr>';

echo '<tr><td>'.chr(10);

if(count($customer['customer_address']))
{
	echo '<table style="border-collapse: collapse;" width="100%">'.chr(10);
	echo '	<tr>'.chr(10);
	echo '		<td class="border" width="40">&nbsp;</td>'.chr(10);
	echo '		<td class="border"><b>'._('Name').'</b></td>'.chr(10);
	echo '		<td class="border"><b>'._('Address').'</b></td>'.chr(10);
	//echo '		<td class="border"><b>'._('Invoice address?').'</b></td>'.chr(10);
	echo '		<td class="border">&nbsp;</td>'.chr(10);
	echo '	</tr>'.chr(10);
	foreach ($customer['customer_address'] as $address)
	{
		$color = '';
		$ico = 'email';
		if($customer['customer_address_id_invoice'] == $address['address_id']) {
			$color = ' style="background-color: #EFEFFF;"';
			$ico = 'email_link';
		}
		echo '	<tr>'.chr(10);
		echo '		<td class="border"'.$color.'>'.
		iconHTML($ico).
		'<input type="hidden" id="address_full'.$address['address_id'].'" value="'.$address['address_full'].'">'.
		'<input type="radio" onclick="choose_address(\''.$address['address_id'].'\');"></td>'.chr(10);
		echo '		<td class="border"'.$color.'>'.$address['address_info'].'</td>'.chr(10);
		
		echo '		<td class="border"'.$color.'>';
		echo nl2br($address['address_full']).chr(10);
		echo '</td>'.chr(10);
		
		/*
		echo '		<td class="border"'.$color.'>';
		if($customer['customer_address_id_invoice'] == $address['address_id'])
			echo _('Yes');
		else
			echo _('No');
		*/
		
		echo '</td>'.chr(10);
		echo '		<td class="border"'.$color.'>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$string.'&amp;address_id='.$address['address_id'].'">Endre</a>';
		echo '</td>'.chr(10);
		
		echo '	</tr>'.chr(10);
	}
	echo '</table>'.chr(10);
	
	echo '<br><br>';
	echo '<table><tr><td style="background-color: #EFEFFF;">'.iconHTML('email_link').' Adressen er standard fakturaadresse for kunden.</td></tr></table>';
	
}
else
	echo _('No addresses.').'<br>';

echo '<br>';
echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$string.'&amp;address_id=0">Ny adresse</a>';

echo '</body></html>';
?>