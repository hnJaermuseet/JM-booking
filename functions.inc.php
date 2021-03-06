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
require_once 'functions/filter.php';
require_once 'functions/template.php';
require_once 'functions/email.php';
require_once 'functions/postal_number.php';
require_once 'functions/entry.php';
require_once 'functions/login.php';

/* Language */
require_once 'lang/lang.php';

function print_header($day, $month, $year, $area){
	global $search_str,$nrbs_pageheader, $testSystem, $login;
	
	debugAddToLog(__FILE__, __LINE__, 'Start of glob_inc.inc.php');

	# If we dont know the right date then make it up 
	if(!$day)
		$day   = date('d');
	if(!$month)
		$month = date('m');
	if(!$year)
		$year  = date('Y');
	if (empty($search_str))
		$search_str = '';
	
	echo '<html>'.chr(10);
	echo '<head>'.chr(10);
	echo '	<title>JM-booking</title>'.chr(10);
    echo '  <link rel="SHORTCUT ICON" HREF="./favicon.ico">'.chr(10);
	echo '	<link rel="stylesheet" type="text/css" href="css/jm-booking.css" />'.chr(10);
	echo '	<link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.7.2.custom.css" />'.chr(10);
	
	echo '	<script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>'.chr(10).chr(10);
	echo '	<script src="js/jquery-ui-1.8.1.custom.min.js" type="text/javascript"></script>'.chr(10).chr(10);
	echo '	<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp.js">'.
		'</script>'.chr(10);
	echo '</head>'.chr(10).chr(10);
	
	echo '<body'.$testSystem['bodyAttrib'].'>'.chr(10);

    echo '<table width="100%" class="hiddenprint">'.chr(10);
    if (strlen($nrbs_pageheader)>0)
    {
        echo '<tr><td style="text-align:center;">'.$nrbs_pageheader.'</td></tr>'.chr(10);
    }

    echo '	<tr>'.chr(10).
        '	<td bgcolor="#5B69A6">'.chr(10).
        '		<table width="100%" border=0>'.chr(10).
        '			<tr>'.chr(10).
        '				<td class="banner'.$testSystem['bannerExtraClass'].'" '.
            'style="text-align:center; font-size: 18px; font-weight: bold;">'.
            '<a href="./" class="lightbluebg">Booking for<br>J&aelig;rmuseet</a>'.
        '</td>'.chr(10).
        '				<td class="banner'.$testSystem['bannerExtraClass'].'">'.chr(10).

        '					<table>'.chr(10).
        '						<tr>'.chr(10).
        '							<td align="right">'.
        '<form action="day.php" method="get" style="margin: 0px; padding: 0px;">';

    genDateSelector("", $day, $month, $year);
    if (!empty($area))
        echo '<input type="hidden" name="area" value='.$area.'>';
    echo '<input type="submit" value="'.__('View day').'">'.
    iconHTML('calendar_view_day').
    '</form>';
    echo '</td>'.chr(10);

    // Week
    echo '							<td align="right">'.
        '<form action="week.php" method="get" style="margin: 0px; padding: 0px;">';

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
    echo '<input type="submit" value="'.__('View week').'">';
    echo iconHTML('calendar_view_week');
    echo '</form>'.
    '</td>'.chr(10).
    '						</tr>'.chr(10);

    // Month
    echo '						<tr>'.chr(10).
        '							<td align="right">'.
        '<form action="month.php" method="get" style="margin: 0px; padding: 0px;">';
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
        echo '>'.__(date("M", $thismonthtime)).'</option>';
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
    echo '<input type="submit" value="'._h('View month').'">'.
    iconHTML('calendar_view_month');
    echo '</form></td>'.chr(10);

    // Find using entry_id
    echo '							<td align="right">';
    echo '<form action="entry.php" method="get" style="margin: 0px; padding: 0px;">';
    echo '<input type="text" id="entry_id_finder" name="entry_id" '.
        'value="'.__('Enter entry ID').'" '.
        'onclick="document.getElementById(\'entry_id_finder\').value=\'\';">';
    echo '<input type="submit" value="'.__('Find').'">';
    echo '</form>';
    echo '</td>'.chr(10);

    echo '						</tr>'.chr(10).
        '					</table>'.chr(10);

    echo '				</td>'.chr(10);

    echo '				<td class="banner'.$testSystem['bannerExtraClass'].'" align="center">'.chr(10);
    echo '					'.__("Logged in as").' <a href="user.php?user_id='.$login['user_id'].'">'.
                                htmlentities($login['user_name'], ENT_QUOTES).
                            '</a><br>'.chr(10);
    echo '					<a href="logout.php">'.
        iconHTML('bullet_delete').' '.
        __("Log out").'</a><br>'.chr(10);
    echo '					<a href="admin.php">'.
        iconHTML('bullet_wrench').' '.
        __("Administration").'</a>'.chr(10);
    echo '				</td>'.chr(10);

    echo '			</tr>'.chr(10).
        '		</table>'.chr(10);


    echo '		 -:- <a class="menubar" href="./edit_entry2.php?day='.$day.'&amp;month='.$month.'&amp;year='.$year.'&amp;area='.$area.'&amp;room=">'.
    iconHTML('page_white_add').' '.
    __('Make a new entry').'</a>'.chr(10);

    //echo '		 -:- <a class="menubar" href="./new_entries.php">'.
    //iconHTML('table').' '.
    //_('List with new entries').'</a>'.chr(10);

    echo '		 -:- <a class="menubar" href="./entry_list.php?listtype=not_confirmed">'.
    iconHTML('email_delete').' '.
    __('Not confirmed').'</a>'.chr(10);

    echo '		 -:- <a class="menubar" href="./entry_list.php?listtype=no_user_assigned">'.
    iconHTML('user_delete').' '.
    __('No users assigned').'</a>'.chr(10);

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
    __('Customers').'</a>'.chr(10);

    if($login['user_invoice'] || $login['user_invoice_setready'])
    {
        echo '		 -:- <a class="menubar" href="./invoice_main.php';

        // By default, use the current area when going into the invoice part
        if(!$login['user_invoice'])
            echo '?area_id='.$area;
        echo '">'.
        iconHTML('coins').' '.
        __('Invoice').'</a>'.chr(10);
    }

    echo '		 -:- <a class="menubar" href="./user_list.php">'.
    iconHTML('user').' '.
    __('Userlist').'</a>'.chr(10);

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

	debugAddToLog(__FILE__, __LINE__, 'Finished printing header');
}

function genDateSelector($prefix, $day, $nonth, $year,$history=0,$id_prefix=''){
	if($day   == 0)
		$day = date('d');
	if($nonth == 0) 
		$nonth = date('m');
	if($year  == 0)
		$year = date('Y');
	
	echo '<select id="'.$id_prefix.'day" NAME="'.$prefix.'day">';
	
	for($i = 1; $i <= 31; $i++)
	{
		echo '<option' . ($i == $day ? ' selected' : '') . ' value="'.$i.'">'.$i.'</option>';
	}
	
	echo '</select>';
	echo '<select id="'.$id_prefix.'month" name="'.$prefix.'month">';
	
	for($i = 1; $i <= 12; $i++){
		$n = __(strftime("%b", mktime(0, 0, 0, $i, 1, $year)));
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

# Apply backslash-escape quoting unless PHP is configured to do it
# automatically. Use this for GET/POST form parameters, since we
# cannot predict if the PHP configuration file has magic_quotes_gpc on.
function slashes($s){
	if (get_magic_quotes_gpc()) 
		return $s;
	else 
		return addslashes($s);
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


function printMonth ($areaUrlString, array $rooms, $roomUrlString, $year, $month, $selected, $selectedType1 = 'day')
{
	
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

	$checkTime = checkTime($monthTime, $monthLast, $rooms);
	
	echo '<table style="width: 100%;">'.chr(10);
	echo ' <tr><td class="B"><center><b><a class="graybg" href="month.php?year='.date('Y', $monthTime).'&amp;month='.date('m', $monthTime).'&amp;day=1&amp;area='.$areaUrlString.'&amp;room='.$roomUrlString.'">';
	if($selectedType == 'month')
		echo '<font color="red">'.__(date('M', $monthTime)).' '.date('Y', $monthTime).'</font>';
	else
		echo __(date('M', $monthTime)).' '.date('Y', $monthTime);
	
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
			'<a class="graybg" href="week.php?year='.date('Y', $monthTime).'&amp;month='.date('m', $monthTime).'&amp;day='.$i.'&amp;area='.$areaUrlString.'&amp;room='.$roomUrlString.'">';
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
		
		echo '     <td><center><a href="day.php?year='.date('Y', $monthTime).'&amp;month='.date('m', $monthTime).'&amp;day='.$i.'&amp;area='.$areaUrlString.'&amp;room='.$roomUrlString.'">';
		$ymd = date('Y',$monthTime);
		if(strlen(date('m',$monthTime)) == 1)
			$ymd .= '0';
		$ymd .= date('m',$monthTime);
		if(strlen($i) == 1)
			$ymd .= '0';
		$ymd .= $i;
		
		if(isset($checkTime[$ymd])) {
			echo '<b>';
        }
		if($selectedType == 'day' && $selected == $i) {
			echo '<font color="red">'.$i.'</font>';
        }
		else {
			echo $i;
        }
		if(isset($checkTime[$ymd])) {
			echo '</b>';
        }
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
        return false;
	}
	
	if(isset($numbers[1]))
		$min = $numbers[1];
	else
	{
		$invalid_date = true;
        return false;
	}
	
	if(isset($numbers[2]))
		$day = $numbers[2];
	else
	{
		$invalid_date = true;
        return false;
	}
	
	if(isset($numbers[3]))
		$month = $numbers[3];
	else
	{
		$invalid_date = true;
        return false;
	}
	
	if(isset($numbers[4]))
		$year = $numbers[4];
	else
	{
		$invalid_date = true;
        return false;
	}

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
		$entry_name2[] = __('Unspesified title');
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
		$Q = db()->prepare("select * from `customer` where customer_id = :customer_id limit 1");
        $Q->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
            $row = $Q->fetch();
			$return = array (
				'customer_id'				=> $row['customer_id'],
				'customer_name'				=> $row['customer_name'],
				'customer_type'				=> $row['customer_type'],
				'customer_municipal_num'	=> $row['customer_municipal_num'],
				'customer_address_id_invoice'	=>  $row['customer_address_id_invoice']
			);
			
			if($row['slettet'] == '1') {
                $return['slettet'] = true;
            }
			else {
                $return['slettet'] = false;
            }
			
			require "libs/municipals_norway.php";
			if(isset($municipals [$return['customer_municipal_num']])) {
                $return ['customer_municipal'] = $municipals [$return['customer_municipal_num']];
            }
			else {
                $return ['customer_municipal'] = '';
            }
			
			// Getting phone numbers
			$return ['customer_phone'] = array();
			$Q = db()->prepare('select * from `customer_phone` where customer_id = :customer_id');
            $Q->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
            $Q->execute();
			while($R = $Q->fetch()) {
                $return ['customer_phone'][$R['phone_id']] = array(
                    'phone_id' => $R['phone_id'],
                    'phone_num' => $R['phone_num'],
                    'phone_name' => $R['phone_name']
                );
            }
			
			// Getting addresses
			$return ['customer_address'] = array();
			$Q = db()->prepare('select * from `customer_address` where customer_id = :customer_id');
            $Q->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
            $Q->execute();
			while($R = $Q->fetch()) {
                $return ['customer_address'][$R['address_id']] = $R;
            }
			
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
		$Q = db()->prepare('select * from `customer_address` where address_id = :address_id limit 1');
        $Q->bindValue(':address_id', $id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			return $Q->fetch();
		}
	}
}

$cache_getUser = array();
/**
 * Get a user from database or cache. Returns empty array when user is not found
 *
 * Caches a user after retriving it.
 *
 * @param  int   $id
 * @param  bool $getGroups
 * @return array
 */
function getUser ($id, $getGroups = false)
{
    global $cache_getUser;

    if(isset($cache_getUser[$id]) && !$getGroups) {
        return $cache_getUser[$id];
    }
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = db()->prepare('select * from `users` where user_id = :user_id limit 1');
        $Q->bindValue(':user_id', $id, PDO::PARAM_INT);
        $Q->execute();
		if(!$Q->rowCount() > 0)
		{
            $cache_getUser[$id] = array();
			return $cache_getUser[$id];
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
			$return = $Q->fetch();
			
			if($return['user_invoice'] == '1') {
                $return['user_invoice'] = true;
            }
			else {
                $return['user_invoice'] = false;
            }
			
			if($return['user_invoice_setready'] == '1') {
                $return['user_invoice_setready'] = true;
            }
			else {
                $return['user_invoice_setready'] = false;
            }
			
			if($getGroups)
			{
				$return['groups'] = array();
				$Q_groups = db()->prepare("select group_id from `groups` where user_ids like '%;".$return['user_id'].";%'");
                $Q_groups->execute();
				while($R_group = $Q_groups->fetch()) {
                    $return['groups'][$R_group['group_id']] = $R_group ['group_id'];
                }
			}

            $cache_getUser[$id] = $return;
			return $cache_getUser[$id];
		}
	}
}

function isUserDeactivated ($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return true;
	}
	else
	{
		$id = (int)$id;
		$Q = db()->prepare('select deactivated from `users` where user_id = :user_id limit 1');
        $Q->bindValue(':user_id', $id, PDO::PARAM_INT);
        $Q->execute();
		return (bool)($Q->fetch()['deactivated']);
	}
}

$cache_getEntryType = array();

/**
 * Get entry type from database or cache.
 *
 * Returns empty array if not found
 *
 * @param  int $id
 * @return array
 */
function getEntryType ( $id ) {

    if ( isset($cache_getEntryType[$id]) ) {
        return $cache_getEntryType[$id];
    }

    if ( !is_numeric( $id ) || $id == '0' ) {
        return array();
    }
    else
    {
        $id = (int)$id;
        $Q = db()->prepare('select * from `entry_type` where entry_type_id = :entry_type_id limit 1');
        $Q->bindValue(':entry_type_id', $id, PDO::PARAM_INT);
        $Q->execute();
        if ( $Q->rowCount() <= 0 ) {
            // -> No entry found, return empty array
            $cache_getEntryType[$id] = array();
            return array();
        }
        else
        {
            $row = $Q->fetch();
            $return = array(
                'entry_type_id' => $row['entry_type_id'],
                'entry_type_name' => $row['entry_type_name'],
                'entry_type_name_short' => $row['entry_type_name_short'],
                'group_id' => $row['group_id'],
                'day_start' => $row['day_start'],
                'day_end' => $row['day_end']
            );

            $cache_getEntryType[$id] = $return;
            return $cache_getEntryType[$id];
        }
    }
}

function getEntryDeleted($id)
{
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = db()->prepare("select * from `entry_deleted` where entry_id = :entry_id limit 1");
        $Q->bindValue(':entry_id', $id, PDO::PARAM_INT);
        $Q->execute();

		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			return getEntryParseDatabaseArray ($Q->fetch());
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
		$Q = db()->prepare("select * from `entry` where entry_id = :entry_id limit 1");
        $Q->bindValue(':entry_id', $id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			return getEntryParseDatabaseArray ($Q->fetch());
		}
	}
}

function getEntryParseDatabaseArray ($return)
{
	$return['room_id']					= splittIDs($return['room_id']);
	$return['edit_by']					= splittIDs($return['edit_by']);
	$return['user_assigned']			= splittIDs($return['user_assigned']);
	$return['contact_person_email2']	= splittEmails($return['contact_person_email']);
	if($return['invoice_content'] == '' ) {
        $return['invoice_content'] = array();
    }
	else
	{
		$return['invoice_content']			= unserialize($return['invoice_content']);
		if(!is_array($return['invoice_content'])) {
            $return['invoice_content'] = array();
        }
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
			if(isset($return['mva'][$vars['mva']])) {
                $return['mva'][$vars['mva']] += $vars['mva_sum'];
            }
			else {
                $return['mva'][$vars['mva']] = $vars['mva_sum'];
            }
			
			$return['mva_grunnlag_sum'] += $vars['belop_sum_netto'];
			if(isset($return['mva_grunnlag'][$vars['mva']])) {
                $return['mva_grunnlag'][$vars['mva']] += $vars['belop_sum_netto'];
            }
			else {
                $return['mva_grunnlag'][$vars['mva']] = $vars['belop_sum_netto'];
            }
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
	else {
        $return['mva_vis'] = false;
    }
	
	return $return;
}

$cache_getArea = array();

function getArea ( $id ) {
    global $cache_getArea;
    if ( isset($cache_getArea[$id]) ) {
        return $cache_getArea[$id];
    }

    if ( !is_numeric( $id ) || $id == '0' ) {
        return array();
    }
    else
    {
        $id = (int)$id;
        $Q = db()->prepare('select * from `mrbs_area` where id = :area_id limit 1');
        $Q->bindValue(':area_id', $id, PDO::PARAM_INT);
        $Q->execute();
        if ( $Q->rowCount() <= 0 ) {
            $cache_getArea[$id] = array();
            return array();
        }
        else
        {
            $return = $Q->fetch();
            $return['area_id'] = $return['id'];
            unset($return['id']);

            $cache_getArea[$id] = $return;
            return $cache_getArea[$id];
        }
    }
}

$cache_getRoom = array();
function getRoom ( $id ) {
    global $cache_getRoom;

    if ( isset($cache_getRoom[$id]) ) {
        return $cache_getRoom[$id];
    }

    if ( !is_numeric( $id ) || $id == '0' ) {
        return array();
    }
    else
    {
        $id = (int)$id;
        $Q = db()->prepare( "select * from `mrbs_room` where id = :room_id limit 1" );
        $Q->bindValue(':room_id', $id, PDO::PARAM_INT);
        $Q->execute();
        if ( $Q->rowCount() <= 0 ) {
            $cache_getRoom[$id] = array();
            return array();
        }
        else
        {
            $row = $Q->fetch();
            $return = array(
                'room_id' => $row['id'],
                'room_name' => $row['room_name'],
                'area_id' => $row['area_id']
            );

            $cache_getRoom[$id] = $return;
            return $cache_getRoom[$id];
        }
    }
}


$cache_getProgram = array();
function getProgram ( $id ) {
    global $cache_getProgram;

    if ( isset($cache_getProgram[$id]) ) {
        return $cache_getProgram[$id];
    }

    if ( !is_numeric( $id ) || $id == '0' ) {
        return array();
    }
    else
    {
        $id = (int)$id;
        $Q = db()->prepare( "select * from `programs` where program_id = :program_id limit 1" );
        $Q->bindValue(':program_id', $id, PDO::PARAM_INT);
        $Q->execute();
        if ( $Q->rowCount() <= 0 ) {
            $cache_getProgram[$id] = array();
            return array();
        }
        else
        {
            $cache_getProgram[$id] = $Q->fetch();
            return $cache_getProgram[$id];
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
		$Q = db()->prepare("select * from `programs_defaultattachment` where program_id = :program_id");
        $Q->bindValue(':program_id', $id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			$return = array();
			while($R = $Q->fetch())
			{
				$att = getAttachment($R['att_id']);
				if(count($att)) {
                    $return[$R['att_id']] = $att;
                }
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
		$Q = db()->prepare("select * from `entry_type_defaultattachment`
			WHERE
				entry_type_id = :entry_type_id AND
				area_id = :area_id
			");
        $Q->bindValue(':entry_type_id', $id, PDO::PARAM_INT);
        $Q->bindValue(':area_id', $areaid, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			$return = array();
			while($R = $Q->fetch())
			{
				$att = getAttachment($R['att_id']);
				if(count($att)) {
                    $return[$R['att_id']] = $att;
                }
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
		$Q = db()->prepare("select * from `entry_confirm` where confirm_id = :confirm_id limit 1");
        $Q->bindValue(':confirm_id', $id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			$return = $Q->fetch();
			$return['confirm_to'] = unserialize($return['confirm_to']);
			
			
			// Get used attachments, if any
			$return['confirm_usedatt'] = array();
			$Q = db()->prepare("select att_id from `entry_confirm_usedatt` where confirm_id = :confirm_id");
            $Q->bindValue(':confirm_id', $id, PDO::PARAM_INT);
            $Q->execute();
			while($R = $Q->fetch()) {
				$att = getAttachment($R['att_id']);
				if(count($att)) {
                    $return['confirm_usedatt'][$att['att_id']] = $att;
                }
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
		$Q = db()->prepare("select * from `entry_confirm_attachment` where att_id = :att_id limit 1");
        $Q->bindValue(':att_id', $id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			$att = $Q->fetch();
			
			if($getAll)
			{
				// Getting uploaded by
				$user = getUser($att['user_id']);
				if(count($user)) {
                    $att['user_name'] = $user['user_name'];
                }
				else {
                    $att['user_name'] = '';
                }
				
				
				// Connections
				$att['connections'] = array();
				$Q_con = db()->prepare("select * from `programs_defaultattachment` where `att_id` = :att_id");
                $Q_con->bindValue(':att_id', $id, PDO::PARAM_INT);
                $Q_con->execute();
				while($R = $Q_con->fetch())
				{
					$program = getProgram($R['program_id']);
					
					if(count($program))
					{
						$area = getArea($program['area_id']);
						if(!count($area)) {
                            $area = array('area_name' => __('UNKNOWN AREA'));
                        }
						
						$att['connections'][] = array(
								'type' => 'Fast program',
								'id' => $program['program_id'],
								'name' => $area['area_name'].' - '.$program['program_name'],
								'icon' => 'package'
							);
					}
				}
				$Q_con = db()->prepare("select * from `entry_type_defaultattachment` where `att_id` = :att_id");
                $Q_con->bindValue(':att_id', $id, PDO::PARAM_INT);
                $Q_con->execute();
                while($R = $Q_con->fetch())
				{
					$area = getArea($R['area_id']);
					if(!count($area))
						$area = array('area_name' => __('UNKNOWN AREA'));
					
					$entry_type = getEntryType($R['entry_type_id']);
					if(count($entry_type)) {
                        $att['connections'][] = array(
                            'type' => 'Bookingtype',
                            'id' => $entry_type['entry_type_id'],
                            'name' => $area['area_name'] . ' - ' . $entry_type['entry_type_name'],
                            'icon' => 'page_white_stack'
                        );
                    }
				}
				
				// Getting usage
				$Q_usedatt = db()->prepare("select * from `entry_confirm_usedatt` where `att_id` = :att_id");
                $Q_usedatt->bindValue(':att_id', $id, PDO::PARAM_INT);
                $Q_usedatt->execute();
				$att['usedatt'] = array();
				while($R = $Q_usedatt->fetch())
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
		$Q = db()->prepare("select * from `groups` where group_id = :group_id limit 1");
        $Q->bindValue(':group_id', $id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			$return = $Q->fetch();
			$return['users'] = splittIDs($return['user_ids']);
			foreach($return['users'] as $key => $user_id)
			{
				$user = getUser($user_id);
				if(isset($user['deactivated']) && $user['deactivated']) {
                    unset($return['users'][$key]);
                }
			}
			return $return;
		}
	}
}

function getEntryLog($id, $entry=false)
{
	if($entry) {
        $id_type = 'entry_id';
    }
	else {
        $id_type = 'log_id';
    }
	
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = db()->prepare("select * from `entry_log` where $id_type = :id");
        $Q->bindValue(':id', $id, PDO::PARAM_INT);
        $Q->execute();
		if($Q->rowCount() <= 0)
		{
			return array();
		}
		else
		{
			if(!$entry)
			{
                $row = $Q->fetch();
				$return = array (
					'log_id'			=> $row['log_id'],
					'entry_id'			=> $row['entry_id'],
					'user_id'			=> $row['user_id'],
					'log_action'		=> $row['log_action'],
					'log_action2'		=> $row['log_action2'],
					'log_time'			=> $row['log_time'],
					'rev_num'			=> $row['rev_num'],
					'log_data'			=> unserialize($row['log_data'])
				);
			}
			else
			{
				while ($R = $Q->fetch())
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
			$return .= __('New entry.');
		elseif($log['log_action'] == 'edit')
		{
			$emails = false;
			switch ($log['log_action2'])
			{
				case 'invoice_readyfor':
					$return .= 'Booking klar til fakturering'; break;
				case 'invoice_exported':
					$return .= 'Bookingen ble merket som sendt til regnskap'; break;
					
				case 'invoice_sent': // Not in use?
					$return .= __('Invoice is registered as sent.'); break;
				case 'invoice_payed':
					$return .= 'Betaling er register p&aring; faktura'; break;
				case 'confirm':
				case 'comfirm':
					$return .= __('Confirmation was sent.'); break;
				case 'confirm_email':
				case 'comfirm_email':
					$return .= __('Confirmation email is sent to');
					$emails = true;
					break;
				case 'ical_sent':
					$return .= __('Icalendar element is sent to');
					$emails = true;
					break;
				case 'entry_deleted':
					$return .= 'Bookingen slettet';
					break;
				case 'entry_undeleted';
					$return .= 'Bookingen reaktivert';
					break;
				case '':
					$return .= __('Entry was edited.');
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
						foreach ($log['log_data']['emails'] as $i => $email)
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
		$middlestring = __('set to');
	elseif($log['log_action2'] == 'entry_deleted' || $log['log_action2'] == 'entry_undeleted')
		$middlestring = 'var';
	else
		$middlestring = __('changed to');
		
	
	foreach ($log['log_data'] as $index => $value)
	{
		if($index != 'rev_num') // Ignore some...
		{
			switch($index)
			{
				case 'customer_municipal':
					// Ignore
					break;
				
				case 'entry_name':
					if($value == '')
						$return[] = __('Entry name').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Entry name').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'entry_title':
					if($value == '')
						$return[] = __('Entry title').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Entry title').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'time_start':
					$return[] = __('Start time').' '.$middlestring.' <i>'.date('H:i d-m-Y', $value).'</i>';
					break;
					
				case 'time_end':
					$return[] = __('End time').' '.$middlestring.' <i>'.date('H:i d-m-Y', $value).'</i>';
					break;
					
				case 'time_created':
					$return[] = 'Opprettet <i>'.date('H:i d-m-Y', $value).'</i>';
					break;
					
				case 'time_last_edit':
					$return[] = 'Sist endret <i>'.date('H:i d-m-Y', $value).'</i>';
					break;
				
				case 'confirm_email':
					if($value == '1')
						$return[] = 'Bekreftelse var sendt';
					else
						$return[] = 'Bekreftelse var ikke sendt';
					break;
					
				case 'area_id':
					$area = getArea($value);
					if(count($area))
						$return[] = __('Area').' '.$middlestring.' <i>'.$area['area_name'].'</i>';
					else
						$return[] = __('Area').' '.$middlestring.' <i>--'.__('Name not found').'--</i>';
					break;
					
				case 'room_id':
					$value = splittIDs($value);
					if(!count($value))
						$return[] = __('Room').' '.$middlestring.' <i>'.__('Whole area').'</i>';
					else
					{
						$values = array();
						foreach ($value as $id)
						{
							if($id == '0')
								$values[] = __('Whole area');
							else
							{
								$thisone = getRoom($id);
								if(count($thisone))
									$values[] = $thisone['room_name'];
							}
						}
						$return[] = __('Room').' '.$middlestring.' <i>'.implode(', ', $values).'</i>';
					}
					break;
					
				case 'created_by':
					$thisone = getUser($value);
					if(count($thisone))
						$return[] = 'Opprettet av '.$thisone['user_name'];
					break;
				
				case 'user_last_edit':
					$thisone = getUser($value);
					if(count($thisone))
						$return[] = 'Sist endret av '.$thisone['user_name'];
					break;
					
				case 'edit_by':
					if(!count($value))
						$return[] = 'Har v&aelig;rt endret av <i>'.__('Nobody').'</i>';
					else
					{
						$values = array();
						foreach ($value as $id)
						{
							if($id == '0')
								$values[] = __('Nobody');
							else
							{
								$thisone = getUser($id);
								if(count($thisone))
									$values[] = $thisone['user_name'];
							}
						}
						$return[] = 'Har v&aelig;rt endret av <i>'.implode(', ', $values).'</i>';
					}
					break;
					
				case 'user_assigned':
					$value = splittIDs($value);
					if(!count($value))
						$return[] = __('Users assigned').' '.$middlestring.' <i>'.__('Nobody').'</i>';
					else
					{
						$values = array();
						foreach ($value as $id)
						{
							if($id == '0')
								$values[] = __('Nobody');
							else
							{
								$thisone = getUser($id);
								if(count($thisone))
									$values[] = $thisone['user_name'];
							}
						}
						$return[] = __('Users assigned').' '.$middlestring.' <i>'.implode(', ', $values).'</i>';
					}
					break;
					
				case 'user_assigned2':
					if($value == '')
						$return[] = __('Manual user assigned').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Manual user assigned').' '.$middlestring.' "'.$value.'"';
					break;
					
				/*
				 * Not in use...
				 case 'customer_name':
					$return .= _('Customer').' '.$middlestring.' "'.$value.'"';
					break;*/
					
				case 'customer_id':
					if($value == 0)
						$return[] = __('Customer').' <i>'.__('not set').'</i>';
					else
					{
						$customer = getCustomer($value);
						if(count($customer))
							$return[] = __('Customer').' '.$middlestring.' <i>'.$customer['customer_name'].'</i>';
						else
							$return[] = __('Customer ID').' '.$middlestring.' "'.$value.'"';
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
						$return[] = __('Municipal').' '.$middlestring.' <i>'.$municipals[$value].'</i>';
					elseif($value == '')
						$return[] = __('Municipal').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Municipal').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'contact_person_name':
					if($value == '')
						$return[] = __('Contact person').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Contact person').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'contact_person_phone':
					if($value == '')
						$return[] = __('Contact persons phone number').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Contact persons phone number').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'contact_person_email':
					if($value == '')
						$return[] = __('Contact persons email').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Contact persons email').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'num_person_child':
					if($value == "" || $value == 0)
						$return[] = __('Number of children').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Number of children').' '.$middlestring.' '.$value;
					break;
				
				case 'num_person_adult':
					if($value == "" || $value == 0)
						$return[] = __('Number of adults').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Number of adults').' '.$middlestring.' '.$value;
					break;
					
				
				case 'num_person_count':
					if($value == "" || $value == 0)
						$return[] = __('Count these numbers').' '.$middlestring.' <i>'.__('not count in booking system / Datanova / cash register').'</i>';
					else
						$return[] = __('Count these numbers').' '.$middlestring.' <i>'.__('count in booking system').'</i>';
					break;
					
				case 'program_description':
					if($value == '')
						$return[] = __('Program description').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Program description').' '.$middlestring.' "'.$value.'"';
					break;

				case 'service_alco':
					if($value)
						$return[] = __('Alcohol').' <i>'.__('is to be served').'</i>';
					else
						$return[] = __('Alcohol').' <i>'.__('is not to be served').'</i>';
					break;
				
				case 'service_description':
					if($value == '')
						$return[] = __('Service description').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Service description').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'comment':
					if($value == '')
						$return[] = __('Comment').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Comment').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'infoscreen_txt':
					if($value == '')
						$return[] = __('Text on infoscreen').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Text on infoscreen').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'invoice':
					if($value)
						$return[] = __('Invoice').' <i>'.__('should be made').'</i>';
					else
						$return[] = __('Invoice').' <i>'.__('should not be made').'</i>';
					break;
					
				case 'invoice_info':
				case 'invoice_internal_comment':
					if($value == '')
						$return[] = __('Invoice').' - '.__('Internal comment').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Invoice').' - '.__('Internal comment').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'invoice_comment':
					if($value == '')
						$return[] = __('Invoice').' - '.__('Comment').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Invoice').' - '.__('Comment').' '.$middlestring.' "'.$value.'"';
					break;
					
				case 'invoice_ref_your':
					if($value == '')
						$return[] = __('Invoice').' - '.__('Your reference').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Invoice').' - '.__('Your reference').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'invoice_electronic':
					if($value)
						$return[] = __('Invoice').' <i>'.__('is to be sendt by e-mail').'</i> ('.__('E-delivery').')';
					else
						$return[] = __('Invoice').' <i>'.__('is to be sendt by regular mail').'</i> ('.__('Not').' '.strtolower(__('E-delivery')).')';
					break;
				
				case 'invoice_email':
					if($value == '')
						$return[] = __('Invoice').' - '.__('E-mail').' <i>'.__('not set').'</i>';
					else
						$return[] = __('Invoice').' - '.__('E-mail').' '.$middlestring.' "'.$value.'"';
					break;
				
				case 'invoice_address_id':
					if($value == 0)
						$return[] = __('Invoice').' - '.__('Address').' <i>'.__('not set').'</i>';
					else
					{
						$address = getAddress($value);
						if(count($address))
							$return[] = __('Invoice').' - '.__('Address').' '.$middlestring.' <i>'.str_replace("\n", ', ', $address['address_full']).'</i>';
						else
							$return[] = __('Invoice').' - '.__('Address').' '.$middlestring.' "'.$value.'"';
					}
					break;
				
				case 'invoice_content':
					if(!is_array($value)) {
						$return[] =  __('Invoice').' - '.__('Content').' has got syntax error in the log.';
					} else
					{
						foreach ($value as $linenr => $line)
						{
							$return[] = __('Invoice').' - '.__('Content') .' - '.
							__('line').' <i>'.$linenr.'</i> '.$middlestring.': '.
							__('name').': <i>'.$line['name'].'</i>, '.
							'pris/stk: <i>'.$line['belop_hver'].'</i>, '.
							__('amount').': <i>'.$line['antall'].'</i>, '.
							__('tax').': <i>'.($line['mva']*100).'%</i>, '.
							'sum u/MVA: <i>'.$line['belop_sum_netto'].'</i>';
						}
					}
					break;
				
				case 'program_id':
					if($value == 0)
						$return[] = __('Fixed program').' <i>'.__('not set').'</i>';
					else
					{
						$program = getProgram($value);
						if(count($program))
							$return[] = __('Fixed program').' '.$middlestring.' <i>'.$program['program_name'].'</i>';
						else
							$return[] = __('ID of fixed program').' '.$middlestring.' "'.$value.'"';
					}
					break;
				
				case 'entry_type_id':
					if($value == 0)
						$return[] = __('Entry type').' <i>'.__('not set').'</i>';
					else
					{
						$entry_type = getEntryType($value);
						if(count($entry_type))
							$return[] = __('Entry type').' '.$middlestring.' <i>'.$entry_type['entry_type_name'].'</i>';
						else
							$return[] = __('ID of entry type').' '.$middlestring.' "'.$value.'"';
					}
					break;
				
				case 'confirm_id':
					$return[] = '<a href="entry_confirm_view.php?confirm_id='.$value.'">'.__('Confirmation sent').'</a>';
					break;
				
				case 'confirm_comment':
					$return[] = __('Comment').': '.$value;
					break;

                case 'resourcenum':
					if($value == '')
						$return[] = 'Ressursnummer <i>'.__('not set').'</i>';
					else
						$return[] = 'Ressursnummer '.$middlestring.' "'.$value.'"';
                    break;
				
				default:
					if(substr($index, 0, 11) == 'email_faild') {
						$return[] = __('Confirmation was <b>not sent</b> to'). ' <i>'.$value.'</i>'; break;
					}
					elseif(substr($index, 0, 5) == 'email') {
						$return[] = _h('Tried sending confirmation e-mail to').' <i>'.$value.'</i><br />('.
							_h('The bookingsystem can not know if it was recived').')'; break;
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
	
	if(!is_array($log_data)) {
        return FALSE;
    }
	
	// Checking log_action
	switch ($log_action)
	{
		case 'add':
			$log_action2 = ''; // No log_action2 for add...
			break;
		case 'edit':
			switch ($log_action2)
			{
				case 'invoice_exported':
				case 'invoice_readyfor':
				case 'invoice_sent':
				case 'invoice_payed':
				case 'confirm':
				case 'entry_deleted':
				case 'entry_undeleted':
					break;
				case 'confirm_email':
				case 'ical_sent':
					// Requires $log_data['emails']
					if(!isset($log_data['emails']) || !count($log_data['emails'])) {
                        return FALSE;
                    }
					
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
	
	if(!is_numeric($rev_num)) {
        return FALSE;
    }
	$rev_num = (int)$rev_num;
	if(!is_numeric($entry_id)) {
        return FALSE;
    }
	$entry_id = (int)$entry_id;
	
	// Inserting into database
	$Q = db()->prepare("INSERT INTO `entry_log` (
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
			:entry_id,
			:user_id,
			:log_action,
			:log_action2,
			:thetime,
			:rev_num,
			:serialized_log_data
		);");
    $Q->bindValue(':entry_id', $entry_id, PDO::PARAM_INT);
    $Q->bindValue(':user_id', $login['user_id'], PDO::PARAM_INT);
    $Q->bindValue(':log_action', $log_action, PDO::PARAM_STR);
    $Q->bindValue(':log_action2', $log_action2, PDO::PARAM_STR);
    $Q->bindValue(':thetime', time(), PDO::PARAM_INT);
    $Q->bindValue(':rev_num', $rev_num, PDO::PARAM_INT);
    $Q->bindValue(':serialized_log_data', serialize($log_data), PDO::PARAM_STR);
    $Q->execute();
	
	return TRUE;
}

function readEntry ($entry_id, $rev_num)
{
	global $login;
	
	$entry_id = (int)$entry_id;
	$rev_num = (int)$rev_num;
	
	$Q = db()->prepare("INSERT INTO `entry_read` (
		`read_id` ,
		`user_id` ,
		`entry_id` ,
		`rev_num` ,
		`time_read`
	)
	VALUES (
		NULL , :user_id, :entry_id, :rev_num, :thetime
	);");
    $Q->bindValue(':user_id', $login['user_id'], PDO::PARAM_INT);
    $Q->bindValue(':entry_id', $entry_id, PDO::PARAM_INT);
    $Q->bindValue(':rev_num', $rev_num, PDO::PARAM_INT);
    $Q->bindValue(':thetime', time(), PDO::PARAM_INT);
    $Q->execute();
}

/**
 * @param $start
 * @param $end
 * @param $area_id
 * @param int $room
 * @return array        $array[roomid][entryid] = entryid;
 */
function checkTime_Room ($start, $end, $area_id, $room = 0) {
    if (!is_array($area_id)) {
        $area_id = array(array('area_id' => $area_id));
    }
    $area_query = array();
    foreach($area_id as $area) {
        $area_query[] = 'area_id = \'' . ((int)$area['area_id']) . '\'';
    }
    $area_query = '(' . implode(' OR ', $area_query) . ')';

	if(is_array($room))
	{
		$whole_area = FALSE;
        $room_query = array();
		foreach ($room as $rid)
		{
			if($rid == '0' && count($room) == 1) {
                $whole_area = TRUE;
            }
			$room_query[] = "room_id LIKE '%;$rid;%'";
		}
		$room_query[] = "room_id like '%;0;%'";

        $room_query = ' AND (' . implode(' || ', $room_query) .')';

		if($whole_area)
		{
			$room_query = '';
			$room = array();
			// Getting all rooms in area
			$Q_rooms = db()->prepare("select id as room_id from `mrbs_room` where $area_query");
            $Q_rooms->execute();
			while($R_room = $Q_rooms->fetch()) {
				$room[$R_room['room_id']] = $R_room['room_id'];
            }
		}

        // Whole area means that the whole area is reserved!
		$room[0] = '0';
	}
	elseif($room != 0) {
		$room_query = " and (room_id like '%;$room;%' || room_id like '%;0;%')"; // This room or the whole building
    }
	else {
		$room_query = '';
    }



	$sql = "select * from `entry` where
		(
			(time_start <= :time_start and time_end > :time_start) or
			(time_start < :time_end and time_end >= :time_end) or
			(time_start > :time_start and time_end < :time_end)
		)
		AND ".$area_query.$room_query;
	$Q_checktime = db()->prepare($sql);
    $Q_checktime->bindValue(':time_start', $start, PDO::PARAM_INT);
    $Q_checktime->bindValue(':time_end', $end, PDO::PARAM_INT);
    $Q_checktime->execute();

	$return = array();
	if($Q_checktime->rowCount() > 0)
	{
		while ($R_entry = $Q_checktime->fetch(PDO::FETCH_ASSOC))
		{
            $R_entry = getEntryParseDatabaseArray($R_entry);
			if(is_array($room))
			{
				$entry_rooms = $R_entry['room_id'];
				foreach ($room as $rid)
				{
					if(isset($entry_rooms['0']) || in_array($rid, $entry_rooms)) {
						$return[$rid][$R_entry['entry_id']] = $R_entry;
                    }
				}
			}
			else {
				$return[$room][$R_entry['entry_id']] = $R_entry;
            }
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
			if($uid == '0' && count($user) == 1) {
                return array();
            }
			
			$i++;
			$user_query .= "user_assigned like '%;$uid;%'";
			if($i < count($user)) {
                $user_query .= " || ";
            }
		}
		$user_query .= ")";
	}
	elseif($user != 0) {
        $user_query = " and (user_assigned like '%;$user;%')";
    }
	else {
        return array();
    }
	$Q_checktime = db()->prepare("select entry_id, user_assigned from `entry` where
		(
			(time_start <= :time_start and time_end > :time_start) or
			(time_start < :time_end and time_end >= :time_end) or
			(time_start > :time_start and time_end < :time_end)
		)
		".$user_query);
    $Q_checktime->bindValue(':time_start', $start, PDO::PARAM_INT);
    $Q_checktime->bindValue(':time_end', $end, PDO::PARAM_INT);
    $Q_checktime->execute();
	
	$return = array();
	if($Q_checktime->rowCount() <= 0) {
        return $return;
    }
	else
	{
		while ($R_entry = $Q_checktime->execute())
		{
			if(is_array($user))
			{
				$R_entry['user_assigned'] = splittIDs($R_entry['user_assigned']);
				foreach ($user as $uid)
				{
					if(in_array($uid, $R_entry['user_assigned'])) {
                        $return[$uid][$R_entry['entry_id']] = $R_entry['entry_id'];
                    }
				}
			}
			else {
                $return[$user][$R_entry['entry_id']] = $R_entry['entry_id'];
            }
		}
	}
	return $return;
}

function checkTime ($start, $end, array $rooms)
{
	/*
		Checks a time for entries
		- Can limit to a room (area is a must)
		
		returns:
		$array[Ymd][entryid] = entryid;
	*/
	

    $whole_area = FALSE;
    $area_queries = array();
    $room_query = " AND (";
    foreach ($rooms as $room)
    {
        $area_queries[$room['area_id']] = 'area_id = \''.$room['area_id'].'\'';
        if($room['room_id'] == '0') {
            $whole_area = TRUE;
        }
        $room_query .= 'room_id like \'%;'.$room['room_id'].';%\' || ';
    }
    $room_query .= "room_id like '%;0;%')";

    if($whole_area)
    {
        $room_query = '';
    }

    if (!count($area_queries)) {
        $area_query = '';
    }
    else {
        $area_query = 'AND ('.implode(' OR ', $area_queries).')';
    }

    $sql = "select entry_id, time_start, time_end from `entry` where
		(
			(time_start <= :time_start and time_end > :time_start) or
			(time_start < :time_end and time_end >= :time_end) or
			(time_start > :time_start and time_end < :time_end)
		)
		".$area_query . $room_query;
	$Q_checktime = db()->prepare($sql);
    $Q_checktime->bindValue(':time_start', $start, PDO::PARAM_INT);
    $Q_checktime->bindValue(':time_end', $end, PDO::PARAM_INT);
    $Q_checktime->execute();
	
	$return = array();
	if($Q_checktime->rowCount() > 0)
	{
		while ($R_entry = $Q_checktime->fetch())
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
	return '<img src="./img/icons/'.$ico.$end.'" style="border: 0px solid black; vertical-align: middle; '.$style.'" alt="'.__('Icon').': '.$ico.'">';
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
	$Q_area = db()->prepare('SELECT MIN(id) as thisid FROM mrbs_area');
    $Q_area->execute();
    $area = $Q_area->fetch()['thisid'];
	return ($area < 0 ? 0 : $area);
}

function checkUser ($user_id = '0')
{
	if($user_id == '0')
		return FALSE;
	else
	{
		$Q_user = db()->prepare("select * from `users` where user_id = :user_id");
        $Q_user->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $Q_user->execute();
		
		if($Q_user->rowCount() <= 0)
			return FALSE;
		else
			return TRUE;
	}
}
