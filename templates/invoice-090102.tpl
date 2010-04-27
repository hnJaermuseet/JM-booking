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

<div style="font-size: x-small;"><b>Jærmuseet</b></div>
<div style="font-size: xx-small; position:fixed; top:55px;"><b>Adresse:</b> Postboks 250, 4367 NÆRBØ<br><b>Org.nr.:</b> NO 971 098 767 MVA<br><b>Banknr:</b> 123</div>
<div style="font-size: xx-small; position:fixed; top:55px; left:250px;"><b>Telefon: </b> (+47) 51 79 94 20<br><b>Telefaks: </b> (+47) 51 79 94 21<br><b>Nettside: </b> http://www.jaermuseet.no/</div>
<br>
<br>
<br>
<br>
{$invoice_to_lines|nl2br}

{if $invoice_to_email != ''}
<br><br>
E-post: {$invoice_to_email}{/if}

{if $invoice_ref_your != ''}
<br><br>
Deres referanse: {$invoice_ref_your}{/if}

<div style="position:fixed; right:10px; top:30px; font-size: xx-large;" align="right"><b>{$invoice_heading}</b></div>
<div style="position:fixed; right:10px; top:70px;" align="right">
<b>Fakturanr:</b> {$invoice_id}<br>
{if $invoice_to_customer_id != 0}
<b>Kundenr:</b> {$invoice_to_customer_id}<br>
<b>Kunde:</b> {$invoice_to_customer_name}<br>{/if}
<b>Fakturadato:</b> {$invoice_time2.day}.{$invoice_time2.month}.{$invoice_time2.year}<br>
<b>Forfallsdato:</b> {$invoice_time_due2.day}.{$invoice_time_due2.month}.{$invoice_time_due2.year}<br>
</div><br>
<i>Alle beløp er i NOK. Denne fakturaen er bare et eksempel på en data-generert faktura.</i>

<br><br><h2>Produkter i faktura:</h2>
<table width="100%">
 <tr>
  <th width="50px" bgcolor="#E0E0E0" align="center" style="font-size:xx-SMALL;"><b>Linjenr</b></th>
  <th bgcolor="#E0E0E0" align="center" style="font-size:xx-SMALL;"><b>Beskrivelse</b></th>
  <th width="50px" bgcolor="#E0E0E0" align="right" style="font-size:xx-SMALL;"><b>Stk.pris</b></th>
  <th width="50px" bgcolor="#E0E0E0" align="center" style="font-size:xx-SMALL;"><b>Antall</b></th>
  <th width="50px" bgcolor="#E0E0E0" align="center" style="font-size:XX-SMALL;"><b>MVA-sats</b></th>
  <th width="80px" bgcolor="#E0E0E0" align="right" style="font-size:xx-SMALL;"><b>Sum eks.mva</b></th>
 </tr>
{foreach from=$invoice_content key=linjenr item=innhold}
 <tr>
  <td align="center" style="font-size: small">{$linjenr}</td>
  <td align="center" style="font-size: small" width="250px">{$innhold.name|nl2br}</td>
  <td align="right" style="font-size: small">kr {$innhold.topay_each}</td>
  <td align="center" style="font-size: small">{$innhold.amount}</td>
  <td align="center" style="font-size: x-small">{math equation="x*100" x=$innhold.tax} %</td>
  <td align="right" style="font-size: small"><b>kr {$innhold.topay_total_net}</b></td>
 </tr>
{/foreach}
</table>
<br><br>

<div align="right" style="font-size: x-small;">MVA-grunnlag: kr {$invoice_topay_total_net}<br>
+ MVA: kr {$invoice_topay_total_tax}</div>
<div align="right" style="font-size: medium;"><b>SUM Å BETALE: kr {$invoice_topay_total}</b></div>
<br><br>
<h2>Betaling</h2>
<i>Ved overføring til bankkonto må du huske å merkere betalingen med fakturaid. Dette må til for at vi skal klare å identifisere betalingen.</i><br>
<b><i>Beløp:</i></b> {$invoice_topay_total} NOK<br>
<b><i>Til konto:</i></b> 123<br>
<b><i>Merker med:</i></b> {$invoice_id}<br>

<br><br>
<h2>Purregebyr</h2>
Lalalaa

<br><br>
<h2>Rentesats hvis forfallsdato ikke overholdes</h2>
kalala
</body></html>