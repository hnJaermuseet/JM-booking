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
*}<h1>Faktura {$invoice_id}</h1>


<table>

<tr>
	<td align="right"><b>Beløp:</b> </td>
	<td>kr {$invoice_topay_total|commify:2:",":"&nbsp;"}</td>
</tr>

<tr>
	<td align="right"><b>Betalt:</b> </td>
	<td>kr {$invoice_payed_amount|commify:2:",":"&nbsp;"}</td>
</tr>

<tr>
	<td align="right"><b>Igjen å betale:</b> </td>
	<td>kr {$invoice_payment_left|commify:2:",":"&nbsp;"}
	{if $invoice_payment_left > 0}<a href="invoice_payment.php?invoice_id={$invoice_id}&amp;return=returnToInvoice">{iconHTML ico='coins_add'} Register betaling</a>{/if}
	</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='group'} <b>Kunde:</b> </td>
	<td>{if $invoice_to_customer_id == 0}<i>Ingen kunde valgt i databasen, se tilfelt i fakturaen</i>
	{else}<a href="customer.php?customer_id={$invoice_to_customer_id}">{$invoice_to_customer_name}</a> ({$invoice_to_customer_id}){/if}</td>
</tr>

<tr>
	<td align="right"><b>Kundens ref.:</b> </td>
	<td>{$invoice_ref_your}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='date'} <b>Fakturadato:</b> </td>
	<td>{$invoice_time2.day}.{$invoice_time2.month}.{$invoice_time2.year}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='date_delete'} <b>Forfallsdato:</b> </td>
	<td>{$invoice_time_due2.day}.{$invoice_time_due2.month}.{$invoice_time_due2.year}</td>
</tr>

<tr>
	<td align="right"><b>Opprettet av:</b> </td>
	<td><a href="user.php?user_id={$invoice_created_by_id}">{$invoice_created_by_name}</a></td>
</tr>

<tr>
	<td align="right"><b>Lenket til:</b> </td>
	<td>{if $invoice_idlinks2|@count > 0}
{foreach from=$invoice_idlinks2 item=linkarray}
- {if $linkarray.link != ''}<a href="{$linkarray.link}">{/if}{$linkarray.name}{if $linkarray.link != ''}</a>{/if}<br>
{/foreach}
{else}<i>-</i>
{/if}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='email'} <b>Adresse:</b> </td>
	<td>{$invoice_to_lines|nl2br}</td>
</tr>

<tr>
	<td align="right"><b>E-post</b> </td>
	<td>{$invoice_to_email}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='comment'} <b>Kommentar:</b> </td>
	<td>{$invoice_comment}</td>
</tr>

<tr>
	<td align="right"><b>Intern kommentar</b> </td>
	<td>{$invoice_internal_comment}</td>
</tr>

<tr>
	<td align="right"><b></b> </td>
	<td></td>
</tr>

</table>

- <a href="invoice/invoice{$invoice_id}.pdf">Last ned PDF-fil</a> ("Faktura")<br>
- <a href="invoice/invoice{$invoice_id}_copy.pdf">Last ned PDF-fil</a> ("Fakturakopi")<br>
<br>

<h2>Produktlinjer</h2>

<table width="100%">
 <tr>
  <th width="50px" bgcolor="#E0E0E0" align="center" style="font-size:xx-SMALL;"><b>Linjenr</b></th>
  <th bgcolor="#E0E0E0" align="center" style="font-size:xx-SMALL;"><b>Beskrivelse</b></th>
  <th width="50px" bgcolor="#E0E0E0" align="right" style="font-size:xx-SMALL;"><b>Stk.pris</b></th>
  <th width="50px" bgcolor="#E0E0E0" align="center" style="font-size:xx-SMALL;"><b>Antall</b></th>
  <th width="50px" bgcolor="#E0E0E0" align="center" style="font-size:XX-SMALL;"><b>MVA-sats</b></th>
  <th width="80px" bgcolor="#E0E0E0" align="right" style="font-size:xx-SMALL;"><b>Sum eks.mva</b></th>
  <th width="80px" bgcolor="#E0E0E0" align="right" style="font-size:xx-SMALL;"><b>Sum ink.mva</b></th>
 </tr>
{foreach from=$invoice_content key=linjenr item=innhold}
 <tr>
  <td align="center" style="font-size: small">{$linjenr}</td>
  <td align="center" style="font-size: small" width="250px">{$innhold.name|nl2br}</td>
  <td align="right" style="font-size: small">kr&nbsp;{$innhold.topay_each|commify:2:",":"&nbsp;"}</td>
  <td align="center" style="font-size: small">{$innhold.amount|commify:2:",":"&nbsp;"}</td>
  <td align="center" style="font-size: x-small">{math equation="x*100" x=$innhold.tax} %</td>
  <td align="right" style="font-size: small"><b>kr&nbsp;{$innhold.topay_total_net|commify:2:",":"&nbsp;"}</b></td>
  <td align="right" style="font-size: small"><b>kr&nbsp;{$innhold.topay_total|commify:2:",":"&nbsp;"}</b></td>
 </tr>
{/foreach}
</table>
<br><br>

<div align="right" style="font-size: x-small;">MVA-grunnlag: kr&nbsp;{$invoice_topay_total_net|commify:2:",":"&nbsp;"}<br>
+ MVA: kr&nbsp;{$invoice_topay_total_tax|commify:2:",":"&nbsp;"}</div>
<div align="right" style="font-size: medium;"><b>SUM Å BETALE: kr&nbsp;{$invoice_topay_total|commify:2:",":"&nbsp;"}</b></div>



<div class="hiddenprint">
<h2>Logg</h2>
<i>Loggen vises ikke enda. Kommer snart. Alt som blir gjort blir fremdeles logget.</i>
</div>