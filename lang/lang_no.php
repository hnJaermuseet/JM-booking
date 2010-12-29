<?php

// Simple transaltor, norwegian

function langNo($text)
{
	switch($text)
	{
		case 'View month':
			return 'Vis måned';
	}
	
	trigger_error('No transaltion for: '.$text);
	return $text;
}

?>