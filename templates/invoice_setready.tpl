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
*}{* ## Klar til fakturering?? ## *}


{if $checkInvoice[0]|count || $checkInvoice[1]|count}

<h1>Feil/advarsler på fakturagrunnlag</h1>
{if $checkInvoice[0]|count}
	<b>Feil med fakturagrunnlag:</b><br />
	<div class="error"><ul style="padding-left: 20px; margin: 0px;">
	{foreach from=$checkInvoice[0] item=error}<li>{$error}</li>{/foreach}
	</ul></div>
{/if}
{if $checkInvoice[1]|count}
	{if !$checkInvoice[0]|count}<br /><br />{/if}
	<b>Advarsler p&aring; fakturagrunnlag:</b><br />
	<div class="notice"><ul style="padding-left: 20px; margin: 0px;">
	{foreach from=$checkInvoice[1] item=warnings}<li>{$warnings}</li>{/foreach}
	</ul></div>
{/if}

	Siden det er <b>feil og/eller advarsler på fakturagrunnlaget</b>, så anbefales det at <b>du retter opp i disse</b>
	
	<br /><br />
	<p style="font-size: 1.4em; margin: 10px;">{iconHTML ico='arrow_right'} <a href="edit_entry2.php?entry_id={$entry_id}">Endre bookingen</a></p>
	<p style="font-size: 1.4em; margin: 10px;">{iconHTML ico='arrow_right'} <a href="entry.php?entry_id={$entry_id}">Gå tilbake til bookingen</a></p><br />
	
	Du kan også sette den klar til fakturering, men det blir å skyve problemene videre på noen andre<br />
	- <a href="invoice_setready.php?entry_id={$entry_id}&amp;set_okey=1&amp;return={$return}">Sett faktureringsklar</a><br>
	- eller <a href="{$return_to}">returnere der du kom fra</a>
{else}
<h1>Klar til fakturering?</h1>
	<p style="font-size: 1.4em; margin: 10px;">{iconHTML ico='tick'} <a href="invoice_setready.php?entry_id={$entry_id}&amp;set_okey=1&amp;return={$return}">Ja</a></p>
	<p style="font-size: 1.4em; margin: 10px;">{iconHTML ico='cross'} <a href="{$return_to}">Nei</a></p><br />
{/if}

<h2>Detaljer:</h2><br>
<table style="border: black 1px solid;">
{include file='invoice_fromEntry.tpl'}
</table>