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
{if 


$area == 'Vitenfabrikken'}
<div class="box3"><img src="img/Vitenfabrikken-logo.jpg" width="220"></div>
<div class="box4"><br>
<b>Bes&oslash;ksadresse:</b><br>
<br>
<b>Postadresse:</b><br>
<br>
<b>Org.nr.:</b><br>
<b>E-post:</b><br>
<b>Sentralbord:</b><br>
<b>Telefaks:</b><br>
<b>Nettside:</b></div>
<div class="box5"><br>
Storgata 28,<br>
4307 Sandnes<br>
Postboks 366,<br>
4303 Sandnes<br>
NO 971 098 767 MVA<br>
sandnes@jaermuseet.no<br>
(+47) 51 97 25 40<br>
(+47) 51 97 25 49<br>
http://www.jaermuseet.no/<br>
http://www.vitenfabrikken.no/
</div>
{elseif 



$area == 'Vitengarden'}
<div class="box3"><img src="img/Vitengarden-logo.jpg" width="220"></div>
<div class="box4"><br><br><br>
<b>Bes&oslash;ksadresse:</b><br>
<br>
<b>Postadresse:</b><br>
<br>
<b>Org.nr.:</b><br>
<b>E-post:</b><br>
<b>Sentralbord:</b><br>
<b>Telefaks:</b><br>
<b>Nettside:</b></div>
<div class="box5"><br><br><br>
Kviavegen 99,<br>
4367 N&aelig;rb&oslash;<br>
Postboks 250,<br>
4367 N&aelig;rb&oslash;<br>
NO 971 098 767 MVA<br>
post@jaermuseet.no<br>
(+47) 51 79 94 20<br>
(+47) 51 79 94 21<br>
http://www.jaermuseet.no/<br>
http://www.vitengarden.no/
</div>
{else


}
<div class="box3"><img src="img/JM-logo.jpg" width="220"></div>
<div class="box4"><br>
<b>Postadresse:</b><br>
<br>
<b>Org.nr.:</b><br>
<b>E-post:</b><br>
<b>Sentralbord:</b><br>
<b>Telefaks:</b><br>
<b>Nettside:</b></div>
<div class="box5"><br>
Postboks 250,<br>
4367 N&aelig;rb&oslash;<br>
NO 971 098 767 MVA<br>
post@jaermuseet.no<br>
(+47) 51 79 94 20<br>
(+47) 51 79 94 21<br>
http://www.jaermuseet.no/
</div>
{/if}
