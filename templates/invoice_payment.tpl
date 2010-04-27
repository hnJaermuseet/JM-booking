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
*}<h1>Register betaling for faktura</h1>

- <a href="{$return}">Tilbake</a><br><br>

<i>Tast inn beløp som ble betalt og når det ble betalt. Betaling av faktura loggføres til fakturaen og eventuelle bookinger den er koblet til.</i><br><br>


<form action="{$SCRIPT_NAME}?invoice_id={$invoice_id}&amp;return={$return2}&amp;reg_payment=1" method="post">
<table class="fakturainfo">
	
	<tr class="fakturainfo">
		<td class="fakturainfo"><b>Fakturaid:</b></td>
		<td class="fakturainfo"><input type="text" disabled="disabled" value="{$invoice_id}"></td>
	</tr>
	
	<tr class="fakturainfo">
		<td class="fakturainfo"><b>Kunde:</b></td>
		<td class="fakturainfo">
			<input size="3" type="text" disabled="disabled" value="{$invoice_to_customer_id}">
			<input type="text" disabled="disabled" value="{$invoice_to_customer_name}">
		</td>
	</tr>
	
	<tr class="fakturainfo">
	<td class="fakturainfo"><b>Beløp:</b></td>
	<td class="fakturainfo"><input type="text" name="payment_amount" value="{$invoice_payment_left}"></td>
	</tr>
	
	<tr class="fakturainfo">
		<td class="fakturainfo"><b>Dato:</b></td>
		<td class="fakturainfo"><input type="text" name="payment_time" value="{$smarty.now|date_format:"%d-%m-%Y"}"> (format: dd-mm-yyyy)</td>
	</tr>
	
	<tr class="fakturainfo">
		<td class="fakturainfo"><b>Kommentar:</b></td>
		<td class="fakturainfo"><input type="text" name="payment_comment" value="" size="30"> (frivillig)</td>
	</tr>
	
	<tr class="fakturainfo">
		<td class="fakturainfo">&nbsp;</td>
		<td class="fakturainfo"><input type="submit" value="Register betaling"></td>
	</tr>

</table>
</form>
<br><br>