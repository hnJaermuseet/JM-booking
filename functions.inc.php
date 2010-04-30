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

/* New functions structure: */
require_once "functions/filter.php";
require_once "functions/template.php";
require_once "functions/email.php";
require_once "functions/postal_number.php";
require_once "functions/entry.php";


#vals[0]=variablename
#vals[1]=typ
#	typ: 0: input   1: textarea  2:checkbox  3:radio  4:select
#	note: checkbox and radio are not finished yet
#vals[2]=param[] list of input params
#vals[3]=title(key for lang[] array) 
#vals[4]=description(key for lang[] array)

function drawFormRow($val){
    global $db_entry_fields,$db_entry_fields_session,$db_entry_fields_optional,$db_entry_all_fields_set,$lang,$$val[0];
    if(isset($_GET[$val[0]]))
	    $value=strip_tags($_GET[$val[0]]);
    else
	$value=strip_tags($$val[0]);
    $offset=array_search($val[0],$db_entry_fields);
    #$db_entry_all_fields_set=true;
    #everything saved in session? then do nothing
    if(isset($_SESSION['db_entry_all_fields_set'])&&$db_entry_fields_session[$offset])
    	return;
    echo "<tr><td width=150 align=right nowrap><b>",$val[3],"</b></td><td CLASS=CL style=\"background-color:#DDDDDD\" width=250><table border=0 cellspacing=0 cellpadding=0><tr><td>";
    switch($val[1]){
    	case 0:echo "<input type=text name=\"",$val[0],"\" value=\"",$value,"\"";
		drawInputParams($val[2]);
		echo ">";
		break;
	case 1:echo "<textarea name=\"",$val[0],"\"";
		drawInputParams($val[2]);
		echo ">",$value,"</textarea>";
		break;
	//note: checkbox and radio are not finished yet
	case 2:echo "<input type=checkbox name=\"",$val[0],"\" value=\"",$value,"\"";
		drawInputParams($val[2]);
		echo ">";
		break;
	case 3:echo "<input type=checkbox name=\"",$val[0],"\" value=\"",$value,"\"";
		drawInputParams($val[2]);
		echo ">";
		break;		
	case 4:echo "<select name=\"",$val[0],"\"";
		drawInputParams($val[2]);
		echo ">";
		foreach($val[2] as $oname=>$ovalue){
			if(substr($oname,0,7)=="option:")
			echo "<option value=\"".substr($oname,7)."\"",($value==substr($oname,7)?" selected":""),">",$ovalue,"</option>";
		}
		echo "</select>";
    }
    if(!$db_entry_fields_optional[$offset]){
	echo "</td><td>";
    	star();
	}
    echo "</td></tr></table></td><td width=650 style=\"background-color:#e0e4f1\">";
	if(@constant($val[4]) != '')
		echo constant($val[4]);
	else
		echo "&nbsp;";
	echo "</td></tr>";
    
}

function drawInputParams($param){
	foreach($param as $key=>$val)
	if(substr($key,0,7)!="option:")
		echo " $key=\"",$val,"\"";
}

#this function kills all booking relevant informations from the session, but retaines instance and language information
function reset_session(){
	global $db_entry_fields;
	session_unregister("session_booking_ids");
	session_unregister("session_booking_rid");
	session_unregister("db_entry_all_fields_set");
	
	foreach($db_entry_fields as $val){
		session_unregister($val);
	}
	$_SESSION["db_entry_all_fields_set"]=false;
}

function buildSelectFormat($start,$end,$step,$selection,$prefix,$suffix){
	for($i=$start;$i<=$end;$i=$i+$step){
		$iv = $prefix.((strlen($i)==1)?("0"):("")).$i.$suffix;
		if($i==$selection)
			echo "<option value='$i' selected>$iv</option>";
		else
			echo "<option value='$i'>$iv</option>";
	}
}

function buildSelect($start,$end,$step,$selection){
	for($i=$start;$i<=$end;$i=$i+$step){
		$iv = ((strlen($i)==1)?("0"):("")).$i;
		if($i==$selection)
			echo "<option value='$i' selected>$iv</option>";
		else
			echo "<option value='$i'>$iv</option>";
	}
}

function formHiddenFields(){
	#certain fields should not be passed again: instance and select_language
	#passing them in every form is unnecessary and prevents further changing of these values
	$skip=array("instance","select_language");
	foreach($_GET as $key=>$val)
	{
		if (strlen($val))
		{
			if(!in_array($key,$skip))
				echo "<input type='hidden' name='".$key."' value='".strip_tags($val)."'>";
		}
	}
}
function hrefGetVar($data,$var){
	$pos = strpos($data,$var."=");
	if($pos===false){
		return "";
	}
	else{
		return substr($data, $pos+strlen($var)+1, strpos($data,"&", $pos+strlen($var)+2)-($pos+strlen($var)+1));
	}
}

function print_header($day, $month, $year, $area){
	global $lang, $mrbs_company, $search_str,$nrbs_pageheader,$instance,$language_available,$session_selected_language,$header_links;
	global $userinfo, $testSystem;
	global $selected_room;
	if(!isset($selected_room))
		$selected_room = 0;

	# If we dont know the right date then make it up 
	if(!$day)
		$day   = date("d");
	if(!$month)
		$month = date("m");
	if(!$year)
		$year  = date("Y");
	if (empty($search_str))
		$search_str = "";
	
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">'.chr(10);
	echo '<html>'.chr(10);
	echo '<head>'.chr(10);
	echo '	<title>JM-booking</title>'.chr(10);
	
	include("style.inc.php");
	
	echo '	<link type="text/css" href="css/ui-lightness/jquery-ui-1.7.2.custom.css" '.
		'rel="stylesheet" />'.chr(10);
	echo '	<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp.js">'.
		'</script>'.chr(10);
	echo '</head>'.chr(10).chr(10);
	
	echo '<body'.$testSystem['bodyAttrib'].'>'.chr(10);
	
	if (!isset($GLOBALS["pview"]) || $GLOBALS["pview"] != 1 )
	{
		echo '<table width="100%" class="hiddenprint">'.chr(10);
		if (strlen($nrbs_pageheader)>0)
		{
			echo '<tr><td style="text-align:center;">'.$nrbs_pageheader.'</td></tr>'.chr(10);
		}
		
		echo '	<tr>'.chr(10).
			'	<td bgcolor="#5B69A6">'.chr(10).
			'		<table width="100%" border=0>'.chr(10).
			'			<tr>'.chr(10).
			'				<td class="banner" '.
				'style="text-align:center; font-size: 18px; font-weight: bold;">'.
				$mrbs_company.
			'				</td>'.chr(10).
			'				<td class="banner">'.chr(10).
				
			'					<table>'.chr(10).
			'						<tr>'.chr(10).
			'							<td align="right">'.
			'<form action="day.php" method="get">';
		
		//formHiddenFields(); 
		genDateSelector("", $day, $month, $year);
		if (!empty($area))
			echo '<input type="hidden" name="area" value='.$area.'>'; 
		echo '<input type="submit" value="'._('View day').'">'.
		iconHTML('calendar_view_day').
		'</form>';
		echo '</td>'.chr(10);
		
		// Week
		echo '							<td align="right">'.
			'<form action="week.php" method="get">';
		
		if (!empty($area))
			echo '<input type="hidden" name="area" value="'.$area.'">';
		$thistime = mktime(0, 0, 0, $month, $day, $year);
		$thisweek = date('W', $thistime);
		$thisyear = date('Y', $thistime);
		echo '<select name="week">';
		for ($i = 1; $i <= 52; $i++)
		{
			echo '<option value="'.$i.'"';
			if($i == $thisweek) echo ' selected="selected"';
			echo '>'.$i.'</option>';
		}
		echo '</select>';
		echo '<select name="year">';
		for ($i = ($thisyear-1); $i <= ($thisyear+10); $i++)
		{
			echo '<option value="'.$i.'"';
			if($i == $thisyear) echo ' selected="selected"';
			echo '>'.$i.'</option>';
		}
		echo '</select>';
		echo '<input type="submit" value="'._('View week').'">'.
		iconHTML('calendar_view_week');
		echo '</form>';
		echo '</td>'.chr(10).
		'						</tr>'.chr(10);
		
		// Month
		echo '						<tr>'.chr(10).
			'							<td align="right">'.
			'<form action="month.php" method="get">';
		echo '<input type="hidden" name="area" value="'.$area.'">';
		echo '<input type="hidden" name="day" value="1">';
		$thistime = mktime(0, 0, 0, $month, $day, $year);
		$thismonth = date('n', $thistime);
		$thisyear = date('Y', $thistime);
		echo '<select name="month">';
		for ($i = 1; $i <= 12; $i++)
		{
			$thismonthtime = mktime (0, 0, 0, $i, 1, $year);
			echo '<option value="'.$i.'"';
			if($i == $thismonth) echo ' selected="selected"';
			echo '>'._(date("M", $thismonthtime)).'</option>';
		}
		echo '</select>';
		echo '<select name="year">';
		for ($i = ($thisyear-1); $i <= ($thisyear+10); $i++)
		{
			echo '<option value="'.$i.'"';
			if($i == $thisyear) echo ' selected="selected"';
			echo '>'.$i.'</option>';
		}
		echo '</select>';
		echo '<input type="submit" value="'._('View month').'">'.
		iconHTML('calendar_view_month');
		echo '</form></td>'.chr(10);
		
		// Find using entry_id
		echo '							<td align="right">';
		echo '<form action="entry.php" method="get">';
		echo '<input type="text" id="entry_id_finder" name="entry_id" '.
			'value="'._('Enter entry ID').'" '.
			'onclick="document.getElementById(\'entry_id_finder\').value=\'\';">';
		echo '<input type="submit" value="'._('Find').'">';
		echo '</form>';
		echo '</td>'.chr(10);
		
		echo '						</tr>'.chr(10).
			'					</table>'.chr(10);
		
		echo '				</td>'.chr(10);
		
		echo '				<td class="banner" align="center">'.chr(10);
		echo '					'._("Logged in as").' <a href="user.php?user_id='.$userinfo['user_id'].'">'.$userinfo['user_name'].'</a><br>'.chr(10);
		echo '					<a href="logout.php">'.
			iconHTML('bullet_delete').' '.
			_("Log out").'</a><br>'.chr(10);
		echo '					<a href="admin.php">'.
			iconHTML('bullet_wrench').' '.
			_("Administration").'</a>'.chr(10);
		echo '				</td>'.chr(10);
		
		echo '			</tr>'.chr(10).
			'		</table>'.chr(10);
		
		
		echo '		 -:- <a class="menubar" href="./edit_entry2.php?day='.$day.'&amp;month='.$month.'&amp;year='.$year.'&amp;area='.$area.'&amp;room='.$selected_room.'">'.
		iconHTML('page_white_add').' '.
		_('Make a new entry').'</a>'.chr(10);
		
		//echo '		 -:- <a class="menubar" href="./new_entries.php">'.
		//iconHTML('table').' '.
		//_('List with new entries').'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./entry_list.php?listtype=not_confirmed">'.
		iconHTML('email_delete').' '.
		_('Not confirmed').'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./entry_list.php?listtype=no_user_assigned">'.
		iconHTML('user_delete').' '.
		_('No users assigned').'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./entry_list.php?listtype=servering">'.
		iconHTML('drink').' '.
		'Servering</a>'.chr(10);
		
		#echo '		 -:- <a class="menubar" href="./entry_list.php?listtype=next_100">'.
		#iconHTML('page_white_go').' '.
		#_('Next 100').'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./statistikk.php">'.
		iconHTML('chart_bar').' '.
		'Statistikk</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./customer_list.php">'.
		iconHTML('group').' '.
		_('Customers').'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./invoice_main.php">'.
		iconHTML('coins').' '.
		_('Invoice').'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./user_list.php">'.
		iconHTML('user').' '.
		_('Userlist').'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="./entry_filters.php?filters=a:1:{i:0;a:3:{i:0;s:10:%22entry_name%22;i:1;s:0:%22%22;i:2;s:0:%22%22;}}&amp;return_to=entry_list">'.
		iconHTML('find').' '.
		'Bookings&oslash;k'.'</a>'.chr(10);
		
		echo '		 -:- <a class="menubar" href="http://booking.jaermuseet.local/wiki/">'.
		iconHTML('wiki_icon', '.gif', 'height: 16px;').' '.
		'Wiki'.'</a>'.chr(10);
		
		
		echo '		 -:-'.chr(10);
		
		echo '		</td>'.chr(10).
			'	</tr>'.chr(10).
			'</table>'.chr(10);
	} 
}

function toTimeString(&$dur, &$units){
	global $lang;
	if($dur >= 60){
		$dur /= 60;		
		if($dur >= 60){
			$dur /= 60;			
			if(($dur >= 24) && ($dur % 24 == 0)){
				$dur /= 24;		
				if(($dur >= 7) && ($dur % 7 == 0)){
					$dur /= 7;
					
					if(($dur >= 52) && ($dur % 52 == 0)){
						$dur  /= 52;
						$units = _("Years");
					}
					else
						$units = _("Weeks");
				}
				else
					$units = _("Days");
			}
			else
				$units = _("Hours");
		}
		else
			$units = _("Minutes");
	}
	else
		$units = _("Secounds");
}

function parseDate($date){
	/* $s means "search";
	 * $r means "replace";
	 */
	$s = array();
	$r = array();
	
	$s[]="Monday";			$r[]=_("Monday");
	$s[]="Tuesday";			$r[]=_("Tuedsay");
	$s[]="Wednesday";		$r[]=_("Wednesday");
	$s[]="Thursday";		$r[]=_("Thursday");
	$s[]="Friday";			$r[]=_("Friday");
	$s[]="Saturday";		$r[]=_("Saturday");
	$s[]="Sunday";			$r[]=_("Sunday");
	
	$s[]="Mon";				$r[]=_("Mon");
	$s[]="Tue";				$r[]=_("Tue");
	$s[]="Wed";				$r[]=_("Wed");
	$s[]="Thu";				$r[]=_("Thu");
	$s[]="Fri";				$r[]=_("Fri");
	$s[]="Sat";				$r[]=_("Sat");
	$s[]="Sun";				$r[]=_("Sun");
	
	$s[]="January";			$r[]=_("January");
	$s[]="February";		$r[]=_("February");
	$s[]="March";			$r[]=_("March");
	$s[]="April";			$r[]=_("April");
	$s[]="May";				$r[]=_("May");
	$s[]="June";			$r[]=_("June");
	$s[]="July";			$r[]=_("July");
	$s[]="August";			$r[]=_("August");
	$s[]="September";		$r[]=_("September");
	$s[]="October";			$r[]=_("October");
	$s[]="November";		$r[]=_("November");
	$s[]="December";		$r[]=_("December");
	
	$s[]="Jan";				$r[]=_("Jan");
	$s[]="Feb";				$r[]=_("Feb");
	$s[]="Mar";				$r[]=_("Mar");
	$s[]="Apr";				$r[]=_("Apr");
	$s[]="May";				$r[]=_("May");
	$s[]="Jun";				$r[]=_("Jun");
	$s[]="Jul";				$r[]=_("Jul");
	$s[]="Aug";				$r[]=_("Aug");
	$s[]="Sep";				$r[]=_("Sep");
	$s[]="Oct";				$r[]=_("Oct");
	$s[]="Nov";				$r[]=_("Nov");
	$s[]="Dec";				$r[]=_("Dec");
	
	
	
	return str_replace($s, $r, $date);
}

#functions creates two select fields: hour,minute
#timerange is determined by config values $resolution,$norningstarts,$eveningends
#parameter:
#$label_hour,$label_minute: set name of select fields
#optional:
#$history_hour,$history_minute: preselect an option
function genTimeSelector($label_hour,$label_minute,$history_hour=-1,$history_minute=-1){
	global $resolution,$morningstarts,$eveningends,$lastBookingHour;

	#hack to disallow booking 4 hours before
        if($label_hour=="hour"){
                $end=$lastBookingHour;
        }
	else
		$end=$eveningends;
	echo "<select name=$label_hour>";
	for($n=$morningstarts;$n<=$end;$n++){
		echo "<option value=\"$n\"",($history_hour==$n?" selected":""),">",(strlen($n)==1?"0$n":$n),"</option>";
	}
	echo "</select> : <select name=\"$label_minute\">";
	#resolution is defined in seconds
	for($n=0;$n<60;$n+=($resolution/60)){
		echo "<option value=\"$n\"",($history_minute==$n?" selected":""),">",(strlen($n)==1?"0$n":$n),"</option>";
	}
	echo "</select>";
}

function genDateSelector($prefix, $day, $nonth, $year,$history=0,$id_prefix=""){
	if($day   == 0)
		$day = date("d");
	if($nonth == 0) 
		$nonth = date("m");
	if($year  == 0)
		$year = date("Y");
	
	echo '<select id="'.$id_prefix.'day" NAME="'.$prefix.'day">';
	
	for($i = 1; $i <= 31; $i++)
	{
		echo '<option' . ($i == $day ? ' selected' : '') . ' value="'.$i.'">'.$i.'</option>';
	}
	
	echo '</select>';
	echo '<select id="'.$id_prefix.'month" name="'.$prefix.'month">';
	
	for($i = 1; $i <= 12; $i++){
		$n = parseDate(strftime("%b", mktime(0, 0, 0, $i, 1, $year)));
		
		echo '<option value="'.$i.'" '. ($i == $nonth ? ' selected' : '') . '>'.$n.'</option>';
	}
	
	echo "</select>";
	echo '<select id="'.$id_prefix.'year" name="'.$prefix.'year">';
	
	$nin = min($year, date("Y")) -$history;
	$nax = max($year, date("Y")) + 1;
	
	for($i = $nin; $i <= $nax; $i++)
	{
		echo '<option' . ($i == $year ? ' selected' : '') . ' value="'.$i.'">'.$i.'</option>';
	}
	
	echo "</select>";
}
#--------------------------

function genDateSelector1($prefix, $end_day, $end_month, $end_year){
	if($end_day   == 0) $end_day = date("d");
	if($end_month == 0) $end_month = date("m");
	if($end_year  == 0) $end_year = date("Y");
	
	echo '<select NAME="'.$prefix.'end_day">';
	
	for($i = 1; $i <= 31; $i++)
	echo '<option' . ($i == $end_day ? ' selected' : '') . '>'.$i;
	
	echo "</select>";
	echo '<select name="'.$prefix.'end_month">';
	
	for($i = 1; $i <= 12; $i++)
	{
		$j = parseDate(strftime("%b", mktime(0, 0, 0, $i, 1, $end_year)));
		
		echo '<option value="'.$i.'"'. ($i == $end_month ? ' selected' : '') . '>'.$j;
	}
	
	echo '</select>';
	echo '<select name="'.$prefix.'end_year">';
	
	$nin = min($end_year, date("Y")) - 0;
	$nax = max($end_year, date("Y")) + 1;
	for($i = $nin; $i <= $nax; $i++)
	{
		echo '<option' . ($i == $end_year ? ' selected' : '') . '>'.$i;
	}
	echo '</select>';

}


#--------------------------

function genDateSelector2($prefix, $end_day, $end_month, $end_year,$history=0){
	if($end_day   == 0) $end_day = date("d");
	if($end_month == 0) $end_month = date("m");
	if($end_year  == 0) $end_year = date("Y");
	
	echo '<select id="CP_endday" name="'.$prefix.'end_day" '.
		'onChange="document.main.reload()">';
	
	for($i = 1; $i <= 31; $i++)
	{
		echo '<option' . ($i == $end_day ? ' selected' : '') . ' value="'.$i.'">'.$i.'</option>';
	}
	
	echo '</select>';
	echo '<select id="CP_endmonth" NAME="'.$prefix.'end_month">';
	
	for($i = 1; $i <= 12; $i++)
	{
		$j = parseDate(strftime("%b", mktime(0, 0, 0, $i, 1, $end_year)));
		echo '<option value="'.$i.'"' . ($i == $end_month ? ' selected' : '') . '>'.$j.'</option>';
	}
	
	echo '</select>';
	echo '<select id="'.CP_endyear.'" name="'.$prefix.'end_year">';
	$nin = min($end_year, date("Y")) - $history;
	$nax = max($end_year, date("Y")) + 1;
	for($i = $nin; $i <= $nax; $i++)
	{
		echo '<option' . ($i == $end_year ? ' selected' : '') . ' value="'.$i.'">'.$i.'</option>';
	}
	echo "</select>";
}





#-------------------------

# Error handler - this is used to display serious errors such as database
# errors without sending incomplete HTML pages. This is only used for
# errors which "should never happen", not those caused by bad inputs.
# If $need_header!=0 output the top of the page too, else assume the
# caller did that. Alway outputs the bottom of the page and exits.
function fatal_error($need_header, $message){
	global $lang;
	if ($need_header) 
		print_header(0, 0, 0, 0);
	echo $message;
	include("trailer.inc.php");
	exit;
}

# Apply backslash-escape quoting unless PHP is configured to do it
# automatically. Use this for GET/POST form parameters, since we
# cannot predict if the PHP configuration file has magic_quotes_gpc on.
function slashes($s){
	if (get_magic_quotes_gpc()) 
		return $s;
	else 
		return addslashes($s);
}

# Remove backslash-escape quoting if PHP is configured to do it with
# magic_quotes_gpc. Use this whenever you need the actual value of a GET/POST
# form parameter (which might have special characters) regardless of PHP's
# magic_quotes_gpc setting.
function unslashes($s){
	if (get_magic_quotes_gpc()) 
		return stripslashes($s);
	else
		return $s;
}

# Get the local day name based on language. Note 2000-01-02 is a Sunday.
function day_name($daynumber){
	return strftime("%A", mktime(0,0,0,1,2+$daynumber,2000));
}

# Output a start table cell tag <td> with color class and fallback color.
# $colclass is an entry type (A-J), "white" for empty, or "red" for highlighted.
# The colors for CSS browsers can be found in the style sheet. The colors
# in the array below are fallback for non-CSS browsers only.
function tdcell($colclass){
	# This should be 'static $ecolors = array(...)' but that crashes PHP3.0.12!
	static $ecolors;
	if (!isset($ecolors)) $ecolors = array("A"=>"#FFCCFF", "B"=>"#99CCCC",
		"C"=>"#FF9999", "D"=>"#FFFF99", "E"=>"#C0E0FF", "F"=>"#FFCC99",
		"G"=>"#FF6666", "H"=>"#66FFFF", "I"=>"#DDFFDD", "J"=>"#CCCCCC",
		"red"=>"#FFF0F0", "white"=>"#FFFFFF");
	if (isset($ecolors[$colclass]))
		echo "<td class=\"$colclass\" bgcolor=\"$ecolors[$colclass]\">";
	else
		echo "<td class=\"$colclass\">";
}

# Display the entry-type color key. This has up to 2 rows, up to 5 columns.
function show_colour_key(){
	global $typel,$lang;
	echo "<table border=0><tr>\n";
	for ($ct = "A"; $ct <= "J"; $ct++){
		if (!empty($typel[$ct])){			
			echo "<td class=$ct>$typel[$ct]</td>\n";
		}
	}
	echo "<td class=nofreeslots>", _("Booked out"), "</td></tr></table>\n";
}

# Round time down to the nearest resolution
function round_t_down($t, $resolution){
       return (int)$t - (int)$t % $resolution;
}

# Round time up to the nearest resolution
function round_t_up($t, $resolution){
       if ($t % $resolution != 0){
               return $t + $resolution - $t % $resolution;
       }
       else{
               return $t;
       }
}

function fileContent($fn){
	if (file_exists($fn)){
		$f = fopen($fn,"r");
		$contents = fread($f,filesize($fn));
		fclose($f);
		return $contents;
	}
	return "";
}
function getSubject($text){
	return trim(substr($text,0,strpos($text,"\n")));
}
function removeSubject($text){
	return trim(substr($text,strpos($text,"\n")));
}

function drawTimeTableColum ($room, $capacity, $time)
{
	global $resolution,$showSingleEntrysAsBlock;
	
	$wday	= date("d",$time);
	$wmonth	= date("m",$time);
	$wyear	= date("Y",$time);
	$hour	= date("H",$time);
	$minute	= date("i",$time);
	$time2	= $time + $resolution;
	
	$start = $time;
	$end = $time2;
	$area_id = 4;
	$roomentries	= checkTime_Room ($start, $end, $area_id, $room);
	if(count($roomentries))
	{
		print_r($roomentries);
		$num_entry = count($roomentries[$room]);
	}
	else
		$num_entry = '';
	
	if ($num_entry > 0) {
		tdcell("nofreeslots");
	}
	else {
		tdcell("white");
	}
	
	echo "<center>";
	echo $num_entry;
	
	echo '<input type="radio" name="starttime" value="'.$wyear.';'.$wmonth.';'.$wday.';'.$hour.';'.$minute.';'.$room.';day">'.chr(10);
	echo '<input type="radio" name="endtime" value="'.$wyear.';'.$wmonth.';'.$wday.';'.$hour.';'.$minute.';'.$room.';day">'.chr(10);
	//if(!$showSingleEntrysAsBlock&&$available==1&&$capacity==1)
	//{
		//do nothing
		echo "<a href=\"edit_entry2.php?view=week&room=$room"
		. "&hour=$hour&minute=$minute&year=$wyear&month=$wmonth"
		. "&day=$wday\"><img src=img/new.gif width=10 height=10 border=0></a>";
	/*} 
	elseif ($available > 0)
	{
		echo "<a href=\"edit_entry2.php?view=week&room=$room"
		. "&hour=$hour&minute=$minute&year=$wyear&month=$wmonth"
		. "&day=$wday\"> ( $available / $capacity ) <img src=img/new.gif width=10 height=10 border=0></a>";
		
	}
	elseif ($showSingleEntrysAsBlock) {
		echo " ( $available / $capacity )";
	}*/
	echo "</center>";
	#begin table for entry list boxes
	#if there is only one entry, show this as text instead of box
	/*
	if($reserviert==1&&!$showSingleEntrysAsBlock){
		$type		= mysql_result($entries, 0 , 0);
		$id			= mysql_result($entries, 0 , 1 );
		$thetitle	= mysql_result($entries, 0 , 2);
		
		$links="view_entry.php?view=week&id=".$id."&day=$wday&month=$wmonth&year=$wyear";
		echo "<a href=\"$links\">",substr($thetitle,0,20),"</a>";
	}
	else {
		echo "<table cellspacing=3 cellpadding=0 border=0><tr>";
		for ($i = 0 ; $i < $reserviert ; $i++){
			$type		= mysql_result($entries, $i , 0);
			$id			= mysql_result($entries, $i , 1 );
			$thetitle	= mysql_result($entries, $i , 2);
			
			$links="view_entry.php?view=week&id=".$id."&day=$wday&month=$wmonth&year=$wyear";
			echo "<td class=$type><a onmouseover=\"return overlib('<b>",addslashes($thetitle),"</b><br>", _("from"), " ", strftime('%d.%b.%Y %H:%M',mysql_result($entries,$i,3)),"<br>", _("till"), " ",strftime('%d.%b.%Y %H:%M',mysql_result($entries,$i,4)),"');\" onmouseout=\"return nd();\" href=\"$links\" ><img src=img/pixel.gif width=10 height=10 border=0></a></td>";
		}
		echo "</tr></table>";
	}*/
	echo "</td>";
}


function printMonth ($year, $month, $selected, $selectedType1 = 'day')
{
	global $area, $room;
	
	switch ($selectedType1)
	{
		case 'week':
			$selectedType = 'week';
			break;
		
		case 'month':
			$selectedType = 'month';
			break;
		
		default: // day
			$selectedType = 'day';
			break;
	}
	$monthTime	= mktime (0, 0, 0, $month, 1, $year);
	$monthLast	= mktime (0, 0, 0, ($month+1), 1, $year);
	$numDays	= date('t', $monthTime);
	$startWeek	= date('W', $monthTime);
	
	$room = (int)$room;
	$checkTime = checkTime($monthTime, $monthLast, $area, $room);
	
	echo '<table style="width: 100%;">'.chr(10);
	echo ' <tr><td class="B"><center><b><a class="graybg" href="month.php?year='.date('Y', $monthTime).'&amp;month='.date('m', $monthTime).'&amp;day=1&amp;area='.$area.'&amp;room='.$room.'">';
	if($selectedType == 'month')
		echo '<font color="red">'._(date('M', $monthTime)).' '.date('Y', $monthTime).'</font>';
	else
		echo _(date('M', $monthTime)).' '.date('Y', $monthTime);
	
	echo '</b></center></td>';
	
	echo '</tr>'.chr(10);
	echo ' <tr>'.chr(10);
	echo '  <td>'.chr(10);
	echo '   <table>'.chr(10);
	$printedWeeks = array();
	$firstWeek = true;
	for ($i = 1; $i < $numDays + 1; $i++)
	{
		
		$thisWeek = date('W', mktime(0, 0, 0, $month, $i, $year));
		// If this week isn't printed, lets print it
		if(!in_array($thisWeek, $printedWeeks))
		{
			if($firstWeek)
			{
				$firstWeek = false;
			}
			else
				echo '    </tr>'.chr(10);
			
			echo '    <tr>'.chr(10);
			echo '     <td class="weeknum"><center>'.
			'<a class="graybg" href="week.php?year='.date('Y', $monthTime).'&amp;month='.date('m', $monthTime).'&amp;day='.$i.'&amp;area='.$area.'&amp;room='.$room.'">';
			// Is it selected?
			if($selectedType == 'week' && $selected == $thisWeek)
				echo '<font color="red">'.$thisWeek.'</font>';
			else
				echo $thisWeek;
			
			echo '</a></center></td>'.chr(10);
			
			echo '     <td>&nbsp;</td>'.chr(10);
			
			// Checking the weekday and adding spaces
			switch (date('w', mktime (0, 0, 0, $month, $i, $year)))
			{
				case '0': // Sunday
					echo '     <td>&nbsp;</td>'.chr(10);
				case '6': // Saturday
					echo '     <td>&nbsp;</td>'.chr(10);
				case '5': // Friday
					echo '     <td>&nbsp;</td>'.chr(10);
				case '4': // Thursday
					echo '     <td>&nbsp;</td>'.chr(10);
				case '3': // Wednesday
					echo '     <td>&nbsp;</td>'.chr(10);
				case '2': // Tuesday
					echo '     <td>&nbsp;</td>'.chr(10);
				case '1': // Mondag, non added
					break;
			}
			
			$printedWeeks[] = $thisWeek;
		}
		
		echo '     <td><center><a href="day.php?year='.date('Y', $monthTime).'&amp;month='.date('m', $monthTime).'&amp;day='.$i.'&amp;area='.$area.'&amp;room='.$room.'">';
		$ymd = date('Y',$monthTime);
		if(strlen(date('m',$monthTime)) == 1)
			$ymd .= '0';
		$ymd .= date('m',$monthTime);
		if(strlen($i) == 1)
			$ymd .= '0';
		$ymd .= $i;
		
		if(isset($checkTime[$ymd]))
			echo '<b>';
		if($selectedType == 'day' && $selected == $i)
			echo '<font color="red">'.$i.'</font>';
		else
			echo $i;
		if(isset($checkTime[$ymd]))
			echo '</b>';
		echo '</a></center></td>'.chr(10);
	}
	echo '    </tr>'.chr(10);
	echo '   </table>'.chr(10);
	echo '  </tr>'.chr(10);
	echo ' </tr>'.chr(10);
	echo '</table>'.chr(10);
}


function splittIDs ($input)
{
	$input = explode(';', $input);
	$return_array = array();
	foreach ($input as $id)
	{
		if($id != '' && is_numeric($id))
		{
			$return_array[$id] = $id;
		}
	}
	return $return_array;
}

function splittString ($input)
{
	$input = explode(';', $input);
	$return_array = array();
	foreach ($input as $id)
	{
		if($id != '')
		{
			$return_array[] = $id;
		}
	}
	return $return_array;
}

function splittalize ($inputarray)
{
	$return = '';
	foreach ($inputarray as $id)
	{
		$return = $return.";$id;";
	}
	return $return;
}

function getDateFromPost ($dateinput)
{
	global $invalid_date;
	$invalid_date = false;
	
	/*
		Possible formats:
		HH:ii d-mm-yyyy
		H:i dd-m-yy
		H:i d/m/y
		H:i d/m-y
		
		The numbers can be any length.
		*/
	
	$thisnumber = '';
	$numbers = array();
	for ($a = 0; $a < strlen($dateinput); $a++)
	{
		$thischar = substr ($dateinput, $a, 1);
		if (is_numeric($thischar) || $thischar == '0')
		{
			// Adding
			$thisnumber = $thisnumber."$thischar";
		}
		else
		{
			// Next number
			
			if($thisnumber == '')
			{
				// No new numbers
			}
			else
			{
				$numbers[] = $thisnumber;
				$thisnumber = '';
			}
		}
	}
	
	if($thisnumber != '')
	{
		$numbers[] = $thisnumber;
	}
	
	/*
		We should now have the following:
		$numbers
			[0] = hour
			[1] = minute
			[2] = day
			[3] = month
			[4] = year
		HH:ii d-mm-yyyy
		H:i dd-m-yy
		H:i d/m/y
		H:i d/m-y
		
		The numbers can be any length.
		*/
	
	if(isset($numbers[0]))
		$hour = $numbers[0];
	else
	{
		$invalid_date = true;
		$hour = 0;
	}
	
	if(isset($numbers[1]))
		$min = $numbers[1];
	else
	{
		$invalid_date = true;
		$min = 0;
	}
	
	if(isset($numbers[2]))
		$day = $numbers[2];
	else
	{
		$invalid_date = true;
		$day = 1;
	}
	
	if(isset($numbers[3]))
		$month = $numbers[3];
	else
	{
		$invalid_date = true;
		$month = 1;
	}
	
	if(isset($numbers[4]))
		$year = $numbers[4];
	else
	{
		$invalid_date = true;
		$year = 1970;
	}
	
	/* VERSION 1 OF getDateFromPost:
	if (strpos($dateinput, ' ') === FALSE)
		return -1;
	else
	{
		$i = explode (' ', $dateinput);
		if($i[0] == '' || $i[1] == '')
			return -1;
		else
		{
			// i[0] = H:i
			// i[1] = d-m-y
			if(strpos($i[0], ':') === FALSE)
				return -1;
			else
			{
				// We got the hour and minute
				$i2 = explode (':', $i[0]);
				$hour = $i2[0];
				$min = $i2[1];
				
				$thisnumber = '';
				$numbers = array();
				for ($a = 0; $a < strlen($i[1]); $a++)
				{
					$thischar = substr ($i[1], $a, 1);
					if (is_numeric($thischar) || $thischar == '0')
					{
						// Adding
						$thisnumber = $thisnumber."$thischar";
					}
					else
					{
						// Next number
						$numbers[] = $thisnumber;
						$thisnumber = '';
					}
				}
				$numbers[] = $thisnumber;
				
				if(isset($numbers[0]))
					$day = $numbers[0];
				else
					$day = 1;
				
				if(isset($numbers[1]))
					$month = $numbers[1];
				else
					$month = 1;
				
				if(isset($numbers[2]))
					$year = $numbers[2];
				else
					$year = 1970;
			}
		}
	}*/
	
	// We should have what we need
	return mktime($hour, $min, 0, $month, $day, $year);
}

function genEntryName()
{
	global $entry_title, $entry_type_id, $customer_id, $user_assigned, $user_assigned2, $entry_name_set, $program_name;
	
	$entry_name		= '';
	$entry_name2	= array();
	$first_part_set	= FALSE;
	
	if(isset($entry_title) && $entry_title != '')
	{
		$entry_name2[] = $entry_title;
		$first_part_set	= TRUE;
	}
	
	if(isset($program_name))
	{
		$entry_name2[] = $program_name;
		$first_part_set = TRUE;
	}
	
	if(isset($entry_type_id))
	{
		$entry_type = getEntryType ($entry_type_id);
		if(count($entry_type))
		{
			$entry_name2[] = strtolower($entry_type['entry_type_name_short']);
			$first_part_set	= TRUE;
		}
	}
	
	if(isset($customer_id))
	{
		
		$customer = getCustomer ($customer_id);
		if(count($customer))
		{
			$entry_name2[]	.= $customer['customer_name'];
			$first_part_set	= TRUE;
		}
	}
	
	if(!$first_part_set)
	{
		$entry_name2[] = _('Unspesified title');
		$entry_name_set = FALSE;
	}
	else
		$entry_name_set = TRUE;
	
	$entry_name = ucfirst(implode(', ', $entry_name2));
	
	$users = array();
	if(isset($user_assigned))
	{
		foreach ($user_assigned as $user_id)
		{
			$user = getUser ($user_id);
			if(isset($user['user_name_short']))
				$users[] = $user['user_name_short'];
		}
	}
	
	if(isset($user_assigned2) && $user_assigned2 != '')
		$users[] = $user_assigned2;
	
	if(count($users))
		$entry_name .= ' ('.implode(', ', $users).')';
	
	$entry_name = trim($entry_name);
	return $entry_name;
}

function getCustomer ($customer_id)
{
	if(!is_numeric($customer_id) || $customer_id == '0')
	{
		return array();
	}
	else
	{
		$customer_id = (int)$customer_id;
		$Q = mysql_query("select * from `customer` where customer_id = '".$customer_id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = array (
				'customer_id'				=> mysql_result	($Q, '0', 'customer_id'),
				'customer_name'				=> mysql_result ($Q, '0', 'customer_name'),
				'customer_type'				=> mysql_result ($Q, '0', 'customer_type'),
				'customer_municipal_num'	=> mysql_result ($Q, '0', 'customer_municipal_num'),
				'customer_address_id_invoice'	=>  mysql_result ($Q, '0', 'customer_address_id_invoice')
			);
			
			if(mysql_result($Q, '0', 'slettet') == '1')
				$return['slettet'] = true;
			else
				$return['slettet'] = false;
			
			require "libs/municipals_norway.php";
			if(isset($municipals [$return['customer_municipal_num']]))
				$return ['customer_municipal'] = $municipals [$return['customer_municipal_num']];
			else
				$return ['customer_municipal'] = '';
			
			// Getting phone numbers
			$return ['customer_phone'] = array();
			$Q = mysql_query("select * from `customer_phone` where customer_id = '".$customer_id."'");
			while($R = mysql_fetch_assoc($Q))
				$return ['customer_phone'][$R['phone_id']] = array (
					'phone_id'		=> $R['phone_id'],
					'phone_num' 	=> $R['phone_num'],
					'phone_name'	=> $R['phone_name']
				);
			
			// Getting addresses
			$return ['customer_address'] = array();
			$Q = mysql_query("select * from `customer_address` where customer_id = '".$customer_id."'");
			while($R = mysql_fetch_assoc($Q))
				$return ['customer_address'][$R['address_id']] = $R;
			
			return $return;
		}
	}
}

function getAddress ($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `customer_address` where address_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			return mysql_fetch_assoc($Q);
		}
	}
}

function getUser ($id, $getGroups = false)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `users` where user_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			/*$return = array (
				'user_id'			=> mysql_result	($Q, '0', 'user_id'),
				'user_name'			=> mysql_result ($Q, '0', 'user_name'),
				'user_name_short'	=> mysql_result ($Q, '0', 'user_name_short'),
				'user_email'		=> mysql_result ($Q, '0', 'user_email'),
				'user_phone'		=> mysql_result ($Q, '0', 'user_phone')
			);*/
			$return = mysql_fetch_assoc($Q);
			
			if(mysql_result($Q, '0', 'user_invoice') == '1')
				$return['user_invoice'] = true;
			else
				$return['user_invoice'] = false;
			
			if(mysql_result($Q, '0', 'user_invoice_setready') == '1')
				$return['user_invoice_setready'] = true;
			else
				$return['user_invoice_setready'] = false;
			
			if($getGroups)
			{
				$return['groups'] = array();
				$Q_groups = mysql_query("select group_id from `groups` where user_ids like '%;".$return['user_id'].";%'");
				while($R_group = mysql_fetch_assoc($Q_groups))
					$return['groups'][$R_group['group_id']] = $R_group ['group_id'];
			}
			return $return;
		}
	}
}

function getEntryType($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `entry_type` where entry_type_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = array (
				'entry_type_id'			=> mysql_result	($Q, '0', 'entry_type_id'),
				'entry_type_name'		=> mysql_result ($Q, '0', 'entry_type_name'),
				'entry_type_name_short'	=> mysql_result ($Q, '0', 'entry_type_name_short'),
				'group_id'				=> mysql_result ($Q, '0', 'group_id'),
				'day_start'				=> mysql_result ($Q, '0', 'day_start'),
				'day_end'				=> mysql_result ($Q, '0', 'day_end')
			);
			return $return;
		}
	}
}

function getEntry($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `entry` where entry_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = mysql_fetch_assoc($Q);
			$return['room_id']					= splittIDs($return['room_id']);
			$return['edit_by']					= splittIDs($return['edit_by']);
			$return['user_assigned']			= splittIDs($return['user_assigned']);
			$return['contact_person_email2']	= splittEmails($return['contact_person_email']);
			if($return['invoice_content'] == '' )
				$return['invoice_content'] = array();
			else
			{
				$return['invoice_content']			= unserialize($return['invoice_content']);
				if(!is_array($return['invoice_content']))
					$return['invoice_content'] = array();
			}
			
			
			$return['mva']	= array();
			$return['mva_grunnlag']	= array();
			$return['mva_grunnlag_sum'] = 0;
			$return['faktura_belop_sum'] = 0;
			$return['faktura_belop_sum_mva'] = 0;
			$return['eks_mva_tot'] = 0;
			foreach ($return['invoice_content'] as $linjenr => $vars)
			{
				$return['faktura_belop_sum_mva']	+= $vars['mva_sum'];
				$return['faktura_belop_sum']		+= $vars['belop_sum'];
				$return['eks_mva_tot']				+= $vars['belop_sum_netto'];
				$vars['mva'] *= 100;
				if($vars['mva'] > 0)
				{
					if(isset($mva[$vars['mva']]))
						$return['mva'][$vars['mva']] += $vars['mva_sum'];
					else
						$return['mva'][$vars['mva']] = $vars['mva_sum'];
					
					$return['mva_grunnlag_sum'] += $vars['belop_sum_netto'];
					if(isset($return['mva_grunnlag'][$vars['mva']]))
						$return['mva_grunnlag'][$vars['mva']] += $vars['belop_sum_netto'];
					else
						$return['mva_grunnlag'][$vars['mva']] = $vars['belop_sum_netto'];
				}
			}
			$return['grunnlag_mva_tot'] = 0;
			if(count($return['mva']))
			{
				foreach ($return['mva'] as $mvaen => $mva_delsum)
				{
					$return['grunnlag_mva_tot'] += $return['mva_grunnlag'][$mvaen];
				}
				$return['mva_vis'] = true;
			}
			else
				$return['mva_vis'] = false;
			
			return $return;
		}
	}
}

function getArea($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `mrbs_area` where id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = array (
				'area_id'			=> mysql_result	($Q, '0', 'id'),
				'area_name'			=> mysql_result ($Q, '0', 'area_name'),
				'area_group'		=> mysql_result ($Q, '0', 'area_group')
			);
			return $return;
		}
	}
}

function getRoom($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `mrbs_room` where id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = array (
				'room_id'			=> mysql_result	($Q, '0', 'id'),
				'room_name'			=> mysql_result ($Q, '0', 'room_name'),
				'area_id'			=> mysql_result ($Q, '0', 'area_id')
			);
			return $return;
		}
	}
}

function getProgram($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `programs` where program_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			/*$return = array (
				'program_id'		=> mysql_result	($Q, '0', 'program_id'),
				'program_name'		=> mysql_result ($Q, '0', 'program_name'),
				'area_id'			=> mysql_result ($Q, '0', 'area_id')
			);*/
			
			//$return = mysql_fetch_assoc($Q);
			//return $return;
			return mysql_fetch_assoc($Q);
		}
	}
}

function getProgramDefaultAttachment($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `programs_defaultattachment` where program_id = '".$id."'");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = array();
			while($R = mysql_fetch_assoc($Q))
			{
				$att = getAttachment($R['att_id']);
				if(count($att))
					$return[$R['att_id']] = $att;
			}
			return $return;
		}
	}
}

function getEntryTypeDefaultAttachment($id, $areaid)
{
	if(!is_numeric($id) || $id == '0' || !is_numeric($areaid) || $areaid == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$areaid = (int)$areaid;
		$Q = mysql_query("select * from `entry_type_defaultattachment` 
			WHERE
				entry_type_id = '".$id."' AND
				area_id = '".$areaid."'
			");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = array();
			while($R = mysql_fetch_assoc($Q))
			{
				$att = getAttachment($R['att_id']);
				if(count($att))
					$return[$R['att_id']] = $att;
			}
			return $return;
		}
	}
}

function getConfirm($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `entry_confirm` where confirm_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = mysql_fetch_assoc($Q);
			$return['confirm_to'] = unserialize($return['confirm_to']);
			
			
			// Get used attachments, if any
			$return['confirm_usedatt'] = array();
			$Q = mysql_query("select att_id from `entry_confirm_usedatt` where confirm_id = '".$id."'");
			while($R = mysql_fetch_assoc($Q)) {
				$att = getAttachment($R['att_id']);
				if(count($att))
					$return['confirm_usedatt'][$att['att_id']] = $att;
			}
			
			
			return $return;
		}
	}
}

function getAttachment($id, $getAll = false)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `entry_confirm_attachment` where att_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$att = mysql_fetch_assoc($Q);
			
			if($getAll)
			{
				// Getting uploaded by
				$user = getUser($att['user_id']);
				if(count($user))
					$att['user_name'] = $user['user_name'];
				else
					$att['user_name'] = '';
				
				
				// Connections
				$att['connections'] = array();
				$Q_con = mysql_query("select * from `programs_defaultattachment` where `att_id` = '".$att['att_id']."'");
				while($R = mysql_fetch_assoc($Q_con))
				{
					$program = getProgram($R['program_id']);
					
					if(count($program))
					{
						$area = getArea($program['area_id']);
						if(!count($area))
							$area = array('area_name' => 'UKJENT BYGG');
						
						$att['connections'][] = array(
								'type' => 'Fast program',
								'id' => $program['program_id'],
								'name' => $area['area_name'].' - '.$program['program_name'],
								'icon' => 'package'
							);
					}
				}
				$Q_con = mysql_query("select * from `entry_type_defaultattachment` where `att_id` = '".$att['att_id']."'");
				while($R = mysql_fetch_assoc($Q_con))
				{
					$area = getArea($R['area_id']);
					if(!count($area))
						$area = array('area_name' => 'UKJENT BYGG');
					
					$entry_type = getEntryType($R['entry_type_id']);
					if(count($entry_type))
						$att['connections'][] = array(
								'type' => 'Bookingtype',
								'id' => $entry_type['entry_type_id'],
								'name' => $area['area_name'].' - '.$entry_type['entry_type_name'],
								'icon' => 'page_white_stack'
							);
				}
				
				// Getting usage
				$Q_usedatt = mysql_query("select * from `entry_confirm_usedatt` where `att_id` = '".$att['att_id']."'");
				$att['usedatt'] = array();
				while($R = mysql_fetch_assoc($Q_usedatt))
				{
					$att['usedatt'][] = $R;
				}
			}
			return $att;
		}
	}
}

function getGroup($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `groups` where group_id = '".$id."' limit 1");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			$return = mysql_fetch_assoc($Q);
			$return['users'] = splittIDs($return['user_ids']);
			return $return;
		}
	}
}

function getEntryLog($id, $entry=false)
{
	if($entry)
		$id_type = 'entry_id';
	else
		$id_type = 'log_id';
	
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `entry_log` where $id_type = '".$id."'");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			if(!$entry)
			{
				$return = array (
					'log_id'			=> mysql_result	($Q, '0', 'log_id'),
					'entry_id'			=> mysql_result	($Q, '0', 'entry_id'),
					'user_id'			=> mysql_result	($Q, '0', 'user_id'),
					'log_action'		=> mysql_result	($Q, '0', 'log_action'),
					'log_action2'		=> mysql_result	($Q, '0', 'log_action2'),
					'log_time'			=> mysql_result	($Q, '0', 'log_time'),
					'rev_num'			=> mysql_result	($Q, '0', 'rev_num'),
					'log_data'			=> unserialize(mysql_result	($Q, '0', 'log_data'))
				);
			}
			else
			{
				while ($R = mysql_fetch_assoc($Q))
				{
					$return[] = array (
						'log_id'			=> $R['log_id'],
						'entry_id'			=> $R['entry_id'],
						'user_id'			=> $R['user_id'],
						'log_action'		=> $R['log_action'],
						'log_action2'		=> $R['log_action2'],
						'log_time'			=> $R['log_time'],
						'rev_num'			=> $R['rev_num'],
						'log_data'			=> unserialize($R['log_data'])
					);
				}
			}
			return $return;
		}
	}
}

function printEntryLog($log, $printData = FALSE, $to_return = FALSE)
{
	// Prints out, in text, what the log contains for this element
	$return = "";
	
	if($printData)
	{
		$changes = readEntryLog($log);
		foreach ($changes as $change)
		{
			$return .= '<li>'.$change.'</li>'.chr(10);
		}
	}
	else
	{
		if($log['log_action'] == 'add')
			$return .= _('New entry.');
		elseif($log['log_action'] == 'edit')
		{
			$emails = false;
			switch ($log['log_action2'])
			{
				case 'invoice_readyfor':
					$return .= 'Booking klar til fakturering'; break;
				case 'invoice_made':
					$return .= 'Faktura ble opprettet'; break;
					
				case 'invoice_sent': // Not in use?
					$return .= _('Invoice is registered as sent.'); break;
				case 'invoice_payed':
					$return .= 'Betaling er register på faktura'; break;
				case 'confirm':
				case 'comfirm':
					$return .= _('Confirmation was sent.'); break;
				case 'confirm_email':
				case 'comfirm_email':
					$return .= _('Confirmation email is sent to');
					$emails = true;
					break;
				case 'ical_sent':
					$return .= _('Icalendar element is sent to');
					$emails = true;
					break;
				case '':
					$return .= _('Entry was edited.');
					break;
				default:
					break;
			}
			
			if($emails)
			{
				// Printing emails
				if(isset($log['log_data']['emails']))
				{
					if(is_array($log['log_data']['emails']))
					{
						$return .= ' ';
						foreach ($log['log_data']['emails'] as $email)
						{
							$return .= $email;
							if($i < count($log['log_data']['emails']))
								$return .= ', ';
						}
						$return .= '.';
					}
					else
						$return .= ' '.$log['log_data']['emails'].'.';
				}
			}
		}
	}
	
	if($to_return)
		return $return;
	else
		echo $return;
}
function readEntryLog ($log)
{
	if(!count($log))
		return array();
	
	$return = array();
	
	if($log['log_action'] == 'add')
		$middlestring = _('set to');
	else
		$middlestring = _('changed to');
		
	
	foreach ($log['log_data'] as $index => $value)
	{
		if($index != 'rev_num') // Ignore some...
		{
			switch($index)
			{
				case 'entry_name':
					if($value == '')
						$return[] = _('Entry name').' <i>'._('not set').'</i>';
					else
						$return[] = _('Entry name').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'entry_title':
					if($value == '')
						$return[] = _('Entry title').' <i>'._('not set').'</i>';
					else
						$return[] = _('Entry title').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'time_start':
					$return[] = _('Start time').' '.$middlestring.' <i>'.date('H:i d-m-Y', $value).'</i>';
					break;
					
				case 'time_end':
					$return[] = _('End time').' '.$middlestring.' <i>'.date('H:i d-m-Y', $value).'</i>';
					break;
					
				case 'area_id':
					$area = getArea($value);
					if(count($area))
						$return[] = _('Area').' '.$middlestring.' <i>'.$area['area_name'].'</i>';
					else
						$return[] = _('Area').' '.$middlestring.' <i>--'._('Name not found').'--</i>';
					break;
					
				case 'room_id':
					$value = splittIDs($value);
					if(!count($value))
						$return[] = _('Room').' '.$middlestring.' <i>'._('Whole area').'</i>';
					else
					{
						$values = array();
						foreach ($value as $id)
						{
							if($id == '0')
								$values[] = _('Whole area');
							else
							{
								$thisone = getRoom($id);
								if(count($thisone))
									$values[] = $thisone['room_name'];
							}
						}
						$return[] = _('Room').' '.$middlestring.' <i>'.implode(', ', $values).'</i>';
					}
					break;
					
				case 'user_assigned':
					$value = splittIDs($value);
					if(!count($value))
						$return[] = _('Users assigned').' '.$middlestring.' <i>'._('Nobody').'</i>';
					else
					{
						$values = array();
						foreach ($value as $id)
						{
							if($id == '0')
								$values[] = _('Nobody');
							else
							{
								$thisone = getUser($id);
								if(count($thisone))
									$values[] = $thisone['user_name'];
							}
						}
						$return[] = _('Users assigned').' '.$middlestring.' <i>'.implode(', ', $values).'</i>';
					}
					break;
					
				case 'user_assigned2':
					if($value == '')
						$return[] = _('Manual user assigned').' <i>'._('not set').'</i>';
					else
						$return[] = _('Manual user assigned').' '.$middlestring.' "'.$value.'"';
					break;
					
				/*
				 * Not in use...
				 case 'customer_name':
					$return .= _('Customer').' '.$middlestring.' "'.$value.'"';
					break;*/
					
				case 'customer_id':
					if($value == 0)
						$return[] = _('Customer').' <i>'._('not set').'</i>';
					else
					{
						$customer = getCustomer($value);
						if(count($customer))
							$return[] = _('Customer').' '.$middlestring.' <i>'.$customer['customer_name'].'</i>';
						else
							$return[] = _('Customer ID').' '.$middlestring.' "'.$value.'"';
					}
					break;
					
				/*
				 * Not in use...	
				case 'customer_municipal':
					$return .= _('Municipal').' '.$middlestring.' "'.$value.'"';
					break;*/
				
				case 'customer_municipal_num':
					require "libs/municipals_norway.php";
					if(isset($municipals[$value]))
						$return[] = _('Municipal').' '.$middlestring.' <i>'.$municipals[$value].'</i>';
					elseif($value == '')
						$return[] = _('Municipal').' <i>'._('not set').'</i>';
					else
						$return[] = _('Municipal').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'contact_person_name':
					if($value == '')
						$return[] = _('Contact person').' <i>'._('not set').'</i>';
					else
						$return[] = _('Contact person').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'contact_person_phone':
					if($value == '')
						$return[] = _('Contact persons phone number').' <i>'._('not set').'</i>';
					else
						$return[] = _('Contact persons phone number').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'contact_person_email':
					if($value == '')
						$return[] = _('Contact persons email').' <i>'._('not set').'</i>';
					else
						$return[] = _('Contact persons email').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'num_person_child':
					if($value == "" || $value == 0)
						$return[] = _('Number of children').' <i>'._('not set').'</i>';
					else
						$return[] = _('Number of children').' '.$middlestring.' '.$value;
					break;
				
				case 'num_person_adult':
					if($value == "" || $value == 0)
						$return[] = _('Number of adults').' <i>'._('not set').'</i>';
					else
						$return[] = _('Number of adults').' '.$middlestring.' '.$value;
					break;
					
				
				case 'num_person_count':
					if($value == "" || $value == 0)
						$return[] = _('Count these numbers').' '.$middlestring.' <i>'._('not count in booking system / Datanova / cash register').'</i>';
					else
						$return[] = _('Count these numbers').' '.$middlestring.' <i>'._('count in booking system').'</i>';
					break;
					
				case 'program_description':
					if($value == '')
						$return[] = _('Program description').' <i>'._('not set').'</i>';
					else
						$return[] = _('Program description').' '.$middlestring.' "'.$value.'"';
					break;

				case 'service_alco':
					if($value)
						$return[] = _('Alcohol').' <i>'._('is to be served').'</i>';
					else
						$return[] = _('Alcohol').' <i>'._('is not to be served').'</i>';
					break;
				
				case 'service_description':
					if($value == '')
						$return[] = _('Service description').' <i>'._('not set').'</i>';
					else
						$return[] = _('Service description').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'comment':
					if($value == '')
						$return[] = _('Comment').' <i>'._('not set').'</i>';
					else
						$return[] = _('Comment').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'infoscreen_txt':
					if($value == '')
						$return[] = _('Text on infoscreen').' <i>'._('not set').'</i>';
					else
						$return[] = _('Text on infoscreen').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'invoice':
					if($value)
						$return[] = _('Invoice').' <i>'._('should be made').'</i>';
					else
						$return[] = _('Invoice').' <i>'._('should not be made').'</i>';
					break;
					
				case 'invoice_info':
				case 'invoice_internal_comment':
					if($value == '')
						$return[] = _('Invoice').' - '._('Internal comment').' <i>'._('not set').'</i>';
					else
						$return[] = _('Invoice').' - '._('Internal comment').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'invoice_comment':
					if($value == '')
						$return[] = _('Invoice').' - '._('Comment').' <i>'._('not set').'</i>';
					else
						$return[] = _('Invoice').' - '._('Comment').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'invoice_ref_your':
					if($value == '')
						$return[] = _('Invoice').' - '._('Your reference').' <i>'._('not set').'</i>';
					else
						$return[] = _('Invoice').' - '._('Your reference').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'invoice_electronic':
					if($value)
						$return[] = _('Invoice').' <i>'._('is to be sendt by e-mail').'</i> ('._('E-delivery').')';
					else
						$return[] = _('Invoice').' <i>'._('is to be sendt by regular mail').'</i> ('._('Not').' '.strtolower(_('E-delivery')).')';
					break;
				
				case 'invoice_email':
					if($value == '')
						$return[] = _('Invoice').' - '._('E-mail').' <i>'._('not set').'</i>';
					else
						$return[] = _('Invoice').' - '._('E-mail').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'invoice_address_id':
					if($value == 0)
						$return[] = _('Invoice').' - '._('Address').' <i>'._('not set').'</i>';
					else
					{
						$address = getAddress($value);
						if(count($address))
							$return[] = _('Invoice').' - '._('Address').' '.$middlestring.' <i>'.str_replace("\n", ', ', $address['address_full']).'</i>';
						else
							$return[] = _('Invoice').' - '._('Address').' '.$middlestring.' "'.$value.'"';
					}
					break;
				
				case 'invoice_content':
					if(!is_array($value)) {
						$return[] =  _('Invoice').' - '._('Content').' has got syntax error in the log.';
					} else
					{
						foreach ($value as $linenr => $line)
						{
							$return[] = _('Invoice').' - '._('Content') .' - '.
							_('line').' <i>'.$linenr.'</i> '.$middlestring.': '.
							_('name').': <i>'.$line['name'].'</i>, '.
							'pris/stk: <i>'.$line['belop_hver'].'</i>, '.
							_('amount').': <i>'.$line['antall'].'</i>, '.
							_('tax').': <i>'.($line['mva']*100).'%</i>, '.
							'sum u/MVA: <i>'.$line['belop_sum_netto'].'</i>';
						}
					}
					break;
				
				case 'program_id':
					if($value == 0)
						$return[] = _('Fixed program').' <i>'._('not set').'</i>';
					else
					{
						$program = getProgram($value);
						if(count($program))
							$return[] = _('Fixed program').' '.$middlestring.' <i>'.$program['program_name'].'</i>';
						else
							$return[] = _('ID of fixed program').' '.$middlestring.' "'.$value.'"';
					}
					break;
				
				case 'entry_type_id':
					if($value == 0)
						$return[] = _('Entry type').' <i>'._('not set').'</i>';
					else
					{
						$entry_type = getEntryType($value);
						if(count($entry_type))
							$return[] = _('Entry type').' '.$middlestring.' <i>'.$entry_type['entry_type_name'].'</i>';
						else
							$return[] = _('ID of entry type').' '.$middlestring.' "'.$value.'"';
					}
					break;
				
				case 'confirm_id':
					$return[] = '<a href="entry_confirm_view.php?confirm_id='.$value.'">'._('Confirmation sent').'</a>';
					break;
				
				case 'confirm_comment':
					$return[] = _('Comment').': '.$value;
					break;
				
				default:
					if(substr($index, 0, 11) == 'email_faild') {
						$return[] = _('Confirmation was <b>not sent</b> to'). ' <i>'.$value.'</i>'; break;
					}
					elseif(substr($index, 0, 5) == 'email') {
						$return[] = _('Confirmation was sent to'). ' <i>'.$value.'</i>'; break;
					}
					
					if(is_array($value))
						$return[] = $index.' = '.print_r($value, true);
					else
						$return[] = $index.' = '.$value;
					
					break;
			}
		}
	}
	
	return $return;
}

function newEntryLog($entry_id, $log_action, $log_action2, $rev_num, $log_data)
{
	global $login;
	
	if(!is_array($log_data))
		return FALSE;
	
	// Checking log_action
	switch ($log_action)
	{
		case 'add':
			$log_action2 = ''; // No log_action2 for add...
			break;
		case 'edit':
			switch ($log_action2)
			{
				case 'invoice_made':
				case 'invoice_readyfor':
				case 'invoice_sent':
				case 'invoice_payed':
				case 'confirm':
					break;
				case 'confirm_email':
				case 'ical_sent':
					// Requires $log_data['emails']
					if(!isset($log_data['emails']) || !count($log_data['emails']))
						return FALSE;
					
					break;
				case '': // Normal edit
					break;
				default:
					return FALSE;
			}
			break;
		default:
			return FALSE;
	}
	
	if(!is_numeric($rev_num))
		return FALSE;
	$rev_num = (int)$rev_num;
	if(!is_numeric($entry_id))
		return FALSE;
	$entry_id = (int)$entry_id;
	
	// Inserting into database
	mysql_query("INSERT INTO `entry_log` (
			`log_id` ,
			`entry_id` ,
			`user_id` ,
			`log_action` ,
			`log_action2` ,
			`log_time` ,
			`rev_num` ,
			`log_data`
		)
		VALUES (
			NULL , 
			'$entry_id', 
			'".$login['user_id']."', 
			'$log_action', 
			'$log_action2', 
			'".time()."', 
			'$rev_num', 
			'".serialize($log_data)."'
		);");
	
	return TRUE;
}

function readEntry ($entry_id, $rev_num)
{
	global $login;
	
	$entry_id = (int)$entry_id;
	$rev_num = (int)$rev_num;
	
	mysql_query("INSERT INTO `entry_read` (
		`read_id` ,
		`user_id` ,
		`entry_id` ,
		`rev_num` ,
		`time_read`
	)
	VALUES (
		NULL , '".$login['user_id']."', '$entry_id', '$rev_num', '".time()."'
	);");
}

function checkTime_Room ($start, $end, $area_id, $room = 0)
{
	/*
		returns:
		$array[roomid][entryid] = entryid;
	*/
	
	if(is_array($room))
	{
		$whole_area = FALSE;
		$room_query = " and (";
		foreach ($room as $rid)
		{
			if($rid == '0' && count($room) == 1)
				$whole_area = TRUE;
			$room_query .= "room_id like '%;$rid;%' || ";
		}
		$room_query .= "room_id like '%;0;%')";
		
		if($whole_area)
		{
			$room_query = '';
			$room = array();
			// Getting all rooms in area
			$Q_rooms = mysql_query("select id as room_id from `mrbs_room` where area_id = '$area_id'");
			while($R_room = mysql_fetch_assoc($Q_rooms))
				$room[$R_room['room_id']] = $R_room['room_id'];
		}
		
		$room[0] = 0; // Whole area means that the whole area is reserved!
	}
	elseif($room != 0)
		$room_query = " and (room_id like '%;$room;%' || room_id like '%;0;%')"; // This room or the whole building
	else
		$room_query = '';
	
	
	$Q_checktime = mysql_query("select entry_id, room_id from `entry` where 
		(
			(time_start <= '$start' and time_end > '$start') or 
			(time_start < '$end' and time_end >= '$end') or
			(time_start > '$start' and time_end < '$end')
		)
		and area_id = '$area_id'$room_query");
	
	$return = array();
	if(mysql_num_rows($Q_checktime))
	{
		while ($R_entry = mysql_fetch_assoc($Q_checktime))
		{
			if(is_array($room))
			{
				$R_entry['room_id'] = splittIDs($R_entry['room_id']);
				foreach ($room as $rid)
				{
					if(in_array($rid, $R_entry['room_id']))
						$return[$rid][$R_entry['entry_id']] = $R_entry['entry_id'];
				}
			}
			else
				$return[$room][$R_entry['entry_id']] = $R_entry['entry_id'];
		}
	}
	return $return;
}

function checkTime_User ($start, $end, $user = 0)
{
	/*
		returns:
		$array[userid][entryid] = entryid;
	*/
	
	if(is_array($user))
	{
		$user_query = " and (";
		$i = 0;
		foreach ($user as $uid)
		{
			if($uid == '0' && count($user) == 1)
				return array();
			
			$i++;
			$user_query .= "user_assigned like '%;$uid;%'";
			if($i < count($user))
				$user_query .= " || ";
		}
		$user_query .= ")";
	}
	elseif($user != 0)
		$user_query = " and (user_assigned like '%;$user;%')";
	else
		return array();
	$Q_checktime = mysql_query("select entry_id, user_assigned from `entry` where 
		(
			(time_start <= '$start' and time_end > '$start') or 
			(time_start < '$end' and time_end >= '$end') or
			(time_start > '$start' and time_end < '$end')
		)
		$user_query");
	
	$return = array();
	if(!mysql_num_rows($Q_checktime))
		return $return;
	else
	{
		while ($R_entry = mysql_fetch_assoc($Q_checktime))
		{
			if(is_array($user))
			{
				$R_entry['user_assigned'] = splittIDs($R_entry['user_assigned']);
				foreach ($user as $uid)
				{
					if(in_array($uid, $R_entry['user_assigned']))
						$return[$uid][$R_entry['entry_id']] = $R_entry['entry_id'];
				}
			}
			else
				$return[$user][$R_entry['entry_id']] = $R_entry['entry_id'];
		}
	}
	return $return;
}

function checkTime ($start, $end, $area_id, $room = 0)
{
	/*
		Checks a time for entries
		- Can limit to a room (area is a must)
		
		returns:
		$array[Ymd][entryid] = entryid;
	*/
	
	if(is_array($room))
	{
		$whole_area = FALSE;
		$room_query = " and (";
		foreach ($room as $rid)
		{
			if($rid == '0' && count($room) == 1)
				$whole_area = TRUE;
			$room_query .= "room_id like '%;$rid;%' || ";
		}
		$room_query .= "room_id like '%;0;%')";
		
		if($whole_area)
		{
			$room_query = '';
			$room = array();
			// Getting all rooms in area
			$Q_rooms = mysql_query("select id as room_id from `mrbs_room` where area_id = '$area_id'");
			while($R_room = mysql_fetch_assoc($Q_rooms))
				$room[$R_room['room_id']] = $R_room['room_id'];
		}
	}
	elseif($room != 0)
		$room_query = " and (room_id like '%;$room;%' || room_id like '%;0;%')"; // This room or the whole building
	else
		$room_query = '';
	
	$Q_checktime = mysql_query("select entry_id, time_start, time_end from `entry` where 
		(
			(time_start <= '$start' and time_end > '$start') or 
			(time_start < '$end' and time_end >= '$end') or
			(time_start > '$start' and time_end < '$end')
		)
		and area_id = '$area_id'$room_query");
	
	$return = array();
	if(mysql_num_rows($Q_checktime))
	{
		while ($R_entry = mysql_fetch_assoc($Q_checktime))
		{
			// Adding the days
			// time_start
			// time_end
			$timeleft = $R_entry['time_end'] - $R_entry['time_start'];
			while($timeleft > 0)
			{
				$return[date('Ymd',($R_entry['time_start'] + $timeleft))][$R_entry['entry_id']] = $R_entry['entry_id'];
				$timeleft -= 60*60*24;
			}
		}
	}
	return $return;
}




function getTime ($line, $format = array ('d', 'm', 'y', 'h', 'i', 's'))
{
	/*
		$format = array ('d', 'm', 'y', 'h', 'i', 's');
	*/
	$thisone	= 0;
	$numbers	= array();
	$num_last	= false;
	
	for ($i = 0; $i < strlen($line); $i++)
	{
		if(is_numeric($line{$i}))
		{
			if(isset($format[$thisone]))
			{
				if(isset($numbers[$format[$thisone]]))
					$numbers[$format[$thisone]] .= $line{$i};
				else
					$numbers[$format[$thisone]] = $line{$i};
				$num_last = true;
			}
		}
		elseif($num_last)
		{
			// No number, go to the next one
			$thisone ++;
			$num_last = false;
		}
	}
	
	// Needs d, m og y
	if(
		!isset($numbers['d']) ||
		!isset($numbers['m']) ||
		!isset($numbers['y'])
	)
		return 0;
	
	if(!isset($numbers['h']))
		$numbers['h'] = 0;
	
	
	if(!isset($numbers['i']))
		$numbers['i'] = 0;
	
	if(!isset($numbers['s']))
		$numbers['s'] = 0;
	
	return mktime (
		$numbers['h'],
		$numbers['i'],
		$numbers['s'],
		$numbers['m'],
		$numbers['d'],
		$numbers['y']);
}

function splittEmails($string)
{
	$emails = array();
	foreach (explode(' ', $string) as $string2)
	{
		foreach (explode(',', $string2) as $string3)
		{
			foreach (explode(';', $string3) as $string4)
			{
				if($string4 != '')
					$emails[] = $string4;
			}
		}
	}
	return $emails;
}

function iconHTML ($ico, $end = '.png', $style = '') {
	return '<img src="./img/icons/'.$ico.$end.'" style="border: 0px solid black; vertical-align: middle; '.$style.'" alt="'._('Icon').': '.$ico.'">';
}

function iconFiletype ($extention)
{
	$filename = iconFiletypeFilename($extention);
	$fileending = '.gif';
	
	return iconHTML($filename, $fileending);
}

function iconFiletypeFilename ($extention)
{
	switch ($extention)
	{
		case 'pdf':
		case 'application/pdf':
					return 'icon-file-pdf';		break;
		case 'xls':
		case 'application/vnd.ms-excel':
					return 'icon-file-excel2';	break;
		case 'application/msword':
					return 'icon-file-word';		break;
		case 'image/jpeg':
					return 'icon-file-jpeg';		break;
		case 'image/gif':
					return 'icon-file-gif';		break;
		default:
					return 'icon-file';			break;
	}
}

function invoiceContentNumbers ($content) {
	$i = 0;
	$return = array();
	foreach ($content as $line => $vars)
	{
		/*if(isset($_POST['type'.$id]) && is_numeric($_POST['type'.$id]))
						$thisone['type']		= $_POST['type'.$id];
					if(isset($_POST['belop_hver_real'.$id]) && is_numeric($_POST['belop_hver_real'.$id]))
						$thisone['belop_hver']	= $_POST['belop_hver_real'.$id];
					if(isset($_POST['antall'.$id]) && is_numeric($_POST['antall'.$id]))
						$thisone['antall']		= $_POST['antall'.$id];
					if(isset($_POST['mva'.$id]) && is_numeric($_POST['mva'.$id]))
						$thisone['mva']			= $_POST['mva'.$id];
					if(isset($_POST['name'.$id]))
						$thisone['name']		= $_POST['name'.$id];*/
		if($vars['type'] != 'belop' ||
		$vars['belop_hver'] != '0' ||
		$vars['antall'] != '0' ||
		$vars['mva'] != '0' || 
		$vars['name'] != '')
		{
			$i++;
			$vars['belop_sum_netto']	= $vars['belop_hver'] * $vars['antall'];
			$vars['mva_sum']			= $vars['mva'] * $vars['belop_sum_netto'];
			$vars['belop_sum']			= $vars['belop_sum_netto'] + $vars['mva_sum'];
			$return[$i] = $vars;
		}
	}
	return $return;
}

# Return a default area; used if no area is already known. This returns the
# lowest area ID in the database (no guaranty there is an area 1).
# This could be changed to implement something like per-user defaults.
function get_default_area(){
	$Q_area = mysql_query("SELECT MIN(id) as thisid FROM mrbs_area");
	$area = mysql_result($Q_area,0, 'thisid');
	return ($area < 0 ? 0 : $area);
}
?>