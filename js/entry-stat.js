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


function changeText(el, newText) {
	// Safari work around
	if (el.innerText) {
		el.innerText = newText;
	} else if (el.firstChild && el.firstChild.nodeValue) {
		el.firstChild.nodeValue = newText;
	}
}

function switchView(id,txt) {
	var toc = document.getElementById('switch' + id);
	if(toc)
	{
		//toc = toc.getElementsByTagName('ul')[0];
		var toggleLink = document.getElementById('switchlink' + id);
	
		if (toc && toggleLink && toc.style.display == 'none') {
			changeText(toggleLink, tocHideText + ' ' + txt);
			toc.style.display = 'table';
		} else {
			changeText(toggleLink, tocShowText + ' ' + txt);
			toc.style.display = 'none';
		}
	}
}

var tocShowText = "Vis";
var tocHideText = "Skjul";

$(document).ready(function(){
	// unblock when ajax activity stops 
	$().ajaxStop($.unblockUI);
	
	$("div .chartplaceholder").click(function () { 
		id = $(this).attr("id");
		$.blockUI({ message: '<h1><img src="img/busy.gif" /> Et øyeblikk...</h1>' });
		var filters = $("#filters").text(); 
		$.get("charts.php", { what: id, filters: filters },
			function(data){
				if(data.substr(0, 5) == 'ok - ')
				{
					$("#"+id).html('<img src="'+data.substr(5)+'"><br><br>');
					$("#"+id).attr('class','');
				}
				else
				{
					alert("Feil oppsto: " + data);
				}
			});
    });	
});