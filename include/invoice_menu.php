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
	Invoice-include - middel/menu
*/


print_header($day, $month, $year, $area);

//layout
echo '<div class="hiddenprint">';
echo '<h2>'._("Invoice").'</h2>'.chr(10);

echo '<a href="invoice_main.php"';
if($section=="main") echo "style='color:red'";
echo '>'._('Main page').'</a> -:- ';

echo '<a href="invoice_create.php"';
if($section=="create") echo "style='color:red'";
echo '>'._('Create blank').'</a> -:- ';

echo '<a href="invoice_soon.php"';
if($section=="soon") echo "style='color:red'";
echo '>Ikke gjennomført</a> ('.$num_invoice_soon.') -:- ';

echo '<a href="invoice_tobemade.php"';
if($section=="tobemade") echo "style='color:red'";
echo '>Ikke klargjort</a> ('.$num_invoice_tobemade.') -:- ';

echo '<a href="invoice_tobemade_ready.php"';
if($section=="tobemade_ready") echo "style='color:red'";
echo '>Klar til eksportering</a> ('.$num_invoice_tobemade_ready.') -:- ';

echo '<a href="invoice_exported.php"';
if($section=="exported") echo "style='color:red'";
echo '>Allerede eksportet</a> ('.$num_invoice_exported.') -:- ';

echo '<hr>'.chr(10);
echo '</div>';

echo '<div class="print" style="font-size: 8px"><i>Generert '.date('H:i:s d-m-Y').', '.$login['user_name'].'</i></div>';

?>