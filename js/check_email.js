function checkEmail (value) {
	if(value == "")
		return true;
	else
		return (/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(value));
}

function checkEmailAndAlert (field)
{
	// Check if the td.message is in place
	tr = field.parent().parent();
	if(tr.children('td.message').length == 0)
	{
		tr.append('<td class="message"></td>');
	}
	tdmessage = tr.children('td.message');
	
	checked = tr.find('input[type=checkbox]').attr('checked');
	
	if(checked && !checkEmail (field.val()))
	{
		// Invalid email
		field.addClass('haserror');
		tdmessage.children('.emailok').slideUp('slow');
		if(tdmessage.children('.emailerror').length == 0)
		{
			tdmessage.append('<div class="emailerror"><img src="img/icons/delete.png"> Ugyldig e-post</div>');
		}
		else
		{
			tdmessage.children('.emailerror').slideDown('slow');
		}
	}
	else if(checked && field.val() != '')
	{
		// Valid email
		field.removeClass('haserror');
		tdmessage.children('.emailerror').slideUp('slow');
		if(tdmessage.children('.emailok').length == 0)
		{
			tdmessage.append('<div class="emailok"><img src="img/icons/accept.png"></div>');
		}
		else
		{
			tdmessage.children('.emailok').slideDown('slow');
		}
	}
	else
	{
		// No text or not checked, but not invalid
		field.removeClass('haserror');
		tdmessage.children('.emailerror').slideUp('slow');
		tdmessage.children('.emailok').slideUp('slow');
	}
}
function checkEmailAndAlert_editentry (field)
{
	// Check if the td.message is in place
	tr = field.parent().parent();
	if(tr.children('td.message').length == 0)
	{
		tr.append('<td class="message"></td>');
	}
	tdmessage = tr.children('td.message');
	
	checked = tr.find('input[type=checkbox]').attr('checked');
	
	if(!checkEmail (field.val()))
	{
		// Invalid email
		field.addClass('haserror');
		tdmessage.children('.emailok').slideUp('slow');
		if(tdmessage.children('.emailerror').length == 0)
		{
			tdmessage.append('<div class="emailerror"><img src="img/icons/delete.png"> Ugyldig e-post</div>');
		}
		else
		{
			tdmessage.children('.emailerror').slideDown('slow');
		}
	}
	else if(field.val() != '')
	{
		// Valid email
		field.removeClass('haserror');
		tdmessage.children('.emailerror').slideUp('slow');
		if(tdmessage.children('.emailok').length == 0)
		{
			tdmessage.append('<div class="emailok"><img src="img/icons/accept.png"></div>');
		}
		else
		{
			tdmessage.children('.emailok').slideDown('slow');
		}
	}
	else
	{
		// No text or not checked, but not invalid
		field.removeClass('haserror');
		tdmessage.children('.emailerror').slideUp('slow');
		tdmessage.children('.emailok').slideUp('slow');
	}
}