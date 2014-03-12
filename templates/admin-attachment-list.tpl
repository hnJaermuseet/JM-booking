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
*}<h1>Vedlegg</h1>

{if !$viewall}
<h2>Last opp nytt</h2>
<form action="admin_attachment.php" method="post" enctype="multipart/form-data">
<b>Last opp nytt vedlegg</b><br>
{if $feilmelding != ''}
	<div style="text-align: center; margin: 5px; width: 200px; border: 1px dashed black; padding: 10px; font-size: 14px; background-color: red; color: white;">
	Feil! {$feilmelding}</div>
{/if}

<label for="file">Velg fil:</label><br>
<input type="file" name="file" id="file" /><br><br> 
<input type="submit" value="Last opp">
</form>
<br><br>


<h2>Liste med vedlegg, 10 siste som er lastet opp</h2>

{if $attachments_lastupload|@count > 0}
<table class="prettytable">
	<tr>
		<th>Filnavn</th>
		<th>Opplastet av</th>
		<th>Koblinger</th>
	</tr>
	{foreach from=$attachments_lastupload item='att'}
	<tr>
		<td><a href="admin_attachment.php?att_id={$att.att_id}">{$att.att_filetype|iconFiletype} {$att.att_filename_orig}</a></td>
		<td><a href="user.php?user_id={$att.user_id}">{$att.user_name}</a>, {$att.att_uploadtime|date_format:"%H:%M %d-%m-%Y"}</td>
		<td>{if $att.connections|@count > 0}
<table class="prettytable">
	{foreach from=$att.connections item='connection'}
	<tr>
		<td>{iconHTML ico=$connection.icon} {$connection.type} {$connection.id}</th>
		<td>{$connection.name}</td>
	</tr>
	{/foreach}
</table>
{else}<i>Ingen koblinger</i>{/if}
	</tr>
	{/foreach}
</table>
{else}<i>Ingen vedlegg funnet</i>{/if}


<h2>Liste med vedlegg, 10 siste brukte vedlegg</h2>

{if $attachments_lastused|@count > 0}
<table class="prettytable">
	<tr>
		<th>Filnavn</th>
		<th>Opplastet av</th>
		<th>Koblinger</th>
	</tr>
	{foreach from=$attachments_lastused item='att'}
	<tr>
		<td><a href="admin_attachment.php?att_id={$att.att_id}">{$att.att_filetype|iconFiletype} {$att.att_filename_orig}</a></td>
		<td><a href="user.php?user_id={$att.user_id}">{$att.user_name}</a>, {$att.att_uploadtime|date_format:"%H:%M %d-%m-%Y"}</td>
		<td>{if $att.connections|@count > 0}
<table class="prettytable">
	{foreach from=$att.connections item='connection'}
	<tr>
		<td>{iconHTML ico=$connection.icon} {$connection.type} {$connection.id}</th>
		<td>{$connection.name}</td>
	</tr>
	{/foreach}
</table>
{else}<i>Ingen koblinger</i>{/if}
	</tr>
	{/foreach}
</table>
{else}<i>Ingen vedlegg funnet</i>{/if}


<h2>Liste med vedlegg, alle opplastet</h2>
- <a href="admin_attachment.php?viewall=1">Vis alle som er opplastet til nå</a>

{else}

<h2>Liste med vedlegg, alle opplastet</h2>

{if $attachments|@count > 0}
<table class="prettytable">
	<tr>
		<th>Filnavn</th>
		<th>Opplastet av</th>
		<th>Koblinger</th>
	</tr>
	{foreach from=$attachments item='att'}
	<tr>
		<td><a href="admin_attachment.php?att_id={$att.att_id}">{$att.att_filetype|iconFiletype} {$att.att_filename_orig}</a></td>
		<td><a href="user.php?user_id={$att.user_id}">{$att.user_name}</a>, {$att.att_uploadtime|date_format:"%H:%M %d-%m-%Y"}</td>
		<td>{if $att.connections|@count > 0}
<table class="prettytable">
	{foreach from=$att.connections item='connection'}
	<tr>
		<td>{iconHTML ico=$connection.icon} {$connection.type} {$connection.id}</th>
		<td>{$connection.name}</td>
	</tr>
	{/foreach}
</table>
{else}<i>Ingen koblinger</i>{/if}
	</tr>
	{/foreach}
</table>
{else}<i>Ingen vedlegg funnet</i>{/if}
{/if}