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

if(!isset($_GET['confirm_id']))
	$confirm = array();
else
	$confirm = getConfirm($_GET['confirm_id']);

if(!count($confirm))
{
	print_header($day,$month,$year,$area);
	echo '<h1>'.__('Confirmation').'</h1>'.chr(10);
	echo __('Can not find confirmation from the confirmation ID you gave.');
	exit();
}

$entry = getEntry($confirm['entry_id']);
if(!count($entry))
{
	print_header($day,$month,$year,$area);
	echo '<h1>'.__('Confirmation').'</h1>'.chr(10);
	echo __('Error.');
	exit();
}

$user = getUser($confirm['user_id']);
if(!count($user))
{
	print_header($day,$month,$year,$area);
	echo '<h1>'.__('Confirmation').'</h1>'.chr(10);
	echo __('Error.');
	exit();
}

print_header($day,$month,$year,$area);
echo '<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>'.chr(10);
echo '<script src="js/hide_unhide.js" type="text/javascript"></script>'.chr(10);

echo '<h1>'.__('Confirmation').', '.$entry['entry_name'].'</h1>'.chr(10);
echo '- <a href="entry.php?entry_id='.$entry['entry_id'].'">'.__('Back to entry').'</a><br><br>';

echo '<table class="prettytable">'.chr(10);
echo '	<tr>'.chr(10);
echo '		<th>'.__('Entry').'</th>'.chr(10);
echo '		<td><a href="entry.php?entry_id='.$entry['entry_id'].'">'.$entry['entry_name'].'</a></td>'.chr(10);
echo '	</tr>'.chr(10);

echo '	<tr>'.chr(10);
echo '		<th>'.__('Sent').'</th>'.chr(10);
echo '		<td>'.date('H:i:s d.m.Y', $confirm['confirm_time']).'</td>'.chr(10);
echo '	</tr>'.chr(10);

echo '	<tr>'.chr(10);
echo '		<th>Sendingstype:</th>'.chr(10);
echo '		<td>';
	if($confirm['confirm_pdf'] == '1' && $confirm['confirm_pdffile'] != '') {
		echo iconFiletype('pdf').' e-post med bekreftelse i vedlegg (PDF)';
	}
	elseif($confirm['confirm_pdf'] == '1') {
		echo 'e-post med vedlegg (PDF ikke sendt pga. ingen mal ble gitt)';
	} else {
		echo 'e-post med bekreftelse i melding (ren tekst og HTML)';
	}
echo '</td>'.chr(10);
echo '	</tr>'.chr(10);

// TODO: Get a message if something failed (from entry-log)
echo '	<tr>'.chr(10);
echo '		<th>'.__('Sent to').'</th>'.chr(10);
echo '		<td>'.implode(', ', $confirm['confirm_to']).'</td>'.chr(10);
echo '	</tr>'.chr(10);

echo '	<tr>'.chr(10);
echo '		<th>'.__('Sent by').'</th>'.chr(10);
echo '		<td><a href="user.php?user_id='.$confirm['user_id'].'">'.$user['user_name'].'</a></td>'.chr(10);
echo '	</tr>'.chr(10).chr(10);

echo '	<tr>'.chr(10);
echo '		<th>'.__('Comment').'</th>'.chr(10);
echo '		<td>'.$confirm['confirm_comment'].'</td>'.chr(10);
echo '	</tr>'.chr(10).chr(10);

echo '	<tr>'.chr(10);
echo '		<th>Antall vedlegg:</th>'.chr(10);
echo '		<td>';
	if($confirm['confirm_pdf'] == '1' && $confirm['confirm_pdffile'] != '')
		echo count($confirm['confirm_usedatt']) + 1;
	else
		echo count($confirm['confirm_usedatt']);
echo '</td>'.chr(10);
echo '	</tr>'.chr(10).chr(10);

echo '</table>'.chr(10);

echo '<h2>'._h('E-mail content').':</h2>'.chr(10);
echo '<textarea cols="85" rows="10">'.$confirm['confirm_txt'].'</textarea>'.chr(10);
echo '<br><br>'.chr(10);

echo '<div class="showButton" id="buttonIdCONFIRMTPL">'.
	'<a href="javascript:void();">'._h('View / Don\'t view template for e-mail content').'</a></div>'.
	'<div class="showField" id="fieldIdCONFIRMTPL" style="display:none;">'.
					'<textarea cols="85" rows="15">'.$confirm['confirm_tpl'].'</textarea>'.
				'</div>'.chr(10);

if($confirm['confirm_pdf'] == '1')
{
	echo '<h2>F&oslash;lgende vedlegg var med bekreftelsen:</h2>';
	echo '<ul>';
	if($confirm['confirm_pdffile'] != '')
		echo '<li>'.
			'<a href="'.$entry_confirm_pdf_path.'/'.$confirm['confirm_pdffile'].'">'.
			iconFiletype('pdf').' '.$confirm['confirm_pdffile'].
			'</a></li>';
	foreach($confirm['confirm_usedatt'] as $att) {
		echo '<li>'.
			'<a href="'.$entry_confirm_att_path.'/'.$att['att_filename'].'">'.
			iconFiletype($att['att_filetype']).
			' '.$att['att_filename_orig'].
			'</a></li>'; 
	}
	echo '</ul>';
	
	echo '<div class="showButton" id="buttonIdCONFIRMPDFTXT">'.
		'<a href="javascript:void();">'._h('View / Don\'t view PDF content as plain text').'</a></div>'.
		'<div class="showField" id="fieldIdCONFIRMPDFTXT" style="display:none;">'.
						'<textarea cols="85" rows="15">'.$confirm['confirm_pdf_txt'].'</textarea>'.
					'</div>'.chr(10);
	echo '<div class="showButton" id="buttonIdCONFIRMPDFTPL">'.
		'<a href="javascript:void();">'._h('View / Don\'t view template for PDF content').'</a></div>'.
		'<div class="showField" id="fieldIdCONFIRMPDFTPL" style="display:none;">'.
						'<textarea cols="85" rows="15">'.$confirm['confirm_pdf_tpl'].'</textarea>'.
					'</div>'.chr(10);
}