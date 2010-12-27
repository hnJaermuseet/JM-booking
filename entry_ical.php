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

// TODO: Remove this file?
//$require_login = FALSE; // TODO: remove?
include_once("glob_inc.inc.php");

filterMakeAlternatives();

if(isset($_GET['listtype']))
	$listtype = $_GET['listtype'];
elseif(isset($_GET['user_id']))
	$listtype = 'user';
else
	$listtype = '';
$addAdfterSQL = '';
$return_to = 'entry_list';

switch($listtype)
{
	case 'user':
		if(isset($_GET['user_id']))
		{
			$tittel = '';
			
			$filters = array();
			$filters = addFilter($filters, 'user_assigned', $_GET['user_id']);
			$filters = addFilter(
					$filters, 
					'time_start', 
					mktime(0,0,0,date('m'), date('d'), date('Y')), 
					'>'
				);
		}
		break;
	
	default:
		$tittel = _('Entry list');
		if(!isset($_GET['filters']))
			$_GET['filters'] = '';
		
		$filters = filterGetFromSerialized($_GET['filters']);
		if(!$filters)
			$filters = array();
		
		$return_to = 'entry_list';
		break;
}

$SQL = genSQLFromFilters($filters, 'entry_id');
$SQL .= " order by `time_start`".$addAdfterSQL;


$Q = mysql_query($SQL);

/**
 * Simulation of Smarty-object
 *
 */
class EntryTemplate 
{
	protected $data = array();
	public function __get($var) {
		return $this->data[$var];
	}
	public function assign($var, $value) {
		$this->data[$var] = $value;
	}
}


function getGMTtimestamp ($timestamp)
{
	// Settings:
	//$tz_mysql = 'Europe/Oslo';
	$tz_gmt = 'Indian/Mahe'; //'UTC';
	
	// Code:
	
	$dtzone = new DateTimeZone($tz_gmt);
	
	$time = date('r', $timestamp);
	echo $time.'<br>';
	$dtime = new DateTime($time);
	$dtime->setTimezone($dtzone);
	
	return $dtime->format('U');
}
/*

TODO: fiks timezone
echo '1266447540<br>';
echo getGMTtimestamp('1266447540'); exit;
*/
// http://msdn.microsoft.com/en-us/library/ee624723%28EXCHG.80%29.aspx
// http://en.wikipedia.org/wiki/ICalendar

echo 'BEGIN:VCALENDAR'.chr(10).
'METHOD:REQUEST'.chr(10).
'X-WR-TIMEZONE:Europe/Oslo'.chr(10).
// TODO: 10 min i stede for 1 min som nå under testing
'X-PUBLISHED-TTL:P0DT0H1M0S'.chr(10). // 10 minutes
'X-PRIMARY-CALENDAR:TRUE'.chr(10). // TODO
//'X-OWNER;CN="Hallvard NygÃ¥rd":mailto:hallvard.nygaard@jaermuseet.no'.chr(10). //TODO: Only on user-viewing
'PRODID:-//JMBooking//iCal Microsoft//NO'.chr(10).
'CALSCALE:GREGORIAN'.chr(10).
'X-WR-CALNAME:JM-booking - Bookingliste'.chr(10).
'X-WR-CALDESC:'.utf8_encode(strip_tags(filterToText($filters))).chr(10).
'VERSION:2.0'.chr(10).

'X-WR-RELCALID:'.md5(utf8_encode(strip_tags(filterToText($filters)))).chr(10).
'X-MS-OLK-WKHRSTART;TZID="W. Europe Standard Time":080000'.chr(10).
'X-MS-OLK-WKHREND;TZID="W. Europe Standard Time":170000'.chr(10).
'X-MS-OLK-WKHRDAYS:MO,TU,WE,TH,FR'.chr(10).
//'X-CALSTART:20100100T000000Z'.chr(10). // TODO
//'X-CALEND:20100709T110000Z'.chr(10); // TODO
'';

/*
echo 'BEGIN:VTIMEZONE'.chr(10).
	'TZID:W. Europe Standard Time'.chr(10).
	'BEGIN:STANDARD'.chr(10).
		'DTSTART:16011028T030000'.chr(10).
		'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10'.chr(10).
		'TZOFFSETFROM:+0200'.chr(10).
		'TZOFFSETTO:+0100'.chr(10).
	'END:STANDARD'.chr(10).
	'BEGIN:DAYLIGHT'.chr(10).
		'DTSTART:16010325T020000'.chr(10).
		'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3'.chr(10).
		'TZOFFSETFROM:+0100'.chr(10).
		'TZOFFSETTO:+0200'.chr(10).
	'END:DAYLIGHT'.chr(10).
'END:VTIMEZONE'.chr(10);*/

echo 'BEGIN:VTIMEZONE'.chr(10).
	'TZID:Europe/Oslo'.chr(10).
	'BEGIN:DAYLIGHT'.chr(10).
		'TZOFFSETFROM:+0100'.chr(10).
		'TZOFFSETTO:+0200'.chr(10).
		'DTSTART:19810329T020000'.chr(10).
		'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU'.chr(10).
		'TZNAME:CEST'.chr(10).
	'END:DAYLIGHT'.chr(10).
	'BEGIN:STANDARD'.chr(10).
		'TZOFFSETFROM:+0200'.chr(10).
		'TZOFFSETTO:+0100'.chr(10).
		'DTSTART:19961027T030000'.chr(10).
		'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU'.chr(10).
		'TZNAME:CET'.chr(10).
	'END:STANDARD'.chr(10).
'END:VTIMEZONE'.chr(10);

$entryObj = new EntryTemplate();
while($R = mysql_fetch_assoc($Q))
{
	$entry_array = getEntry($R['entry_id']);
	templateAssignEntry('entryObj', $entry_array);
	
	echo 'BEGIN:VEVENT'.chr(10).
	'SEQUENCE:'.$entryObj->rev_num.chr(10).
	'TRANSP:TRANSPARENT'.chr(10).
	'UID:JMBOOKING-'.$entryObj->entry_id.'-'.time().chr(10).
	
	/*
	'DTSTART:'.date('Ymd',$entryObj->time_start).'T'.date('His', $entryObj->time_start).'Z'.chr(10).
	'DTEND:'.date('Ymd',$entryObj->time_end).'T'.date('His', $entryObj->time_end).'Z'.chr(10).
	'CREATED:'.date('Ymd',$entryObj->time_created).'T'.date('His', $entryObj->time_created).chr(10).
	'DTSTAMP:'.date('Ymd').'T'.date('His').chr(10);
	*/
	'DTSTART;TZID=Europe/Oslo:'.date('Ymd',$entryObj->time_start).'T'.date('His', $entryObj->time_start).chr(10).
	'DTEND;TZID=Europe/Oslo:'.date('Ymd',$entryObj->time_end).'T'.date('His', $entryObj->time_end).chr(10).
	'CREATED;TZID=Europe/Oslo:'.date('Ymd',$entryObj->time_created).'T'.date('His', $entryObj->time_created).chr(10).
	'DTSTAMP;TZID=Europe/Oslo:'.date('Ymd').'T'.date('His').chr(10);
	
	
	//echo date('Y-m-d H:i', strtotime('2008-10-09 08:55 GMT'));
	
	/*
	$days = 
	echo 'DURATION:P0DT0H0M'.($entryObj->time_end - $entryObj->time_start).chr(10);
	*/
	
	echo 
	'SUMMARY:'.utf8_encode(htmlspecialchars_decode( 
				$entryObj->entry_name,
				ENT_QUOTES
			)).chr(10).
	'DESCRIPTION:'.
	$systemurl.'/entry.php?entry_id='.$entryObj->entry_id.' \n'.
	utf8_encode('BID: '. $entryObj->entry_id).'\n'.
	utf8_encode('Type: '. $entryObj->entry_type).'\n'.
	utf8_encode('Kunde: '. $entryObj->customer_name).'\n'.
	utf8_encode('Vert(er): '. $entryObj->user_assigned_names).'\n'.
	utf8_encode('Antall voksne: '. $entryObj->num_person_adult).'\n'.
	utf8_encode('Antall barn: '. $entryObj->num_person_child).'\n';
	
	if($entryObj->program_id_name != '')
		echo utf8_encode('Fast program: '. $entryObj->program_id_name).'\n';
	
	echo '\n'.
	utf8_encode('Programbeskrivelse:\n'.
		str_replace("\n", '\n', str_replace("\r", '',
			htmlspecialchars_decode( 
				$entryObj->program_description,
				ENT_QUOTES
			)
		))
	).'\n'.
	chr(10).
	
	'LOCATION:'.utf8_encode(implode(', ', $entryObj->rooms)).' ('.$entryObj->area.')'.chr(10).
	//'DTEND;VALUE=DATE:20100102'.chr(10).
	'END:VEVENT'.chr(10);
}


echo 'END:VCALENDAR'.chr(10);


?>