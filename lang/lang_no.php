<?php

// Simple transaltor, norwegian

function langNo($text)
{
	switch($text)
	{
		case 'View month':
			return 'Vis måned';
		case 'Export to Kommfakt';
			return 'Eksportert til Kommfakt';
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
		case 'current time';
			return 'nåværende tid';
	}
	
	trigger_error('No transaltion for: '.$text);
	return $text;
}

?>