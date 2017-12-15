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
 * Class that generates editor for MySQL
 */

class editor {
	/*
	 * $VARS:
	 * 					default:
	 * ['var']			''			Must be set
	 * ['name']			''			Must be set
	 * ['type']			'text'		HTML-type, must be set
	 * ['processor']	'text'		Must be set, mostly set by ['type']
	 * ['chekcer']		'text'		Must be set, mostly set by ['type']
	 * ['desc']			''
	 * ['class']		''			(not displayed with '')
	 * ['id']			''			(not displayed with '')
	 * ['required']		false		Required?
	 * ['before']		HTML-codes
	 * ['after']		HTML-codes
	 * ['onchange']		''			(not displayed with '')
	 * ['onsubmit']		''			(not displayed with '')
	 * ['onreset']		''			(not displayed with '')
	 * ['onselect']		''			(not displayed with '')
	 * ['onblur']		''			(not displayed with '')
	 * ['onfocus']		''			(not displayed with '')
	 * ['disabled']		''			(not displayed with '')
	 * 
	 * ['choice']		array
	 	* []
	 		* ['value']	
	 		* ['id']						
	 		* ['name']						must be set, text displayed
			* ['before']		''
			* ['after']			''
			* ['onchange']		''			(not displayed with '')
			* ['onsubmit']		''			(not displayed with '')
			* ['onreset']		''			(not displayed with '')
			* ['onselect']		''			(not displayed with '')
			* ['onblur']		''			(not displayed with '')
			* ['onfocus']		''			(not displayed with '')
			* ['disabled']		''			(not displayed with '')
	 * ['value']		''			string
	 * ['value_array']	array()		array	
	 	* [choiceid] = choiceid
	 */
	var $vars			= array();
	var $heading_before	= "<table class=\"editor\">\n\t<tr>\n\t\t<td colspan=\"3\" align=\"center\"><h1>";
	var $heading		= 'Editor';
	var $heading_after	= "</h1></td>\n\t</tr>";
	var $fields_before	= "\n";
	var $fields_after	= "</table>\n";
	var $form_url;
	var $submit_txt		= 'Submit';
	var $id				= '';
	var $showid			= false;
	
	var $warningfunction		= '';
	var $warnings				= array();
	
	var $db_table				= '';
	var $db_field_id			= 'id';
	var $db_field_edit			= '';
	var $db_field_edit_by		= '';
	var $db_field_edit_by_value	= '';
	
	var $error_code;
	var $error_input	= array();
	
	function Editor ($table, $url, $id = '')
	{
		if($id != '')
		{
			$this->setID($id);
		}
		
		$this->setDBTable($table);
		$this->setUrl($url);
		
		return TRUE;
	}
	
	function makeNewField ($var, $name, $type, $options = array()) {
		if($var == '')
		{
			$this->error_code = 'Var for field is not set'; // Var for field is not set
			return FALSE;
		}
		if($name == '')
			$name = $var;
		
		$this->vars[$var] = array();
		$this->vars[$var]['var']			= $var;
		switch ($type)
		{
			case 'select':
			case 'text':
			case 'textarea':
			case 'checkbox':
			case 'boolean':
			case 'password':
			case 'hidden':
				$this->vars[$var]['type'] = $type;
				$this->vars[$var]['processor'] = $type;
				$this->vars[$var]['checker'] = $type;
				break;
			default:
				$this->error_code = 'Type of field is invalid.'; // Type of field is invalid.
				return FALSE;
				break;
		}
		$this->vars[$var]['name']			= $name;
		$this->vars[$var]['desc']			= '';
		$this->vars[$var]['class']			= '';
		$this->vars[$var]['id']				= '';
		$this->vars[$var]['required']		= FALSE;
		$this->vars[$var]['before']			= "\t<tr>\n\t\t<td>";
		$this->vars[$var]['after']			= "</td>\n\t</tr>\n\n";
		$this->vars[$var]['onchange']		= '';
		$this->vars[$var]['onsubmit']		= '';
		$this->vars[$var]['onreset']		= '';
		$this->vars[$var]['onselect']		= '';
		$this->vars[$var]['onblur']			= '';
		$this->vars[$var]['onfocus']		= '';
		$this->vars[$var]['disabled']		= FALSE;
		$this->vars[$var]['choice']			= array();
		$this->vars[$var]['DBQueryPerform']	= true;
		
		if(isset($options['noDB']) && $options['noDB'])
			$this->vars[$var]['noDB'] = true;
		else
			$this->vars[$var]['noDB'] = false;
		
		if(isset($options['defaultValue']))
			$this->vars[$var]['value']			= $options['defaultValue'];
		else
			$this->vars[$var]['value']			= '';
		
		if(isset($options['defaultValueArray']) && is_array($options['defaultValueArray']))
			$this->vars[$var]['value_array']	= $options['defaultValueArray'];
		else
			$this->vars[$var]['value_array']	= array();
		
		return TRUE;
	}
		
	function setFieldProcessor ($var, $functionName) {
		if(!array_key_exists($var, $this->vars))
		{
			$this->error_code = 'Field does not exist: '.$var; // Field does not exist.
			return FALSE;
		}
		if($functionName == '')
		{
			$this->error_code = 'Function name is not set.'; // Function name is not set.
			return FALSE;
		}
		$this->vars[$var]['processor'] = $functionName;
		return TRUE;
	}
	
	function setFieldChecker ($var, $functionName) {
		if(!array_key_exists($var, $this->vars))
		{
			$this->error_code = 'Field does not exist: '.$var; // Field does not exist.
			return FALSE;
		}
		if($functionName == '')
		{
			$this->error_code = 'Function name is not set.'; // Function name is not set.
			return FALSE;
		}
		$this->vars[$var]['checker'] = $functionName;
		return TRUE;
	}
	
	function setUrl ($url) {
		$this->form_url = $url;
		return TRUE;
	}
	
	function setHeading ($heading) {
		$this->heading = $heading;
		return TRUE;
	}
	
	function setSubmitTxt ($submit_txt) {
		$this->submit_txt = $submit_txt;
		return TRUE;
	}
	
	function setID ($id)
	{
		$this->id = $id;
		return TRUE;
	}
	
	function setDBFieldID ($id_field)
	{
		$this->db_field_id		= $id_field;
		return TRUE;
	}
	
	function setDBFieldEdit ($edit_field, $edit_field_by = '')
	{
		$this->db_field_edit	= $edit_field;
		$this->db_field_by		= $edit_field_by;
		return TRUE;
	}
	
	function setDBTable ($table)
	{
		$this->db_table = $table;
		return TRUE;
	}
	
	function setDBEditBy ($edit_by)
	{
		$this->db_field_edit_by_value = $edit_by;
		return TRUE;
	}
	
	function setWarningfunction ($func)
	{
		$this->warningfunction = $func;
		return TRUE;
	}
	
	function showID ($show) {
		$this->showid = $show;
		return TRUE;
	}
	
	function addChoice ($var, $value, $name, $options = array()) {
		$thischoice = array();
		$thischoice['value']	= $value;
		$thischoice['name']		= $name;
		// Checking options and adding em
		if(isset($options['id']))		$thischoice['id']		= $options['id'];
		else							$thischoice['id']		= '';
		if(isset($options['before']))	$thischoice['before']	= $options['before'];
		else							$thischoice['before']	= '';
		if(isset($options['after']))	$thischoice['after']	= $options['after'];
		else							$thischoice['after']	= '';
		if(isset($options['onchange']))	$thischoice['onchange']	= $options['onchange'];
		else							$thischoice['onchange']	= '';
		if(isset($options['onsubmit']))	$thischoice['onsubmit']	= $options['onsubmit'];
		else							$thischoice['onsubmit']	= '';
		if(isset($options['onreset']))	$thischoice['onreset']	= $options['onreset'];
		else							$thischoice['onreset']	= '';
		if(isset($options['onselect']))	$thischoice['onselect']	= $options['onselect'];
		else							$thischoice['onselect']	= '';
		if(isset($options['onblur']))	$thischoice['onblur']	= $options['onblur'];
		else							$thischoice['onblur']	= '';
		if(isset($options['onfocus']))	$thischoice['onfocus']	= $options['onfocus'];
		else							$thischoice['onfocus']	= '';
		if(isset($options['disabled']) && $options['disabled'])	$thischoice['disabled']	= true;
		else							$thischoice['disabled']	= false;
		
		// Setting in $vars
		$this->vars[$var]['choice'][$value] = $thischoice;
		
		return TRUE;
	}
	
	function input ($input) {
		if(!is_array($input))
		{
			$this->error_code = 'Input is not array'; // Input is not array
			return FALSE;
		}
		
		$error_found = false;
		foreach ($this->vars as $var)
		{
			if(isset($input[$var['var']]))
			{
				$func = 'processInput_' . $var['processor'];
				
				if($var['type'] == 'checkbox')
					$this->vars[$var['var']]['value_array'] = $this->$func($var['var'], $input[$var['var']]);
				else
					$this->vars[$var['var']]['value'] = $this->$func($var['var'], $input[$var['var']]);
			}
			else
			{
				$this->vars[$var['var']]['value_array'] = array();
				$this->vars[$var['var']]['value'] = '';
			}
			
			$func = 'checkInput_' . $var['checker'];
			if(!$this->$func($var['var']))
			{
				$error_found = true;
			}
		}
		
		if($error_found)
		{
			$this->error_code = 'Error was found in input'; // Error was found in input
			return FALSE;
		}
		else
		{
			if($this->warningfunction != '')
			{
				$func = $this->warningfunction;
				if(!$func())
					return FALSE;
				else
					return TRUE;
			}
			else
			{
				return TRUE;
			}
		}
	}

	function processInput_text ($var, $input) {
		return slashes(htmlspecialchars($input,ENT_QUOTES));
	}
	
	function checkInput_text ($var) {
		if($this->vars[$var]['required'] && $this->vars[$var]['value'] == '')
		{
			$this->error_input[] = __('A required field').', '.$this->vars[$var]['name'].', '. __('is empty');
			return FALSE;
		}
		
		return TRUE;
	}
	
	function processInput_textarea ($var, $input) {
		return $this->processInput_text($var, $input);
	}
	
	function checkInput_textarea ($var) {
		return $this->checkInput_text($var);
	}
	
	function processInput_select ($var, $input) {
		foreach($this->vars[$var]['choice'] as $choice)
		{
			if($choice['value'] == $input)
				return $input;
		}
		
		// Still running? Non selected
		return '';
	}
	
	function checkInput_select ($var) {
		if($this->vars[$var]['required'] && $this->vars[$var]['value'] == '')
		{
			$this->error_input[] = __('A required field').', '.$this->vars[$var]['name'].', '. __('is empty');
			return FALSE;
		}
		
		return TRUE;
	}

	function processInput_boolean ($var, $input) {
		if($input == '1')
			return TRUE;
		else
			return FALSE;
	}
	
	function checkInput_boolean ($var) {
		// Will always be true or false from processInput
		return TRUE;
	}
	
	function processInput_hidden ($var, $input) {
		return $this->processInput_text($var, $input);
	}
	
	function checkInput_hidden ($var) {
		// Will always be true or false from processInput
		return TRUE;
	}
	
	function processInput_checkbox ($var, $input) {
		$inputarray = array();
		foreach($input as $thisinput)
		{
			if(array_key_exists($thisinput, $this->vars[$var]['choice']))
				$inputarray[]= $thisinput;
		}
		return $inputarray;
	}
	
	function checkInput_checkbox ($var) {
		if($this->vars[$var]['required'] && !count($var['value_array']))
		{
			$this->error_input[] = __('A required field').', '.$this->vars[$var]['name'].', '. __('is empty');
			return FALSE;
		}
		
		return TRUE;
	}
	
	function getDB () {
		if(!empty($this->id))
		{
			if(is_array($this->id))
			{
				// Composite PK
				$where = array();
				foreach($this->db_field_id as $pk) {
					$where[$pk] = '`'.$pk.'` = \''.$this->id[$pk].'\'';
				}
				$QUERY = db()->prepare("
					SELECT * 
					FROM
						`".$this->db_table."`
					WHERE
						".implode(' AND ', $where));
			}
			else
			{
				$QUERY = db()->prepare("
					SELECT *
					FROM
						`".$this->db_table."`
					WHERE
						`".$this->db_field_id."` = :id");
                $QUERY->bindValue(':id', $this->id, PDO::PARAM_INT);
			}
            $QUERY->execute();
			if($QUERY->rowCount() > 0)
			{
                $row = $QUERY->fetch();
				foreach ($this->vars as $var)
				{
					if(!$var['noDB'])
					{

						if($var['type'] == 'checkbox') {
                            $this->vars[$var['var']]['value_array'] = splittIDs($row[$var['var']]);
                        }
						else {
                            $this->vars[$var['var']]['value'] = $row[$var['var']];
                        }
					}
				}
			}
			else
			{
				echo implode(', ', db()->errorInfo());
				return false;
			}
			return TRUE;
		}
		else
			return TRUE;
	}
	
	function printEditor () {
		echo '<form action="'.$this->form_url.'" method="POST">'.chr(10);
		echo $this->heading_before.$this->heading.$this->heading_after;
		echo $this->fields_before;
		
		// Errors or warnings?
		if(count($this->error_input))
		{
			echo '<tr><td colspan="3" style="border: 1px black solid;">'.chr(10);
			
			echo '<div class="error">'.__('One or more errors occured in the data submited').'</div>'.chr(10);
			echo '<ul>'.chr(10);
			foreach ($this->error_input as $error)
			{
				echo '<li>'.$error.'</li>'.chr(10);
			}
			echo '</ul>'.chr(10);
			echo '</td></tr>'.chr(10);
			
			// Make some space
			echo '<tr><td colspan="3">&nbsp;</td></tr>'.chr(10).chr(10);
		}
		if(count($this->warnings))
		{
			echo '<tr><td colspan="3" style="border: 1px black solid;">'.chr(10);
			
			echo '<center><font color="red" size="5">'.__('One or more warnings where generated.').'</font></center>'.chr(10);
			echo '<ul>'.chr(10);
			foreach ($this->warnings as $warning)
			{
				echo '<li>'.$warning.'</li>'.chr(10);
			}
			echo '</ul>'.chr(10);
			echo '<i>'.__('Changes are still not saved. Please fix warnings or ignore them to get the changes saved.').'</i><br><br>'.chr(10);
			
			echo '<input type="checkbox" value="1" name="warningignore"> '.__('I have seen the warnings and still want to proceed.').'<br>'.chr(10);
			echo '<input type="submit" value="'.__('Proceed').'"><br>'.chr(10);
			echo '</td></tr>'.chr(10);
			
			// Make some space
			echo '<tr><td colspan="3">&nbsp;</td></tr>'.chr(10).chr(10);
		}
		if($this->error_code != '')
		{
			echo '<tr><td colspan="3" style="border: 1px black solid;">'.chr(10);
			echo '<div class="error">Feil oppsto:<br>'.$this->error_code.'</div>'.chr(10);
			echo '</td></tr>'.chr(10);
			
			// Make some space
			echo '<tr><td colspan="3">&nbsp;</td></tr>'.chr(10).chr(10);
		}
		
		if($this->showid && $this->id != '') {
			echo "\t<tr>\n\t\t<td>".__('ID')."</td>\n\t\t<td>".
			"<input size=\"6\" type=\"text\" disabled=\"disabled\" value=\"";
			if(is_array($this->id))
				echo current($this->id);
			else
				echo $this->id;
			echo "\"></td>\n\t</tr>\n";
		}
		
		// Fields:
		foreach ($this->vars as $var)
		{
			echo $var['before'];
			
			if($var['type'] != 'hidden')
				echo $var['name']."</td>\n\t\t<td>";
			
			if($var['onchange'] != '')	$onchange	= ' onchange="'.$var['onchange'].'"';
			else						$onchange	= '';
			if($var['onsubmit'] != '')	$onsubmit	= ' onsubmit="'.$var['onsubmit'].'"';
			else						$onsubmit	= '';
			if($var['onreset'] != '')	$onreset	= ' onreset="'.$var['onreset'].'"';
			else						$onreset	= '';
			if($var['onselect'] != '')	$onselect	= ' onselect="'.$var['onselect'].'"';
			else						$onselect	= '';
			if($var['onblur'] != '')	$onblur		= ' onblur="'.$var['onblur'].'"';
			else						$onblur		= '';
			if($var['onfocus'] != '')	$onfocus	= ' onfocus="'.$var['onfocus'].'"';
			else						$onfocus	= '';
			if($var['disabled'])		$disabled	= ' disabled="disabled"';
			else						$disabled	= '';
			if($var['id'] != '')		$id			= ' id="'.$var['id'].'"';
			else						$id			= '';
			
			switch ($var['type'])
			{
				case 'text':
					echo '<input type="text" name="'.$var['var'].'" value="'.$var['value'].'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.'>';
					break;
				
				case 'hidden':
				echo '<input type="hidden" name="'.$var['var'].'" value="'.$var['value'].'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.'>';
				break;
				
				case 'password':
					echo '<input type="password" name="'.$var['var'].'" value="'.$var['value'].'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.'>';
					break;
				
				case 'textarea':
					echo '<textarea name="'.$var['var'].'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.'>'.$var['value'].'</textarea>';
					break;
				
				case 'radio':
					foreach ($var['choice'] as $choice)
					{
						if($choice['onchange'] != '')			$onchange	= ' onchange="'.$choice['onchange'].'"';
						else									$onchange	= '';
						if($choice['onsubmit'] != '')			$onsubmit	= ' onsubmit="'.$choice['onsubmit'].'"';
						else									$onsubmit	= '';
						if($choice['onreset'] != '')			$onreset	= ' onreset="'.$choice['onreset'].'"';
						else									$onreset	= '';
						if($choice['onselect'] != '')			$onselect	= ' onselect="'.$choice['onselect'].'"';
						else									$onselect	= '';
						if($choice['onblur'] != '')				$onblur		= ' onblur="'.$choice['onblur'].'"';
						else									$onblur		= '';
						if($choice['onfocus'] != '')			$onfocus	= ' onfocus="'.$choice['onfocus'].'"';
						else									$onfocus	= '';
						if($choice['disabled'])					$disabled	= ' disabled="disabled"';
						else									$disabled	= '';
						if($choice['id'] != '')					$id			= ' id="'.$choice['id'].'"';
						else									$id			= '';
						if($choice['value'] == $var['value'])	$checked	= ' checked="checked"';
						else									$checked	= '';
						
						echo $choice['before'];
						echo '<label><input type="radio" name="'.$var['var'].'" value="'.$choice['value'].'" '.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.$checked.' /> - '.$choice['name'].'</label><br />';
						echo $choice['after'];
					}
					break;
				
				case 'select':
					echo '<select name="'.$var['var'].'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.'>';
					foreach ($var['choice'] as $choice)
					{
						if($choice['onchange'] != '')			$onchange	= ' onchange="'.$choice['onchange'].'"';
						else									$onchange	= '';
						if($choice['onsubmit'] != '')			$onsubmit	= ' onsubmit="'.$choice['onsubmit'].'"';
						else									$onsubmit	= '';
						if($choice['onreset'] != '')			$onreset	= ' onreset="'.$choice['onreset'].'"';
						else									$onreset	= '';
						if($choice['onselect'] != '')			$onselect	= ' onselect="'.$choice['onselect'].'"';
						else									$onselect	= '';
						if($choice['onblur'] != '')				$onblur		= ' onblur="'.$choice['onblur'].'"';
						else									$onblur		= '';
						if($choice['onfocus'] != '')			$onfocus	= ' onfocus="'.$choice['onfocus'].'"';
						else									$onfocus	= '';
						$disabled	= ''; // Not used for <option>
						if($choice['id'] != '')					$id			= ' id="'.$choice['id'].'"';
						else									$id			= '';
						if($choice['value'] == $var['value'])	$selected	= ' selected="selected"';
						else									$selected	= '';
						
						echo $choice['before'];
						echo '<option value="'.$choice['value'].'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.$selected.'>'.$choice['name'].'</option>';
						echo $choice['after'];
					}
					echo '</select>';
					break;
				
				case 'checkbox':
					foreach ($var['choice'] as $choice)
					{
						if($choice['onchange'] != '')			$onchange	= ' onchange="'.$choice['onchange'].'"';
						else									$onchange	= '';
						if($choice['onsubmit'] != '')			$onsubmit	= ' onsubmit="'.$choice['onsubmit'].'"';
						else									$onsubmit	= '';
						if($choice['onreset'] != '')			$onreset	= ' onreset="'.$choice['onreset'].'"';
						else									$onreset	= '';
						if($choice['onselect'] != '')			$onselect	= ' onselect="'.$choice['onselect'].'"';
						else									$onselect	= '';
						if($choice['onblur'] != '')				$onblur		= ' onblur="'.$choice['onblur'].'"';
						else									$onblur		= '';
						if($choice['onfocus'] != '')			$onfocus	= ' onfocus="'.$choice['onfocus'].'"';
						else									$onfocus	= '';
						if($choice['disabled'])					$disabled	= ' disabled="disabled"';
						else									$disabled	= '';
						if($choice['id'] != '')					$id			= ' id="'.$choice['id'].'"';
						else									$id			= '';
						if(in_array($choice['value'], $var['value_array']))	$checked	= ' checked="checked"';
						else												$checked	= '';
						
						echo $choice['before'];
						echo '<label><input type="checkbox" name="'.$var['var'].'[]" value="'.$choice['value'].'" '.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.$checked.' /> - '.$choice['name'].'</label><br />';
						echo $choice['after'];
					}
					break;
				
				case 'submit':
					echo '<input type="submit" name="'.$var['var'].'" value="'.$var['name'].'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.'>';
					break;
				
				case 'date':
					echo '<input type="text" name="'.$var['var'].'" value="'.date('H:i d-m-Y', $var['value']).'"'.$onchange.$onsubmit.$onreset.$onselect.$onblur.$onfocus.$disabled.$id.'>';
					break;
				
				case 'boolean':
					$checked_yes = $checked_no = '';
					if($var['value'])	$checked_yes	= ' checked="checked"';
					else			$checked_no	= ' checked="checked"';
					echo '<label><input type="radio" name="'.$var['var'].'" value="1" '.$checked_yes.' /> - '.__('Yes').'</label><br />';
					echo '<label><input type="radio" name="'.$var['var'].'" value="0" '.$checked_no.' /> - '.__('No').'</label><br />';
					
					break;
			}
			
			if($var['type'] != 'hidden')
			{
				echo "</td>\n\t\t<td>";
				if($var['desc'] == '')
					echo '&nbsp;';
				echo $var['desc'];
				echo $var['after'];
			}
		}
		echo "\t<tr>\n\t\t<td>&nbsp;</td>\n\t\t<td><input type=\"submit\" value=\"".$this->submit_txt."\"></td>\n\t</tr>\n";
		echo $this->fields_after;
		echo '<input type="hidden" name="editor_submit" value="true">'.chr(10);
		if(!is_array($this->id) && $this->id != '')
			echo '<input type="hidden" name="id" value="'.$this->id.'">'.chr(10);
		echo '</form>'.chr(10);
	}
	
	function error () {
		return implode(', ', db()->errorInfo());
	}
	
	function performDBquery ()
	{
		/* Making MySQL query */
        $enumColumn = array();
        $enumColumn['user_invoice_setready'] = 'user_invoice_setready';
		
		// Getting fields and values
		$fields = array();
		foreach ($this->vars as $var)
		{
			if($var['DBQueryPerform'])
			{
				if($var['type'] == 'checkbox') {
                    // Splittalize?
                    $fields[$var['var']] = splittalize($var['value_array']);
                }
				else {
                    $fields[$var['var']] = $var['value'];
                }

                if(isset($enumColumn[$var['var']])) {
                    $fields[$var['var']] = $var['value'] ? '1' : '0';
                }
            }
		}
		if($this->db_field_edit != '') {
            $fields[$this->db_field_edit] = time();
        }
		if($this->db_field_edit_by != '') {
            $fields[$this->db_field_edit_by] = $this->db_field_edit_by_value;
        }
		
		// Edit or add?
		$SQL = '';
		if($this->id != '')
		{
			// Edit
			$SQL = "UPDATE `".$this->db_table."` SET ";
			$i = 0;
			foreach ($fields as $field => $value) {
				$i++;
                $fieldContent = ':'.$field;
                if(isset($enumColumn[$field])) {
                    $fieldContent = "'$value'";
                }
				$SQL .= "`$field` = $fieldContent";
				if($i < count($fields)) {
                    $SQL .= ', ';
                }
                $SQL .= chr(10);
			}
			
			if(is_array($this->id))
			{
				// Composite PK
				$where = array();
				foreach($this->db_field_id as $pk) {
					$where[$pk] = '`'.$pk.'` = \''.$this->id[$pk].'\'';
				}
				$SQL .= ' WHERE '.implode(' AND ', $where) . chr(10);
			}
			else
			{	
				$SQL .= " WHERE `".$this->db_field_id."` = '".$this->id."'\n";
			}
			$SQL .= " LIMIT 1 ;";
		}
		else
		{
			// Add
			$SQL .= 'INSERT INTO `'.$this->db_table.'` (';
			$i = 0;
			foreach ($fields as $field => $value) {
				$i++;
				$SQL .= "`$field`";
				if($i < count($fields))
					$SQL .= ',';
			}
			$SQL .= ') VALUES (';
			$i = 0;
			foreach ($fields as $field => $value) {
				$i++;
                $fieldContent = ':'.$field;
                if(isset($enumColumn[$field])) {
                    $fieldContent = "'$value'";
                }
				$SQL .= "$fieldContent";
				if($i < count($fields))
					$SQL .= ',';
			}
			$SQL .= ');';
		}

        /* Running MySQL query */

        $Q = db()->prepare($SQL);
        foreach ($fields as $field => $value) {
            if ($field == 'user_invoice_setready') {
                continue;
            }
            switch($this->vars[$field]['type']) {
                case 'text':
                case 'select':
                case 'textarea':
                case 'hidden':
                    $Q->bindValue(':' . $field, $value, PDO::PARAM_STR);
                    break;
                case 'boolean':
                    $value = '' . ($value ? '1' : '0');
                    $Q->bindValue(':' . $field, $value, PDO::PARAM_STR);
                    break;
                default:
                    throw new Exception('Unknown field type: ' . $this->vars[$field]['type']);
            }
        }                 /*
        echo '<pre>';
        var_dump($fields);
        var_dump($Q);   */
        if(!$Q->execute()) {
            return false;
        }

        if($this->id == '') {
            $this->id = db()->lastInsertId();

        }
		
		return TRUE;
	}
}
