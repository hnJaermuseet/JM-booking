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

include "include/admin_top.php";

//fake register globals, its save in the admin-only section
foreach($_GET as $key=>$val){
	$$key=$val;
}


//hier saven/endern
$area_error="";
$category_error="";
if(isset($submit)){
	
}
if(isset($del_type)) {
	
}

if(isset($_GET['section']))
	$section = $_GET['section'];
else
	$section = '';
if(isset($_GET['area']))
	$section="areas";
if(isset($pid))
	$section="periods";

include "include/admin_middel.php";

?>