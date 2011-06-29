<?php

// Simple transaltor, norwegian

function langNo($text)
{
	switch($text)
	{
		case 'View month':
			return 'Vis måned';
		case 'Text on infoscreen':
			return 'Tekst på infoskjerm';
		case 'Customer list':
			return 'Kundeliste';
		case 'is':
			return 'er akkurat';
		case 'is bigger than':
			return 'er større enn';
		case 'is bigger than or same as':
			return 'er større enn eller samme som';
		case 'is less than':
			return 'er mindre enn';
		case 'is less than or same as':
			return 'er mindre enn eller samme som';
		case 'current time':
			return 'nåværende tid';
		case 'E-mail content':
			return 'Innhold i e-post';
		case 'View / Don\'t view template for e-mail content';
			return 'Vis / Ikke vis mal for epost-innholdet';
		case 'View / Don\'t view PDF content as plain text':
			return 'Vis / Ikke vis innholdet i PDF som ren tekst';
		case 'View / Don\'t view template for PDF content':
			return 'Vis / Ikke vis mal for innholdet i PDF';
		case 'Choose already save template:':
			return 'Velg en lagret mal:';
		case 'Make PDF from the following template:':
			return 'Lag PDFens innhold fra følgende mal:';
		case 'Use the following template for the e-mail content:':
			return 'Bruk følgende mal for innholdet til e-post:';
		case 'Template for the content of the e-mail (not attachment):':
			return 'Mal for hva som skal stå i e-posten (ikke PDF-vedlegget):';
		case 'Templates':
			return 'Maler';
		case 'Tried sending confirmation e-mail to':
			return 'Bekreftelsesepost ble forsøkt send til';
		case 'The bookingsystem can not know if it was recived':
			return 'Bookingsystemet kan ikke vite om den kom frem';
		case 'Choose template':
			return 'Velg mal';
		case 'Content of PDF file':
			return 'PDF-filens innhold';
		case 'can be edited here':
			return 'kan endres her';
		case 'Content of the e-mail':
			return 'E-postens innhold';
		case 'Change the templates':
			return 'Endre på malene';
		case 'Users not in this group':
			return 'Brukere som ikke er i gruppen';
		case 'Add user to group':
			return 'Legg brukeren til gruppen';
		case 'You have not selected a template.':
			return 'Du har ikke valgt en mal.';
		case 'Are you sure you want to send?':
			return 'Er du sikker på at du vil sende?';
		case 'Incorrect email(s)':
			return 'Ugyldig epost(er)';
		case 'One or more emails are incorrect.':
			return 'En eller flere eposter er ugyldige.';
		case 'Fix them or remove them.':
			return 'Rett opp i e-postene eller stryk de ut.';
		case 'Go to today':
			return 'Gå til i dag';
		case 'Go to next day':
			return 'Gå til neste dag';
		case 'Go to previous day':
			return 'Gå til forrige dag';
		case 'Go to other dayview':
			return 'Gå til den andre dagsvisningen';
		case 'Syncronize with Exchange':
			return 'Synkronisering med Exchange ("Outlook")';
		case 'Main Exchange e-mail':
			return 'Hovedadresse i Exchange ("Outlook")';
		case 'Datanova import':
			return 'Datanova-import';
		case 'Shop id':
			return 'Butikknr.';
		case 'Alert email(s)':
			return 'Varlingsepostadresse(r)';
		case 'Email(s) receiving alerts when new goods are detected for this shop.':
			return 'E-postadresse(r) som får varsling når nye varer, som systemet ikke kjenner, blir funnet for dette butikknret';
		case 'Can be multiple emails seperated by space, comma or semicolon.':
			return 'Kan være flere epostadresser skilt med mellomrom, komma eller semikolon.';
		/*
		case '':
			return '';
		*/
	}
	
	trigger_error('No translation for: '.$text);
	return $text;
}

?>