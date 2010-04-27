<?php

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

// New class to extend pData

class jmData extends pData
{
	var $Data;
	var $DataDescription;
	
	function jmData ()
	{
		$this->Data                           = "";
		$this->DataDescription                = "";
		$this->DataDescription["Position"]    = "Name";
		$this->DataDescription["Format"]["X"] = "number";
		$this->DataDescription["Format"]["Y"] = "number";
		$this->DataDescription["Unit"]["X"]   = NULL;
		$this->DataDescription["Unit"]["Y"]   = NULL;
		
	}
	
	function setPeople ()
	{
		$this->AddSerie('c');
		$this->AddSerie('a');
		$this->AddSerie('p');
		$this->SetAbsciseLabelSerie();
		$this->SetSerieName("Barn","c");
		$this->SetSerieName("Voksne","a");
		$this->SetSerieName("Totalt","p");
		$this->SetYAxisName("Antall");
	}
	
	
	function setChildren ()
	{
		$this->AddSerie('c');
		$this->SetAbsciseLabelSerie();
		$this->SetSerieName("Barn","c");
		$this->SetYAxisName("Antall");
	}
	
	function setEntries()
	{
		$this->AddSerie('e');
		$this->SetAbsciseLabelSerie();
		$this->SetSerieName("Bookinger","e");
		$this->SetYAxisName("Antall");
	}
	function importArray ($array, $clear = true)
	{
		if(!is_array($array) || !count($array)) {
			echo 'Ingen data.';
			exit();
		}
		
		if($clear)
			$this->Data = array();
		
		foreach ($array as $value)
		{
			$this->Data[] = $value;
		}
		return true;
	}
}

?>