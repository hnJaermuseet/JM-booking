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
*}{* Denne templaten blir inkludert av andre.
 * Innholder tabell-rader med fakturainformasjon. Variabler som er definert skal være fra entry
 *}
 
<tr>
	<td align="right">{iconHTML ico='coins'} <b>Faktura?</b> </td>
	<td>{$invoice3|ucfirst}</td>
</tr>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>Fakturastatus:</b> </td>
	<td>{$invoice_status2}
		{if $linkview}
			{if $invoice_status == 1 && ($user_invoice || $user_invoice_setready)} (<a href="invoice_setready.php?entry_id={$entry_id}">sett til faktureringsklar</a>){/if}
			{if $invoice_status == 2 && $user_invoice} (<a href="invoice_tobemade_ready.php">opprett faktura</a>){/if}
		{/if}
	</td>
</tr>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right">{iconHTML ico='group'} <b>Kunde:</b> </td>
	<td>
		{if $customer_id == '0'}
			<span style="color: gray;"><i>Ingen valgt</i></span>
		{else}
	<a href="customer.php?customer_id={$customer_id}">{$customer_name}</a> (Kundeid {$customer_id}){/if}</td>
</tr{if !$invoice2} class="invoiceGray"{/if}>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>Kommentar:</b> </td>
	<td>{$invoice_comment|nl2br}</td>
</tr{if !$invoice2} class="invoiceGray"{/if}>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>Intern kommentar:</b> </td>
	<td>{$invoice_internal_comment|nl2br}</td>
</tr>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>E-sending:</b> </td>
	<td>{$invoice_electronic3|ucfirst}</td>
</tr>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>E-post:</b> </td>
	<td>{$invoice_email}</td>
</tr{if !$invoice2} class="invoiceGray"{/if}>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>Adresse:</b> </td>
	<td>{$invoice_address|nl2br}</td>
</tr>

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>Deres referanse:</b> </td>
	<td>{$invoice_ref_your}</td>
</tr>

{* ## PRODUKTLINJER ## *}
<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>Produktlinjer:</b> </td>
	<td>
	{if !$invoice_content|count}
		<span style="color: gray;"><i>Ingen linjer</i></span>
	{else}
		<table width="700" style="border-collapse: collapse;">
			<tr{if !$invoice2} class="invoiceGray"{/if}>
				<td class="border"><b>Linjenr</b></td>
				<td class="border"><b>Beskrivelse</b></td>
				<td class="border"><b>Stk.pris&nbsp;eks.mva</b></td>
				<td class="border"><b>Antall</b></td>
				<td class="border"><b>Sum&nbsp;eks.mva</b></td>
				<td class="border"><b>MVA-sats</b></td>
				<td class="border"><b>Sum&nbsp;ink.mva</b></td>
			</tr>
			
			{foreach from=$invoice_content key=linjenr item=innhold}
			<tr{if !$invoice2} class="invoiceGray"{/if}>
				<td class="border" align="center">{$linjenr}</td>
				<td class="border" align="center">{$innhold.name|nl2br}</td>
				<td class="border" align="right">kr&nbsp;{$innhold.belop_hver|commify:2:",":" "}</td>
				<td class="border" align="center">{$innhold.antall}</td>
				<td class="border" align="right">kr&nbsp;{$innhold.belop_sum_netto|commify:2:",":" "}</td>
				<td class="border" align="center">{math equation="x*100" x=$innhold.mva}&nbsp;%</td>
				<td class="border" align="right"><b>kr&nbsp;{$innhold.belop_sum|commify:2:",":" "}</b></td>
			</tr>
			{/foreach}
		
		</table>
	{/if}
	</td>
</tr>

{* ## MVA-visning ## *}
{if $mva_vis}
<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>MVA-grunnlag:</b> </td>
	<td>
		<table style="border-collapse: collapse;">
			<tr{if !$invoice2} class="invoiceGray"{/if}>
				<td class="border">MVA-%</td>
				<td class="border">Grunnlag</td>
				<td class="border">MVA</td>
			</tr>
			{foreach from=$mva key=mvaen item=mva_delsum}
			<tr{if !$invoice2} class="invoiceGray"{/if}>
				<td class="border" align="right">{$mvaen}&nbsp;%</td>
				<td class="border" align="right">kr&nbsp;{$mva_grunnlag.$mvaen|commify:2:",":" "}</td>
				<td class="border" align="right">kr&nbsp;{$mva_delsum|commify:2:",":" "}</td>
			</tr>
			{/foreach}
			<tr{if !$invoice2} class="invoiceGray"{/if}>
				<td class="border" align="right">SUM</td>
				<td class="border" align="right">kr&nbsp;{$mva_grunnlag_sum|commify:2:",":" "}</td>
				<td class="border" align="right">kr&nbsp;{$faktura_belop_sum_mva|commify:2:",":" "}</td>
			</tr>
		</table>
	</td>
</tr>
{/if}

<tr{if !$invoice2} class="invoiceGray"{/if}>
	<td align="right"><b>Sum å betale:</b> </td>
	<td>NOK {$faktura_belop_sum|commify:2:",":" "}</td>
</tr>