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

$section = 'invoiced';
require "include/invoice_menu.php";

echo '<h1 style="margin-bottom: 0px;">Fakturagrunnlag</h1>';

echo '<i>F�lgende samle-PDFer med fakturagrunnlag er laget:</i>';

$Q_invoiced = db()->prepare("select * from `invoiced` order by `created` desc");
$Q_invoiced->execute();

if($Q_invoiced->rowCount() > 0)
{
	echo '<table class="prettytable">';
	
	echo '<tr>'.
			'<th>Tid</th>'.
			'<th>Bookinger</th>'.
			'<th>Last ned</th>'.
			'<th>Sendt til</th>'.
		'</tr>'.chr(10);
	
	while($R_invoiced = $Q_invoiced->fetch(PDO::FETCH_ASSOC))
	{
		// Emails
		$sendto = '';
		if($R_invoiced['emailed'] == '1')
		{
			$Q_emails = db()->prepare("select * from `invoiced_emails` where `invoiced_id` = '".$R_invoiced['invoiced_id']."'");
            $Q_emails->execute();
			$sendto = array();
			while($R_emails = $Q_emails->fetch(PDO::FETCH_ASSOC))
			{
				$sendto[] = $R_emails['email_addr'];
			}
			$sendto = implode($sendto, ',<br />');
		}
		
		// Entries
		$Q_entries = db()->prepare("
			SELECT entry . *
				FROM 
					`entry_invoiced`
				LEFT JOIN
					`entry`
				ON
					`entry_invoiced`.entry_id = `entry`.entry_id
				WHERE
					`entry_invoiced`.invoiced_id = '".$R_invoiced['invoiced_id']."';
			");
        $Q_entries->execute();
		$entries = array();
		while($R_entry = $Q_entries->fetch(PDO::FETCH_ASSOC))
		{
			$entries[] = '<a href="entry.php?entry_id='.$R_entry['entry_id'].'">'.
				iconHTML('page_white_star').' ('.$R_entry['entry_id'].') '.$R_entry['entry_title'].'</a>';
		}
		$entries = implode($entries, ',<br />');
		
		$pdf_link = '';
		if($R_invoiced['pdf_name'] != '')
		{
			$pdf_link = '<a href="'.$invoice_location.$R_invoiced['pdf_name'].'">'.iconFiletype('pdf').' '.$R_invoiced['pdf_name'].'</a>';
		}
		
		// Highlight
		$highlighted = '';
		if(isset($_GET['highlight']) && $_GET['highlight'] == $R_invoiced['invoiced_id'])
			$highlighted = ' class="notice"';
		
		echo '<tr'.$highlighted.'>'.
				'<td>'.date('d-m-Y H:i:s', $R_invoiced['created']).'</td>'.
				'<td>'.$entries.'</td>'.
				'<td>'.$pdf_link.'</td>'.
				'<td>'.$sendto.'</td>'.
			'</tr>'.chr(10);
	}
	echo '</table>';
}