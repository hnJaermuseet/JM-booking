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

waitingForTemplate = false;
function useTemplate(tpl_id)
{
	waitingForTemplate = true;
	$(".chooseTemplate").removeClass('noTemplate');
	$("#chooseTemplate_anim").html('<img src="img/busy.gif">');
	var xmlHttp=null; // Defines that xmlHttp is a new variable.
	// Try to get the right object for different browser
	try {
		// Firefox, Opera 8.0+, Safari, IE7+
		xmlHttp = new XMLHttpRequest(); // xmlHttp is now a XMLHttpRequest.
	} catch (e) {
		// Internet Explorer
		try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4)
			try { // In some instances, status cannot be retrieved and will produce 
			      // an error (e.g. Port is not responsive)
				if (xmlHttp.status == 200) {
					var tpl=xmlHttp.responseText;
					if(tpl != "undefined")
						document.getElementById("confirm_tpl").value = replace_nor_char2(tpl);
					waitingForTemplate = false;
					$("#chooseTemplate_anim").html('<img src="img/icons/accept.png">');
				}
			} catch (e) {
				//document.getElementById("ajax_output").innerHTML 
				//= "Error on Ajax return call : " + e.description;
			}
	}
	xmlHttp.open("get","autosuggest.php?template_id="+tpl_id); // .open(RequestType, Source);
	xmlHttp.send(null); // Since there is no supplied form, null takes its place 
	                    // as a new form.
}

function replace_nor_char2 (str) {
	str = str.replace(/&aelig;/g, String.fromCharCode(230));
	str = str.replace(/&AElig;/g, String.fromCharCode(198));
	str = str.replace(/&Oslash;/g, String.fromCharCode(216));
	str = str.replace(/&oslash;/g, String.fromCharCode(248));
	str = str.replace(/&Aring;/g, String.fromCharCode(197));
	str = str.replace(/&aring;/g, String.fromCharCode(229));
	str = str.replace(/&quot;/g, "\"");
	return str;
}

function addEmailField () {
	form = document.forms['entry_confirm'];
	formelements = form.elements;
	var valuelastrow = 0;
	var valuehighestrow = 0;
	var valuehighestline = 0;
	for (var i = 0; i<formelements.length;i++)
	{
		if(formelements[i].name == "emails[]")
		{
			if(formelements[i].value > valuehighestrow)
				valuehighestrow = formelements[i].value;
		}
	}
	valuehighestrow++;
	thisvalue = valuehighestrow;
	
	
	var table = $("#emailTable");
	var tr = 
		'<tr>'+
			'<td>'+
				'<input type="checkbox" name="emails[]" value="'+thisvalue+'" checked="checked">'+
			'</td>'+
			'<td>'+
				'<input type="text" name="email'+thisvalue+'">'+
			'</td>'+
		'</tr>';
	table.append(tr);
	
	// Keyup for text field
	table.find('[name="email'+thisvalue+'"]').keyup(function() {
		checkEmailAndAlert($(this));
	});
	table.find('[name="email'+thisvalue+'"]').change(function() {
		checkEmailAndAlert($(this));
	});
	table.find('[name="email'+thisvalue+'"]').click(function() {
		checkEmailAndAlert($(this));
	});
	
	// Change for checkbox
	table.find('[name="email'+thisvalue+'"]').parent().parent().
		find("input[type=checkbox][name=emails[]]").change(function() {
		checkEmailAndAlert($(this).parent().parent().find('input[type=text]'));
	});
	table.find('[name="email'+thisvalue+'"]').parent().parent().
		find("input[type=checkbox][name=emails[]]").click(function() {
		checkEmailAndAlert($(this).parent().parent().find('input[type=text]'));
	});
}


function disableAttachment()
{
	$("#emailAttachment").slideUp('slow');
	$("#emailAttachmentDisabled").fadeIn('slow');
}

function enableAttachment ()
{
	$("#emailAttachment").slideDown('slow');
	$("#emailAttachmentDisabled").fadeOut('slow');
}

$(document).ready(function(){

	$("input[name=emailTypePDF]").click(function () {
		if($("input[name=emailTypePDF]:checked").val() != '1') {
			disableAttachment();
			
			// Fade in some new text
			$("#txt_heading1").fadeTo('slow', 0.1, function() {
				$("#txt_heading1").html($("#txt_heading1_nopdf").html());
				$("#txt_heading1").fadeTo('slow', 1);
			});
			
			$("#pdf_mailbody").slideUp('slow');
		} else {
			enableAttachment();
			
			// Fade in some new text
			$("#txt_heading1").fadeTo('slow', 0.1, function() {
				$("#txt_heading1").html($("#txt_heading1_pdf").html());
				$("#txt_heading1").fadeTo('slow', 1);
			});
			
			$("#pdf_mailbody").slideDown('slow');
		}
	});
	
	$("form[name=entry_confirm]").submit(function () {
		if($("#confirm_tpl").value == "" && !$("#nopdf_confirm:checked").length) {
			$("#noPDF").slideDown();
			return false;
		}
		
		failedEmails = false;
		$("input[type=text][name^=email]").each(function ()	{
			tr = $(this).parent().parent();
			checked = tr.find('input[type=checkbox]').attr('checked');
			if(checked && !checkEmail ($(this).val()))
			{
				$.blockUI({ message: $('#dialog_failedEmail'), css: { width: '375px' } }); 
				failedEmails = true;
				return false;
			}
		});
		
		if(failedEmails)
			return false;
		
		if($("select.noTemplate").hasClass('noTemplate'))
		{
			$.blockUI({ message: $('#dialog_question'), css: { width: '475px' } }); 
			return false;
		}
		
		$.blockUI({ message: '<h1 style="font-size: 16px;">Sender bekreftelse,<br>vennligst vent...</h1>' });
	});
	
	$('#dialog_yes').click(function() { 
		$("select.noTemplate").removeClass('noTemplate');
		$("form[name=entry_confirm]").submit();
	});

	$('#dialog_no').click(function() { 
		$.unblockUI(); 
		return false; 
	});

	$('#dialog_ok').click(function() { 
		$.unblockUI(); 
		return false; 
	});
	
	$("input[type=text][name^=email]").each(function ()	{
		checkEmailAndAlert($(this));
	});
	
	$("input[type=text][name^=email]").keyup(function() {
		checkEmailAndAlert($(this));
	});
	$("input[type=text][name^=email]").change(function() {
		checkEmailAndAlert($(this));
	});
	$("input[type=text][name^=email]").click(function() {
		checkEmailAndAlert($(this));
	});
	
	$("input[type=checkbox][name=emails[]]").change(function() {
		checkEmailAndAlert($(this).parent().parent().find('input[type=text]'));
	});
	$("input[type=checkbox][name=emails[]]").click(function() {
		checkEmailAndAlert($(this).parent().parent().find('input[type=text]'));
	});
	
	
	$("#chooseTemplate_anim").html('<img src="img/icons/delete.png"> Ingen mal valg');
});
