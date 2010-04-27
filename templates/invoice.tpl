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
{if isset($invoice_css)}{fetch file="templates/$invoice_css"}{else}{fetch file="templates/invoice_css_pdf.css"}{/if}
</style>

<div class="box3"><img src="img/JM-logo.jpg" width="150"></div>
<div class="box4"><br>
<b>Adresse:</b><br>
<b>Org.nr.:</b><br>
<b>Banknr:</b><br>
<b>Telefon:</b><br>
<b>Telefaks:</b><br>
<b>Nettside:</b></div>
<div class="box5"><br>
Postboks 250, 4367 NÆRBØ<br>
NO 971 098 767 MVA<br>
123<br>
(+47) 51 79 94 20<br>
(+47) 51 79 94 21<br>
http://www.jaermuseet.no/
</div>
<div class="box1" align="right"><b>Test. {$invoice_heading}</b></div>
<div class="box2" align="right">
<b>Fakturanr:</b> {$invoice_id}<br>
{if $invoice_to_customer_id != 0}
<b>Kundenr:</b> {$invoice_to_customer_id}<br>{/if}
<b>Fakturadato:</b> {$invoice_time2.day}.{$invoice_time2.month}.{$invoice_time2.year}<br>
<b>Forfallsdato:</b> {$invoice_time_due2.day}.{$invoice_time_due2.month}.{$invoice_time_due2.year}<br>
</div>

<div style="width: 520px">
<br>
<br>
<br>
<br>
{$invoice_to_lines3|nl2br}

{if $invoice_to_email != ''}<br><br>
E-post: {$invoice_to_email}{/if}
{if $invoice_ref_your != ''}<br><br>
Deres referanse: {$invoice_ref_your}{/if}
{if $invoice_comment != ''}<br><br><br>
{$invoice_comment|nl2br}{/if}
</div>
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
  <td align="right" style="font-size: small">kr&nbsp;{$innhold.topay_each|commify:2:",":"&nbsp;"}</td>
  <td align="center" style="font-size: small">{$innhold.amount|commify:2:",":"&nbsp;"}</td>
  <td align="center" style="font-size: x-small">{math equation="x*100" x=$innhold.tax} %</td>
  <td align="right" style="font-size: small"><b>kr&nbsp;{$innhold.topay_total_net|commify:2:",":"&nbsp;"}</b></td>
 </tr>
{/foreach}
</table>
<br><br>

<div align="right" style="font-size: x-small;">MVA-grunnlag: kr&nbsp;{$invoice_topay_total_net|commify:2:",":"&nbsp;"}<br>
+ MVA: kr {$invoice_topay_total_tax|commify:2:",":"&nbsp;"}</div>
<div align="right" style="font-size: medium;"><b>SUM Å BETALE: kr&nbsp;{$invoice_topay_total|commify:2:",":"&nbsp;"}</b></div>
<br><br>

{*
<h2>Betaling</h2>
<i>Ved overføring til bankkonto må du huske å merkere betalingen med fakturaid. Dette må til for at vi skal klare å identifisere betalingen.</i><br>
<b><i>Beløp:</i></b> {$invoice_topay_total|commify:2:",":"&nbsp;"} NOK<br>
<b><i>Til konto:</i></b> 123<br>
<b><i>Merker med:</i></b> {$invoice_id}<br>

<br><br>
<h2>Gebyr</h2>
- Purregebyr<br>
- Rentesats hvis forfallsdato ikke overholdes
*}
<div class="giro1">K o n t o t i l - Jærmuseeet</div>
<div class="giro2" align="right">{$invoice_topay_total|commify:2:",":"&nbsp;"}</div>
<div class="giro4"><br>
Fakturaid: {$invoice_id}<br>
Fakturadato: {$invoice_time2.day}.{$invoice_time2.month}.{$invoice_time2.year|substr:2:2}<br>
{if $invoice_to_customer_id != 0}Kundenr: {$invoice_to_customer_id}<br>{/if}</div>
<div class="giro3">{$invoice_time_due2.day}.{$invoice_time_due2.month}.{$invoice_time_due2.year|substr:2:2}</div>
<div class="giro5"><br>
{$invoice_to_lines|nl2br}</div>
<div class="giro6">Jærmuseet<br>Postboks 250<br>4367 NÆRBØ</div>
<div class="giro7" align="right">KID ? ? ? ? ? ? ? ? ?</div>
<div class="giro8" align="right">{$invoice_topay_total|commify:2:"&nbsp; &nbsp;":""}</div>
<div class="giro9" align="right">K o n t o t i l - Jærmuseeet</div>
</body></html>