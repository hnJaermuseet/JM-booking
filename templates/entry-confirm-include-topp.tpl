{*
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
*}<html>
<head>
</head>

<body>
<style type="text/css">
{fetch file="css/entry-confirm_pdf.css"}
</style>

<div class="box3">
{if $area_id == '6'}
    <img src="img/Vitenfabrikken-logo.jpg" width="220">
{elseif $area_id == '12'}
    <img src="img/Vitengarden-logo.jpg" width="220">
{elseif $area_id == '11'}
    <img src="img/logo-flyhistorisk_museum-220x101.png" width="220">
{elseif $area_id == '16'}
    <img src="img/logo-garborgsenteret-220x78.png" width="220">
{elseif $area_id == '9'}
    <img src="img/logo-grodaland-220x98.jpg" width="220">
{elseif $area_id == '8'}
    <img src="img/logo-haugabakka-220x98.jpg" width="220">
{elseif $area_id == '17'}
    <img src="img/logo-krigshistorisk_museum-220x101.png" width="220">
{elseif $area_id == '7'}
    <img src="img/logo-limagarden-220x98.jpg" width="220">
{elseif $area_id == '15'}
    <img src="img/logo-vistnestunet-220x81.jpg" width="220">
{else}
    <img src="img/JM-logo.jpg" width="220">
{/if}
</div>
