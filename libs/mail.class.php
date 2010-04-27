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

// Mailsending...
//
// Class som bruker PHPmailer for sending av mail


require "libs/phpmailer/class.phpmailer.php";

class mail extends PHPMailer
{
	// Settings
	var $From			= 'hn@jaermuseet.no';
	var $FromName		= 'Bookingsystemet';
	var $Sender			= 'hn@jaermuseet.no';
	
	var $WordWrap		= 70; // Ny linje etter 70 tegn
	var $ContentType	= 'text/html'; // Sender HTML meldinger
	
	//var $Mailer			= 'sendmail'; // Bruker sendmail
	var $Mailer			= 'mail';
	
	function mail()
	{
		$PHPMAILER_LANG = array();
		
		$PHPMAILER_LANG["provide_address"] = 'Du må ha med minst en' .
		                                     'mottager adresse.';
		$PHPMAILER_LANG["mailer_not_supported"] = ' mailer er ikke supportert.';
		$PHPMAILER_LANG["execute"] = 'Kunne ikke utføre: ';
		$PHPMAILER_LANG["instantiate"] = 'Kunne ikke instantiate mailfunksjonen.';
		$PHPMAILER_LANG["authenticate"] = 'SMTP Feil: Kunne ikke authentisere.';
		$PHPMAILER_LANG["from_failed"] = 'Følgende Fra feilet: ';
		$PHPMAILER_LANG["recipients_failed"] = 'SMTP Feil: Følgende' .
		                                       'mottagere feilet: ';
		$PHPMAILER_LANG["data_not_accepted"] = 'SMTP Feil: Data ble ikke akseptert.';
		$PHPMAILER_LANG["connect_host"] = 'SMTP Feil: Kunne ikke koble til SMTP host.';
		$PHPMAILER_LANG["file_access"] = 'Kunne ikke få tilgang til filen: ';
		$PHPMAILER_LANG["file_open"] = 'Fil feil: Kunne ikke åpne filen: ';
		$PHPMAILER_LANG["encoding"] = 'Ukjent encoding: ';
		
		$this->language = $PHPMAILER_LANG;
	}
}

?>