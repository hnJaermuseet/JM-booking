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

// Errors.php
//
// Alle errorer som blir brukt i skript

$error		= array();
$error[0]	= 'Ingen error funnet';
$error[1]	= 'MySQL-error'; // Spesial error som oppstår når MySQL feiler
$error[2]	= 'Sending av mail feilet.';

// Error 100 til og med 199 = faktura.class.php
$error[100]	= 'Det oppsto feil med dataene sendt til server.';
$error[101]	= 'ID til fakturaen er ikke oppgitt eller den er ikke et tall.';
$error[102] = 'Finner ikke fakturaen.';
$error[103] = 'Beløpet er ikke ett tall.';
$error[104] = 'Beløpet er 0. Ingen betaling registert.';
$error[105] = 'Betalingstypen er ikke-eksisterende.';
$error[106] = 'Ugyldig dato for betalingstidspunktet.';
$error[107] = 'Det kan ikke registeres minus-betalinger.';


/*

// FUNKSJONEN SOM BLIR SATT INN I KLASSER:
var $error_code = 0;
function error()
{
	// Returnerer siste error
	
	include "config/errors.php"; // Hent inn alle error meldinger
	
	if(array_key_exists($this->error_code,$error))
	{
		// Erroren som denne klassen har lagt finnes
		
		if($this->error_code == '1')
		{
			// Spesial error for MySQL
			return 'MySQL error nr '.mysql_errno().': '.mysql_error();
		}
		else	return $error[$this->error_code];
	}
	else
	{
		// Erroren eksisterer ikke i konfigrasjonen
		return 'Ukjent error';
	}
}

*/

?>