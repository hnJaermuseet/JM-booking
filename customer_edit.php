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
require "libs/municipals_norway.php";

if(isset($_GET['customer_id']))
	$customer_id = (int)$_GET['customer_id'];
elseif(isset($_POST['customer_id']))
	$customer_id = (int)$_POST['customer_id'];
else
	$customer_id = 0;

if($customer_id != 0)
{
	$customer = getCustomer($customer_id);
	if(!count($customer))
	{
		echo _("Can't find the customer you are looking for.");
		exit();
	}
	
	if(isset($_GET['viewer']))
	{
		$data = $customer;
		if(isset($_GET['add']))
			$add = true;
		else
			$add = false;
		
		
		echo '<HTML>
		<HEAD>
		<TITLE>JM-booking</TITLE><LINK REL="stylesheet" href="css/jm-booking.css" type="text/css">
		<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
		
		<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp.js"></script>
		</HEAD>
		
		<body>
		';
		$invoice_address_full = '';
		$invoice_address_id = '';
		if($data['customer_address_id_invoice'] != 0)
		{
			$invoice_address_id = $data['customer_address_id_invoice'];
			$thisADDR = getAddress($data['customer_address_id_invoice']);
			if(count($thisADDR))
				$invoice_address_full = $thisADDR['address_full'];
		}
		
		if(!isset($_GET['returnToInvoiceCreate']))
		{
			echo '<script language="javascript">
			
			function chooseCustomer ()
			{
				if (top.opener && !top.opener.closed)
				{
					thisid = top.opener.document.getElementById(\'customer_id\');
					thisid.value = \''.$data['customer_id'].'\';
					
					thisname = top.opener.document.getElementById(\'customer_name\');
					thisname.value = \''.$data['customer_name'].'\';
					
					thisid = top.opener.document.getElementById(\'customer_id2\');
					thisid.value = \''.$data['customer_id'].'\';
					
					thisname = top.opener.document.getElementById(\'customer_municipal_num\');
					thisname.value = \''.$data['customer_municipal_num'].'\';
					
					thisname = top.opener.document.getElementById(\'customer_municipal_num2\');
					thisname.value = \''.$data['customer_municipal_num'].'\';
					
					thisname = top.opener.document.getElementById(\'customer_municipal\');
					thisname.value = \''.$data['customer_municipal'].'\';
					
					thisname = top.opener.document.getElementById(\'invoice_address_id\');
					thisname.value = \''.$invoice_address_id.'\';
					thisname = top.opener.document.getElementById(\'invoice_address_id2\');
					thisname.value = \''.$invoice_address_id.'\';
					thisname = top.opener.document.getElementById(\'invoice_address\');
					thisname.value = \''.str_replace(chr(10), '\n', $invoice_address_full).'\';
					
					top.close();
				}
			}
			</script>';
		}
		else
		{
			echo '<script language="javascript">
			
			function chooseCustomer ()
			{
				if (top.opener && !top.opener.closed)
				{
					top.opener.selectCustomer('.$data['customer_id'].');
					
					top.close();
				}
			}
			</script>';
		}
		
		echo '<table width="100%" height="100%" style="border: 1px solid black;">'.chr(10);
		echo '<tr><td align="center" height="40">'.chr(10);
		echo '<h1>'._('Viewing').' '.$data['customer_name'].'</h1>'.chr(10);
		
		echo '</td></tr>';
		
		echo '<td>'.chr(10);
		if($add)
			echo _('Customer has been created.');
		else
			echo _('Customer has been edited.');
		echo '<br><br>'.chr(10);
		
		echo '<table><tr><td align="right">'.chr(10);
		echo '<b>'._('Customer ID').':&nbsp;</b></td><td>'.$data['customer_id'].'</td></tr>'.chr(10);
		echo '<tr><td align="right">'.chr(10);
		echo '<b>'._('Name').':&nbsp;</b></td><td>'.$data['customer_name'].'</td></tr>'.chr(10);
		
		echo '<tr><td align="right"><b>'.str_replace(' ', '&nbsp;',_('Type of customer')).':&nbsp;</b></td><td>';
		if($data['customer_type'] == 'person')
			echo _('Private person').chr(10);
		elseif($data['customer_type'] == 'firm')
			echo _('School, company, organization, etc').chr(10);
		echo '</td></tr>'.chr(10);
		
		echo '<tr><td align="right"><b>'._('Municipal').':&nbsp;</b></td><td>'.chr(10);
		if($data['customer_municipal'] != '')
			echo $data['customer_municipal'].' ('.$data['customer_municipal_num'].')';
		else
			echo '<i>'._('Non selected').'</i>';
		echo '</td></tr>'.chr(10);
		
		echo '<tr><td>&nbsp;</td><td><br><input type="button" onclick="chooseCustomer(); return false;" value="'._('Choose this customer').'"></td></tr>'.chr(10);
		echo '</table>'.chr(10);
		echo '</td></tr></table>'.chr(10);
		exit();
	}
}

if(isset($_POST['form_submit']))
{
	$data	= array();
	$errors	= array();

	if(isset($_POST['customer_name']))
		$data['customer_name'] = trim(slashes(htmlspecialchars($_POST['customer_name'],ENT_QUOTES)));
	else
	{
		$data['customer_name'] = '';
		$errors['customer_name'] = '- '._('You must type in a name for the customer.');
	}
	if($data['customer_name'] == '')
		$errors['customer_name'] = '- '._('You must type in a name for the customer.');
	

	if(isset($_POST['customer_type']))
	{
		switch ($_POST['customer_type']) 
		{
			case 'person':
				$data['customer_type'] = 'person';
				break;
			case 'firm':
			default:
				$data['customer_type'] = 'firm';
				break;
		}
	}
	else
	{
		$data['customer_type'] = 'firm'; // Set to default
	}
	
	if(isset($_POST['customer_municipal_num']))
	{
		if (is_numeric($_POST['customer_municipal_num']) && isset($municipals[$_POST['customer_municipal_num']]))
		{
			$data['customer_municipal_num']	= $_POST['customer_municipal_num'];
			$data['customer_municipal']		= $municipals[$data['customer_municipal_num']];
		}
		else
		{
			$data['customer_municipal_num']	= '';
			$data['customer_municipal']		= '';
		}
	}
	else
	{
		$data['customer_municipal_num']	= '';
		$data['customer_municipal']		= '';
	}
	
	$data['customer_phone'] = array();
	$tmp_table_phone = array(); // Saving phone_id
	if(isset($_POST['rows_phone']) && is_array($_POST['rows_phone']))
	{
		foreach ($_POST['rows_phone'] as $id)
		{
			$thisone = array();
			$thisone['phone_id']	= 0;
			$thisone['phone_num']	= '';
			$thisone['phone_name']	= '';
			
			if(isset($_POST['phone_id'.$id]))
			{
				if(isset($customer['customer_phone']) &&
				is_array($customer['customer_phone']) && 
				array_key_exists($_POST['phone_id'.$id], $customer['customer_phone']))
					$thisone['phone_id']		= $_POST['phone_id'.$id];
			}
			if(isset($_POST['phone_num'.$id]))			$thisone['phone_num']	= $_POST['phone_num'.$id];
			if(isset($_POST['phone_name'.$id]))			$thisone['phone_name']	= $_POST['phone_name'.$id];
			
			if($thisone['phone_num'] != '' && $thisone['phone_name'] != '') {
				$data['customer_phone'][] = $thisone;
				$tmp_table_phone[$thisone['phone_id']] = $thisone['phone_id'];
			}
		}
	}
	
	$data['customer_address_id_invoice'] = -1;
	if(isset($_POST['customer_address_id_invoice']) && is_numeric($_POST['customer_address_id_invoice']))
		$data['customer_address_id_invoice'] = 'oldid'. ((int)$_POST['customer_address_id_invoice']);
	
	$data['customer_address'] = array();
	$tmp_table_address = array(); // Saving the address_id
	if(isset($_POST['rows_address']) && is_array($_POST['rows_address']))
	{
		$z = 0;
		foreach ($_POST['rows_address'] as $id)
		{
			$thisone = array();
			$thisone['address_id']		= 0;
			$thisone['address_info']	= '';
			$thisone['address_line_1']	= '';
			$thisone['address_line_2']	= '';
			$thisone['address_line_3']	= '';
			$thisone['address_line_4']	= '';
			$thisone['address_line_5']	= '';
			$thisone['address_line_6']	= ''; // Postalnumber + place
			$thisone['address_line_7']	= ''; // Country, if any
			$thisone['address_postalnum']	= ''; // Postalnumber
			
			
			if(isset($_POST['address_id'.$id]))
			{
				if(isset($customer['customer_address']) &&
				is_array($customer['customer_address']) && 
				array_key_exists($_POST['address_id'.$id], $customer['customer_address']))
					$thisone['address_id'] = $_POST['address_id'.$id];
			}
			if(isset($_POST['address_info'.$id]))
				$thisone['address_info'] = $_POST['address_info'.$id];
			if(isset($_POST['address_lines'.$id]))
			{
				$i = 0;
				foreach(explode("\n", $_POST['address_lines'.$id], 5) as $line)
				{
					$i++;
					if($i != 5)
						$thisone['address_line_'.$i] = trim(slashes(htmlspecialchars($line,ENT_QUOTES)));
					else
						$thisone['address_line_'.$i] = str_replace("\r", '', str_replace("\n", ', ', trim(slashes(htmlspecialchars($line,ENT_QUOTES)))));
				}
			}
			if(isset($_POST['address_postalnum'.$id]))
			{
				if(postalNumber($_POST['address_postalnum'.$id]))
				{
					$thisone['address_line_6']		= $_POST['address_postalnum'.$id].' '.slashes(htmlspecialchars(postalNumber($_POST['address_postalnum'.$id]),ENT_QUOTES));
					$thisone['address_postalnum']	= $_POST['address_postalnum'.$id];
				}
			}
			if(isset($_POST['address_country'.$id]))
				$thisone['address_line_7'] = slashes(htmlspecialchars($_POST['address_country'.$id],ENT_QUOTES));
			
			if($data['customer_address_id_invoice'] == 'oldid'.$id)
			{
				if($thisone['address_id'] != 0)
					$data['customer_address_id_invoice'] = $thisone['address_id'];
				else
					$data['customer_address_id_invoice'] = 'id'.$z;
			}
			
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
			
			if(
				($thisone['address_full'] == $data['customer_name'] 
					|| $thisone['address_full'] == '') 
				&& $thisone['address_info'] == ''
			)
			{
				// Ignore => is is just the default
			}
			else
			{
				$data['customer_address'][$z] = $thisone;
				$tmp_table_address[$thisone['address_id']] = $thisone['address_id'];
				$z++;
			}
		}
	}
	
	if(substr($data['customer_address_id_invoice'], 0, 5) == 'oldid')
		$data['customer_address_id_invoice'] = 0;
	
	if($customer_id != 0)
	{
		// Checking if some of the phones or addresses are to be deleted
		$delete_phone = array();
		foreach ($customer['customer_phone'] as $phone) {
			if(!in_array($phone['phone_id'], $tmp_table_phone))
				$delete_phone[$phone['phone_id']] = $phone['phone_id'];
		}
		$delete_address = array();
		foreach ($customer['customer_address'] as $address) {
			if(!in_array($address['address_id'], $tmp_table_address))
				$delete_address[$address['address_id']] = $address['address_id'];
		}
	}
	
	if(!count($errors))
	{
		// MYSQL...
		if($customer_id == 0)
		{
			// Add
			mysql_query("INSERT INTO `customer` (
				`customer_id` ,
				`customer_name` ,
				`customer_type` ,
				`customer_municipal_num`,
				`customer_address_id_invoice`
			) VALUES (
				NULL , 
				'".$data['customer_name']."', 
				'".$data['customer_type']."', 
				'".$data['customer_municipal_num']."',
				'".$data['customer_address_id_invoice']."'
			);");
			
			$customer_id = mysql_insert_id();
			
			foreach ($data['customer_phone'] as $phone)
			{
				mysql_query("INSERT INTO `customer_phone` (
					`phone_id` , `customer_id` , `phone_num` , `phone_name`) 
					VALUES (NULL , '$customer_id', '".$phone['phone_num']."', '".$phone['phone_name']."');");
			}
			
			$new_address_id = array();
			foreach ($data['customer_address'] as $z => $address)
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
					VALUES (NULL , '$customer_id', '".$address['address_info']."', 
						'".$address['address_line_1']."',
						'".$address['address_line_2']."',
						'".$address['address_line_3']."',
						'".$address['address_line_4']."',
						'".$address['address_line_5']."',
						'".$address['address_line_6']."',
						'".$address['address_line_7']."',
						'".$address['address_full']."',
						'".$address['address_postalnum']."'
						);");
				$new_address_id[$z] = mysql_insert_id();
			}
			
			if(substr($data['customer_address_id_invoice'], 0, 2) == 'id' && 
				isset($new_address_id[substr($data['customer_address_id_invoice'], 2)])) {
				mysql_query("UPDATE `customer` SET 
				`customer_address_id_invoice` = '".(int)$new_address_id[substr($data['customer_address_id_invoice'], 2)]."'
					WHERE `customer_id` = ".$customer_id." LIMIT 1 ;");
			}
			
			
			if(isset($_GET['returnToCustomerList']))
				header('Location: customer_list.php');
			elseif(isset($_GET['returnToCustomerView']))
				header('Location: customer.php?customer_id='.$customer_id);
			elseif(isset($_GET['returnToInvoiceCreate']))
				header('Location: '.$_SERVER['PHP_SELF'].'?returnToInvoiceCreate=1&viewer=1&add=1&customer_id='.$customer_id);
			else
				header('Location: '.$_SERVER['PHP_SELF'].'?viewer=1&add=1&customer_id='.$customer_id);
		}
		else
		{
			// Edit
			mysql_query("UPDATE `customer` SET 
				`customer_name` = '".$data['customer_name']."',
				`customer_type` = '".$data['customer_type']."',
				`customer_municipal_num` = '".$data['customer_municipal_num']."',
				`customer_address_id_invoice` = '".$data['customer_address_id_invoice']."'
			WHERE `customer_id` = ".$customer_id." LIMIT 1 ;");
			
			foreach ($data['customer_phone'] as $phone)
			{
				if($phone['phone_id'] == 0)
				{
					mysql_query("INSERT INTO `customer_phone` (
						`phone_id` , `customer_id` , `phone_num` , `phone_name`) 
						VALUES (NULL , '$customer_id', '".$phone['phone_num']."', '".$phone['phone_name']."');");
				} else {
					mysql_query("UPDATE `customer_phone` SET 
							`phone_num` = '".$phone['phone_num']."',
							`phone_name` = '".$phone['phone_name']."' 
						WHERE `phone_id` = ".$phone['phone_id']." LIMIT 1 ;");
				}
			}
			
			foreach ($data['customer_address'] as $z => $address)
			{
				if($address['address_id'] == 0)
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
						VALUES (NULL , '$customer_id', '".$address['address_info']."', 
							'".$address['address_line_1']."',
							'".$address['address_line_2']."',
							'".$address['address_line_3']."',
							'".$address['address_line_4']."',
							'".$address['address_line_5']."',
							'".$address['address_line_6']."',
							'".$address['address_line_7']."',
							'".$address['address_full']."',
							'".$address['address_postalnum']."'
							);");
					$new_address_id[$z] = mysql_insert_id();
				} else {
					mysql_query("UPDATE `customer_address` SET 
							`address_info` = '".$address['address_info']."',
							`address_line_1` = '".$address['address_line_1']."',
							`address_line_2` = '".$address['address_line_2']."',
							`address_line_3` = '".$address['address_line_3']."',
							`address_line_4` = '".$address['address_line_4']."',
							`address_line_5` = '".$address['address_line_5']."',
							`address_line_6` = '".$address['address_line_6']."',
							`address_line_7` = '".$address['address_line_7']."',
							`address_full` = '".$address['address_full']."',
							`address_postalnum` = '".$address['address_postalnum']."'
						WHERE `address_id` = ".$address['address_id']." LIMIT 1 ;");
					$new_address_id[$z] = $address['address_id'];
				}
			}
			
			foreach ($delete_phone as $phone_id) {
				mysql_query("DELETE FROM `customer_phone` WHERE `phone_id` = ".$phone_id." AND `customer_id` = ".$customer_id);
			}
			
			foreach ($delete_address as $address_id) {
				mysql_query("DELETE FROM `customer_address` WHERE `address_id` = ".$address_id." AND `customer_id` = ".$customer_id);
			}
			
			if(substr($data['customer_address_id_invoice'], 0, 2) == 'id' && 
				isset($new_address_id[substr($data['customer_address_id_invoice'], 2)])) {
				mysql_query("UPDATE `customer` SET 
				`customer_address_id_invoice` = '".(int)$new_address_id[substr($data['customer_address_id_invoice'], 2)]."'
					WHERE `customer_id` = ".$customer_id." LIMIT 1 ;");
			}
			
			if(isset($_GET['returnToCustomerList']))
				header('Location: customer_list.php');
			elseif(isset($_GET['returnToCustomerView']))
				header('Location: customer.php?customer_id='.$customer_id);
			elseif(isset($_GET['returnToInvoiceCreate']))
				header('Location: '.$_SERVER['PHP_SELF'].'?returnToInvoiceCreate=1&viewer=1&customer_id='.$customer_id);
			else
				header('Location: '.$_SERVER['PHP_SELF'].'?viewer=1&customer_id='.$customer_id);
		}
		
		exit();
	}
}
else
{
	// Default values or values from existing customer
	$errors = array();
	if($customer_id == 0)
	{
		$data = array();
		$data['customer_name']			= '';
		$data['customer_type']			= 'firm';
		$data['customer_municipal_num']	= '';
		$data['customer_phone']			= array();
		$data['customer_address']		= array();
		$data['customer_municipal']		= '';
		
		if(isset($_GET['customer_name']))
		{
			$data['customer_name'] = slashes(htmlspecialchars($_GET['customer_name'],ENT_QUOTES));
			
			if(!isset($_GET['customer_add_force']))
			{
				$Q_customer = mysql_query("select customer_id from `customer` 
					WHERE
						`customer_name` = '".$data['customer_name']."' AND
						`slettet` = '0'
					");
				if(mysql_num_rows($Q_customer))
				{
					filterMakeAlternatives();
					
					echo '<HTML>
					<HEAD>
					<TITLE>JM-booking</TITLE><LINK REL="stylesheet" href="default/mrbs.css" type="text/css">
					<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
					
					</HEAD>
					
					<body>';
					echo '<h1>Kunde med samme navn eksisterer</h1>';
					echo 'Det ble funnet en eller flere kunder i databasen som har samme navn som den du prøver &aring; opprette. Vil du velge en av de?<br><br>';
					echo '<script language="javascript">
				
					function chooseCustomer (id)
					{
						if (top.opener && !top.opener.closed)
						{
							top.opener.selectCustomer(id);
							
							top.close();
						}
					}
					</script>';
					echo '<table>';
					while($R = mysql_fetch_assoc($Q_customer))
					{
						$customer = getCustomer($R['customer_id']);
						$filter = addFilter(array(), 'customer_id', $customer['customer_id']);
						$filters_serialized = filterSerialized($filter);
						echo '	<tr>'.chr(10);
						echo '		<td><b>'.
							'<input type="button" onclick="chooseCustomer(\''.$customer['customer_id'].'\'); return false;" value="'._('Choose this customer').'"> '.
							'Kundenr '.$customer['customer_id'].', '.
							'<a href="customer.php?customer_id='.$customer['customer_id'].'">'.
							iconHTML('group').' '.
							$customer['customer_name'].'</a></b></td>'.chr(10);
						echo '		<td style="vertical-align: middle;">'.
						//'<font size="1">'.
						//'<a href="customer_edit.php?customer_id='.$customer['customer_id'].'">'.
						//iconHTML('group_edit').' '.
						//_('Edit').'</a>'.
						' (<a href="entry_list.php?filters='.$filters_serialized.'">'.
						//iconHTML('page_white').' '.
						mysql_num_rows(mysql_query(genSQLFromFilters ($filter, 'entry_id'))).
						' bookinger</a>)'.
						//'</font>'.
						'</td>'.chr(10);
						echo '	</tr>'.chr(10).chr(10);
					}
					echo '</table>';
					echo '<br><br>';
					echo '<form method="get">';
					echo '<input type="hidden" name="customer_name" value="'.$data['customer_name'].'">';
					echo '<input type="hidden" name="customer_add_force" value="yes">';
					echo '<input type="submit" value="Nei, jeg vil opprette ny">';
					echo '</form>';
					
					echo '</body></html>';
					exit();
				}
			}
		}
	}
	else
	{
		$data = $customer;
	}
}

/*
 * Form...
 * 
 * Data contains:
 * - customer_name
 * - customer_type
 * - customer_muncipal_num, id
 * - customer_phone, array
 * 		- phone_id, int id
 * 		- phone_num, varchar
 * 		- phone_name, varchar
 * - customer_address
 * 		- address_id, int id
 * 		- address_line_1 to _6
 * - customer_municipal
 * 
 */
/* Form..
 */

echo '<HTML>
<HEAD>
<TITLE>JM-booking</TITLE>
<LINK REL="stylesheet" href="default/mrbs.css" type="text/css">
<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css">
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp-postal.js"></script>
</HEAD>

<body>
';
echo '<script language="javascript">

function chooseMunicipal (id, name)
{
	wMunicipal = window.open("municipal_choose.php?id=" + id + "&name=" + name + "&two=1", "wMunicipal", "width=450,height=610");
	wMunicipal.focus();
}
</script>';


echo '<script type="text/javascript">'.chr(10);
echo 'var autos_options_post = {
	script: "autosuggest.php?",
	varname: "postal_place",
	json: true,
	maxresults: 35,
	shownoresults: false
};
';
echo '</script>'.chr(10);

echo '<script language="javascript" src="js/jquery-1.3.2.min.js"></script>'.chr(10);
echo '<script language="javascript" src="js/jquery-ui-1.7.2.full.min.js"></script>'.chr(10);
echo '<script language="javascript" src="js/customer-edit.js"></script>'.chr(10);


echo '<div align="center" height="40">'.chr(10);
if($customer_id == 0)
	echo '<h1>'._('Create new customer').'</h1>'.chr(10);
else
	echo '<h1>'._('Edit customer').' - '.$data['customer_name'].'</h1>'.chr(10);

echo '</div>';

if(count($errors))
{
	echo '<div class="error">'.chr(10);
	echo _('There was one or more errors in your inputdata').'<br>';
	echo implode ('<br>', $errors);
	echo '</div>'.chr(10);
}

if(isset($_GET['returnToCustomerList']))
	echo '<form action="'.$_SERVER['PHP_SELF'].'?returnToCustomerList=1" method="post" name="customer">'.chr(10);
elseif(isset($_GET['returnToCustomerView']))
	echo '<form action="'.$_SERVER['PHP_SELF'].'?returnToCustomerView=1" method="post" name="customer">'.chr(10);
elseif(isset($_GET['returnToInvoiceCreate']))
	echo '<form action="'.$_SERVER['PHP_SELF'].'?returnToInvoiceCreate=1" method="post" name="customer">'.chr(10);
else
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="customer">'.chr(10);


// Tabs
echo '
<div id="tabs">
	<ul>
		<li><a href="#area1">'._('Generell').'</a></li>
		<li><a href="#area2">'._('Phone numbers').'</a></li>
		<li><a href="#area3">'._('Addresses').'</a></li>
	</ul>
';

echo '<div id="area1">'.chr(10);
echo '<h2>'._('Generell').'</h2>'.chr(10);
echo '<table><tr><td align="right">'.chr(10);
echo '<b>'._('Name').':&nbsp;</b></td><td><input type="text" name="customer_name" '.
' id="customer_name" value="'.$data['customer_name'].'"></td></tr>'.chr(10);

echo '<tr><td align="right"><b>'._('Type of customer').':&nbsp;</b></td><td><select name="customer_type">'.chr(10);
echo '<option value="person"';
if($data['customer_type'] == 'person') echo ' selected';
echo '>'._('Private person').'</option>'.chr(10);
echo '<option value="firm"';
if($data['customer_type'] == 'firm') echo ' selected';
echo '>'._('School, company, organization, etc').'</option>'.chr(10);
echo '</td></tr>'.chr(10);

echo '<tr><td align="right"><b>'._('Municipal').':&nbsp;</b></td><td>'.chr(10);
echo '<input type="text" size="1" id="customer_municipal_num" value="'.$data['customer_municipal_num'].'" disabled="disabled">&nbsp;';
echo '<input type="text" size="15" id="customer_municipal" value="'.$data['customer_municipal'].'" disabled="disabled">&nbsp;';
echo '<input type="button" value="'._('Choose').'" onclick="chooseMunicipal(\'customer_municipal_num\', \'customer_municipal\'); return false;">'.chr(10);
echo '<input type="hidden" size="1" name="customer_municipal_num" id="customer_municipal_num2" value="'.$data['customer_municipal_num'].'">';
echo '<input type="hidden" size="15" name="customer_municipal" id="customer_municipal2" value="'.$data['customer_municipal'].'">';
echo '</td></tr>'.chr(10);

echo '</table></div>'.chr(10);

/* ### ### ### ### ### ### ### */


/*
 * test data:
$data['customer_phone'] = array();
$data['customer_phone'][] = array('phone_num' => '51703926', 'phone_name' => 'Sentralbord', 'phone_id' => '14');
 */

echo '<div id="area2">'.chr(10);
echo '<h2>'._('Phone numbers').'</h2>'.chr(10);
echo '<table id="fieldrowsphone"><tr><td><b>'._('Number').':</b></td><td><b>'._('Name').':</b></td></tr>'.chr(10);
$id = -1;
foreach ($data['customer_phone'] as $phone)
{
	$id++;
	echo '<tr id="rowphone'.$id.'">'.chr(10);
	echo '<td>';
	echo '<input type="hidden" name="rows_phone[]" value="'.$id.'">'.chr(10);
	echo '<input type="hidden" name="phone_id'.$id.'" id="phone_id'.$id.'" value="'.$phone['phone_id'].'">'.chr(10);
	echo '<input type="text" size="7" name="phone_num'.$id.'" id="phone_num'.$id.'" value="'.$phone['phone_num'].'"></td>'.chr(10);
	echo '<td><input type="text" size="30" name="phone_name'.$id.'" id="phone_name'.$id.'" value="'.$phone['phone_name'].'"></td>'.chr(10);
	echo '<td><input type="button" value="Fjern linje" onclick="removeFieldPhone(\''.$id.'\');"></td>'.chr(10);
	echo '</tr>'.chr(10);
}
echo '</table>

<input type="button" value="'._('Add one more phone fields').'" onclick="addFieldPhone();">
</div>'.chr(10);

/* ### ### ### ### ### ### ### */


echo '<div id="area3">'.chr(10);
echo '<h2>'._('Addresses').'</h2>'.chr(10);
echo '<table id="fieldrowsaddress">'.
'	<tr>
		<td><b>'._('Invoice address').':</b></td>
		<td><b>'._('Address name/info').':</b></td>
		<td><b>'._('Address').'*:</b></td>
		<td><b>'._('Postalnumber').'**:</b></td>
		<td><b>'._('Country').'***:</b></td>
	</tr>'.chr(10);

/*
 * test data:
$data['customer_address_id_invoice'] = 0;
$data['customer_address'] = array();
$data['customer_address'][] = 
	array('address_id' => 0,
	'address_info' => '',
	'address_line_1' => $data['customer_name'],
	'address_line_2' => '',
	'address_line_3' => '',
	'address_line_4' => '',
	'address_line_5' => '',
	'address_line_6' => '');
 */

$id = -1;
$run_after = '';
foreach ($data['customer_address'] as $thisnum => $address)
{
	$id++;
	echo '<tr id="rowaddress'.$id.'">'.chr(10);
	
	echo '<td align="right">';
	echo '<input type="hidden" name="rows_address[]" value="'.$id.'">'.chr(10);
	echo '<input type="hidden" name="address_id'.$id.'" id="address_id'.$id.'" value="'.$address['address_id'].'">'.chr(10);
	
	echo '<input type="radio" name="customer_address_id_invoice" value="'.$id.'"';
	if($data['customer_address_id_invoice'] == 'id'.$thisnum)
		echo ' checked="checked"';
	elseif($address['address_id'] != 0 && $data['customer_address_id_invoice'] == $address['address_id'])
		echo ' checked="checked"';
	echo '></td>'.chr(10);
	
	echo '<td><input type="text" size="20" name="address_info'.$id.'" id="address_info'.$id.'" value="'.$address['address_info'].'"></td>'.chr(10);
	echo '<td><textarea rows="4" cols="25" name="address_lines'.$id.'" id="address_lines'.$id.'">';
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
	echo '</textarea></td>'.chr(10);
	echo '<td><input type="text" size="5" name="address_postalnum'.$id.'" id="address_postalnum'.$id.'" '.
	'value="'.$address['address_postalnum'].'" '.
	'onkeyup="selectPostalNumber (\'address_postalplace'.$id.'\', document.getElementById(\'address_postalnum'.$id.'\').value);" '.
	'autocomplete="off"><br>'.
	'<input type="text" size="12" name="address_postalplace'.$id.'" id="address_postalplace'.$id.'" value="'.postalNumber($address['address_postalnum']).'"></td>'.chr(10);
	echo '<td><input type="text" size="15" name="address_country'.$id.'" value="'.$address['address_line_7'].'"></td>'.chr(10);
	echo '<td><input type="button" value="Fjern linje" onclick="removeFieldAddress(\''.$id.'\');"></td>'.chr(10);
	echo '</tr>'.chr(10);
	
	$run_after .= 'as['.$id.'] = new bsn.AutoSuggest(\'address_postalplace\' + '.$id.', autos_options_post, \'address_postalnum\' + '.$id.', \'\');'.chr(10);
}
echo '</table>

<input type="button" value="'._('Add one more address field').'" onclick="addFieldAddress();"><br><br>

* '._('Max 5 lines in each address').'<br>
** '._('Place will be automaticlly found from the postalnumber. If the number is not valid, the field will just be empty.').' '._('You can also type a postal place in the field below.').'<br>
*** '._('For Norway, please leave this field empty.').'<br>
<br>
</div>'.chr(10);

/* ### ### ### ### ### ### ### */

echo '</div>';

echo '<br><br><input type="submit" class="title" value="';
if($customer_id == 0)
	echo _('Create customer');
else
	echo _('Save changes');
echo '">'.chr(10);
if($customer_id != 0)
	echo '<input type="hidden" name="customer_id" value="'.$customer_id.'">'.chr(10);
echo '<input type="hidden" name="form_submit" value="1">'.chr(10);
echo '</form>'.chr(10);


echo '<script langauge="javascript">'.chr(10);
echo 'var as = new Array();'.chr(10);
if(!count($data['customer_phone']))
	echo 'addFieldPhone();'.chr(10);
if(!count($data['customer_address']))
	echo 'addFieldAddress();'.chr(10);
echo $run_after;
echo '</script>'.chr(10);

echo '</body></html>';
?>