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

require "libs/Smarty/Smarty.class.php";

function templateAssignEntry($var, $entry)
{
	global $$var;
	
	if(count($entry)) // Just checking
	{
		$$var->assign ('entry_id', $entry['entry_id']);
		$$var->assign ('entry_name', $entry['entry_name']);
		$$var->assign ('entry_title', $entry['entry_title']);
		$$var->assign ('confirm_email', $entry['confirm_email']);
		if($entry['confirm_email'])
			$$var->assign ('confirm_email2', _('yes'));
		else
			$$var->assign ('confirm_email2', _('no'));
		$$var->assign ('entry_type_id', $entry['entry_type_id']);
		
		if($entry['entry_type_id'] == 0)
		{
				$entry_type = _('Non selected');
		}
		else
		{
			$entry_type = getEntryType($entry['entry_type_id']);
			if(count($entry_type))
				$entry_type = $entry_type['entry_type_name'];
			else
				$entry_type = 'ERROR';
		}
		$$var->assign ('entry_type', $entry_type);
		
		$$var->assign ('num_person_child', $entry['num_person_child']);
		$$var->assign ('num_person_adult', $entry['num_person_adult']);
		$$var->assign ('num_person_count', $entry['num_person_count']);
		if($entry['num_person_count'])
			$$var->assign ('num_person_count2', _('yes'));
		else
			$$var->assign ('num_person_count2', _('no'));
		$$var->assign ('program_id', $entry['program_id']);
		$program = getProgram($entry['program_id']);
		if(count($program))
		{
			$$var->assign ('program_id_name', $program['program_name']);
			$$var->assign ('program_id_desc', $program['program_desc']);
		}
		else
		{
			$$var->assign ('program_id_name', '');
			$$var->assign ('program_id_desc', '');
		}
		$$var->assign ('program_description', $entry['program_description']);
		$$var->assign ('service_alco', $entry['service_alco']);
		if($entry['service_alco'])
			$$var->assign ('service_alco2', _('yes'));
		else
			$$var->assign ('service_alco2', _('no'));
		$$var->assign ('service_description', $entry['service_description']);
		$$var->assign ('comment', $entry['comment']);
		$$var->assign ('infoscreen_txt', $entry['infoscreen_txt']);
		$$var->assign ('time_start', $entry['time_start']);
		$$var->assign ('time_end', $entry['time_end']);
		$$var->assign ('room_id', $entry['room_id']);
		// Room
		$rooms = array();
		if(!count($entry['room_id']))
			$rooms[] = _('Whole area');
		elseif(count($entry['room_id']) == '1')
		{
			// Single room
			foreach ($entry['room_id'] as $rid)
			{
				if ($rid == '0')
					$rooms[] = _('Whole area');
				else
				{
					$room = getRoom($rid);
					if(count($room))
						$rooms[] = $room['room_name'];
					else
						$rooms[] = 'ERROR';
				}
			}
		}
		else
		{
			foreach ($entry['room_id'] as $rid)
			{
				if($rid != '0')
				{
					$room = getRoom($rid);
					if(count($room))
						$rooms[] = $room['room_name'];
					else
						$rooms[] = 'ERROR';
				}
			}
		}
		if(!count($rooms))
			$rooms[] = _('Whole area');
		$$var->assign ('room', implode(', ', $rooms));
		$$var->assign ('rooms', $rooms);
		$$var->assign ('area_id', $entry['area_id']);
		// Area
		$area = getArea($entry['area_id']);
		if(count($area))
			$area = $area['area_name'];
		else
			$area = 'ERROR';
		$$var->assign ('area', $area);
		$$var->assign ('user_assigned', $entry['user_assigned']);
		$$var->assign ('user_assigned2', $entry['user_assigned2']);
		// User_assigned_names
		$names = array();
		$names2 = array();
		foreach ($entry['user_assigned'] as $user_id) {
			if($user_id != 0)
			{
				$user = getUser($user_id);
				if(count($user))
				{
					$names[] = $user['user_name'];
					$names2[] = '<a href="user.php?user_id='.$user['user_id'].'">'.$user['user_name'].'</a>';
				}
				else
				{
					$names[] = 'ERROR';
					$names2[] = 'ERROR';
				}
			}
		}
		if($entry['user_assigned2'] != '')
		{
			$names[] = $entry['user_assigned2'];
			$names2[] = $entry['user_assigned2'];
		}
		if(!count($names))
		{
			$names[] = _('Nobody');
			$names2[] = _('Nobody');
			$$var->assign ('user_assigned_any', false);
		} else
			$$var->assign ('user_assigned_any', true);
		$$var->assign ('user_assigned_names', implode(', ', $names));
		$$var->assign ('user_assigned_names2', implode(', ', $names2));
		$$var->assign ('customer_id', $entry['customer_id']);
		$$var->assign ('customer_name', $entry['customer_name']);
		$$var->assign ('contact_person_name', $entry['contact_person_name']);
		$$var->assign ('contact_person_phone', $entry['contact_person_phone']);
		$$var->assign ('contact_person_email', $entry['contact_person_email']);
		$$var->assign ('customer_municipal_num', $entry['customer_municipal_num']);
		$$var->assign ('customer_municipal', $entry['customer_municipal']);
		$$var->assign ('created_by', $entry['created_by']);
		// created_by_name
		$user = getUser($entry['created_by']);
		if(count($user))
			$$var->assign ('created_by_name', $user['user_name']);
		else
			$$var->assign ('created_by_name', '');
		$$var->assign ('time_created', $entry['time_created']);
		$$var->assign ('edit_by', $entry['edit_by']);
		// Edit_by_names
		$names = array();
		foreach ($entry['edit_by'] as $user_id) {
			$user = getUser($user_id);
			if(count($user))
				$names[] = $user['user_name'];
			else
				$names[] = 'ERROR';
		}
		if(!count($names))
			$names[] = _('Nobody');
		$$var->assign ('edit_by_names', implode(', ', $names));
		$$var->assign ('time_last_edit', $entry['time_last_edit']);
		$$var->assign ('user_last_edit', $entry['user_last_edit']);
		$user = getUser($entry['user_last_edit']);
		if(count($user))
			$$var->assign ('user_last_edit_name', $user['user_name']);
		else
			$$var->assign ('user_last_edit_name', '');
		$$var->assign ('rev_num', $entry['rev_num']);
		
		$$var->assign ('invoice', $entry['invoice']);
		if($entry['invoice'] == '1') {
			$$var->assign ('invoice2', true);
			$$var->assign ('invoice3', _('yes'));
		}
		else {
			$$var->assign ('invoice2', false);
			$$var->assign ('invoice3', _('no'));
		}
		$$var->assign ('invoice_status', $entry['invoice_status']);
		switch($entry['invoice_status'])
		{
			case '0':	$$var->assign ('invoice_status2', _('not to be made'));	break;
			case '1':	$$var->assign ('invoice_status2', 'skal lages, ikke klar');	break;
			case '2':	$$var->assign ('invoice_status2', 'skal lages, klar til fakturering');	break;
			case '3':	$$var->assign ('invoice_status2', 'faktura eksportert til Komfakt');	break;
		}
		$$var->assign ('invoice_electronic', $entry['invoice_electronic']);
		if($entry['invoice_electronic'] == '1') {
			$$var->assign ('invoice_electronic2', true);
			$$var->assign ('invoice_electronic3', _('yes'));
		}
		else {
			$$var->assign ('invoice_electronic2', false);
			$$var->assign ('invoice_electronic3', _('no'));
		}
		$$var->assign ('invoice_email', $entry['invoice_email']);
		$$var->assign ('invoice_comment', $entry['invoice_comment']);
		$$var->assign ('invoice_internal_comment', $entry['invoice_internal_comment']);
		$$var->assign ('invoice_ref_your', $entry['invoice_ref_your']);
		$$var->assign ('invoice_address_id', $entry['invoice_address_id']);
		$invoice_address = getAddress($entry['invoice_address_id']);
		if(count($invoice_address))
			$$var->assign ('invoice_address', $invoice_address['address_full']);
		else
			$$var->assign ('invoice_address', '');
		$$var->assign ('invoice_content', $entry['invoice_content']);
		$$var->assign ('invoice_exported_time', $entry['invoice_exported_time']);
		$$var->assign ('mva_vis', $entry['mva_vis']);
		$$var->assign ('mva', $entry['mva']);
		$$var->assign ('mva_grunnlag', $entry['mva_grunnlag']);
		$$var->assign ('mva_grunnlag_sum', $entry['mva_grunnlag_sum']);
		$$var->assign ('faktura_belop_sum', $entry['faktura_belop_sum']);
		$$var->assign ('faktura_belop_sum_mva', $entry['faktura_belop_sum_mva']);
		$$var->assign ('eks_mva_tot', $entry['eks_mva_tot']);
		$$var->assign ('grunnlag_mva_tot', $entry['grunnlag_mva_tot']);
	}
}

function templateAssignSystemvars($var)
{
	global $$var, $systemurl, $login;
	
	$$var->assign ('systemurl', $systemurl);
	$$var->assign ('user_name', $login['user_name']);
	$$var->assign ('user_name_short', $login['user_name_short']);
	$$var->assign ('user_email', $login['user_email']);
	$$var->assign ('user_phone', $login['user_phone']);
	$$var->assign ('user_position', $login['user_position']);
	$$var->assign ('user_invoice', $login['user_invoice']);
	$$var->assign ('user_invoice_setready', $login['user_invoice_setready']);
	$$var->compile_check = true;
	#$$var->debugging = true;
	$$var->debugging = false;
	$$var->caching = false;
	
	$$var->register_resource("db", array("smarty_resource_db_source",
                                       "smarty_resource_db_timestamp",
                                       "smarty_resource_db_secure",
                                       "smarty_resource_db_trusted"));
	
	$$var->register_function ('iconHTML', 'templateIconHtml');
	$$var->register_modifier('commify', 'smarty_modifier_commify');
	$$var->register_modifier('file_size', 'smarty_modifier_file_size');
}

function templateAssignEntryChanges ($var, $entry, $rev_num, $remove_invoice = false)
{
	global $$var;
	
	$rev_num = (int)$rev_num;
	if(count($entry) && $rev_num > 0)
	{
		$Q_rev = mysql_query("select * from `entry_log` where `entry_id` = '".$entry['entry_id']."' and `rev_num` = '".$rev_num."' limit 1");
		if(!mysql_num_rows($Q_rev))
		{
			// Assigning all the var with zero value
			$$var->assign('log_time', 0);
			$$var->assign('log_action_real', '');
			$$var->assign('log_user_id', 0);
			$$var->assign('log_user', '');
			$$var->assign('log_changes', array());
		}
		else
		{
			$thislog = mysql_fetch_assoc($Q_rev);
			$thislog['log_data'] = unserialize($thislog['log_data']);
			$$var->assign('log_time', $thislog['log_time']);
			$$var->assign('log_action_real', printEntryLog($thislog, false, true));
			$$var->assign('log_user_id', $thislog['user_id']);
			$user = getUser($thislog['user_id']);
			if(count($user))
				$$var->assign('log_user', $user['user_name']);
			else
				$$var->assign('log_user', 'ERROR');
			
			$changes = array();
			if($remove_invoice)
			{
				foreach($thislog['log_data'] as $var2 => $value)
				{
					if(substr($var2, 0, 7) == 'invoice')
					{
						unset($thislog['log_data'][$var2]);
					}
				}
			}
			foreach (readEntryLog($thislog) as $change)
			{
				$changes[] = strip_tags($change);
			}
			$$var->assign('log_changes', $changes);
		}
	}
	else
	{
		$$var->assign('log_time', 0);
		$$var->assign('log_action_real', '');
		$$var->assign('log_user_id', 0);
		$$var->assign('log_user', '');
		$$var->assign('log_changes', array());
	}
}

function templateAssignInvoice($var, $invoice)
{
	global $$var;
	
	foreach ($invoice->variables_to_template as $thisvar) {
		$$var->assign ($thisvar, $invoice->$thisvar);
	}
	/*
	$$var->assign ('invoice_id', $invoice->invoice_id);
	$$var->assign ('invoice_time_created', $invoice->invoice_time_created);
	$$var->assign ('invoice_time', $invoice->invoice_time);
	$$var->assign ('invoice_time2', $invoice->invoice_time2);
	$$var->assign ('invoice_time_due', $invoice->invoice_time_due);
	$$var->assign ('invoice_time_due2', $invoice->invoice_time_due2);
	$$var->assign ('invoice_time_payed', $invoice->invoice_time_payed);
	$$var->assign ('invoice_payment_info', $invoice->invoice_payment_info);
	$$var->assign ('invoice_content', $invoice->invoice_content);
	$$var->assign ('invoice_topay_total', $invoice->invoice_topay_total);
	$$var->assign ('invoice_topay_total_net', $invoice->invoice_topay_total_net);
	$$var->assign ('invoice_topay_total_tax', $invoice->invoice_topay_total_tax);
	$$var->assign ('invoice_payed', $invoice->invoice_payed);
	$$var->assign ('invoice_payed2', $invoice->invoice_payed2);
	$$var->assign ('invoice_to_line1', $invoice->invoice_to_line1);
	$$var->assign ('invoice_to_line2', $invoice->invoice_to_line2);
	$$var->assign ('invoice_to_line3', $invoice->invoice_to_line3);
	$$var->assign ('invoice_to_line4', $invoice->invoice_to_line4);
	$$var->assign ('invoice_to_line5', $invoice->invoice_to_line5);
	$$var->assign ('invoice_to_line6', $invoice->invoice_to_line6);
	$$var->assign ('invoice_to_line7', $invoice->invoice_to_line7);
	$$var->assign ('invoice_to_lines', $invoice->invoice_to_lines);
	$$var->assign ('invoice_to_email', $invoice->invoice_to_email);*/
}

function templateFetchFromVariable ($var, $tpl)
{
	global $$var;
	
	// register the resource name "var"
	$$var->register_resource("var", array("smarty_resource_var_template",
	                                       "smarty_resource_var_timestamp",
	                                       "smarty_resource_var_secure",
	                                       "smarty_resource_var_trusted"));
	
	// template variable
	$$var->assign('MyTemplate', $tpl);
	
	// the timestamp for the template variable
	//$smarty->assign('mytpl_time',strtotime('Sep 20 2006 00:00:00'));
	
	return $$var->fetch('var:MyTemplate');
}

function templateError ($error)
{
	echo '<div class="error" style="width: 500px;">'.$error.'</div>';
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * -------------------------------------------------------------
 */
function smarty_resource_db_source($tpl_name, &$tpl_source, &$smarty)
{
	// do database call here to fetch your template,
	// populating $tpl_source
	$Q = mysql_query("select template
		from template
		where template_id='$tpl_name'");
	if (mysql_num_rows($Q)) {
		$tpl_source = htmlspecialchars_decode(mysql_result($Q, 0, 'template'), ENT_QUOTES);
		return true;
	} else {
		return false;
	}
}

function smarty_resource_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
	// do database call here to populate $tpl_timestamp.
	/*
	$Q = mysql_query("select template_time_last_edit
		from template
		where template_id='$tpl_name'");
	if (mysql_num_rows($Q)) {
		$tpl_timestamp = mysql_result($Q, 0, 'template_time_last_edit');
		return true;
	} else {
		return false;
	}*/
	$tpl_timestamp = time();
	return true;
}

function smarty_resource_db_secure($tpl_name, &$smarty)
{
	// assume all templates are secure
	return true;
}

function smarty_resource_db_trusted($tpl_name, &$smarty)
{
	// not used for templates
}


// Variable resource, the resource functions
function smarty_resource_var_template ($tpl_name, &$tpl_source, &$smarty_obj)
{
   
    $tpl_source = $smarty_obj->get_template_vars($tpl_name);
	//return empty($tpl_source) ? false : true;
	return true;
}

function smarty_resource_var_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
{
    // if var has a timestamp value, use it
    $time = $smarty_obj->get_template_vars($tpl_name.'_time');
    //return !empty($time) ? $time : time();
    $tpl_timestamp = time();
    return true;
}

function smarty_resource_var_secure($tpl_name, &$smarty_obj)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_var_trusted($tpl_name, &$smarty_obj)
{
    // not used for templates
}

function templateIconHtml ($params, &$smarty) {
	if(isset($params['ico'])) {
		$ico = $params['ico'];
	}
	else
		$ico = '';
	if(isset($params['end'])) {
		$end = $params['end'];
	}
	else
		$end = '.png';
	return iconHTML($ico, $end);
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:        modifier
 *
 * Name:        commify
 *
 * Purpose:     format numbers by inserting thousands seperators
 *              this is basically a wrapper for number_format
 *              with additional processing to retain the original
 *              digits after the decimal point (if any)
 *
 * Input:       string: number to be formatted
 *              decimals: [optional] number of decimal places
 *              dec_point: [optional] decimal point character
 *              thousands_sep: [optional] thousands seperator
 *
 * Examples:    {$number|commify}    12345.288 => 12,345.288
 *              {$number|commify:2}    12345.288 => 12,345.29
 *
 * Install:     Drop into the plugin directory as modifier.commify.php.
 *
 * Author:      James Brown <james [at] hmpg [dot] net>
 * -------------------------------------------------------------
 */
 
function smarty_modifier_commify($string, $decimals=-1, $dec_point='.', $thousands_sep=',')
{
    if ($decimals == -1)
    {
        if (preg_match('/\.\d+/', $string))
            return number_format($string) . preg_replace('/.*(\.\d+).*/', '$1', $string);
        else
            return number_format($string);
    }
    else
        return str_replace('z', $thousands_sep, str_replace('v', $dec_point, number_format($string, $decimals, 'v', 'z,')));
}

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
 
/**
 * Smarty file_size modifier plugin
 *
 * Type:     modifier<br>
 * Name:     file_size<br>
 * Purpose:  format file size represented in bytes into a human readable string<br>
 * Input:<br>
 *         - bytes: input bytes integer
 * @author   Rob Ruchte <rob at thirdpartylabs dot com>
 
 * @param integer
 * @return string
 */
function smarty_modifier_file_size($bytes=0)
{
    $mb = 1024*1024;
 
    if ($bytes > $mb)
    {
        $output = sprintf ("%01.2f",$bytes/$mb) . " MB";
    }
    elseif ( $bytes >= 1024 )
    {
        $output = sprintf ("%01.0f",$bytes/1024) . " Kb";
    }
    else
    {
        $output = $bytes . " bytes";
    }
 
    return $output;
}



function wikiLink ($article) {
	return 'http://booking.jaermuseet.local/wiki/index.php/'.$article;
}

?>