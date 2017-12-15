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
	Admin-include - middel/menu
*/


print_header($day, $month, $year, $area);

//layout
echo '<h2>'.__("Administration").'</h2>'.chr(10);

echo '<a href="admin_user2.php"';
if($section=="users") echo "style='color:red'";
echo '>'.__('Users').'</a> -:- ';

echo '<a href="admin_group.php"';
if($section=="groups") echo "style='color:red'";
echo '>'.__('Usergroups').'</a> -:- ';

echo '<a href="admin_products.php"';
if($section=="products") echo "style='color:red'";
echo '>Produktregister</a> -:- ';

echo '<a href="admin_programs.php"';
if($section=="programs") echo "style='color:red'";
echo '>'.__('Fixed programs').'</a> -:- ';

echo '<a href="admin_area.php"';
if($section=="area") echo "style='color:red'";
echo '>'.__('Area').'</a> -:- ';

echo '<a href="admin_entry_type.php"';
if($section=="entry_type") echo "style='color:red'";
echo '>'.__('Entrytype').'</a> -:- ';

echo '<a href="admin_template.php"';
if($section=="template") echo "style='color:red'";
echo '>'.__('Templates').'</a> -:- ';

echo '<a href="admin_attachment.php"';
if($section=="attachment") echo "style='color:red'";
echo '>Vedlegg</a> -:- ';

echo '<a href="admin_customer_merge.php"';
if($section=="customer_merge") echo "style='color:red'";
echo '>Kundeopprydning</a> -:- ';

echo '<a href="admin_import_dn.php"';
if($section=="import_dn") echo "style='color:red'";
echo '>Statistikkimport</a> -:- ';

echo '<a href="entry_list.php?listtype=deleted"';
echo '>Slettede bookinger</a> -:- ';

echo '<hr>'.chr(10);
