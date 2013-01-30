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


function choose_area(area_id)
{
	hide_all_areas();
	
	var showAllUsers = true;
	if(typeof( window.grouparray[area_id]) != 'undefined') {
		showAllUsers = false;
	}
	var spans = document.getElementsByTagName('span');
	for (var i=0;i<spans.length;i++) {
		if(spans[i].id == 'area_id' + area_id ||
		spans[i].id == 'areaid_' + area_id) {
			spans[i].style.display = 'inline';
		}
		if(spans[i].id.substr(0,7) == 'user_id') {
			if(showAllUsers) {
				spans[i].style.display = 'inline';
			}
			else if($("#user_assigned"+spans[i].id.substr(7)).attr("checked")) {
				spans[i].style.display = 'inline';
			}
			else {
				spans[i].style.display = 'none';
			}
		}
	}
	
	if(!showAllUsers) {
		for (var i=0; i<grouparray[area_id].length; i++) {
			if(document.getElementById('user_id'+ grouparray[area_id][i]) != undefined)
				document.getElementById('user_id'+ grouparray[area_id][i]).style.display = 'inline';
		}
		if(document.getElementById('user_id0') != undefined)
			document.getElementById('user_id0').style.display = 'inline';
	} else {
		if(document.getElementById('user_id0') != undefined)
			document.getElementById('user_id0').style.display = 'none';
	}
}

function hide_all_areas ()
{
	var spans = document.getElementsByTagName('span');
	for (var i=0;i<spans.length;i++) {
		if(spans[i].id.substr(0,7) == 'area_id' ||
		spans[i].id.substr(0,8) == 'areaid_') {
			spans[i].style.display = 'none';
		}
	}
}

function show_all_users ()
{
	var spans = document.getElementsByTagName('span');
	$('span[id^=user_id]').each(function() {
		if(!$(this).hasClass('graytext') || $('input', this).is(':checked')) {
			// -> Not a disabled user OR it is checked
			$(this).show();
		}
	});
	
	if(document.getElementById('user_id0') != undefined)
		document.getElementById('user_id0').style.display = 'none';
}

function new_customer ()
{
	if(!document.getElementById)
		return 0;
	
	this.cnf = _b.DOM.gE("customer_name");
	if(!this.cnf)
		return 0;
	this.customer_id = _b.DOM.gE("customer_id");
	if(!this.customer_id)
		return 0;
	if(this.customer_id.value == 0 || this.customer_id.value == "")
	{
		wCustomer = window.open("customer_edit.php?customer_name=" + cnf.value, "list", "width=900,height=610,scrollbars=yes,resizable=yes");
	} else {
		wCustomer = window.open("customer_edit.php?customer_id=" + this.customer_id.value, "list", "width=900,height=610,scrollbars=yes,resizable=yes");
	}
	wCustomer.focus();
}

function chooseMunicipal (id, name)
{
	/* THIS METHOD IS NOT IN USE AS OF 11.07.2011 */
	wMunicipal = window.open("municipal_choose.php?id=" + id + "&name=" + name + "&id2=1", "wMunicipal", "width=450,height=610");
	wMunicipal.focus();
}

function chooseAddress (id, name)
{
	customer_id = document.getElementById('customer_id').value;
	if(customer_id == '')
		alert('Kunde er ikke valgt. Skriv inn kundenavn lenger oppe i skjemaet '+
			'og velg en eksisterende eller opprett en ny.');
	else
	{
		wAddress = window.open("customer_address_choose.php?id=" + id + "&name=" + name + "&id2=1&customer_id=" + customer_id, "wAddress", "width=450,height=610");
		wAddress.focus();
	}
}

function replace_nor_char2 (str) {
	str = str.replace(/&aelig;/g, String.fromCharCode(230));
	str = str.replace(/&AElig;/g, String.fromCharCode(198));
	str = str.replace(/&Oslash;/g, String.fromCharCode(216));
	str = str.replace(/&oslash;/g, String.fromCharCode(248));
	str = str.replace(/&Aring;/g, String.fromCharCode(197));
	str = str.replace(/&aring;/g, String.fromCharCode(229));
	return str;
}

function selectCustomer (customer_id)
{	
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
					var customer=eval('('+xmlHttp.responseText+')');
					if(customer.customer_id != "undefined")
					{
						document.getElementById("customer_id").value = replace_nor_char2(customer.customer_id);
						document.getElementById("customer_id2").value = replace_nor_char2(customer.customer_id);
					}
					if(customer.customer_name != "undefined")
						document.getElementById("customer_name").value = replace_nor_char2(customer.customer_name);
					if(customer.customer_municipal != "undefined")
						document.getElementById("customer_municipal").value = replace_nor_char2(customer.customer_municipal);
					if(customer.customer_municipal_num != "undefined")
						document.getElementById("customer_municipal_num").value = customer.customer_municipal_num;
					if(customer.customer_municipal_num != "undefined")
						document.getElementById("customer_municipal_num2").value = customer.customer_municipal_num;
					if(customer.customer_address_id_invoice != "undefined" && customer.customer_address_id_invoice > 0)
					{
						document.getElementById("invoice_address_id").value = customer.customer_address_id_invoice;
						document.getElementById("invoice_address_id2").value = customer.customer_address_id_invoice;
						selectAddress(customer.customer_address_id_invoice);
					}
					else
					{
						document.getElementById("invoice_address_id").value = '';
						document.getElementById("invoice_address_id2").value = '';
						document.getElementById("invoice_address").value = '';
					}
					checkCustomerButton ();
				}
			} catch (e) {
				//document.getElementById("ajax_output").innerHTML 
				//= "Error on Ajax return call : " + e.description;
			}
	}
	xmlHttp.open("get","autosuggest.php?customer_id="+customer_id); // .open(RequestType, Source);
	xmlHttp.send(null); // Since there is no supplied form, null takes its place 
	                    // as a new form.
}

function selectAddress (address_id)
{	
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
					var address_full=xmlHttp.responseText;
					if(address_full != "undefined")
						document.getElementById("invoice_address").innerHTML = address_full;
				}
			} catch (e) {
				//document.getElementById("ajax_output").innerHTML 
				//= "Error on Ajax return call : " + e.description;
			}
	}
	xmlHttp.open("get","autosuggest.php?address_id="+address_id); // .open(RequestType, Source);
	xmlHttp.send(null); // Since there is no supplied form, null takes its place 
	                    // as a new form.
}



function putInTextForInfoscreen () {
	alert ("ABC");
}

function tabInTaxField (e)
{
	if(e.keyCode == 9) // Tab key
	{
		// Checking if the last mva field is selected
		form = document.forms['entry'];
		formelements = form.elements;
		valuelastrow = 0;
		valuehighestrow = 0;
		for (var i = 0; i<formelements.length;i++)
		{
			if(formelements[i].name == "rows[]")
			{
				if(formelements[i].value > valuehighestrow)
					valuehighestrow = parseInt(formelements[i].value);
			}
		}
		
		if($(this).attr('name').substr(3) == valuehighestrow)
		{
			// Are the last line, making a new and focusing on the first input field
			addFieldInvoice();
			
			// thisvalue is being changed by addFieldInvoice, its now the new row
			$('textarea[name="name'+thisvalue+'"]').focus();
			
			// Preventing default, dont want browser to focus on the wrong field
			e.preventDefault();
		}
		
	}
}

function checkCustomerButton ()
{
	customer_id = $('#customer_id').val();
	if(customer_id != "" && customer_id != "0")
	{
		$('#customer_edit_button img').attr('src', './img/icons/group_edit.png');
	}
	else
	{
		$('#customer_edit_button img').attr('src', './img/icons/group_add.png');
	}
}

$(document).ready(function(){
	$('#time_start').datepicker({
			dateFormat: 'dd-mm-yy',
			duration: 'slow',
			showTime: true,
			showAnim: 'fadeIn',
			constrainInput: false,
			showButtonPanel: true,
			firstDay: 1,
			currentText: 'I dag',
			dayNames: new Array('Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'),
			dayNamesMin: new Array('Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'),
			dayNamesShort: new Array('Søn', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør'),
			monthNames: new Array('Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'),
			monthNamesShort: new Array('Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'),
			nextText: 'Neste',
			prevText: 'Forrige',
			closeText: 'Ok',
			showOn: 'button',
			buttonText: 'Velg',
			buttonImage: 'img/icons/calendar.png'
		});
	
	$('#time_end').datepicker({
			dateFormat: 'dd-mm-yy',
			duration: 'slow',
			showTime: true,
			showAnim: 'fadeIn',
			constrainInput: false,
			showButtonPanel: true,
			firstDay: 1,
			currentText: 'I dag',
			dayNames: new Array('Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'),
			dayNamesMin: new Array('Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'),
			dayNamesShort: new Array('Søn', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør'),
			monthNames: new Array('Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember'),
			monthNamesShort: new Array('Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'),
			nextText: 'Neste',
			prevText: 'Forrige',
			closeText: 'Ok',
			showOn: 'button',
			buttonText: 'Velg',
			buttonImage: 'img/icons/calendar.png'
	});
	
	$("form[name=entry]").submit(function () {
		$.blockUI({ message: '<h1 style="font-size: 16px;">Lagrer booking,<br>vennligst vent...</h1>' });
	});
	
	
	
	$('.programHover').hoverbox();
	
	
	$('input[type=text][name^=mva]').keydown(tabInTaxField);
	
	
	$("input[type=text][name=contact_person_email]").each(function ()	{
		checkEmailAndAlert_editentry($(this));
	});
	
	$("input[type=text][name=contact_person_email]").keyup(function() {
		checkEmailAndAlert_editentry($(this));
	});
	$("input[type=text][name=contact_person_email]").change(function() {
		checkEmailAndAlert_editentry($(this));
	});
	$("input[type=text][name=contact_person_email]").click(function() {
		checkEmailAndAlert_editentry($(this));
	});
	
	checkCustomerButton();
});
