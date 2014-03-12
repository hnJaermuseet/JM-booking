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
	
	
	$("form[name=filters]").submit(function () {
		$.blockUI({ message: '<h1 style="font-size: 16px;">Henter statistikk,<br>vennligst vent...</h1>' });
	});
	
	// Disable / enable time fields when choosing current time or not
	$("input:radio[name='time_start_nu']").click(function() {
		time_nu_checker('time_start');
	});
	$("input:radio[name='time_end_nu']").click(function() {
		time_nu_checker('time_end');
	});
	time_nu_checker('time_start');
	time_nu_checker('time_end');
});

function time_nu_checker(time_field_name)
{
	$time_nu = $("input:radio[name='"+time_field_name+"_nu']:checked").val();
	if($time_nu == '1') // Currect time selected
	{
		// Disable time field and calendar
		$("input[name='"+time_field_name+"']").attr('disabled', 'disabled');
		$('#'+time_field_name).datepicker('disable');
	}
	else
	{
		$("input[name='"+time_field_name+"']").attr('disabled', null);
		$('#'+time_field_name).datepicker('enable');
	}
}