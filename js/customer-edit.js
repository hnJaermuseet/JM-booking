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


function selectPostalNumber (postfield_name, postalnum)
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
					var postal_place=xmlHttp.responseText;
					if(postal_place != "undefined")
						document.getElementById(postfield_name).value = postal_place;
				}
			} catch (e) {
				//document.getElementById("ajax_output").innerHTML 
				//= "Error on Ajax return call : " + e.description;
			}
	}
	xmlHttp.open("get","autosuggest.php?postal_num="+postalnum); // .open(RequestType, Source);
	xmlHttp.send(null); // Since there is no supplied form, null takes its place 
	                    // as a new form.
}
function addFieldPhone ()
{
	form = document.forms['customer'];
	formelements = form.elements;
	valuelastrow = 0;
	valuehighestrow = 0;
	valuehighestline = 0;
	for (var i = 0; i<formelements.length;i++)
	{
		if(formelements[i].name == "rows_phone[]")
		{
			if(formelements[i].value > valuehighestrow)
				valuehighestrow = formelements[i].value;
		}
	}
	valuehighestrow++;
	thisvalue = valuehighestrow;
	valuehighestline++;
	thisline = valuehighestline;
	
	var tr = '<tr id="rowphone'+ thisvalue+'">';
	
	
	var td1 = '<td>'+
		'<input type="hidden" name="rows_phone[]" value="'+thisvalue+'">' +
		'<input type="hidden" name="phone_id'+thisvalue+'" value="0">' +
		'<input type="text" name="phone_num'+thisvalue+'" size="7">' +
		'</td>';
	
	
	var td2 = '<td>' +
		'<input name="phone_name'+thisvalue+'" size="30">' +
		'</td>';
	
	
	var td3 = '<td>'+
		'<input '+
			'type="button" '+
			'value="Fjern linje" '+
			'onclick="removeFieldPhone(\''+thisvalue+'\');" '+
		'></td>';
	
	
	var tr_slutt = '</tr>';
	
	$('#fieldrowsphone').append(
		tr + td1 + td2 + td3 + tr_slutt);
	
}

function removeFieldPhone (id)
{
	tr = document.getElementById('rowphone'+id);
	tr.parentNode.removeChild(tr);
	
	return true;
}

function addFieldAddress ()
{
	form = document.forms['customer'];
	formelements = form.elements;
	valuelastrow = 0;
	valuehighestrow = 0;
	valuehighestline = 0;
	for (var i = 0; i<formelements.length;i++)
	{
		if(formelements[i].name == "rows_address[]")
		{
			if(formelements[i].value > valuehighestrow)
				valuehighestrow = formelements[i].value;
		}
	}
	valuehighestrow++;
	thisvalue = valuehighestrow;
	valuehighestline++;
	thisline = valuehighestline;
	
	
	var tr = '<tr id="rowaddress'+ thisvalue+'">';
	
	
	customer_address_id_invoice = form.elements["customer_address_id_invoice"];
	if(customer_address_id_invoice == undefined) {
		invoiceadr_checked = ' checked="checked"';
	}
	else {
		invoiceadr_checked = '';
	}
	var td1 = '<td align="right">'+
		'<input type="hidden" name="rows_address[]" value="'+thisvalue+'">' +
		'<input type="hidden" name="address_id'+thisvalue+'" value="0">' +
		'<input type="radio" name="customer_address_id_invoice" value="'+thisvalue+'"'+invoiceadr_checked+'>' +
		'</td>';
	
	
	var td2 = '<td>' +
		'<input name="address_info'+thisvalue+'" size="20">' +
		'</td>';
	
	
	customer_name = document.getElementById('customer_name');
	if(customer_name != null)
		textarea_info = customer_name.value + "\n";
	else
		textarea_info = '';
	var td3 = '<td>' +
		'<textarea name="address_lines'+thisvalue+'" rows="4" cols="25">' + textarea_info + '</textarea>'+
		'</td>';
	
	
	var td4 = '<td>' +
		'<input '+
			'name="address_postalnum'+thisvalue+'" '+
			'size="5" '+
			'id="address_postalnum'+thisvalue+'" '+
			'onkeyup="selectPostalNumber (\'address_postalplace'+thisvalue+'\', document.getElementById(\'address_postalnum'+thisvalue+'\').value);" '+
			'autocomplete="off" ' +
		'><br />'+
		'<input '+
			'name="address_postalplace'+thisvalue+'" '+
			'size="12" '+
			'id="address_postalplace'+thisvalue+'" ' +
		'></td>';
	
	
	var td5 = '<td>'+
		'<input '+
			'name="address_country'+thisvalue+'" '+
			'size="15" '+
		'></td>';
	
	
	var td6 = '<td>'+
		'<input '+
			'type="button" '+
			'value="Fjern linje" '+
			'onclick="removeFieldAddress(\''+thisvalue+'\');" '+
		'></td>';
		
	
	var tr_slutt = '</tr>';
	
	
	$('#fieldrowsaddress').append(
		tr + td1 + td2 + td3 + td4 + td5 + td6 + tr_slutt);
	
	as[thisvalue] = new bsn.AutoSuggest('address_postalplace' + thisvalue, autos_options_post, 'address_postalnum' + thisvalue, '');
}

function removeFieldAddress (id)
{
	tr = document.getElementById('rowaddress'+id);
	tr.parentNode.removeChild(tr);
	
	return true;
}

$(document).ready(function(){
	$("#tabs").tabs();
});