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
	sum.value = belop_sum;
	sum = document.getElementById('mva_sum');
	sum.value = mva_sum;
}

function addFieldInvoice ()
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