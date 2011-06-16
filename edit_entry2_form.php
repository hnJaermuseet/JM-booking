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


print_header($day, $month, $year, $area);

$icon = array();
$icon['entry_id']				= 'page_white_star.png';
$icon['entry_type_id']			= 'page_white_stack.png';
$icon['time_start']				= 'date_previous.png';
$icon['time_end']				= 'date_next.png';
$icon['user_assigned']			= 'user.png';
$icon['user_assigned2']			= 'user.png';
$icon['area_id']				= 'house.png';
$icon['room_id']				= 'shape_square.png';
$icon['customer_name']			= 'group.png';
$icon['customer_municipal']		= 'map.png';
$icon['num_person_child']		= 'user_small.png';
$icon['num_person_adult']		= 'user_suit.png';
$icon['program_id']				= 'package.png';
$icon['program_description']	= 'script.png';
$icon['service_description']	= 'drink.png';
$icon['comment']				= 'comment.png';
$icon['infoscreen_txt']			= 'monitor.png';
$icon['invoice']				= 'coins.png';

echo '<script src="js/invoiceFields.js" type="text/javascript"></script>'.chr(10).chr(10);
//echo '<script src="js/jquery-1.3.2.js" type="text/javascript"></script>'.chr(10).chr(10);
//echo '<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>'.chr(10).chr(10);
//echo '<script src="js/DatePicker.js" type="text/javascript"></script>'.chr(10).chr(10);
//echo '<script src="js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>'.chr(10).chr(10);
echo '<script src="js/timepicker.js" type="text/javascript"></script>'.chr(10).chr(10);
echo '<script src="js/check_email.js" type="text/javascript"></script>'.chr(10);
echo '<script src="js/entry-edit.js" type="text/javascript"></script>'.chr(10).chr(10);
echo '<script src="js/jquery.blockUI.js" type="text/javascript"></script>'.chr(10);
echo '<script src="js/jquery.hoverbox.min.js" type="text/javascript"></script>'.chr(10);
echo '<script type="text/javascript">

var grouparray = new Array();
';
foreach ($area_group as $area_id => $users) {
	echo 'grouparray['.$area_id.'] = new Array(';
	$i = 0;
	foreach ($users as $users_id)
	{
		$i++;
		echo '"'.$users_id.'"';
		if($i < count($users))
			echo ', ';
	}
	echo ');'.chr(10);
}
echo '</script>';

/* ## Products ## */
echo 
'<div
	class="ui-dialog-content ui-widget-content"
	id="products"
	title="Produktregister">';
// Print all products
echo 'Trykk p&aring; ett produkt for &aring; legge til kun det produktet. '.
	'Bruk plusstegnet for &aring; legge til flere p&aring; likt.<br /><br />';
$Q_prod = mysql_query("select * from `products` order by area_id, product_name");
$last_area_id = -1;
$open = false;
while($R_prod = mysql_fetch_assoc($Q_prod))
{
	if($last_area_id != $R_prod['area_id'])
	{
		if($open)
			echo '</table></div>';
		echo '<div class="area_products" id="area_products'.$R_prod['area_id'].'">';
		
		if($R_prod['area_id'] != 0)
			echo iconHTML('house').' Produkter for <b>'.$area2[$R_prod['area_id']].'</b>';
		else
			echo iconHTML('chart_organisation').' Produkter for <b>Alle anlegg</b>';
		$last_area_id = $R_prod['area_id'];
		$open = true;
		
		echo '<table class="prettytable" style="margin: 5px; width: 650px;">'.
			'<tr>'.
				'<th style="width: 350px;">Produkt</th>'.
				'<th style="width: 100px;">Pris u/MVA</th>'.
				'<th style="width: 100px;">MVA</th>'.
				'<th style="width: 100px;">Pris m/MVA</th>'.
				'<th style="width: 40px;">&nbsp;</th>'.
			'</tr>'
		;
	}
	echo
	'<tr id="product'.$R_prod['product_id'].'">'.
		
		'<td '.
			'style="font-size: 1.1em; vertical-align: middle;"'.
			'onclick="addFromProducts (this, \''.$R_prod['product_name'].'\', \''.$R_prod['product_price'].'\', \''.$R_prod['product_tax'].'\'); '.
				'$(\'#products\').dialog(\'close\');"'.
		'>'.
			$R_prod['product_name'];
		if($R_prod['product_desc'] != '')
			echo '<br /><span style="font-size: 0.8em;"><i>'.$R_prod['product_desc'].'</i></span>';
		echo '</td>'.
		
		'<td class="rightalign" style="font-size: 1.1em; vertical-align: middle;">'.
			smarty_modifier_commify($R_prod['product_price'],2,",","&nbsp;").
		'</td>'.
		
		'<td class="rightalign" style="font-size: 1.1em; vertical-align: middle;">'.
			$R_prod['product_tax'].' %'.
		'</td>'.
		
		'<td class="rightalign" style="font-size: 1.1em; vertical-align: middle;">'.
			smarty_modifier_commify(round($R_prod['product_price']*(100+$R_prod['product_tax'])/100,2),2,",","&nbsp;").
		'</td>'.
		
		'<td style="text-align: center;">'.
			'<input type="button" style="width: 25px;" value="+" '.
				'onclick="addFromProducts (this, \''.$R_prod['product_name'].'\', \''.$R_prod['product_price'].'\', \''.$R_prod['product_tax'].'\'); '.
				'return false;">'.
		'</td>'.
	'</tr>';
}
if($open)
	echo '</table></div>';
echo '</div>';
echo '<script type="text/javascript">
$(\'#products\').dialog({ 
	autoOpen: false,
	minHeight: 200,
	height: 400,
	minWidth: 700,
	width: 800,
//	show: \'blind\',
	open: function(event, ui) {
		$(this).closest(\'.ui-dialog\').css({
			position: \'absolute\',
			top: $(this).closest(\'.ui-dialog\').offset().top
		});
    }
});
$(\'#products\').bind( "dialogopen", function(event, ui) {
	// Updating productlist according to selected area
	$(\'#products .area_products\').hide();
	
	selected_area_id = $(\'#selected_area_id\').val();
	$(\'#area_products\'+selected_area_id).show();
	
	$(\'#area_products0\').show();
});

function addFromProducts(denne, description, topay_each, tax)
{
	addFieldInvoiceWithProducts(description, topay_each, tax);
}
</script>'.chr(10);


echo '<br>'.chr(10);

echo '<form action="'.$_SERVER['PHP_SELF'].'?view='.$view.'" method="POST" name="entry">'.chr(10);
echo '<input type="hidden" name="data_submit" value="1">'.chr(10);
echo chr(10);

echo '<table style="border: 1px solid black;">'.chr(10);

echo '<tr><td colspan="4" align="center">'.chr(10);
if($entry_add)
	echo '<h1>'._('Add entry').'</h1>'.chr(10);
else
{
	echo '<h1>'._('Edit entry').'</h1>'.chr(10);
	echo '- <a href="entry.php?entry_id='.$entry_fields['entry_id']['value'].'">'._('Back to entry').'</a> ('._('Entry will not be saved!').')';
}
echo '</td></tr>'.chr(10).chr(10);

if(count($form_errors))
{
	echo '<tr><td colspan="4" style="border: 1px black solid;">'.chr(10);
	
	echo '<div class="error">';
	echo '<center><font color="red">'._('One or more errors occured in the data submited').'</font></center>'.chr(10);
	echo '<ul>'.chr(10);
	foreach ($form_errors as $error)
	{
		echo '<li>'.$error.'</li>'.chr(10);
	}
	echo '</ul>'.chr(10);
	echo '</div>';
	echo '</td></tr>'.chr(10);
	
	// Make some space
	echo '<tr><td colspan="4">&nbsp;</td></tr>'.chr(10).chr(10);
}

/*
	Each field contain:
	['var']		variabel name
	['add']		in add-field
	['type']	hidden / text / select / etc
	['name']	name of the entry
	['desc']	description
	['value']	value, if any
	['choice']	choices, if any (for select, osv)
		[choiceid] = choice (name of the choice)
*/

if(count($warnings))
{
	echo '<tr><td colspan="4" style="border: 0px;">'.chr(10);
	
	echo '<div class="notice">';
	echo '<h2>'._('One or more warnings where generated.').'</h2>'.chr(10);
	echo '<ul>'.chr(10);
	foreach ($warnings as $warning)
	{
		echo '<li style="font-size: 16px; padding: 6px;">'.$warning.'</li>'.chr(10);
	}
	echo '</ul>'.chr(10);
	echo '<i>'._('Changes are still not saved. Please fix warnings or ignore them to get the changes saved.').'</i><br><br>'.chr(10);
	
	echo '<label><input type="checkbox" value="1" name="warningignore"> '.
		_('I have seen the warnings and still want to proceed.').'</label><br>'.chr(10);
	echo '<input type="submit" value="'._('Proceed').'"><br>'.chr(10);
	
	echo '</div>';
	echo '</td></tr>'.chr(10);
	
	// Make some space
	echo '<tr><td colspan="4">&nbsp;</td></tr>'.chr(10).chr(10);
}

if($copy_entry)
{
	
	echo '<tr><td colspan="4" style="border: 0px;" align="center">'.chr(10);
	
	echo '<div class="notice" style="text-align: center; font-size: 20px; width:500px;">';
	echo 'Obs! Du kopierer nå en booking.<br>'.
	'<span style="font-size: 16px;">Hvis du skulle endre heller, så trykk tilbakeknappen.</span>'.chr(10);
	
	echo '</div>';
	echo '</td></tr>'.chr(10);
	
	// Make some space
	echo '<tr><td colspan="4">&nbsp;</td></tr>'.chr(10).chr(10);
}

if(!$entry_add && $entry['invoice_status'] > 1)
{
	
	echo '<tr><td colspan="4" style="border: 0px;" align="center">'.chr(10);
	
	echo '<div class="notice" style="text-align: center; font-size: 20px; width:600px;">';
	echo 'Obs! Denne bookingen sin fakturadel er ';
	if($entry['invoice_status'] == '2')
		echo 'satt faktureringsklar';
	else
		echo 'eksport til Kommfakt';
	echo '.<br>'.
	'<span style="font-size: 16px;">V&aelig;r obs på hva du gj&oslash;r.</span>'.chr(10);
	
	echo '</div>';
	echo '</td></tr>'.chr(10);
	
	// Make some space
	echo '<tr><td colspan="4">&nbsp;</td></tr>'.chr(10).chr(10);
}

$hidden_after = '';
foreach ($entry_fields as $field)
{
	if ( ($entry_add && $field['add']) || (!$entry_add))
	{
		$onchange = '';
		$elementid = '';
		$disabled = '';
		$before = '';
		$after = '';
		$classes = '';
		if(isset($field['onchange']))						$onchange = ' onchange="'.$field['onchange'].'"';
		if(isset($field['id']) && $field['id'] != '')		$elementid = ' id="'.$field['id'].'"';
		if(isset($field['disabled']) && $field['disabled'])	$disabled = ' disabled="disabled"';
		if(isset($field['before']))							$before = $field['before'];
		if(isset($field['after']))							$after = $field['after'];
		if(isset($field['class']) && count($field['class']))
			$classes = ' '.implode($field['class']);
		
		// Vertical alignment
		if($field['type'] != 'radio' && $field['type'] != 'checkbox')
			$valign = ' valign';
		else
			$valign = '';
		
		if($field['type'] == 'hidden')
		{
			$hidden_after .= chr(10).'<input type="hidden" name="'.$field['var'].'" value="'.$field['value'].'"'.$elementid.'>'.$after;
		}
		else
		{
			echo '<tr>'.chr(10);
			echo ' <td align="right" class="edit_entry'.$valign.'">';
			if($field['var'] != 'empty' && $field['type'] != 'hidden' && $field['type'] != 'submit') {
				if(isset($icon[$field['var']]))
					echo iconHTML($icon[$field['var']], '').'&nbsp;';
				echo '<b>'.$field['name'].'</b>';
			}
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
		
			if($field['type'] == 'invoice_content') {
				
			}
			elseif(isset($field['desc']) && $field['desc'] != '')
			{
				echo ' <td class="edit_entry'.$valign.'">'.
					'<a class="programHover infoicon" title="'.nl2br($field['desc']).'">&nbsp;'.
						///iconHTML('information').
						//'abc'.
					'</a>'.
				'</td>'.chr(10);
			}
			else
			{
				echo ' <td class="edit_entry" style="width: 15px; text-align: center;">&nbsp;</td>'.chr(10);
			}
			
			if($field['type'] == 'invoice_content' || (isset($field['colspanDesc']) && $field['colspanDesc']))
				echo ' <td class="edit_entry_fields" colspan="2">';
			else
				echo ' <td class="edit_entry_fields">';
			if(isset($field['beforeChoices']))
				echo $field['beforeChoices'];
			switch ($field['type'])
			{
				
				case 'text':
					echo $before.'<input type="text" class="edit_entry'.$classes.'" name="'.$field['var'].'" value="'.$field['value'].'"'.$elementid.$disabled.'>'.$after;
					break;
				
				case 'textarea':
					echo $before.'<textarea class="edit_entry'.$classes.'" cols="75" rows="5" name="'.$field['var'].'"'.$elementid.$disabled.'>'.$field['value'].'</textarea>'.$after;
					break;
				
				case 'radio':
					echo $before;
					foreach ($field['choice'] as $choiceid => $choice)
					{
						if(isset($field['choice_before']) && array_key_exists($choiceid, $field['choice_before']))
							echo $field['choice_before'][$choiceid];
						echo '<label><input type="radio" name="'.$field['var'].'" value="'.$choiceid.'" id="'.$field['var'].$choiceid.'"'.$disabled;
						if ($choiceid == $field['value'])
							echo ' checked="checked"';
						echo '> - <span class="edit_entry">'.$choice.'</span></label><br>';
						if(isset($field['choice_after']) && array_key_exists($choiceid, $field['choice_after']))
							echo $field['choice_after'][$choiceid];
					}
					echo $after;
					break;
				
				case 'select':
					echo $before.'<select class="edit_entry'.$classes.'" name="'.$field['var'].'"'.$onchange.$elementid.$disabled.'>';
					foreach ($field['choice'] as $choiceid => $choice)
					{
						echo '<option value="'.$choiceid.'"';
						if($choiceid == $field['value'])
							echo ' selected';
						echo '>'.$choice.'</option>';
					}
					echo '</select>'.$after;
					break;
				
				case 'checkbox':
					echo $before;
					foreach ($field['choice'] as $choiceid => $choice)
					{
						if(isset($field['choice_before']) && array_key_exists($choiceid, $field['choice_before']))
							echo $field['choice_before'][$choiceid];
						echo '<label><input type="checkbox" name="'.$field['var'].'[]" value="'.$choiceid.'" id="'.$field['var'].$choiceid.'"';
						if (in_array($choiceid, $field['value_array']))
							echo ' checked="checked"';
						echo '> - '.$choice.'</label><br>';
						if(isset($field['choice_after']) && array_key_exists($choiceid, $field['choice_after']))
							echo $field['choice_after'][$choiceid];
					}
					echo $after;
					break;
				
				case 'submit':
					echo $before.'<input type="submit" class="edit_entry_submit'.$classes.'" name="'.$field['var'].'" value="'.$field['name'].'"'.$elementid.$disabled.'>'.$after;
					break;
				
				case 'date':
					echo $before.'<input type="text" class="edit_entry'.$classes.'" name="'.$field['var'].'" value="'.date('H:i d-m-Y', $field['value']).'"'.$elementid.$disabled.'>'.$after;
					break;
				
				case 'empty':
					echo $before.'&nbsp;'.$after;
					break;
				
				case 'invoice_content':
					echo '<table id="invoicerows">'.chr(10);
					
					echo '<tr>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo ' <td colspan="2" style="text-align: center; border: 1px dashed lightgray;">Stkpris</td>'.chr(10);
					echo ' <td style="text-align: center; border: 1px dashed lightgray;" rowspan="2">Sum MVA</td>'.chr(10);
					echo ' <td style="text-align: center; border: 1px dashed lightgray;" rowspan="2">Sum ink.mva</td>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo '</tr>'.chr(10).chr(10);
					echo '<tr>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">Linjenr</td>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">Produktbeskrivelse</td>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">Pris</td>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">Antall</td>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">MVA-%</td>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">Eks.mva?</td>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">eks.mva</td>'.chr(10);
					echo ' <td style="border: 1px dashed lightgray;">ink.mva</td>'.chr(10);
					echo ' <td>&nbsp;</td>'.chr(10);
					echo '</tr>'.chr(10).chr(10);
					
					$id = 0;
					$after_run = '';
					foreach ($field['value_array'] as $invoice_content)
					{
						$id++;
						echo '<tr id="row'.$id.'">'.chr(10);
						
						// Linjenum
						echo ' <td>';
						echo ' <input type="hidden" name="rows[]" value="'.$id.'"'.$disabled.'>'.chr(10);
						echo ' <input type="hidden" name="type'.$id.'" value="belop"'.$disabled.'>'.chr(10);
						//echo ' <input type="hidden" name="id_type'.$id.'" value="0">'.chr(10); // Disabled
						//echo ' <input type="hidden" name="id_ekstra'.$id.'" value="0">'.chr(10); // Disabled
						echo ' <input type="hidden" name="belop_hver_real'.$id.'" id="belop_hver_real'.$id.'" value="0"'.$disabled.'>'.chr(10); // The real value of this one, eks tax
						echo '<input type="text" size="3" value="'.$id.'" disabled></td>'.chr(10);
						
						// Beskrivelse
						echo ' <td>'.
							'<textarea '.
								'rows="1" cols="50" '.
								'name="name'.$id.'"'.
								$disabled.
							'>'.
								$invoice_content['name'].
							'</textarea>'.
							'</td>'.chr(10);
						
						// Belop_hver
						echo ' <td><input type="text" size="6" id="belop_hver'.$id.'" name="belop_hver'.$id.'" value="'.$invoice_content['belop_hver'].'" '.
							'onchange="updateMva('.$id.');" '.
							'onkeyup="updateMva('.$id.');" '.
							'onclick="updateMva('.$id.');" '.
							$disabled.'></td>'.chr(10);
						
						// Antall
						echo ' <td><input type="text" size="6" id="antall'.$id.'" name="antall'.$id.'" value="'.$invoice_content['antall'].'" '.
							'onchange="updateMva('.$id.');" '.
							'onkeyup="updateMva('.$id.');" '.
							'onclick="updateMva('.$id.');" '.
							$disabled.'></td>'.chr(10);
						
						// Mva
						echo ' <td><input type="text" size="3" id="mva'.$id.'" name="mva'.$id.'" value="'.($invoice_content['mva']*100).'" '.
							'onchange="updateMva('.$id.');" '.
							'onkeyup="updateMva('.$id.');" '.
							'onclick="updateMva('.$id.');" '.
							$disabled.'></td>'.chr(10);
						
						// Ink mva / eks mva
						echo ' <td><input name="mva_eks'.$id.'" id="mva_eks'.$id.'" value="1" type="checkbox" '.
							'onchange="updateMva('.$id.');" '.
							'onkeyup="updateMva('.$id.');" '.
							'onclick="updateMva('.$id.');" '
							;
						if($invoice_content['mva_eks'])
							echo ' checked="checked"';
						echo $disabled.'></td>'.chr(10);
						
						// Mva_hver / belop_hver_real
						echo ' <td>'.
							'<input type="text" size="3" id="belop_hver_real2'.$id.'" name="belop_hver_real2'.$id.'" '.
								'value="" disabled>'.
							'<input type="hidden" id="mva_hver'.$id.'" name="mva_hver'.$id.'" value="" disabled>'.
							'</td>'.chr(10);
						echo ' <td>'.
							'<input type="text" size="3" id="belop_hver_withtax3'.$id.'" name="belop_hver_withtax3'.$id.'" '.
								'value="" disabled>'.
							'</td>'.chr(10);
						
						// Mva_sum
						echo ' <td><input type="text" size="3" id="mva_sum_hver'.$id.'" name="mva_sum_hver'.$id.'" value="" disabled></td>'.chr(10);
						
						// Belop_sum
						echo ' <td><input type="text" size="6" id="belop_delsum'.$id.'" name="belop_delsum'.$id.'" value="" disabled></td>'.chr(10);
						
						// RemoveField
						echo ' <td><input type="button" value="Ta vekk linje" onclick="removeInvoiceField(\''.$id.'\');"'.$disabled.'></td>'.chr(10);
						echo '</tr>'.chr(10);
						
						$after_run .= 'updateMva('.$id.');'.chr(10); 
					}
					if(!count($field['value_array']))
						$after_run .= 'addFieldInvoice();'.chr(10);
					
					echo '</table>'.chr(10);
					echo '<input type="button" onclick="$(\'#products\').dialog(\'open\');" value="&Aring;pne produktregister"> ';
					echo '<input type="button" value="Legg til ny linje" onclick="addFieldInvoice();"'.$disabled.'><br>'.chr(10);
					
					echo '<br>'.chr(10);
					echo '<input type="text" id="belop_sum" name="belop_sum" value="0" disabled> - Sum<br>'.chr(10);
					echo '<input type="text" id="mva_sum" name="mva_sum" value="0" disabled> - Sum MVA'.chr(10);
					
					break;
				
				default:
					echo _('Error with inputfields for entry.');
					break;
			}
			echo '</td>'.chr(10);
			echo '</tr>'.chr(10);
		}
	}
}
echo '</table>'.chr(10);

echo $hidden_after.chr(10);
echo '</form>'.chr(10);

if($entry_fields['area_id']['value'] != '0')
{
	echo '<script type="text/javascript">'.chr(10);
	echo 'choose_area('.$entry_fields['area_id']['value'].');'.chr(10);
	echo '</script>'.chr(10);
}

echo '<script type="text/javascript">'.chr(10);
if($entry_fields['customer_id']['value'] != '0')
	echo 'document.getElementById(\'customer_id2\').value = \''.$entry_fields['customer_id']['value'].'\';'.chr(10);
if($entry_fields['customer_municipal_num']['value'] != '0')
	echo 'document.getElementById(\'customer_municipal_num2\').value = \''.$entry_fields['customer_municipal_num']['value'].'\';'.chr(10);
if($entry_fields['invoice_address_id']['value'] != '0')
{
	echo 'document.getElementById(\'invoice_address_id2\').value = \''.$entry_fields['invoice_address_id']['value'].'\';'.chr(10);
	$entry_fields['invoice_address_id']['value'] = (int)$entry_fields['invoice_address_id']['value'];
	$Q = mysql_query("select address_full from `customer_address` where address_id = '".$entry_fields['invoice_address_id']['value']."'");
	if(mysql_num_rows($Q))
	{
		echo 'document.getElementById(\'invoice_address\').value = "'.str_replace(chr(10), '\n', mysql_result($Q, 0, 'address_full')).'";'.chr(10);
	}
}
echo '</script>'.chr(10);

echo '<script type="text/javascript">'.chr(10);
echo 'var options = {
	script: "autosuggest.php?",
	varname: "customer_name",
	json: true,
	maxresults: 35,
	shownoresults: false
};
var as = new bsn.AutoSuggest(\'customer_name\', options, \'customer_id\', \'customer_id2\');
';
echo '</script>'.chr(10);

echo '<script type="text/javascript">
'.$after_run.'
</script>'.chr(10);

?>