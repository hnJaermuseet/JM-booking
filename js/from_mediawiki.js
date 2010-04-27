// JS taken from mediawiki and modified


// Onload run:
histrowinit();


function historyRadios(parent) {
	var inputs = parent.getElementsByTagName('input');
	var radios = [];
	for (var i = 0; i < inputs.length; i++) {
		if (inputs[i].name == "starttime" || inputs[i].name == "endtime") {
			radios[radios.length] = inputs[i];
		}
	}
	return radios;
}

// check selection and tweak visibility/class onclick
function diffcheck() {
	var dli = false; // the li where the diff radio is checked
	var oli = false; // the li where the oldid radio is checked
	var hf = document.getElementById('bookingRadios');
	if (!hf) {
		return true;
	}
	
	// oli = first input radio
	// dli = second input radio
	
	var lis = hf.getElementsByTagName('td');
	for (var i=0;i<lis.length;i++) {
		var inputs = historyRadios(lis[i]);
		if (inputs[1] && inputs[0]) 
		{
			if (inputs[1].checked || inputs[0].checked) // this row has a checked radio button
			{
				// Both checked, same values
				if (inputs[1].checked && inputs[0].checked && inputs[0].value == inputs[1].value) {
					return false;
				}
				
				if (inputs[0].checked)
				{
					oli = lis[i];
					inputs[1].style.visibility = 'hidden';
				}
				if (inputs[1].checked)
				{
					dli = lis[i];
					inputs[0].style.visibility = 'hidden';
				}
				
				/*
				if (oli) { // First radio checked
					if (inputs[0].checked)
					{
						oli.checked = false;
						oli = lis[i];
					}
				}
				else if (dli)
				{
					if (inputs[0].checked)
					//inputs[0].style.visibility = 'hidden';
					//inputs[0].checked = false;
					//inputs[1].style.visibility = 'hidden';
					//inputs[1].checked = false;
				}
				
				/*
				if (oli) { // second radio check
					if (inputs[0].checked) { // S
						oli.className = "selected";
						return false;
					}
				} else if (inputs[1].checked) {
					return false;
				}
				if (inputs[1].checked) {
					dli = lis[i];
				}
				if (!dli) {
					inputs[0].style.visibility = 'hidden';
				}
				if (oli) {
					inputs[1].style.visibility = 'hidden';
				}
				lis[i].className = "selected";
				oli = lis[i];*/
			}
			else // no radio is checked in this row
			{
				if (!dli) {
					inputs[0].style.visibility = 'visible';
				} else {
					inputs[0].style.visibility = 'hidden';
				}
				if (!oli) {
					inputs[1].style.visibility = 'hidden';
					
					if(startup) {
						inputs[1].style.visibility = 'visible';
					}
				} else {
					inputs[1].style.visibility = 'visible';
				}
				//lis[i].className = "";
			}
		}
	}
	return true;
}

// page history stuff
// attach event handlers to the input elements on history page
function histrowinit() {
	var hf = document.getElementById('bookingRadios');
	if (!hf) {
		return;
	}
	var lis = hf.getElementsByTagName('td');
	for (var i = 0; i < lis.length; i++) {
		var inputs = historyRadios(lis[i]);
		if (inputs[0] && inputs[1]) {
			inputs[0].onclick = diffcheck;
			inputs[1].onclick = diffcheck;
		}
	}
	startup=true;
	diffcheck();
	startup=false;
}