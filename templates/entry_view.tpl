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
*}<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery.scrollTo-1.4.2-min.js"></script>
{if $program_id_desc != ""}
<script type="text/javascript" src="js/jquery.dimensions.js"></script>
<script type="text/javascript" src="js/jquery.tooltip.min.js"></script>
<script type="text/javascript" src="js/entry-view.js"></script>
{/if}

<span class="hiddenprint">
<h1>Viser booking</h1>

<span style="font-size: 16px;">
{if !$deleted}
	<a href="edit_entry2.php?entry_id={$entry_id}" style="font-weight: bold;">
	{iconHTML ico="page_white_edit"} Endre denne bookingen</a>
	&nbsp;&nbsp;-:- &nbsp;&nbsp;<a href="edit_entry2.php?copy_entry_id={$entry_id}">
	{iconHTML ico="page_white_copy"} Kopier denne bookingen</a>
{else}
	<div class="notice" style="width: 400px;"><b>Denne bookingen er slettet.</b><br /><br />Den kan gjenopprettes eller kopieres hvis innholdet i bookingen skal brukes videre.</div>
	<a href="entry_delete.php?entry_id={$entry_id}&amp;undelete=1" style="font-weight: bold;">
	{iconHTML ico="page_white_get"} Gjenopprett booking</a>
	&nbsp;&nbsp;-:- &nbsp;&nbsp;<a href="edit_entry2.php?copy_entry_id={$entry_id}">
	{iconHTML ico="page_white_copy"} Kopier denne slettede bookingen til en ny</a>
{/if}
</span>
<br><br><br></span>

<span class="print"><h2>{$time_start|date_format:"%Y-%m-%d"}: {$entry_name}</h2></span>

<table>

<tr>
	<td align="right"><b>Bookingnavn: </b></td>
	<td>{$entry_name}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico="page_white_star"} <b>Bookingid:</b> </td>
	<td>{$entry_id}</td>
</tr>


<tr>
	<td align="right"><b>Tittel:</b> </td>
	<td>{$entry_title}</td>
</tr>

<tr>
	<td align="right"{if $entry_type_id == 0} style="color: gray;"{/if}>{iconHTML ico='page_white_stack'} <b>Type:</b> </td>
	<td>
	{if $entry_type_id == '0'}<i><span style="color: gray;">{/if}
	{$entry_type}
	{if $entry_type_id == '0'}</span></i>{/if}
	</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='date_previous'} <b>Starter:</b> </td>
	<td>{$time_start|date_format:"%H:%M"} {$time_start|date_format:"%A"|lower} 
<a href="day.php?year={$time_start|date_format:"%Y"}&amp;month={$time_start|date_format:"%m"}&amp;day={$time_start|date_format:"%d"}&amp;area={$area_id}">{$time_start|date_format:"%e"}</a>. 
<a href="month.php?year={$time_start|date_format:"%Y"}&amp;month={$time_start|date_format:"%m"}&amp;day={$time_start|date_format:"%d"}&amp;area={$area_id}">{$time_start|date_format:"%b"}</a> 
{$time_start|date_format:"%Y"}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='date_next'} <b>Ferdig:</b> </td>
	<td>{$time_end|date_format:"%H:%M"} {$time_end|date_format:"%A"|lower} 
<a href="day.php?year={$time_end|date_format:"%Y"}&amp;month={$time_end|date_format:"%m"}&amp;day={$time_end|date_format:"%d"}&amp;area={$area_id}">{$time_end|date_format:"%e"}</a>. 
<a href="month.php?year={$time_end|date_format:"%Y"}&amp;month={$time_end|date_format:"%m"}&amp;day={$time_end|date_format:"%d"}&amp;area={$area_id}">{$time_end|date_format:"%b"}</a> 
{$time_end|date_format:"%Y"}</td>
</tr>

<tr>
	<td align="right"{if !$user_assigned_any} style="color: gray;"{/if}>{iconHTML ico='user'} <b>Vert(er):</b> </td>
	<td>{if !$user_assigned_any}{iconHTML ico='user_delete'} <i><span style="color: gray;">{/if}{$user_assigned_names2}{if !$user_assigned_any}</span></i>{/if}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='house'} <b>Anlegg:</b> </td>
	<td>{$area}</td>
</tr>

<tr><td align="right">{iconHTML ico='shape_square'} <b>Rom:</b> </td><td>
	{if $rooms|@count > 1}
	<ul>
		{foreach from=$rooms item='room_name'}
		<li>{$room_name}</li>
		{/foreach}
	</ul>
	{else}
	{$room}
	{/if}
</td></tr>

<tr>
	<td align="right">{iconHTML ico='email'} <b>Bekreftelses-epost sendt?</b> </td>
	<td>{$confirm_email2} (<a href="entry_confirm.php?entry_id={$entry_id}">Send bekreftelse</a>)</td>
</tr>

<tr>
	<td align="right"{if $customer_id == '0'} style="color: gray;"{/if}>{iconHTML ico='group'} <b>Kunde:</b> </td>
	<td>
		{if $customer_id == '0'}
			<span style="color: gray;"><i>Ingen valgt</i></span>
		{else}
	<a href="customer.php?customer_id={$customer_id}">{$customer_name}</a> (Kundeid {$customer_id}){/if}</td>
</tr>

<tr><td align="right"{if $contact_person_name == ""} style="color: gray;"{/if}><b>Kontaktperson:</b> </td><td>{$contact_person_name}</td></tr>
<tr><td align="right"{if $contact_person_phone == ""} style="color: gray;"{/if}><b>Telefon:</b> </td><td>{$contact_person_phone}</td></tr>
<tr><td align="right"{if $contact_person_email == ""} style="color: gray;"{/if}><b>Kontaktpersons epost:</b> </td><td>{$contact_person_email}</td></tr>

<tr>
	<td align="right"{if $customer_municipal == ""} style="color: gray;"{/if}>{iconHTML ico='map'} <b>Kommune:</b> </td>
	<td>{$customer_municipal}</td>
</tr>


<tr>
	<td align="right"> <b>Opprettet av:</b> </td>
	<td>{$created_by_name}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='user_small'} <b>Antall barn:</b> </td>
	<td>{if $num_person_child == 0}<span style="color: gray;">{/if}{$num_person_child}{if $num_person_child == 0}</span>{/if}</td>
</tr>

<tr>
	<td align="right">{iconHTML ico='user_suit'} <b>Antall voksne:</b> </td>
	<td>{if $num_person_adult == 0}<span style="color: gray;">{/if}{$num_person_adult}{if $num_person_adult == 0}</span>{/if}</td>
</tr>

<tr>
	<td align="right"> <b>Tell i bookingsystem:</b> </td>
	<td>{$num_person_count2|ucfirst}</td>
</tr>

<tr>
	<td align="right"{if $program_id == 0} style="color: gray;"{/if}>{iconHTML ico='package'} <b>Program:</b> </td>
	<td{if $program_id == 0} style="color: gray;"{/if}>{$program_id_name}{if $program_id_name == ""}<i>Ingen</i>{/if}
	{if $program_id_desc != ""}<img src="img/icons/information.png" id="program_infosign">
	<span style="display: none;" id="program_id_desc">{$program_id_desc|nl2br}</span>{/if}</td>
</tr>

<tr>
	<td align="right"{if $program_description == ""} style="color: gray;"{/if}>{iconHTML ico='script'} <b>Programbeskrivelse:</b> </td>
	<td{if $program_description == ""} style="color: gray;"{/if}>{$program_description|nl2br}{if $program_description == ""}<i>Ingen</i>{/if}</td>
</tr>

<tr>
	<td align="right"{if $service_description == ""} style="color: gray;"{/if}>{iconHTML ico='drink'} <b>Serveringsbeskrivelse:</b> </td>
	<td{if $service_description == ""} style="color: gray;"{/if}>{$service_description|nl2br}{if $service_description == ""}<i>Ingen</i>{/if}</td>
</tr>

<tr>
	<td align="right"{if $service_alco == 0} style="color: gray;"{/if}> <b>Alkoholservering:</b> </td>
	<td{if $service_alco == 0} style="color: gray;"{/if}>{$service_alco2|ucfirst}</td>
</tr>

<tr>
	<td align="right"{if $comment == ""} style="color: gray;"{/if}>{iconHTML ico='comment'} <b>Kommentar:</b> </td>
	<td>{$comment|nl2br}</td>
</tr>

<tr>
	<td align="right"{if $infoscreen_txt == ""} style="color: gray;"{/if}>{iconHTML ico='monitor'} <b>Tekst på infoskjerm:</b> </td>
	<td>{$infoscreen_txt}</td>
</tr>
<tr class="hiddenprint">
	<td align="right"{if $infoscreen_txt == ""} style="color: gray;"{/if}>{iconHTML ico='monitor'} <b>Forhåndsvis infoskjerm:</b> </td>
	<td><a href="infoskjerm.php?area={$area_id}&amp;date={$time_start|date_format:"%d.%m.%Y"}"{if $infoscreen_txt == ""} style="color: gray;"{/if}>Forhåndsvis kvelden for det nåværende anlegg*</td>
</tr>
<tr class="hiddenprint">
	<td colspan="2"{if $infoscreen_txt == ""} style="color: gray;"{/if}>* Vil bare vise noe hvis bookingen er lagt til tidspunkt etter 16.00. Gjelder kun invendige skjermer på Vitenfabrikken</td>
</tr>


{* ### FAKTURA ### *}
<tr>
	<td colspan="2"><br><font size="3"><b>Faktura</b></font></td>
</tr>
{include file='invoice_fromEntry.tpl' linkview=true}
<tr class="hiddenprint">
	<td colspan="2">- <a href="entry_invoice.php?entry_id={$entry_id}">Vis som fakturagrunnlag</td>
</tr>

</table>



{* ### ENDRINGSLOGG ### *}
<table class="hiddenprint">
	<tr>
		<td>
			<br>
			<h2>Endringslogg</h2>
			<b>Antall endringer:</b> {$rev_num}<br>
			<b>Sist endret av:</b> {$user_last_edit_name} ({$time_last_edit|date_format:"%H:%M:%S %d-%m-%Y"})<br>


<script type="text/javascript" src="js/entry-changelog.js">
</script>

<br><br>
<table class="prettytable">
	<tr>
		<th>Revisjon</th>
		<th>Tidspunkt</th>
		<th>Handling</th>
		<th>Hvem</th>
		<th>Hva</th>
	</tr>
{* Resten av endringloggen ligger i PHP-filen *}