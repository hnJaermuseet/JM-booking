<?php

/*
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
*/

/*
 * Følgende legges inn for å få opp knapp:
 * <ul id="vedlegg">
 * </ul>
 * <input type="button" id="velgVedlegg" class="ui-button ui-state-default ui-corner-all" value="Velg fil(er)">
 * 
 */

?>
<style type="text/css">
div#users-contain {
	width: 350px;
	margin: 20px 0;
}
div#users-contain table {
	margin: 1em 0;
	border-collapse: collapse;
	width: 100%;
}
div#users-contain table td, div#users-contain table th {
	border: 1px solid #eee;
	padding: .6em 10px;
	text-align: left;
}

.attSelector {
	padding-left: 20px;
	margin: 4px;
}
.attSelected {
	background-color: transparent;
	background-image: url("img/check-green-graa.gif");
	background-repeat: no-repeat;
	background-position: left center;
} 		
</style>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom-dialog.min.js"></script>
<script type="text/javascript" src="js/jquery.scrollTo-1.4.2-min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	$(function() {
		
		$("#dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			width: 550,
			height: 400,
			modal: true,
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
			
		});
		
		
		
		$('#velgVedlegg').click(function() {
			$('#dialog').dialog('open');
		})
		.hover(
			function(){ 
				$(this).addClass("ui-state-hover"); 
			},
			function(){ 
				$(this).removeClass("ui-state-hover"); 
			}
		).mousedown(function(){
			$(this).addClass("ui-state-active"); 
		})
		.mouseup(function(){
				$(this).removeClass("ui-state-active");
		});
		
		$('input[type=button]:not(.attSelected),input[type=submit]')
		.hover(
			function(){ 
				$(this).addClass("ui-state-hover"); 
			},
			function(){ 
				$(this).removeClass("ui-state-hover"); 
			}
		).mousedown(function(){
			$(this).addClass("ui-state-active"); 
		})
		.mouseup(function(){
				$(this).removeClass("ui-state-active");
		});
	});
	
	
	$('.attSelector').click(function () {
		if($('#vedleggValgt' + $(this).attr('id').substr(6)).length) {
			removeAttachment($(this).attr('id').substr(6));
		} else {
			addAttachment($(this).attr('id').substr(6), $(this).html());
		}
	});
	
	$('#attSearchForm').submit(function () {
		attSearch();
		return false;
	});
	
	$('#attSearchButton').click(function () {	attSearch();	});
	
	updateAttSelector ();
});


function addAttachment(att_id, att_html)
{
	if($('#noAttachmentsSelected').is(':visible'))
	{
		$('#noAttachmentsSelected').fadeOut('normal', function () {
			addAttachment2 (att_id, att_html);
		});
	}
	else
	{
		addAttachment2 (att_id, att_html);
	}
}

function addAttachment2 (att_id, att_html)
{
	$('#vedlegg').append(
		'<li id="vedleggValgt' + att_id + '" style="display: none;">' +
			'<input type="hidden" name="attachment[]" value="' + att_id + '">' + 
			att_html +
			' <input type="button" onclick="removeAttachment(' + att_id + ');" value="Fjern" style="font-size: 10px">'+
		'</li>');
	$('#vedleggValgt' + att_id).fadeIn();
	 updateAttSelector ();
}

function removeAttachment (att_id)
{
	$('#vedleggValgt' + att_id).fadeOut('normal', function () {
		$(this).remove();
		updateAttSelector ();
	});
}

function updateAttSelector ()
{
	$('.attSelected').removeClass('attSelected');
	// Getting selected
	$('input[name^=attachment]').each(function() {
		if($('.attid'+$(this).attr('value')).length)
			$('.attid'+$(this).attr('value')).addClass('attSelected');
	});
	
	if($('#vedlegg li').length)
	{
		$('#noAttachmentsSelected').fadeOut();
	} else {
		$('#noAttachmentsSelected').fadeIn();
	}
}

function attSearch ()
{
	var searchFor = $("#attSearch").val();
	$('.attSearchResult').remove();
	$.getJSON("autosuggest.php?attSearch",{'attSearch': searchFor},
		function(data){
		
		if(!data.results.length) {
			$('#attSearchResult').
				append('<li class="attSelector attSearchResult"><div class="error"><i>Ingen resultater</i></div></li>');
		}
		else
		{
			$.each(data.results, function(i,att){
				$('#attSearchResult').append(
					'<li class="attSelector attSearchResult attid' + att.att_id + '" id="attId-' + att.att_id + '">' +
					'<img src="./img/icons/' + att.att_filetype_icon + '" style="border: 0px solid black; vertical-align: middle;" alt=""> '+
					att.att_displayname +
					'</li>');
			});
			
			$('.attSearchResult').click(function () {
				if($('#vedleggValgt' + $(this).attr('id').substr(6)).length) {
					removeAttachment($(this).attr('id').substr(6));
				} else {
					addAttachment($(this).attr('id').substr(6), $(this).html());
				}
			});
			updateAttSelector ();
			
		}
		$("#dialog").scrollTo("#attSearchResult", 3000);
	});
}
</script>



<div id="dialog" title="Velg fil(er)">
	<i>Klikk på et vedlegg for å velge dette.</i>
<?php
/* Getting the attachments */
$Q_att = mysql_query("
	SELECT att.att_id
	FROM `entry_confirm_attachment` att 
	LEFT JOIN `entry_confirm_usedatt` used 
	ON att.att_id = used.att_id  
	GROUP BY att.att_id
	ORDER BY used.timeused, att.att_uploadtime desc LIMIT 10");
if(mysql_num_rows($Q_att))
{
	echo '<h2 style="font-weight: bold; font-size: 14px; padding: 5px; margin-bottom: 0">10 sist brukte vedlegg:</h2>';
	echo '	<ul style="padding-left: 0px; margin-top: 0px; list-style-image:none; list-style-position:outside; list-style-type:none;">';
	while($R_att = mysql_fetch_assoc($Q_att))
	{
		$att = getAttachment($R_att['att_id']);
		if(count($att))
			echo '		<li class="attSelector attid'.$att['att_id'].'" id="attId-'.$att['att_id'].'">'.
			iconFiletype($att['att_filetype']).' '.$att['att_filename_orig'].
				' ('.smarty_modifier_file_size($att['att_filesize']).')';
	}
	echo '	</ul>';
}
$Q_att = mysql_query("
	SELECT att.att_id
	FROM `entry_confirm_attachment` att
	ORDER BY att.att_uploadtime desc LIMIT 10");
if(mysql_num_rows($Q_att))
{
	echo '<h2 style="font-weight: bold; font-size: 14px; padding: 5px; margin-bottom: 0">10 sist opplastet vedlegg:</h2>';
	echo '	<ul style="padding-left: 0px; margin-top: 0px; list-style-image:none; list-style-position:outside; list-style-type:none;">';
	while($R_att = mysql_fetch_assoc($Q_att))
	{
		$att = getAttachment($R_att['att_id']);
		if(count($att))
			echo '		<li class="attSelector attid'.$att['att_id'].'" id="attId-'.$att['att_id'].'">'.
			iconFiletype($att['att_filetype']).' '.$att['att_filename_orig'].
				' ('.smarty_modifier_file_size($att['att_filesize']).')';
	}
	echo '	</ul>';
}
?>

	<h2 style="font-weight: bold; font-size: 14px; padding: 5px; margin-bottom: 0">Søk etter andre vedlegg:</h2>
	<form id="attSearchForm">
	Filnavn: <input type="text" value="" id="attSearch"> <input type="button" value="Søk" id="attSearchButton">
	</form>
	<ul style="padding-left: 0px; margin-top: 0px; list-style-image:none; list-style-position:outside; list-style-type:none;" id="attSearchResult">
	</ul>
</div>