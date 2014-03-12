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
*}<h1>Viser vedlegg {$att.att_id}</h1>
<div style="margin-left: 20px; width: 500px;">
<table class="prettytable">
	<tr>
		<th>Vedleggid</th>
		<td>{$att.att_id}</td>
	</tr>
	<tr>
		<th>Filnavn</th>
		<td><a href="{$entry_confirm_att_path}/{$att.att_filename}">{$att.att_filetype|iconFiletype} {$att.att_filename_orig}</a></td>
	</tr>
	<tr>
		<th>Størrelse</th>
		<td>{$att.att_filesize|file_size}</td>
	</tr>
	<tr>
		<th>Brukt</th>
		<td>{$att.usedatt|@count} ganger</td>
	</tr>
	<tr>
		<th>Lastet opp av</th>
		<td><a href="user.php?user_id={$att.user_id}">{$att.user_name}</a>, 
		{$att.att_uploadtime|date_format:"%H:%M %d-%m-%Y"}</td>
	</tr>
</table>
</div>

<h2>Koblinger til faste program og bookingtyper</h2>
<div style="margin-left: 20px; width: 500px;">
Ved sending av bekreftelse som har et av programmene som er 
koblet til eller av en av bookingtypene som er koblet til, så 
vil vedlegget automatisk bli valgt (kan velges vekk også).<br><br>
{if $att.connections|@count > 0}
<table class="prettytable">
	{foreach from=$att.connections item='connection'}
	<tr>
		<th>{iconHTML ico=$connection.icon} {$connection.type} {$connection.id}</th>
		<td>{$connection.name}</td>
	</tr>
	{/foreach}
</table>
{else}<div class="notice">Ingen koblinger</div>{/if}
</div>
<h3>Opprett ny kobling</h3>
<div style="margin-left: 20px;">
	<b>Program</b><br>
	<form action="{$SCRIPT_NAME}?att_id={$att.att_id}" method="POST">
		<select name="connection_program">
		{foreach from=$programs item='name' key='id'}
			<option value="{$id}">{$name}</option>
		{/foreach}
		</select><br>
		<input type="submit" value="Koble til">
	</form>
</div>
<div style="margin-left: 20px; margin-top: 10px;">
	<b>Bookingtype</b><br>
	<form action="{$SCRIPT_NAME}?att_id={$att.att_id}" method="POST">
		Type:
		<select name="connection_entry_type">
		{foreach from=$entry_types item='name' key='id'}
			<option value="{$id}">{$name}</option>
		{/foreach}
		</select><br>
		Sted:
		<select name="connection_area">
		{foreach from=$areas item='name' key='id'}
			<option value="{$id}">{$name}</option>
		{/foreach}
		</select>
		<br>
		<input type="submit" value="Koble til">
	</form>
</div>

<h2>Logg over bruk</h2>
<div style="margin-left: 20px; width: 500px;">
{if $att.usedatt|@count > 0}
<table class="prettytable">
	{foreach from=$att.usedatt item='used'}
	<tr>
		<th>{$used.timeused|date_format:"%H:%M %d-%m-%Y"}</th>
		<td><a href="entry_confirm_view.php?confirm_id={$used.confirm_id}">Bekreftelse {$used.confirm_id}</a></td>
	</tr>
	{/foreach}
</table>
{else}<div class="notice">Ikke bruk/sendt ut til noen</div>{/if}
</div>