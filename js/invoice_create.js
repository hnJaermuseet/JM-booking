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

function updateMva (id)
{
	eks_mva		= document.getElementById("mva_eks" + id);
	
	var antall			= document.getElementById("antall" + id);
	if(antall == undefined || antall.value == undefined || antall.value == null || isNaN(antall.value))
		antall.value = 0;
	antall.value	= parseFloat(antall.value.replace(",", "."));
	
	belop_hver			= document.getElementById("belop_hver" + id);
	belop_hver.value	= parseFloat(belop_hver.value.replace(",", "."));
	if(isNaN(belop_hver.value))
		belop_hver.value = 0;
	
	belop_delsum		= document.getElementById("belop_delsum" + id);
	belop_delsum.value	= parseFloat(belop_delsum.value.replace(",", "."));
	if(isNaN(belop_delsum.value))
		belop_delsum.value = 0;
	
	mva			= document.getElementById("mva" + id);
	mva.value	= parseFloat(mva.value.replace(",", "."));
	if(isNaN(mva.value))
		mva.value = 0;
	
	mva_hver	= document.getElementById("mva_hver" + id);
	mva_hver.value	= parseFloat(mva_hver.value.replace(",", "."));
	if(isNaN(mva_hver.value))
		mva_hver.value = 0;
	
	mva_sum_hver		= document.getElementById("mva_sum_hver" + id);
	mva_sum_hver.value	= parseFloat(mva_sum_hver.value.replace(",", "."));
	if(isNaN(mva_sum_hver.value))
		mva_sum_hver.value = 0;
	
	belop_hver_real			= document.getElementById("belop_hver_real" + id);
	belop_hver_real.value	= parseFloat(belop_hver_real.value.replace(",", "."));
	if(isNaN(belop_hver_real.value))
		belop_hver_real.value = 0;
	
	if(eks_mva.checked) {
		mva_hver.value			= parseFloat(belop_hver.value) * (parseFloat(mva.value) / 100);
		belop_hver_real.value	= parseFloat(belop_hver.value);
		mva_sum_hver.value		= parseFloat(mva_hver.value) * parseFloat(antall.value);
		belop_delsum.value		= (parseFloat(belop_hver_real.value) + parseFloat(mva_hver.value)) * parseFloat(antall.value);
	} else {
		belop_delsum.value		= parseFloat(belop_hver.value) * parseFloat(antall.value);
		mva_hver.value			= parseFloat(belop_hver.value) * (parseFloat(mva.value) / (parseFloat(mva.value) + 100));
		belop_hver_real.value	= parseFloat(belop_hver.value) - parseFloat(mva_hver.value);
		mva_sum_hver.value		= parseFloat(mva_hver.value) * parseFloat(antall.value);
	}
	
	updateMvaSum();
	
}

function updateMvaSum ()
{
	// Update sums
	form = document.forms['invoiceform'];
	formelements = form.elements;
	belop_sum = 0;
	mva_sum = 0;
	for (var i = 0; i<formelements.length;i++)
	{
		if(formelements[i].name.substr(0, "belop_delsum".length) == "belop_delsum")
		{
			if(formelements[i].value != "")
			{
				belop_sum += parseFloat(formelements[i].value.replace(",", "."));
			}
		} else if(formelements[i].name.substr(0, "mva_sum_hver".length) == "mva_sum_hver")
		{
			if(formelements[i].value != "")
			{
				mva_sum += parseFloat(formelements[i].value.replace(",", "."));
			}
		}
	}
	sum = document.getElementById('belop_sum');
	sum.value = belop_sum;
	sum = document.getElementById('mva_sum');
	sum.value = mva_sum;
}

function addFieldInvoice ()
{
	form = document.forms['invoiceform'];
	formelements = form.elements;
	valuelastrow = 0;
	valuehighestrow = 0;
	valuehighestline = 0;
	for (var i = 0; i<formelements.length;i++)
	{
		if(formelements[i].name == "rows[]")
		{
			if(formelements[i].value > valuehighestrow)
				valuehighestrow = formelements[i].value;
		}
	}
	valuehighestrow++;
	thisvalue = valuehighestrow;
	valuehighestline++;
	thisline = valuehighestline;
	
	
	var tr = '<tr id="row'+ thisvalue+'">' +
		'<input type="hidden" name="rows[]" value="'+thisvalue+'">'+
		'<input type="hidden" name="type'+thisvalue+'" value="belop">'+
		'<input type="hidden" name="id_type'+thisvalue+'" value="0">'+
		'<input type="hidden" name="id_extra'+thisvalue+'" value="0">'+
		'<input '+
			'type="hidden" '+
			'id="belop_hver_real'+thisvalue+'" '+
			'name="belop_hver_real'+thisvalue+'" '+
			'value="0"'+
		'>';
	
	
	var td1 = 
		'<td>'+
			'<input type="text" size="3" value="'+thisvalue+'" disabled="disabled">' +
		'</td>';
	
	
	var td2 = '<td>' +
		'<textarea name="name'+thisvalue+'" cols="50" style="height: 40px;"></textarea>' +
		'</td>';
	
	
	var td3 = '<td>' +
			'<input '+
				'class="right" ' + 
				'type="text" '+
				'id="belop_hver'+thisvalue+'" '+
				'name="belop_hver'+thisvalue+'" '+
				'value="0" '+
				'size="6" '+
				'onchange="updateMva(\''+thisvalue+'\');"'+
			'>'+
		'</td>';
	
	
	var td4 = '<td>' +
			'<input '+
				'class="right" ' + 
				'type="text" '+
				'id="antall'+thisvalue+'" '+
				'name="antall'+thisvalue+'" '+
				'value="1" '+
				'size="6" '+
				'onchange="updateMva(\''+thisvalue+'\');"'+
			'>'+
		'</td>';
	
	
	var td5 = '<td>' +
			'<input '+
				'class="right" ' + 
				'type="text" '+
				'id="mva'+thisvalue+'" '+
				'name="mva'+thisvalue+'" '+
				'value="0" '+
				'size="3" '+
				'onchange="updateMva(\''+thisvalue+'\');"'+
			'>'+
		'</td>';
	
	
	var td6 = '<td>' +
			'<input '+
				'type="checkbox" '+
				'id="mva_eks'+thisvalue+'" '+
				'name="mva_eks'+thisvalue+'" '+
				'value="1" '+
				'checked="checked" '+
				'onchange="updateMva(\''+thisvalue+'\');"'+
			'>'+
		'</td>';
	
	
	var td7 = '<td>' +
			'<input '+
				'class="right" ' + 
				'type="text" '+
				'id="mva_hver'+thisvalue+'" '+
				'name="mva_hver'+thisvalue+'" '+
				'value="0" '+
				'size="3" '+
				'disabled="disabled" '+
			'>'+
		'</td>';
	
	
	var td8 = '<td>' +
			'<input '+
				'class="right" ' + 
				'type="text" '+
				'id="mva_sum_hver'+thisvalue+'" '+
				'name="mva_sum_hver'+thisvalue+'" '+
				'value="0" '+
				'size="3" '+
				'disabled="disabled" '+
			'>'+
		'</td>';
	
	
	var td9 = '<td>' +
			'<input '+
				'class="right" ' + 
				'type="text" '+
				'id="belop_delsum'+thisvalue+'" '+
				'name="belop_delsum'+thisvalue+'" '+
				'value="0" '+
				'size="6" '+
				'disabled="disabled" '+
			'>'+
		'</td>';
	
	
	var td10 = '<td>'+
		'<input '+
			'type="button" '+
			'value="Fjern linje" '+
			'onclick="removeField(\''+thisvalue+'\');" '+
		'></td>';
		
	
	var tr_slutt = '</tr>';
	
	
	$('#invoicerows').append(
		tr + 
			td1 + td2 + 
			td3 + td4 + 
			td5 + td6 + 
			td7 + td8 +
			td9 + td10 +
		tr_slutt);
	
	
	updateMva(thisvalue);
}

function removeField (id)
{
	tr = document.getElementById('row'+id);
	tr.parentNode.removeChild(tr);
	
	updateMvaSum ();
	
	return true;
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
					{
						var lines = address_full.split("\n");
						document.getElementById("invoice_to_line1").value = replace_nor_char2(lines[0]);
						document.getElementById("invoice_to_line2").value = replace_nor_char2(lines[1]);
						document.getElementById("invoice_to_line3").value = replace_nor_char2(lines[2]);
						document.getElementById("invoice_to_line4").value = replace_nor_char2(lines[3]);
						document.getElementById("invoice_to_line5").value = replace_nor_char2(lines[4]);
						document.getElementById("invoice_to_line6").value = replace_nor_char2(lines[5]);
						document.getElementById("invoice_to_line7").value = replace_nor_char2(lines[6]);
					}
				}
			} catch (e) {
				//document.getElementById("ajax_output").innerHTML 
				//= "Error on Ajax return call : " + e.description;
			}
	}
	xmlHttp.open("get","autosuggest.php?address_id="+address_id+"&address_format=2"); // .open(RequestType, Source);
	xmlHttp.send(null); // Since there is no supplied form, null takes its place 
	                    // as a new form.
}

function chooseAddress (id, name)
{
	customer_id = document.getElementById('invoice_to_customer_id').value;
	if(customer_id == '' || customer_id == '0')
		alert('Ingen kunde er satt');
	else
	{
		wAddress = window.open("customer_address_choose.php?callSelectAddress=1&id=" + id + "&name=" + name + "&id2=1&customer_id=" + customer_id, "wAddress", "width=450,height=610");
		wAddress.focus();
	}
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
					if(customer.customer_name != "undefined")
						document.getElementById("invoice_to_customer_name").value = replace_nor_char2(customer.customer_name);
					if(customer.customer_id != "undefined")
					{
						document.getElementById("invoice_to_customer_id").value = customer.customer_id;
						document.getElementById("invoice_to_customer_id2").value = customer.customer_id;
					}
					if(customer.customer_address_id_invoice != "undefined" && customer.customer_address_id_invoice > 0)
					{
						document.getElementById("invoice_to_address_id").value = customer.customer_address_id_invoice;
						document.getElementById("invoice_to_address_id2").value = customer.customer_address_id_invoice;
						selectAddress(customer.customer_address_id_invoice);
					}
					else
					{
						document.getElementById("invoice_to_address_id").value = '';
						document.getElementById("invoice_to_address_id2").value = '';
						selectAddress(0);
					}
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

function replace_nor_char2 (str) {
	str = str.replace(/&aelig;/g, String.fromCharCode(230));
	str = str.replace(/&AElig;/g, String.fromCharCode(198));
	str = str.replace(/&Oslash;/g, String.fromCharCode(216));
	str = str.replace(/&oslash;/g, String.fromCharCode(248));
	str = str.replace(/&Aring;/g, String.fromCharCode(197));
	str = str.replace(/&aring;/g, String.fromCharCode(229));
	return str;
}

function onchangeAddress() {
	if(document.getElementById("invoice_to_address_id2").value != '' && 
	document.getElementById("invoice_to_address_id2").value != '0')
	{
		alert('Ved å skrive i adressefeltene, så vil ikke endringene bli lagret. Trykk på "Velg adresse" for å velge en kundeadresse eller endre på de.');
	}
	document.getElementById("invoice_to_address_id").value = '';
	document.getElementById("invoice_to_address_id2").value = '';
}

function new_customer ()
{
	if(!document.getElementById)
		return 0;
	
	this.cnf = _b.DOM.gE("invoice_to_customer_name");
	if(!this.cnf)
		return 0;
	this.customer_id = _b.DOM.gE("invoice_to_customer_id");
	if(!this.customer_id)
		return 0;
	if(this.customer_id.value == 0 || this.customer_id.value == "")
	{
		wCustomer = window.open("customer_edit.php?returnToInvoiceCreate=1&customer_name=" + cnf.value, "list", "width=900,height=410,scrollbars=yes,resizable=yes");
	} else {
		wCustomer = window.open("customer_edit.php?returnToInvoiceCreate=1&customer_id=" + this.customer_id.value, "list", "width=900,height=410,scrollbars=yes,resizable=yes");
	}
	wCustomer.focus();
}
