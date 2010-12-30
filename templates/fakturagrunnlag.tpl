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
	<title>Fakturagrunnlag</title>
	<link type="text/css" href="css/jm-booking.css" rel="stylesheet">
</head><body>
<div align="right">{$smarty.now|date_format:"%d-%m-%Y"}</div>
{$invoice_address|nl2br}
<h1 style="font-size: x-large;">Fakturagrunnlag p&aring; arrangement ved {$area} {$time_start|date_format:"%d-%m-%Y"}</h1>

{if $invoice_electronic}
<span style="font-size: 1.2em; color: red;" class="hiddenprint"><b>Faktura &oslash;nskes tilsendt per e-post til {$contact_person_email}</b></span>
<span style="font-size: 1.2em;" class="print"><b>Faktura &oslash;nskes tilsendt per e-post til {$contact_person_email}</b></span>
<br /><br />
{/if}

<b>Bookingid:</b> {$entry_id}<br>
<b>Arrangementstittel:</b> {$entry_title}<br>
<b>Tidrom:</b> {$time_start|date_format:"%A %d-%m-%Y %H:%M"} - {$time_end|date_format:"%d-%m-%Y %H:%M"}<br>
<b>Rom:</b> {$room}<br>
<b>Kontaktperson:</b> {$contact_person_name}<br>
<b>Referanse:</b> {$invoice_ref_your}<br>
<br><br>

<table class="entry_invoice">
 <tr>
  <th width="50px" align="center"><b>Lnr</b></th>
  <th align="left"><b>Beskrivelse</b></th>
  <th width="80px" align: right;"><b>Stk.pris<br />eks. mva</b></th>
  <th width="50px" align="center"><b>Antall</b></th>
  <th width="80px" align="right"><b>Sum eks.mva</b></th>
  <th width="50px" align="right"><b>MVA<br />-sats</b></th>
  <th width="80px" align="right"><b>Sum ink.mva</b></th>
 </tr>
{foreach from=$invoice_content key=linjenr item=innhold}
 <tr>
  <td align="center">{$linjenr}</td>
  <td align="left" width="250px">{$innhold.name|nl2br}</td>
  <td align="right">kr {$innhold.belop_hver|commify:2:",":" "}</td>
  <td align="center">{$innhold.antall}</td>
  <td align="right"><b>kr {$innhold.belop_sum_netto|commify:2:",":" "}</b></td>
  <td align="right"><b>{math equation="x*100" x=$innhold.mva} %</b></td>
  <td align="right"><b>kr {$innhold.belop_sum|commify:2:",":" "}</b></td>
 </tr>
{/foreach}
</table>

{if $mva_vis}<div align="right" style="font-size: x-small;">Sum eks mva: kr {$eks_mva_tot|commify:2:",":" "}<br>
+ MVA: kr {$faktura_belop_sum_mva}</div>{/if}
<div align="right" style="font-size: medium;"><b>Sum ink. mva: kr {$faktura_belop_sum|commify:2:",":" "}</b></div>

{if $mva_vis}
<br><br>
<table class="entry_invoice">
 <tr>
  <th>MVA-%</th>
  <th>Grunnlag</th>
  <th>MVA</th>
 </tr>
{foreach from=$mva key=mvaen item=mva_delsum}
 <tr>
  <td align="right">{$mvaen}&nbsp;%</td>
  <td align="right">kr&nbsp;{$mva_grunnlag.$mvaen|commify:2:",":" "}</td>
  <td align="right">kr&nbsp;{$mva_delsum|commify:2:",":" "}</td>
 </tr>
{/foreach}
 <tr>
  <td align="right">SUM</td>
  <td align="right">kr&nbsp;{$mva_grunnlag_sum|commify:2:",":" "}</td>
  <td align="right">kr&nbsp;{$faktura_belop_sum_mva|commify:2:",":" "}</td>
 </tr>
</table>{/if}

</body>