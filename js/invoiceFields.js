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
	
	antall			= document.getElementById("antall" + id);
	antall.value	= parseFloat(antall.value.replace(",", "."));
	if(isNaN(antall.value))
		antall.value = 0;
	
	belop_hver			= document.getElementById("belop_hver" + id);
	belop_hver.value	= Math.round(parseFloat(belop_hver.value.replace(",", "."))*100)/100;
	if(isNaN(belop_hver.value))
		belop_hver.value = 0;
	
	belop_delsum		= document.getElementById("belop_delsum" + id);
	belop_delsum.value	= Math.round(parseFloat(belop_delsum.value.replace(",", "."))*100)/100;
	if(isNaN(belop_delsum.value))
		belop_delsum.value = 0;
	
	mva			= document.getElementById("mva" + id);
	mva.value	= parseFloat(mva.value.replace(",", "."));
	if(isNaN(mva.value))
		mva.value = 0;
	
	mva_hver	= document.getElementById("mva_hver" + id);
	mva_hver.value	= Math.round(parseFloat(mva_hver.value.replace(",", "."))*100)/100;
	if(isNaN(mva_hver.value))
		mva_hver.value = 0;
	
	mva_sum_hver		= document.getElementById("mva_sum_hver" + id);
	mva_sum_hver.value	= Math.round(parseFloat(mva_sum_hver.value.replace(",", "."))*100)/100;
	if(isNaN(mva_sum_hver.value))
		mva_sum_hver.value = 0;
	
	belop_hver_real			= document.getElementById("belop_hver_real" + id);
	belop_hver_real.value	= Math.round(parseFloat(belop_hver_real.value.replace(",", "."))*100)/100;
	if(isNaN(belop_hver_real.value))
		belop_hver_real.value = 0;
	
	if(eks_mva.checked) {
		mva_hver.value			= Math.round((parseFloat(belop_hver.value) * (parseFloat(mva.value) / 100))*100)/100;
		belop_hver_real.value	= Math.round(parseFloat(belop_hver.value)*100)/100;
		mva_sum_hver.value		= Math.round((parseFloat(mva_hver.value) * parseFloat(antall.value))*100)/100;
		belop_delsum.value		= Math.round((parseFloat(belop_hver_real.value) + parseFloat(mva_hver.value)) * parseFloat(antall.value)*100)/100;
	} else {
		belop_delsum.value		= Math.round((parseFloat(belop_hver.value) * parseFloat(antall.value))*100)/100;
		mva_hver.value			= Math.round((parseFloat(belop_hver.value) * parseFloat(mva.value) / (parseFloat(mva.value) + 100))*100)/100;
		belop_hver_real.value	= Math.round((parseFloat(belop_hver.value) - parseFloat(mva_hver.value))*100)/100;
		mva_sum_hver.value		= Math.round((parseFloat(mva_hver.value) * parseFloat(antall.value))*100)/100;
	}
	
	belop_hver_real2		= document.getElementById("belop_hver_real2" + id);
	belop_hver_withtax3		= document.getElementById("belop_hver_withtax3" + id);
	belop_hver_real2.value = belop_hver_real.value; // Showing it to the user also
	belop_hver_withtax3.value = Math.round((parseFloat(belop_hver_real2.value)+parseFloat(mva_hver.value))*100)/100;
	
	updateMvaSum();
	
}

function updateMvaSum ()
{
	// Update sums
	form = document.forms['entry'];
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
	sum.value = Math.round(belop_sum*100)/100;
	sum = document.getElementById('mva_sum');
	sum.value = Math.round(mva_sum*100)/100;
}

function addFieldInvoiceWithValues (description, topay_each, tax)
{
	form = document.forms['entry'];
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
		'<textarea name="name'+thisvalue+'" cols="50" style="height: 40px;">' + description + '</textarea>' +
		'</td>';
	
	
	var td3 = '<td>' +
			'<input '+
				'type="text" '+
				'id="belop_hver'+thisvalue+'" '+
				'name="belop_hver'+thisvalue+'" '+
				'value="'+topay_each+'" '+
				'size="6" '+
				'onchange="updateMva(\''+thisvalue+'\');"'+
			'>'+
		'</td>';
	
	
	var td4 = '<td>' +
			'<input '+
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
				'type="text" '+
				'id="mva'+thisvalue+'" '+
				'name="mva'+thisvalue+'" '+
				'value="'+tax+'" '+
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
				'type="text" '+
				'id="belop_hver_real2'+thisvalue+'" '+
				'name="belop_hver_real2'+thisvalue+'" '+
				'value="0" '+
				'size="3" '+
				'disabled="disabled" '+
			'>'+
			'<input '+
				'type="hidden" '+
				'id="mva_hver'+thisvalue+'" '+
				'name="mva_hver'+thisvalue+'" '+
				'value="0" '+
				'disabled="disabled" '+
			'>'+
		'</td>'+
		'<td>' +
			'<input '+
				'type="text" '+
				'id="belop_hver_withtax3'+thisvalue+'" '+
				'name="belop_hver_withtax3'+thisvalue+'" '+
				'value="0" '+
				'size="3" '+
				'disabled="disabled" '+
			'>'+
		'</td>';
	
	
	var td8 = '<td>' +
			'<input '+
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
				'type="text" '+
				'id="belop_delsum'+thisvalue+'" '+
				'name="belop_delsum'+thisvalue+'" '+
				'value="0" '+
				'size="8" '+
				'disabled="disabled" '+
			'>'+
		'</td>';
	
	
	var td10 = '<td>'+
		'<input '+
			'type="button" '+
			'value="Fjern linje" '+
			'onclick="removeInvoiceField(\''+thisvalue+'\');" '+
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

function removeInvoiceField (id)
{
	tr = document.getElementById('row'+id);
	tr.parentNode.removeChild(tr);
	
	updateMvaSum ();
	
	return true;
}

function addFieldInvoice()
{
	// Default values for a new line
	addFieldInvoiceWithValues("", 0, 0);
}

function addFieldInvoiceWithProducts(description, topay_each, tax)
{
	// Check if a invoice line with the same values already exists
	// => If it does exist, we just add one more of it
	
	found_match = false;
	$('#invoicerows tr').each(function () {
		
		if(!found_match)
		{
			this_description  = "";
			this_topay_each   = 0;
			this_tax          = 0;
			this_id           = 0;
			
			// Checking each row
			$(this).find('input').each(function() {
				if($(this).attr('name').substr(0,10) == 'belop_hver')
				{
					this_topay_each   = $(this).val();
				}
				
				else if(
						$(this).attr('name').substr(0,3) == 'mva' &&
						$(this).attr('name').substr(3,1) != '_'
					)
				{
					this_tax          = $(this).val();
				}
				
				else if ($(this).attr('name') == 'rows[]')
				{
					this_id = $(this).val();
				}
			});
			$(this).find('textarea').each(function() {
				if($(this).attr('name').substr(0,4) == 'name')
				{
					this_description  = $(this).val();
				}
			});
			
			if (
				description == this_description &&
				topay_each == this_topay_each &&
				tax == this_tax
			) {
				// Have found a match
				found_match = true;
				
				// Adding one to amount on this line
				new_amount = parseInt($('#antall'+this_id).val())+1;
				$('#antall'+this_id).val(new_amount);
				updateMva(this_id);
			}
		}
	});
	
	if(!found_match)
		addFieldInvoiceWithValues(description, topay_each, tax);
}