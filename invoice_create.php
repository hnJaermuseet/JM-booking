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

require "include/invoice_top.php";

$section = 'create';
$new_datafrom = false;

// Input?
if(isset($_POST['invoice_input']))
{
	// All checking is in invoice.class
	$TMP_invoice_extra_content = array();
	$TMP_invoice_extra_content['invoice_created_by_id']	= $login['user_id'];
	
	if(isset($_POST['invoice_to_customer_id']))
		$TMP_invoice_extra_content['invoice_to_customer_id']	= $_POST['invoice_to_customer_id'];
	else
		$TMP_invoice_extra_content['invoice_to_customer_id']	= '';
	
	if(isset($_POST['invoice_to_address_id']))
		$TMP_invoice_extra_content['invoice_to_address_id']		= $_POST['invoice_to_address_id'];
	else
		$TMP_invoice_extra_content['invoice_to_address_id']		= '';
	
	if(isset($_POST['invoice_to_line1']))
		$TMP_invoice_extra_content['invoice_to_line1']		= $_POST['invoice_to_line1'];
	else
		$TMP_invoice_extra_content['invoice_to_line1']		= '';
	
	if(isset($_POST['invoice_to_line2']))
		$TMP_invoice_extra_content['invoice_to_line2']		= $_POST['invoice_to_line2'];
	else
		$TMP_invoice_extra_content['invoice_to_line2']		= '';
	
	if(isset($_POST['invoice_to_line3']))
		$TMP_invoice_extra_content['invoice_to_line3']		= $_POST['invoice_to_line3'];
	else
		$TMP_invoice_extra_content['invoice_to_line3']		= '';
	
	if(isset($_POST['invoice_to_line4']))
		$TMP_invoice_extra_content['invoice_to_line4']		= $_POST['invoice_to_line4'];
	else
		$TMP_invoice_extra_content['invoice_to_line4']		= '';
	
	if(isset($_POST['invoice_to_line5']))
		$TMP_invoice_extra_content['invoice_to_line5']		= $_POST['invoice_to_line5'];
	else
		$TMP_invoice_extra_content['invoice_to_line5']		= '';
	
	if(isset($_POST['invoice_to_line6']))
		$TMP_invoice_extra_content['invoice_to_line6']		= $_POST['invoice_to_line6'];
	else
		$TMP_invoice_extra_content['invoice_to_line6']		= '';
	
	if(isset($_POST['invoice_to_line7']))
		$TMP_invoice_extra_content['invoice_to_line7']		= $_POST['invoice_to_line7'];
	else
		$TMP_invoice_extra_content['invoice_to_line7']		= '';
	/*
	if(isset($_POST['invoice_to_phone']))
		$TMP_invoice_extra_content['invoice_to_phone']		= $_POST['invoice_to_phone'];
	else
		$TMP_invoice_extra_content['invoice_to_phone']		= '';
	*/
	if(isset($_POST['invoice_to_email']))
		$TMP_invoice_extra_content['invoice_to_email']		= $_POST['invoice_to_email'];
	else
		$TMP_invoice_extra_content['invoice_to_email']		= '';
	
	if(isset($_POST['invoice_electronic']))
		$TMP_invoice_extra_content['invoice_electronic']		= $_POST['invoice_electronic'];
	else
		$TMP_invoice_extra_content['invoice_electronic']		= '';
	
	if(isset($_POST['invoice_comment']))
		$TMP_invoice_extra_content['invoice_comment']			= $_POST['invoice_comment'];
	else
		$TMP_invoice_extra_content['invoice_comment']			= '';
	
	if(isset($_POST['invoice_internal_comment']))
		$TMP_invoice_extra_content['invoice_internal_comment']	= $_POST['invoice_internal_comment'];
	else
		$TMP_invoice_extra_content['invoice_internal_comment']	= '';
	
	if(isset($_POST['invoice_ref_your']))
		$TMP_invoice_extra_content['invoice_ref_your']			= $_POST['invoice_ref_your'];
	else
		$TMP_invoice_extra_content['invoice_ref_your']			= '';
	
	if(isset($_POST['invoice_time']))
		$TMP_invoice_extra_content['invoice_time']			= $_POST['invoice_time'];
	else
		$TMP_invoice_extra_content['invoice_time']			= '';
	
	if(isset($_POST['invoice_time_due']))
		$TMP_invoice_extra_content['invoice_time_due']		= $_POST['invoice_time_due'];
	else
		$TMP_invoice_extra_content['invoice_time_due']		= '';
	
	if(isset($_POST['invoice_idlinks']))
		$TMP_invoice_extra_content['invoice_idlinks']		= $_POST['invoice_idlinks'];
	else
		$TMP_invoice_extra_content['invoice_idlinks']		= '';
	
	$TMP_invoice_extra_content['invoice_time'] = str_replace('-', '', str_replace('.', '', $TMP_invoice_extra_content['invoice_time']));
	$TMP_invoice_extra_content['invoice_time_due'] = str_replace('-', '', str_replace('.', '', $TMP_invoice_extra_content['invoice_time_due']));
	
	// Getting invoice_content
	$TMP_invoice_content = array();
	$i = 0;
	if(isset($_POST['rows']) && is_array($_POST['rows']))
	{
		foreach ($_POST['rows'] as $id)
		{
			$i++;
			$thisone = array();
			$thisone['type']		= 'belop';
			$thisone['topay_each']	= 0;
			$thisone['amount']		= 1;
			$thisone['tax']			= 0;
			$thisone['name']		= '';
			$thisone['id_of_type']	= 0;
			$thisone['mva_eks']		= true;
			
			if(isset($_POST['type'.$id]))				$thisone['type']		= $_POST['type'.$id];
			if(isset($_POST['belop_hver_real'.$id]))	$thisone['topay_each']	= $_POST['belop_hver_real'.$id];
			if(isset($_POST['antall'.$id]))				$thisone['amount']		= $_POST['antall'.$id];
			if(isset($_POST['mva'.$id]))				$thisone['tax']			= $_POST['mva'.$id];
			if(isset($_POST['name'.$id]))				$thisone['name']		= $_POST['name'.$id];
			if(isset($_POST['id_type'.$id]))			$thisone['id_of_type']	= $_POST['id_type'.$id];
			
			$thisone['tax'] = ((float)$thisone['tax'])/100;
			
			$TMP_invoice_content[$i] = $thisone;
		}
	}
	
	$warning = array();
	
	/*
	if($TMP_invoice_extra_content['invoice_time'] < date('Ymd'))
		$warning[] = 'Fakturadato må være i dag eller senere.';
	elseif($TMP_invoice_extra_content['invoice_time'] > $TMP_invoice_extra_content['invoice_time_due'])
		$warning[] = 'Fakturadato må være før forfallsdato.';
	*/
	
	if(!isset($_POST['invoice_preview']))
		$preview = false;
	elseif($_POST['invoice_preview'] == '1')
		$preview = true;
	else
		$preview = false;
	
	if(!count($warning))
	{
		$invoice = new invoice();
	}
	
	if(!count($warning) && $preview)
	{
		if(!$invoice->doDaChecking(
			$TMP_invoice_extra_content['invoice_time_due'],
			$TMP_invoice_extra_content['invoice_time'],
			$TMP_invoice_content,
			$TMP_invoice_extra_content))
		{
			if($invoice->error_code != '100')
			{
				require "include/invoice_menu.php";
				echo '<h1>Opprett faktura - feilmelding</h1>'.chr(10);
				
				echo 'Problemer med faktura. Vennligst ta kontakt.';
				echo '<br><br>';
				echo 'Feilmelding:<br>';
				echo $invoice->error();
				
				exit();
			}
			else
			{
				$error_msg = $invoice->error();
				$error = $invoice->inndata_error; // array
				$preview = false;		
			}
		}
	}
	elseif(!count($warning))
	{
		if(!$invoice->create (
			$TMP_invoice_extra_content['invoice_time_due'],
			$TMP_invoice_extra_content['invoice_time'],
			$TMP_invoice_content,
			$TMP_invoice_extra_content
		))
		{
			if($invoice->error_code != '100')
			{
				require "include/invoice_menu.php";
				echo '<h1>Opprett faktura - feilmelding</h1>'.chr(10);
				
				echo 'Problemer med faktura. Vennligst ta kontakt.';
				echo '<br><br>';
				echo 'Feilmelding:<br>';
				echo $invoice->error();
				
				exit();
			}
			else
			{
				$error_msg = $invoice->error();
				$error = $invoice->inndata_error; // array				
			}
		}
		else
		{
			// Redirect
			header('Location: invoice_view.php?invoice_id='.$invoice->invoice_id);
			exit();
		}
	}
}
else
{
	// Clean data
	$invoice = new invoice();
	
	// Entry sent?
	if(isset($_GET['entry_ids']))
	{
		$entries1 = splittIDs($_GET['entry_ids']);
		if(count($entries1))
		{
			$get_this_from_entry = array(
				'customer_id' => 'invoice_to_customer_id',
				'invoice_address_id' => 'invoice_to_address_id',
				'invoice_electronic' => 'invoice_electronic',
				'invoice_email' => 'invoice_to_email'
			);
			
			$to_invoice = array();
			$entries = array();
			$failed_in = array();
			foreach ($entries1 as $entry_id)
			{
				$entry_id = (int)$entry_id;
				$entries[$entry_id] = getEntry($entry_id);
				if(!count($entries[$entry_id]))
				{
					echo '<b>'._('Error:').'</b>'._('Can\'t find the entry/one of the entries you tried to make an invoice from.');
					exit();
				}
				
				$invoice->invoice_idlinks[] = 'e='.$entry_id;
				
				// Setting the info found in the entry
				foreach ($get_this_from_entry as $entry_var => $invoice_var)
				{
					if(isset($to_invoice[$invoice_var]) && $to_invoice[$invoice_var] != $entries[$entry_id][$entry_var])
						$failed_in[] = $entry_var;
					elseif(!isset($to_invoice[$invoice_var]))
						$to_invoice[$invoice_var] = $entries[$entry_id][$entry_var];
				}
				
				// Invoice_content
				foreach ($entries[$entry_id]['invoice_content'] as $invoice_content)
				{
					// Translation from some norwegian variablenames
					$content = array();
					$content['type']			= 'entry';
					$content['topay_each']		= $invoice_content['belop_hver'];
					$content['amount']			= $invoice_content['antall'];
					$content['tax']				= $invoice_content['mva'];
					if(count($entries1) > 1)
						$content['name']		= '(BID'.$entry_id.') '.$invoice_content['name'];
					else
						$content['name']		= $invoice_content['name'];
					$content['id_of_type']		= $entry_id;
					$content['mva_eks']			= $invoice_content['mva_eks'];
					$content['topay_total_net']	= $invoice_content['belop_sum_netto'];
					$content['tax_total']		= $invoice_content['mva_sum'];
					$content['topay_total']		= $invoice_content['belop_sum'];
					$to_invoice['invoice_content'][] = $content;
				}
				
				// Invoice_ref_your
				if(!isset($to_invoice['invoice_ref_your'])) $to_invoice['invoice_ref_your'] = '';
				if($entries[$entry_id]['invoice_ref_your'] != '')
				{
					if($to_invoice['invoice_ref_your'] != '')
						$to_invoice['invoice_ref_your'] .= ', ';
					$to_invoice['invoice_ref_your'] .= '(BID'.$entry_id.') '.$entries[$entry_id]['invoice_ref_your'];
				}
				
				// Invoice_comment
				if(!isset($to_invoice['invoice_comment']))
					$to_invoice['invoice_comment'] = '';
				if($entries[$entry_id]['invoice_comment'] != '')
				{
					if($to_invoice['invoice_comment'] != '')
						$to_invoice['invoice_comment'] .= "\n\n";
					$to_invoice['invoice_comment'] .= 
					'- Kommentar fra BID'.$entry_id.':'.chr(10).
					$entries[$entry_id]['invoice_comment'];
				}
				
				// Invoice_internal_comment
				if(!isset($to_invoice['invoice_internal_comment']))
					$to_invoice['invoice_internal_comment'] = '';
				if($entries[$entry_id]['invoice_internal_comment'] != '')
				{
					if($to_invoice['invoice_internal_comment'] != '')
						$to_invoice['invoice_internal_comment'] .= "\n\n";
					$to_invoice['invoice_internal_comment'] .= 
					'- Internkommentar fra BID'.$entry_id.':'.chr(10).
					$entries[$entry_id]['invoice_internal_comment'];
				}
			}
			
			if(count($failed_in))
			{
				require "include/invoice_menu.php";
				echo '<h1>Opprett faktura</h1>'.chr(10);
				echo '<b>Feil oppsto!</b><br>';
				echo 'Bookingene hadde ikke de samme opplysningene i forhold til faktura:<br>';
				foreach ($failed_in as $error_var)
				{
					echo '- ';
					switch ($error_var)
					{
						case 'customer_id':	echo 'Ikke samme kunde.';	break;
						case 'invoice_address_id':	echo 'Ikke samme fakturaadresse.';	break;
						default:			echo 'Ukjent feil ('.$error_var.').';	break;
					}
					echo '<br>'.chr(10);
				}
				
				echo '<br>Du prøvde å hente fra følgenede bookinger:<br>';
				foreach($invoice->invoice_idlinks as $link)
				{
					list($idtype, $id) = explode('=', $link);
					switch ($idtype)
					{
							case 'e': // Entry
							echo '- ';
							$thisentry = getEntry ($id);
							if(!count($thisentry))
								echo '(BID'.$id.') UKJENT BOOKING (ikke funnet i databasen)';
							else
								echo date('d-m-Y', $thisentry['time_start']).' - '.
								'<a href="entry.php?entry_id='.$thisentry['entry_id'].'">'.
								$thisentry['entry_name'].'</a>'.
								' (BID'.$id.')';
							echo '<br>';
							break;
						
					}
				}
				exit();
			}
			foreach($to_invoice as $invoice_var => $invoice_value)
			{
				$invoice->$invoice_var = $invoice_value;
			}
			
			$new_datafrom = true;
			
			if($invoice->invoice_to_address_id > 0)
			{
				$address = getAddress($invoice->invoice_to_address_id);
				if(count($address))
				{
					$invoice->invoice_to_line1 = $address['address_line_1'];
					$invoice->invoice_to_line2 = $address['address_line_2'];
					$invoice->invoice_to_line3 = $address['address_line_3'];
					$invoice->invoice_to_line4 = $address['address_line_4'];
					$invoice->invoice_to_line5 = $address['address_line_5'];
					$invoice->invoice_to_line6 = $address['address_line_6'];
					$invoice->invoice_to_line7 = $address['address_line_7'];
				}
				else
					$invoice->invoice_to_address_id = '';
			}
			else
				$invoice->invoice_to_address_id = '';
			
			if($invoice->invoice_to_customer_id > 0)
			{
				$customer = getCustomer($invoice->invoice_to_customer_id);
				if(count($customer))
					$invoice->invoice_to_customer_name = $customer['customer_name'];
			}
		}
	}
}
require "include/invoice_menu.php";

if(isset($preview) && $preview)
{
	echo '<h1>Forhåndsvisning</h2>';
	echo '<b>(Opprettelse forsetter under)</b><br>';
	echo '<div style="border: 1px solid black; overflow: auto; width: 800px; height: 400px;">';
	$smarty = new Smarty;
	
	templateAssignInvoice('smarty', $invoice);
	templateAssignSystemvars('smarty');
	
	$smarty->assign('invoice_heading', 'Forhåndsvisning av faktura');
	$smarty->assign('invoice_css', 'invoice_css.css');
	$smarty->display('file:invoice.tpl');
	echo '</div>';
}

echo '<h1>Opprett faktura</h1>'.chr(10);

echo '<script language="javascript" src="js/jquery-1.3.2.min.js"></script>'.chr(10);

//echo nl2br(str_replace('  ', '_ ', print_r($invoice, 1)));
echo '<form method="post" name="invoiceform" action="'.$_SERVER['PHP_SELF'].'">'.chr(10);

if(count($invoice->invoice_idlinks))
{
	$datafrom_string = 'Fakturadata hentet fra'.chr(10);
	foreach($invoice->invoice_idlinks as $link)
	{
		list($idtype, $id) = explode('=', $link);
		$datafrom_string .= '- ';
		switch ($idtype)
		{
			case 'e': // Entry
				$thisentry = getEntry ($id);
				if(!count($thisentry))
					$datafrom_string .= '(BID'.$id.') UKJENT BOOKING (ikke funnet i databasen)';
				else
				{
					$entryarea = getArea($thisentry['area_id']);
					$a = '';
					if(count($entryarea))
						$a = ' - '.$entryarea['area_name'];
					$datafrom_string .= 'Booking'.$a.', '.date('d-m-Y', $thisentry['time_start']).', BID'.$id.' - '.
					'<a href="entry.php?entry_id='.$thisentry['entry_id'].'">'.
					$thisentry['entry_name'].'</a>';
					//' (BID'.$id.')';
					if($thisentry['contact_person_name'] != '')
						$datafrom_string .= ', kontaktperson: '.$thisentry['contact_person_name'];
				}
				break;
			
			default:
				$datafrom_string .= _('Unknown sourcetype');
			break;
		}
		$datafrom_string .= chr(10);
	}
	echo nl2br($datafrom_string);
	
	if($new_datafrom)
	{
		if($invoice->invoice_comment != '')
			$invoice->invoice_comment = strip_tags($datafrom_string)."\n".$invoice->invoice_comment;
		else
			$invoice->invoice_comment = strip_tags($datafrom_string);
	}
}
else
	echo 'Blank faktura opprettes. Tast inn alle detaljer.';
echo '<br><br>'.chr(10).chr(10);

if(isset($error) && is_array($error) && count($error))
{
	echo '<h2 style="color: red;">Feilmeldinger</h2>'.chr(10);
	echo $error_msg.'<br><br>';
	foreach($error as $warn)
	{
		echo '- '.$warn.'<br>'.chr(10);
	}
	echo '<br><br>';
}

if(isset($warning) && is_array($warning) && count($warning))
{
	echo '<h2 color="red">Advarsler</h2>'.chr(10);
	foreach($warning as $warn)
	{
		echo '- '.$warn.'<br>'.chr(10);
	}
	echo '<br><br>';
}

//echo '<h2>Til:</h2>'.chr(10);

echo '<table class="invoiceinfo">'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">'.iconHTML('group').' Kunde&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_customer_name" id="invoice_to_customer_name" value="'.$invoice->invoice_to_customer_name.'" autocomplete="off">'.
'<input size="4" type="text" name="invoice_to_customer_id2" id="invoice_to_customer_id2" value="'.$invoice->invoice_to_customer_id.'" disabled="disabled">'.
'<input type="hidden" name="invoice_to_customer_id" id="invoice_to_customer_id" value="'.$invoice->invoice_to_customer_id.'">'.
'<input type="button" onclick="new_customer(); return false;" value="+"/>'.
'</td>'.chr(10);

echo '<td class="invoiceinfo">Deres ref.&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo"><input size="40" type="text" name="invoice_ref_your" value="'.$invoice->invoice_ref_your.'"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo">
<td class="invoiceinfo">'.iconHTML('email').' Adr.id&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="4" type="text" name="invoice_to_address_id2" id="invoice_to_address_id2" value="'.$invoice->invoice_to_address_id.'" disabled="disabled">'.
'<input type="hidden" name="invoice_to_address_id" id="invoice_to_address_id" value="'.$invoice->invoice_to_address_id.'">'.
'<input type="button" value="Endre/velg annen adresse" onclick="chooseAddress(\'invoice_to_address_id\', \'invoice_to_address\'); return false;">'.
'</td>';
echo '<td class="invoiceinfo">E-post&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo"><input size="40" type="text" name="invoice_to_email" value="'.$invoice->invoice_to_email.'"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">Adr.linje 1&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_line1" id="invoice_to_line1" '.
'value="'.$invoice->invoice_to_line1.'" onkeyup="onchangeAddress();"></td>'.chr(10);

echo '<td class="invoiceinfo">Efaktura&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo"><input size="40" type="text" name="invoice_electronic" value="'.$invoice->invoice_electronic.'"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">Adr.linje 2&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_line2" id="invoice_to_line2" '.
'value="'.$invoice->invoice_to_line2.'" onkeyup="onchangeAddress();"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">Adr.linje 3&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_line3" id="invoice_to_line3" '.
'value="'.$invoice->invoice_to_line3.'" onkeyup="onchangeAddress();"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">Adr.linje 4&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_line4" id="invoice_to_line4" '.
'value="'.$invoice->invoice_to_line4.'" onkeyup="onchangeAddress();"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">Adr.linje 5&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_line5" id="invoice_to_line5" '.
'value="'.$invoice->invoice_to_line5.'" onkeyup="onchangeAddress();"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">Adr.linje 6&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_line6" id="invoice_to_line6" '.
'value="'.$invoice->invoice_to_line6.'" onkeyup="onchangeAddress();"></td></tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">Adr.linje 7&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo">'.
'<input size="40" type="text" name="invoice_to_line7" id="invoice_to_line7" '.
'value="'.$invoice->invoice_to_line7.'" onkeyup="onchangeAddress();"></td></tr>'.chr(10);

echo '</td></tr>';

echo '</table>'.chr(10);
echo '
<table><tr><td>
'.iconHTML('comment').' Kommentar:</td><td>Intern kommentar:</td></tr>

<tr>
	<td>
		<textarea cols="50" rows="10" name="invoice_comment">'.$invoice->invoice_comment.'</textarea>
	</td>
	<td>
		<textarea cols="50" rows="10" name="invoice_internal_comment">'.$invoice->invoice_internal_comment.'</textarea>
	</td>
</tr></table>
';



echo '
<br>
<b>Produktlinjer:</b><br>
<table><tr><td>
<table id="invoicerows">'.chr(10);

echo '<tr>'.chr(10);
echo ' <td>Linjenr</td>'.chr(10);
echo ' <td>Produktbeskrivelse</td>'.chr(10);
echo ' <td>Pris</td>'.chr(10);
echo ' <td>Antall</td>'.chr(10);
echo ' <td>MVA-%</td>'.chr(10);
echo ' <td>Eks.mva?</td>'.chr(10);
echo ' <td>MVA-hver</td>'.chr(10);
echo ' <td>Sum-MVA</td>'.chr(10);
echo ' <td>Sum</td>'.chr(10);
echo ' <td>&nbsp;</td>'.chr(10);
echo '</tr>'.chr(10).chr(10);

$id = 0;
$after_run = '';
foreach ($invoice->invoice_content as $invoice_content)
{
	$id++;
	echo '<tr id="row'.$id.'">'.chr(10);
	echo ' <input type="hidden" name="rows[]" value="'.$id.'">'.chr(10);
	echo ' <input type="hidden" name="type'.$id.'" value="belop">'.chr(10);
	echo ' <input type="hidden" name="id_type'.$id.'" value="0">'.chr(10); // Disabled
	echo ' <input type="hidden" name="belop_hver_real'.$id.'" id="belop_hver_real'.$id.'" value="0">'.chr(10); // The real value of this one, eks tax
	
	// Linjenum
	echo ' <td><input type="text" size="3" value="'.$id.'" disabled></td>'.chr(10);
	
	// Beskrivelse
	echo ' <td><textarea rows="1" cols="50" name="name'.$id.'">'.$invoice_content['name'].'</textarea></td>'.chr(10);
	
	// Belop_hver
	echo ' <td><input class="right" type="text" size="6" id="belop_hver'.$id.'" name="belop_hver'.$id.'" value="'.$invoice_content['topay_each'].'" onchange="updateMva('.$id.');"></td>'.chr(10);
	
	// Antall
	echo ' <td><input class="right" type="text" size="6" id="antall'.$id.'" name="antall'.$id.'" value="'.$invoice_content['amount'].'" onchange="updateMva('.$id.');"></td>'.chr(10);
	
	// Mva
	echo ' <td><input class="right" type="text" size="3" id="mva'.$id.'" name="mva'.$id.'" value="'.($invoice_content['tax']*100).'" onchange="updateMva('.$id.');"></td>'.chr(10);
	
	// Ink mva / eks mva
	echo ' <td><input name="mva_eks'.$id.'" id="mva_eks'.$id.'" value="1" type="checkbox" onchange="updateMva('.$id.');"';
	if($invoice_content['mva_eks'])
		echo ' checked="checked"';
	echo '></td>'.chr(10);
	
	// Mva_hver
	echo ' <td><input class="right" type="text" size="3" id="mva_hver'.$id.'" name="mva_hver'.$id.'" value="" disabled>'.chr(10);
	
	// Mva_sum
	echo ' <td><input class="right" type="text" size="3" id="mva_sum_hver'.$id.'" name="mva_sum_hver'.$id.'" value="" disabled>'.chr(10);
	
	// Belop_sum
	echo ' <td><input class="right" type="text" size="6" id="belop_delsum'.$id.'" name="belop_delsum'.$id.'" value="" disabled></td>'.chr(10);
	
	// RemoveField
	echo ' <td><input type="button" value="Ta vekk linje" onclick="removeField(\''.$id.'\');"></td>'.chr(10);
	echo '</tr>'.chr(10);
	
	$after_run .= 'updateMva('.$id.');'.chr(10); 
}
if(!count($invoice->invoice_content))
	$after_run .= 'addFieldInvoice();'.chr(10);

echo '</table><br>'.chr(10);
echo '</td></tr><tr><td align="right">';
echo '<input type="button" value="Legg til ny linje" onclick="addFieldInvoice();">'.chr(10);

echo '<br><br>'.chr(10).chr(10);

echo '<b>Sum å betale:</b>&nbsp;'.chr(10);
echo '<input type="text" id="belop_sum" name="belop_sum" value="0" disabled="disabled" class="title right" style="width: 200px;"><br>'.chr(10);
echo 'MVA:&nbsp;'.chr(10);
echo '<input type="text" id="mva_sum" name="mva_sum" value="0" disabled="disabled" class="right" style="width: 200px;padding:5px;">'.chr(10);

echo '</td></tr></table>'.chr(10);

/*
echo '<table>'.chr(10);
echo '	<tr>'.chr(10);
echo '		<td>MVA-%</td>'.chr(10);
echo '		<td>Grunnlag</td>'.chr(10);
echo '		<td>MVA</td>'.chr(10);
echo '	</tr>'.chr(10);

echo '	<tr>
		<td align="right">25&nbsp;%</td>
		<td align="right">kr&nbsp;800,00</td>
		<td align="right">kr&nbsp;200,00</td>
	</tr>
	<tr>
		<td align="right">SUM&nbsp;MVA</td>
		<td align="right">kr&nbsp;800,00</td>
		<td align="right">kr&nbsp;200,00</td>
	</tr>
</table>
';

echo '<br><br>'.chr(10).chr(10);*/


// Datoer
echo '<table>'.chr(10);
echo '<tr class="invoiceinfo"><td class="invoiceinfo">'.iconHTML('date').' Fakturadato&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo"><input size="40" type="text" name="invoice_time" value="'.
substr($invoice->invoice_time, 0, 4).'-'.substr($invoice->invoice_time, 4, 2).'-'.substr($invoice->invoice_time, 6).'"></td>'.
'</tr>'.chr(10);

echo '<tr class="invoiceinfo"><td class="invoiceinfo">'.iconHTML('date_delete').' Forfallsdato&nbsp;&nbsp;</td>'.
'<td class="invoiceinfo"><input size="40" type="text" name="invoice_time_due"  id="invoice_time_due" value="'.
substr($invoice->invoice_time_due, 0, 4).'-'.substr($invoice->invoice_time_due, 4, 2).'-'.substr($invoice->invoice_time_due, 6).'"></td>'.
'<td>';

$time = mktime(0,0,0,$invoice->invoice_time2['month'], $invoice->invoice_time2['day'], $invoice->invoice_time2['year']);
echo '
<input type="button" onclick="document.getElementById(\'invoice_time_due\').value=\''.date('Y-m-d', ($time+((1*7)*24*60*60))).'\'; return false;" value="1 uke">
<input type="button" onclick="document.getElementById(\'invoice_time_due\').value=\''.date('Y-m-d', ($time+((2*7)*24*60*60))).'\'; return false;" value="2 uke">
<input type="button" onclick="document.getElementById(\'invoice_time_due\').value=\''.date('Y-m-d', ($time+((3*7)*24*60*60))).'\'; return false;" value="3 uke">
<input type="button" onclick="document.getElementById(\'invoice_time_due\').value=\''.date('Y-m-d', ($time+((4*7)*24*60*60))).'\'; return false;" value="4 uke">

</td></tr>'.chr(10);

echo '</table><br>'.chr(10);


if(count($invoice->invoice_idlinks))
{
	echo 'Denne fakturaen er lenket med:<br>';
	foreach($invoice->invoice_idlinks as $link)
	{
		list($idtype, $id) = explode('=', $link);
		echo '<input type="hidden" name="invoice_idlinks[]" value="'.$link.'">';
		echo '<input type="checkbox" checked="checked" disabled="disabled"> - ';
		switch ($idtype)
		{
			case 'e': // Entry
				echo 'Booking, id: '.$id;
				break;
			
			default:
				echo _('Unknown sourcetype');
			break;
		}
		echo '<br>';
	}
}

echo '<br><br>
<input type="hidden" name="invoice_input" value="1">
<input type="hidden" name="invoice_preview" value="0" id="invoice_preview">
<input type="submit" value="Opprett faktura" class="title"><br>
Etter opprettelse kan ingenting endres<br><br>

<input type="button" value="Forhåndvis" class="title" onclick="document.getElementById(\'invoice_preview\').value=\'1\'; document.forms[\'invoiceform\'].submit();">
<br>Forhåndvis fakturaen, kan endres videre etterpå
';

echo '</form>'.chr(10).chr(10);


echo '
<script type="text/javascript" src="js/invoice_create.js"></script>
';
echo '
<script type="text/javascript">

'.$after_run.'
';

/*if($invoice->invoice_to_address_id > 0)
{
	echo 'selectAddress("'.$invoice->invoice_to_address_id.'");'.chr(10);
}*/


echo '
</script>'.chr(10);


echo '<script type="text/javascript">

var options = {
	script: "autosuggest.php?",
	varname: "customer_name",
	json: true,
	maxresults: 35,
	shownoresults: false
};
var as = new bsn.AutoSuggest(\'invoice_to_customer_name\', options, \'invoice_to_customer_id\', \'invoice_to_customer_id2\');

var options = {
	script: "autosuggest.php?",
	varname: "customer_name",
	json: true,
	maxresults: 35,
	shownoresults: false
};
var as = new bsn.AutoSuggest(\'invoice_customer_name\', options, \'invoice_customer_id\', \'invoice_customer_id2\');
';
echo '</script>'.chr(10);

?>