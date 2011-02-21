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

$return_to = '';
if(isset($_GET['return_to']))
{
	switch ($_GET['return_to'])
	{
		case 'entry_stat':
			$return_to = 'entry_stat'; break;
		case 'entry_list':
			$return_to = 'entry_list'; break;
		case 'invoice_soon':
			$return_to = 'invoice_soon'; break;
		case 'invoice_tobemade_ready':
			$return_to = 'invoice_tobemade_ready'; break;
		case 'invoice_tobemade':
			$return_to = 'invoice_tobemade'; break;
		case 'invoice_exported':
			$return_to = 'invoice_exported'; break;
		case 'customer_list':
			$return_to = 'customer_list'; break;
		default:
			$return_to = ''; break;
	}
}

filterMakeAlternatives();

if(isset($_GET['filters']))
{
	// From serialized
	$filters = unserialize(htmlspecialchars_decode($_GET['filters'],ENT_QUOTES));
	if(!$filters)
		$filters = array();
}
else
{
	$filters = readFiltersFromGet();
	if($return_to != '')
	{
		$filters_serialized = serialize($filters);
		switch ($return_to)
		{
			case 'entry_stat':
				header('Location: entry_stat.php?filters='.$filters_serialized); break;
			case 'entry_list':
				header('Location: entry_list.php?filters='.$filters_serialized); break;
			case 'customer_list':
				header('Location: entry_list.php?listtype=customer_list&filters='.$filters_serialized); break;
			case 'invoice_soon':
				header('Location: invoice_soon.php?filters='.$filters_serialized); break;
			case 'invoice_tobemade_ready':
				header('Location: invoice_tobemade_ready.php?filters='.$filters_serialized); break;
			case 'invoice_tobemade':
				header('Location: invoice_tobemade.php?filters='.$filters_serialized); break;
			case 'invoice_exported':
				header('Location: invoice_exported.php?filters='.$filters_serialized); break;
			default:
				echo 'Return to not found. Code error.'; break;
		}
		exit();
	}
}

$SQL = genSQLFromFilters($filters);

print_header($day, $month, $year, $area);

echo '<h1>'. _('View / Edit filters'). '</h1>'.chr(10).chr(10);
echo '<script language="javascript" src="js/jquery-1.3.2.min.js"></script>'.chr(10);
echo 'Velg bookinger hvor:';
echo '<form method="get" name="filters" action="'.$_SERVER['PHP_SELF'].'">'.chr(10);
echo '<table id="filterrows">'.chr(10);
$id = -1;
$run_after = '';
foreach ($filters as $filter)
{
	$id++;
	echo '<tr id="row'.$id.'">'.chr(10);
	echo '<input type="hidden" name="rows[]" value="'.$id.'">'.chr(10);
	
	//$run_after = 'selectFilterType('.$id.');'.chr(10);
	echo '<td>';
	echo '<select name="filter['.$id.']" onchange="selectFilterType('.$id.');" id="filter'.$id.'">'.chr(10);
	foreach ($alternatives as $var => $alternative)
	{
		echo ' <option value="'.$var.'"';
		if($var == $filter[0])
			echo ' selected="selected"';
		echo '>'.$alternative['name'].'</option>'.chr(10);
	}
	echo '</select>'.chr(10);
	echo '</td>'.chr(10);
	
	echo '<td>';
	echo '	<span id="showfield'.$id.'">'.chr(10);
	echo '		';
	switch ($alternatives[$filter[0]]['type']) {
		case 'id':
			if(isset($alternatives[$filter[0]]['table']) && count($alternatives[$filter[0]]['table']))
			{
				// We have got an ID-field with a few selectable options (not like customers)
				$table = $alternatives[$filter[0]]['table'];
				$Q_id = mysql_query('
					SELECT 
						'.$table['id_field'].' AS id, 
						'.$table['value_field'].' AS value
					FROM '.$table['table'].'
					ORDER BY '.$table['value_field']);
				echo '<select name="filtervalue1_'.$id.'">';
				while($R_id = mysql_fetch_assoc($Q_id))
				{
					echo '<option value="'.$R_id['id'].'"';
					if($filter[1] == $R_id['id']) {
						echo ' selected="selected"';
					}
					echo '>'.$R_id['value'].'</option>';
				}
				echo '</select>';
				break;
			}
		case 'text':
			echo '<input type="text" name="filtervalue1_'.$id.'" value="'.$filter[1].'">';
			break;
		case 'bool':
			echo '<select name="filtervalue1_'.$id.'">';
			if($filter[1] == '0') {
				echo '<option value="1">'._('Yes').'</option>';
				echo '<option value="0" selected="selected">'._('No').'</option>';
			} else {
				echo '<option value="1" selected="selected">'._('Yes').'</option>';
				echo '<option value="0">'._('No').'</option>';
			}
			echo '</select>';
			break;
		case 'select':
		case 'id2':
			// Make a select with the spesified choices
			echo '<select name="filtervalue1_'.$id.'">';
			foreach($alternatives[$filter[0]]['choice'] as $theID => $choice)
			{
				echo ' <option value="'.$theID.'"';
				if($theID == $filter[1])
					echo ' selected="selected"';
				echo '>'.$choice.'</option>'.chr(10);
			}
			echo '</select>';
			break;
		case 'date':
			echo '<select name="filtervalue2_'.$id.'">';
			echo ' <option value="="';	if('=' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Is exactly').'</option>';
			
			echo ' <option value=">"';	if('>' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Bigger than').'</option>';
			
			echo ' <option value=">="';	if('>=' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Bigger than or same as').'</option>';
			
			echo ' <option value="<"';	if('<' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Less than').'</option>';
			
			echo ' <option value="<="';	if('<=' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Less than or same as').'</option>';
			
			echo '</select>';
			
			if($filter[1] != 'current')
				echo '<input type="text" name="filtervalue1_'.$id.'" value="'.date('Y-m-d H:i', $filter[1]).'">';
			else
				echo '<input type="text" name="filtervalue1_'.$id.'" value="'.$filter[1].'">';
			
			break;
			
		case 'num':
			echo '<select name="filtervalue2_'.$id.'">';
			echo ' <option value="="';	if('=' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Is exactly').'</option>';
			
			echo ' <option value=">"';	if('>' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Bigger than').'</option>';
			
			echo ' <option value=">="';	if('>=' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Bigger than or same as').'</option>';
			
			echo ' <option value="<"';	if('<' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Less than').'</option>';
			
			echo ' <option value="<="';	if('<=' == $filter[2]) echo ' selected="selected"';
				echo '>'._('Less than or same as').'</option>';
			
			echo '</select>';
			
			echo '<input type="text" name="filtervalue1_'.$id.'" value="'.$filter[1].'">';
			
			break;
	}
	echo '	</span>'.chr(10);
	echo '</td>'.chr(10);
	
	echo '<td><input type="button" value="-" onclick="removeField(\''.$id.'\');"></td>'.chr(10);
	echo '</tr>'.chr(10);
}
echo '
</table>
<input type="button" value="+" onclick="addFieldFilters();"><br><br>'.chr(10);

echo '<b>'._('Return to').'</b><br>'.chr(10);
echo '<label><input type="radio" name="return_to" value="entry_list"';
if($return_to == 'entry_list')
	echo ' checked="checked"';
echo '> '._('Entry list').'</label><br>'.chr(10);

echo '<label><input type="radio" name="return_to" value="entry_stat"';
if($return_to == 'entry_stat')
	echo ' checked="checked"';
echo '> '._('Entry stats').'</label><br>'.chr(10);

echo '<label><input type="radio" name="return_to" value="customer_list"';
if($return_to == 'customer_list')
	echo ' checked="checked"';
echo '> Kundeliste</label><br>'.chr(10);

if($login['user_invoice'] || $login['user_invoice_setready'])
{
	echo '<label><input type="radio" name="return_to" value="invoice_soon"';
	if($return_to == 'invoice_soon')
		echo ' checked="checked"';
	echo '> Faktura - Ikke gjennomf&oslash;rt</label><br>'.chr(10);
	echo '<label><input type="radio" name="return_to" value="invoice_tobemade"';
	if($return_to == 'invoice_tobemade')
		echo ' checked="checked"';
	echo '> Faktura - Skal lages</label><br>'.chr(10);
	echo '<label><input type="radio" name="return_to" value="invoice_tobemade_ready"';
	if($return_to == 'invoice_tobemade_ready')
		echo ' checked="checked"';
	echo '> Faktura - Klar til å lages</label><br>'.chr(10);
	echo '<label><input type="radio" name="return_to" value="invoice_exported"';
	if($return_to == 'invoice_exported')
		echo ' checked="checked"';
	echo '> Faktura - Eksportet til Kommfakt</label><br>'.chr(10);

}

echo '<br><br>'.chr(10);
echo '<input type="submit" value="'._('Choose filter').'">'.chr(10);
echo '</form>'.chr(10);

echo '<h2>'._('About filters').'</h2>'.chr(10);
echo _('For text fields, % can be used to match any or zero characters. Type _ for one character. If "Per" and "P&aring;l" was in the database, P_r would match only Per and P__ whould match both. Per% or Pe% whould match Per. P% whould match everybody/everything that starts with P.');
echo '<br><br>';
echo _('Dates are specified as YY-mm-dd hh:mm. 20 of june 2008 at 15:32 should be entered as 2008-06-20 15:32. Can also be 08-6-20 15.32 if you like. The current time is always specified by entering "current".');
//echo 'An other thing is to use multiple filters of same type. This will result in filter1 or filter2 matching.'

?><script type="text/javascript"><?php
echo '
function selectFilterType (numID) {
	var span = document.getElementById("showfield" + numID);
	while (span.firstChild) 
	{
		span.removeChild(span.firstChild);
	};
	
	var filter = document.getElementById("filter" + numID);
	switch (filter.value) {
';

foreach ($alternatives as $var => $alternative)
{
	switch ($alternative['type']) {
		case 'id':
			if(isset($alternatives[$var]['table']) && count($alternatives[$var]['table']))
			{
				// We have got an ID-field with a few selectable options (not like customers)
				$table = $alternatives[$var]['table'];
				$Q_id = mysql_query('
					SELECT 
						'.$table['id_field'].' AS id, 
						'.$table['value_field'].' AS value
					FROM '.$table['table'].'
					ORDER BY '.$table['value_field']);
				echo '		case \''.$var.'\':'.chr(10);
				echo '			var select=document.createElement(\'select\');'.chr(10);
				echo '			select.name="filtervalue1_"+numID;'.chr(10);
				echo '			span.appendChild(select);'.chr(10);
				while($R_id = mysql_fetch_assoc($Q_id))
				{
					echo '			var option=document.createElement(\'option\');'.chr(10);
					echo '			option.value="'.$R_id['id'].'";'.chr(10);
					echo '			option.innerHTML="'.$R_id['value'].'";'.chr(10);
					echo '			select.appendChild(option);'.chr(10);
				}
				echo '			break;'.chr(10);
				break;
			}
		case 'text':
			echo '		case \''.$var.'\':'.chr(10);
			echo '			var input=document.createElement(\'input\');'.chr(10);
			echo '			input.name="filtervalue1_"+numID;'.chr(10);
			echo '			span.appendChild(input);'.chr(10);
			echo '			break;'.chr(10);
			break;
		case 'bool':
			// Make a select with 2 choices
			echo '		case \''.$var.'\':'.chr(10);
			echo '			var select=document.createElement(\'select\');'.chr(10);
			echo '			select.name="filtervalue1_"+numID;'.chr(10);
			echo '			span.appendChild(select);'.chr(10);
			echo '			var option=document.createElement(\'option\');'.chr(10);
			echo '			option.value=1;'.chr(10);
			echo '			option.innerHTML="'._('Yes').'";'.chr(10);
			echo '			select.appendChild(option);'.chr(10);
			echo '			var option=document.createElement(\'option\');'.chr(10);
			echo '			option.value=0;'.chr(10);
			echo '			option.innerHTML="'._('No').'";'.chr(10);
			echo '			select.appendChild(option);'.chr(10);
			echo '			break;'.chr(10);
			break;
		case 'select':
		case 'id2':
			// Make a select with the spesified choices
			echo '		case \''.$var.'\':'.chr(10);
			echo '			var select=document.createElement(\'select\');'.chr(10);
			echo '			select.name="filtervalue1_"+numID;'.chr(10);
			echo '			span.appendChild(select);'.chr(10);
			foreach($alternative['choice'] as $id => $choice)
			{
				echo '			var option=document.createElement(\'option\');'.chr(10);
				echo '			option.value='.$id.';'.chr(10);
				echo '			option.innerHTML="'.$choice.'";'.chr(10);
				echo '			select.appendChild(option);'.chr(10);
			}
			echo '			break;'.chr(10);
			break;
		case 'date':
			// Special dataselector
			//echo '		case \''.$var.'\':'.chr(10);
			//echo '			break;'.chr(10);
			
			//break;
		case 'num':
			echo '		case \''.$var.'\':'.chr(10);
			
			echo '			var select=document.createElement(\'select\');'.chr(10);
			echo '			select.name="filtervalue2_"+numID;'.chr(10);
			echo '			span.appendChild(select);'.chr(10);
			
			echo '			var option=document.createElement(\'option\');'.chr(10);
			echo '			option.value="=";'.chr(10);
			echo '			option.innerHTML="'._('Is exactly').'";'.chr(10);
			echo '			select.appendChild(option);'.chr(10);
			
			echo '			var option=document.createElement(\'option\');'.chr(10);
			echo '			option.value=">";'.chr(10);
			echo '			option.innerHTML="'._('Bigger than').'";'.chr(10);
			echo '			select.appendChild(option);'.chr(10);
			
			echo '			var option=document.createElement(\'option\');'.chr(10);
			echo '			option.value=">=";'.chr(10);
			echo '			option.innerHTML="'._('Bigger than or same as').'";'.chr(10);
			echo '			select.appendChild(option);'.chr(10);
			
			echo '			var option=document.createElement(\'option\');'.chr(10);
			echo '			option.value="<";'.chr(10);
			echo '			option.innerHTML="'._('Less than').'";'.chr(10);
			echo '			select.appendChild(option);'.chr(10);
			
			echo '			var option=document.createElement(\'option\');'.chr(10);
			echo '			option.value="<=";'.chr(10);
			echo '			option.innerHTML="'._('Less than or same as').'";'.chr(10);
			echo '			select.appendChild(option);'.chr(10);
			
			
			echo '			var input=document.createElement(\'input\');'.chr(10);
			echo '			input.name="filtervalue1_"+numID;'.chr(10);
			echo '			span.appendChild(input);'.chr(10);
			echo '			break;'.chr(10);
			break;
	}
}
?>
	}
}

function addFieldFilters ()
{
	form = document.forms['filters'];
	formelements = form.elements;
	valuelastrow = 0;
	valuehighestrow = 0;
	valuehighestline = 0;
	for (var i = 0; i<formelements.length;i++)
	{
		if(formelements[i].name == "rows[]")
		{
			if(formelements[i].value > valuehighestrow)
				valuehighestrow = formelements[i].value;
		}
	}
	valuehighestrow++;
	thisvalue = valuehighestrow;
	valuehighestline++;
	thisline = valuehighestline;
	
	var tr = '<tr id="row'+ thisvalue+'">' +
		'<input type="hidden" name="rows[]" value="'+thisvalue+'">';
	
	
	var td1 = 
		'<td>'+
			'<select ' +
				'id="filter'+thisvalue+'" ' +
				'onchange="javascript:selectFilterType(\''+thisvalue+'\');" ' +
				'name="filter['+thisvalue+']" ' +
			'>';
	var td1_slutt =	'</select></td>';
	
	var options = ""
<?php
	foreach ($alternatives as $var => $alternative)
	{
		echo '	+ \'<option value="'.$var.'">'.$alternative['name'].'</option>\''.chr(10);
	}?>;
	
	var td2 = 
		'<td>' +
			'<span id="showfield'+thisvalue+'"></span>' +
		'</td>';
	
	var td3 = 
		'<td>' +
			'<input type="button" value="-" onclick="removeField(\''+thisvalue+'\');" />' +
		'</td>';
	
	var tr_slutt = '</tr>';
	
	$('#filterrows').append(
		tr +
			td1 + 
				options + 
			td1_slutt +
			td2 + 
			td3 +
		tr_slutt);
	
	selectFilterType(thisvalue);
}

function removeField (id)
{
	tr = document.getElementById('row'+id);
	tr.parentNode.removeChild(tr);
	
	return true;
}
</script>