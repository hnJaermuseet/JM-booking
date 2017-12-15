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
	Merging of customers
*/

$section = 'customer_merge';

include "include/admin_top.php";

if(
	isset($_GET['cid1']) && is_numeric($_GET['cid1']) &&
	isset($_GET['cid2']) && is_numeric($_GET['cid2'])
) // Merge
{	
	$cid1 = $_GET['cid1'];
	$cid2 = $_GET['cid2'];
	
	$customer1 = getCustomer($cid1);
	$customer2 = getCustomer($cid2);
	
	if(!count($customer1) || $customer1['slettet']) {
		echo 'Finner ikke kunde 1. ID: '.$cid1; exit();
	}
	
	if(!count($customer2) || $customer2['slettet']) {
		echo 'Finner ikke kunde 2. ID: '.$cid2; exit();
	}
	
	filterMakeAlternatives();
	
	if(isset($_GET['mergego'])) {
		include "include/admin_middel.php";
		
		echo '<h1>Kundeopprydding</h1>';
		
		$Q_entries = db()->prepare("update `entry` set `customer_id` = :customer_id1 where `customer_id` = :customer_id2");
        $Q_entries->bindValue(':customer_id1', $customer1['customer_id'], PDO::PARAM_INT);
        $Q_entries->bindValue(':customer_id2', $customer2['customer_id'], PDO::PARAM_INT);
		$Q_entries->execute();

		echo 'Flyttet bookinger til kundeid '.$customer1['customer_id'].': <span style="color: green">OK, '.$Q_entries->rowCount().' bookinger flyttet</span><br>';
		$Q_customer = db()->prepare("update `customer` set `slettet` = '1' where `customer_id` = :customer_id2");
        $Q_customer->bindValue(':customer_id2', $customer2['customer_id'], PDO::PARAM_INT);
		$Q_customer->execute();
		echo 'Slette kundeid '.$customer2['customer_id'].': <span style="color: green">OK</span><br>';
		echo '<br><a href="admin_customer_merge.php">Tilbake til kundeopprydning</a>';
		exit();
	}
	
	
	include "include/admin_middel.php";
	
	echo '<h1>Kundeopprydding</h1>';
	
	echo '<a href="'.$_SERVER['PHP_SELF'].'?cid1='.$customer2['customer_id'].
		'&amp;cid2='.$customer1['customer_id'].'">Bytt hvilken kunde som beholdes</a>';
	echo '<table class="prettytable">'.
		'<tr>'.
			'<th>&nbsp;</td>'.
			'<th>Kunde 1 (beholdes)</th>'.
			'<th>Kunde 2 <span style="color:red;">(slettes)</span></th>'.
		'</tr>';
	echo '<tr>'.
			'<th>Valg</td>'.
			'<td><a href="customer_edit.php?customer_id='.$customer1['customer_id'].'&returnToCustomerView=1">Endre</a></td>'.
			'<td><a href="customer_edit.php?customer_id='.$customer2['customer_id'].'&returnToCustomerView=1">Endre</a></td>'.
		'</tr>';
	echo '<tr>'.
			'<th>Navn</td>'.
			'<td>'.$customer1['customer_name'].'</td>'.
			'<td style="color:red;">'.$customer2['customer_name'].'</td>'.
		'</tr>';
	echo '<tr>'.
			'<th>Kommune</td>'.
			'<td>('.$customer1['customer_municipal_num'].') '.$customer1['customer_municipal'].'</td>'.
			'<td style="color:red;">('.$customer2['customer_municipal_num'].') '.$customer2['customer_municipal'].'</td>'.
		'</tr>';
	echo '<tr>'.
			'<th>Type</td>'.
			'<td>'; if($customer1['customer_type'] == 'person') echo 'privatperson'; else echo 'Skole, firma, organisasjon, osv'; echo '</td>'.
			'<td style="color:red;">'; if($customer2['customer_type'] == 'person') echo 'privatperson'; else echo 'Skole, firma, organisasjon, osv'; echo '</td>'.
		'</tr>';
	echo '<tr>'.
			'<th>Telefonnr.</td>'.
			'<td><ul>';
	foreach($customer1['customer_phone'] as $phone) {
		echo '<li>'.$phone['phone_name'].' - '.$phone['phone_num'].'</li>';
	}
	echo '</ul></td>'.
			'<td><ul>';
	foreach($customer2['customer_phone'] as $phone) {
		echo '<li>'.$phone['phone_name'].' - '.$phone['phone_num'].'</li>';
	}
	echo '</ul></td>'.
		'</tr>';
	echo '<tr>'.
			'<th>Adresser</td>'.
			'<td><ul>';
	foreach($customer1['customer_address'] as $address) {
		echo '<li>'.$address['address_info'].':<br>'.nl2br($address['address_full']).'</li>';
	}
	echo '</ul></td>'.
			'<td><ul>';
	foreach($customer2['customer_address'] as $address) {
		echo '<li>'.$address['address_info'].':<br>'.nl2br($address['address_full']).'</li>';
	}
	echo '</ul></td>'.
		'</tr>';
	echo '<tr>'.
			'<th>Booking(er)<br>(beholdes)</td>'.
			'<td>';
	$filters = array();
	$filters = addFilter($filters, 'customer_id', $customer1['customer_id']);
	$SQL = genSQLFromFilters($filters, 'entry_id').' order by time_start';
	$Q_next_entries = db()->prepare($SQL);
	$Q_next_entries->execute();
	
	if($Q_next_entries->rowCount() <= 0) {
        echo '<i>Ingen</i>' . chr(10);
    }
	else
	{
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td class="border"><b>'.__('Starts').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Name').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Where').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Contact person').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Phone').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('E-mail').'</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R_entry = $Q_next_entries->fetch())
		{
			$entry = getEntry($R_entry['entry_id']);
			if(count($entry))
			{
				echo ' <tr>'.chr(10);
				echo '  <td class="border"><b>'.date('d-m-Y H:i', $entry['time_start']).'</b></td>'.chr(10);
				echo '  <td class="border"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.$entry['entry_name'].'</a></td>'.chr(10);
				echo '  <td class="border">';
				$area = getArea($entry['area_id']);
				if(count($area))
					echo $area['area_name'].' - ';
				$rooms = array();
				foreach ($entry['room_id'] as $rid)
				{
					if($rid == '0')
						$rooms[] = __('Whole area');
					else
					{
						$room = getRoom($rid);
						if(count($room))
							$rooms[] = $room['room_name'];
					}
				}
				echo implode(', ', $rooms);
				echo '</td>'.chr(10);
				echo '  <td class="border">'.$entry['contact_person_name'].'</td>'.chr(10);
				echo '  <td class="border">'.$entry['contact_person_phone'].'</td>'.chr(10);
				echo '  <td class="border">'.$entry['contact_person_email'].'</td>'.chr(10);
				echo ' </tr>'.chr(10);
			}
		}
		echo '</table>'.chr(10);
	}
	
	echo '</td>'.
			'<td>';
	$filters = array();
	$filters = addFilter($filters, 'customer_id', $customer2['customer_id']);
	$SQL = genSQLFromFilters($filters, 'entry_id').' order by time_start';
	$Q_next_entries = db()->prepare($SQL);
	$Q_next_entries->execute();
	
	if($Q_next_entries->rowCount() <= 0) {
        echo '<i>Ingen</i>' . chr(10);
    }
	else
	{
		echo '<span style="color:green">Flyttes til kunde 1:</span><br>';
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td class="border"><b>'.__('Starts').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Name').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Where').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Contact person').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('Phone').'</b></td>'.chr(10);
		echo '  <td class="border"><b>'.__('E-mail').'</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R_entry = $Q_next_entries->fetch())
		{
			$entry = getEntry($R_entry['entry_id']);
			if(count($entry))
			{
				echo ' <tr>'.chr(10);
				echo '  <td class="border"><b>'.date('d-m-Y H:i', $entry['time_start']).'</b></td>'.chr(10);
				echo '  <td class="border"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.$entry['entry_name'].'</a></td>'.chr(10);
				echo '  <td class="border">';
				$area = getArea($entry['area_id']);
				if(count($area)) {
                    echo $area['area_name'] . ' - ';
                }
				$rooms = array();
				foreach ($entry['room_id'] as $rid)
				{
					if($rid == '0') {
                        $rooms[] = __('Whole area');
                    }
					else
					{
						$room = getRoom($rid);
						if(count($room))
							$rooms[] = $room['room_name'];
					}
				}
				echo implode(', ', $rooms);
				echo '</td>'.chr(10);
				echo '  <td class="border">'.$entry['contact_person_name'].'</td>'.chr(10);
				echo '  <td class="border">'.$entry['contact_person_phone'].'</td>'.chr(10);
				echo '  <td class="border">'.$entry['contact_person_email'].'</td>'.chr(10);
				echo ' </tr>'.chr(10);
			}
		}
		echo '</table>'.chr(10);
	}
	echo '</td>'.
		'</tr>';
	echo '</table>';
	
	
	echo '<form action="admin_customer_merge.php" method="get">'.chr(10).
		
		'<input type="text" disabled="disabled" value="'.$customer1['customer_id'].'"> - Kunde 1, denne beholdes<br>'.
		'<input type="text" disabled="disabled" value="'.$customer2['customer_id'].'"> - Kunde 2, denne slettes<br><br>'.
		'<input type="hidden" name="cid1" value="'.$customer1['customer_id'].'">'.
		'<input type="hidden" name="cid2" value="'.$customer2['customer_id'].'">'.
		'<input type="hidden" name="mergego" value="yes">'.
		'<input type="submit" value="Slett kunde 2 og flytt over bookingene til kunde 1">'.
	'</form>';
	
}
else
{
	include "include/admin_middel.php";
	
	echo '<h1>Kundeopprydding</h1>';
	echo '<form action="admin_customer_merge.php" method="get">'.chr(10).
		'<i>Tast inn <b>kundeid</b> p&aring; to kunder du vil sl&aring; sammen. '.
		'Merk at du m&aring; flytte over adresser og andre opplysninger manuelt. '.
		'Kun bookinger blir flyttet.</i><br><br>'.
		
		'<input type="text" name="cid1"> - Kunde 1, denne <b>beholdes</b><br>'.
		'<input type="text" name="cid2"> - Kunde 2, denne <b>slettes</b><br><br>'.
		'<input type="submit" value="Neste">'.
	'</form>';
	
	echo '<br><br>';
	
	echo '<table class="prettytable">';
	echo '<tr>'.
			'<th>To eller flere kunder heter</th>'.
			'<th>Kunde 1, denne beholdes</th>'.
			'<th>Kunde 2, denne slettes</th>'.
			'<th>&nbsp;</th>'.
		'</tr>';
	$Q = db()->prepare("
			SELECT customer_id, customer_name 
			FROM `customer` 
			WHERE `slettet` = '0' 
			ORDER BY customer_name
		");
    $Q->execute();
	$last = '';
	$last_id = 0;
	while($R = $Q->fetch())
	{
		if(strtolower($last) == strtolower($R['customer_name']))
		{
			echo '<tr><form action="admin_customer_merge.php" method="get">'.chr(10).
				'<td><b>'.$last.'</b></td>'.
				'<td><input type="text" name="cid1" value="'.$last_id.'"></td>'.
				'<td><input type="text" name="cid2" value="'.$R['customer_id'].'"></td>'.
				'<td><input type="submit" value="Slett kunde 2 og flytt over bookingene til kunde 1"></td>'.
			'</form></tr>';
		}
		$last = $R['customer_name'];
		$last_id = $R['customer_id'];
	}
	
	echo '</table>';
}

echo '</td>
</tr>
</table>
</HTML>';