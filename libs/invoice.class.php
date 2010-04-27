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


/*
	Invoice class
	
	- Made by Hallvard Nygård <futti@warpcrew.com>
	
	Parts of this file is not translated from norwegian
	
	*/

require "libs/html2pdf/html2fpdf.php";

class invoice
{
	var $gotten = FALSE;
	var $invoice_id;
	
	var $invoice_to_customer_name = '';
	var $invoice_to_customer_id	= 0;
	var $invoice_to_address_id	= 0;
	
	var $invoice_to_line1		= '';
	var $invoice_to_line2		= '';
	var $invoice_to_line3		= '';
	var $invoice_to_line4		= '';
	var $invoice_to_line5		= '';
	var $invoice_to_line6		= '';
	var $invoice_to_line7		= '';
	var $invoice_to_lines		= '';
	var $invoice_to_lines2		= '';
	var $invoice_to_email		= '';
	var $invoice_electronic		= '';
	var $invoice_electronic2	= '';
	
	var $invoice_comment			= '';
	var $invoice_internal_comment	= '';
	var $invoice_ref_your			= '';
	
	var $invoice_time			= '';
	var $invoice_time2			= '';
	var $invoice_time_due		= '';
	var $invoice_time_due2		= '';
	var $invoice_content = array();
	
	var $invoice_created_by_id		= 0;
	var $invoice_created_by_name	= '';
	
	var	$invoice_idlinks		= array();
	var	$invoice_idlinks2		= array();
	
	var $invoice_payed			= 0;
	var $invoice_payed2			= false;
	var $invoice_payed_amount	= 0;
	var $invoice_payment_left	= 0;
	
	var $invoice_payment_comment	= '';
	
	var $invoice_topay_total		= 0;
	var $invoice_topay_total_net	= 0;
	var $invoice_topay_total_tax	= 0;
	
	var $variables_to_template	= array (
		'invoice_id',
		'invoice_to_customer_name',
		'invoice_to_customer_id',
		'invoice_to_address_id',
		'invoice_to_line1',
		'invoice_to_line2',
		'invoice_to_line3',
		'invoice_to_line4',
		'invoice_to_line5',
		'invoice_to_line6',
		'invoice_to_line7',
		'invoice_to_lines',
		'invoice_to_lines2',
		'invoice_to_lines3',
		'invoice_to_email',
		'invoice_electronic',
		'invoice_electronic2',
		'invoice_comment',
		'invoice_internal_comment',
		'invoice_ref_your',
		'invoice_time',
		'invoice_time2',
		'invoice_time_due',
		'invoice_time_due2',
		'invoice_created_by_id',
		'invoice_created_by_name',
		'invoice_content',
		'invoice_topay_total',
		'invoice_topay_total_net',
		'invoice_topay_total_tax',
		'invoice_payed',
		'invoice_payed2',
		'invoice_payed_amount',
		'invoice_payment_left',
		'invoice_payment_comment',
		'invoice_idlinks2'
	);
	
	// Konstruktør
	function invoice ($invoice_id = 0)
	{
		if($invoice_id != 0)
			$this->invoice_id = $invoice_id;
		
		$this->invoice_time 		= date('Ymd');
		$this->invoice_time_due 	= date('Ymd', (time() + (14*24*60*60)));
		$this->invoice_time2		= $this->hent_ddmmyyyy($this->invoice_time);
		$this->invoice_time_due2	= $this->hent_ddmmyyyy($this->invoice_time_due);
	}
	
	function check_inndata ($var, $value)
	{
		$this->TEMP_inndata_error = '';
		
		switch ($var)
		{
			case 'invoice_time_due':
			case 'invoice_time':
					// YYYYMMDD
					if(strlen($value) == 8)
						return TRUE;
					else
					{
						if($var == 'invoice_time_due')
							$this->TEMP_inndata_error = 'Forfallsdato er ugyldig.';
						else
							$this->TEMP_inndata_error = 'Fakturadato er ugyldig.';
						return FALSE;
					}
				break;
			
			case 'invoice_content':
				if(!is_array($value))
				{
					// Sjekker om det allerede er en feil med invoice_innhold
					if(substr($value, 0, strlen('invoice_content_error')) == 'invoice_content_error')
					{
						$abc = explode(' ', $value, 2);
						switch($abc[1])
						{
							case 'not_array':
								$this->TEMP_inndata_error = 'Problemer med formatet på innholdet i fakturaen ('.$abc[1].').';
								return FALSE;
							case 'produkttype unknown':
								$this->TEMP_inndata_error = 'Ukjent produkttype.';
								return FALSE;
							case 'not_numeric topay_each':
								$this->TEMP_inndata_error = 'Prisen for produktene må være tall (ikke legg til valutta).';
								return FALSE;
							case 'not_numeric amount':
								$this->TEMP_inndata_error = 'Antallet av produktene må være tall.';
								return FALSE;
							case 'not_numeric tax':
								$this->TEMP_inndata_error = 'MVA på produktene må være et tall. Kan være 0.';
								return FALSE;
							case 'not_numeric id_of_type':
								$this->TEMP_inndata_error = 'ID_type ikke tall.';
								return FALSE;
							case 'produkt not_array':
								$this->TEMP_inndata_error = 'Problemer med formatet på innholdet i fakturaen ('.$abc[1].').';
								return FALSE;
							case 'unknown_field':
								$this->TEMP_inndata_error = 'Problemer med et felt i fakturaen.';
								return FALSE;
							default:
								$this->TEMP_inndata_error = 'Problemer med formatet på innholdet i fakturaen ('.$abc[1].')';
								return FALSE;
						}
					}
					
					$this->TEMP_inndata_error = 'Problemer med formatet på innholdet i fakturaen.';
					return FALSE;
				}
				else
				{
					if(!count($value))
					{
						$this->TEMP_inndata_error = 'Intet innhold i fakturaen.';
						return FALSE;
					}
					else
						return TRUE;
				}
				break;
			
			case 'invoice_to_line1':
			case 'invoice_to_line2':
			case 'invoice_to_line3':
			case 'invoice_to_line4':
			case 'invoice_to_line5':
			case 'invoice_to_line6':
			case 'invoice_to_line7':
			case 'invoice_to_email':
			case 'invoice_comment':
			case 'invoice_internal_comment':
			case 'invoice_ref_your':
				return true;
				break;
			
			case 'invoice_created_by_id':
			case 'invoice_to_customer_id':
			case 'invoice_to_address_id':
			case 'invoice_electronic':
				return TRUE;
				break;
			
			case 'invoice_idlinks':
				if(!is_array($value))
				{
					$this->TEMP_inndata_error = 'Feil format på innlenkede elementer, 1.';
					return FALSE;
				}
				else
				{
					foreach($value as $link)
					{
						list($idtype, $id) = explode('=', $link);
						if(!is_numeric($id))
						{
							$this->TEMP_inndata_error = 'Feil format på innlenkede elementer, 2.';
							return FALSE;
						}
						switch ($idtype)
						{
							/* These idlinks are okey: */
							case 'e': // Entry
								break;
							default:
								$this->TEMP_inndata_error = 'Feil format på innlenkede elementer, 3.';
								return FALSE;
								break;
						}
					}
				}
				return TRUE;
				
				break;
			
			default:
				$this->TEMP_inndata_error = 'Ukjent variabel ('.$var.').';
				return FALSE;
				break;
		}
	}
	
	// Kjører inndata gjennom denne funksjonen for å ta vekk stygge greier som kan være med
	function inndata_behandling ($var, $value)
	{
		switch ($var)
		{
			case 'invoice_content': // Tricky one
				// $value = array
				$value2 = array(); // Behandlede
				if(!is_array($value))
					return 'invoice_content_error not_array'; // Feilmelding må genereres av sjekk_inndata()
				else
				{
					$i = 1; // Starter på linjenr 1
					foreach ($value as $linenum => $plinje)
					{
						if (is_array ($plinje))
						{
							$value2[$i] = array();
							
							foreach ($plinje as $type => $valueen)
							{
								switch ($type)
								{
									case 'type':
										switch ($valueen)
										{
											// These contenttypes are allowed:
											case 'belop':
												$valueen = '';
											case 'entry':
											case '':
												$value2[$i][$type] = $valueen;
												break;
											default:
												return 'invoice_content_error produkttype unknown'; // Feilmelding må genereres av sjekk_inndata()
										}
										break;
									
									case 'name':
										$value2[$i][$type] = htmlspecialchars(strip_tags($valueen),ENT_QUOTES);
										break;
									
									// Tall:
									case 'topay_each':
									case 'amount':
									case 'tax':
										if(!is_numeric($valueen))
										{
											return "invoice_content_error not_numeric $type";
										}
										else
										{
											// Caster ikke til int, kan være 150,50 eller 1,5
											// Replacer , med . i tilfelle norske desimal-tegn er kommet med
											$value2[$i][$type] = str_replace(',','.',$valueen); 
										}
										break;
									
									case 'id_of_type':
										if(!is_numeric($valueen))
										{
											return "invoice_content_error not_numeric $type";
										}
										else
											$value2[$i][$type] = (int)$valueen; // Caster til int
										break;
									
									case 'mva_eks':
										// ignore
										if($valueen)
											$value2[$i][$type] = true;
										else
											$value2[$i][$type] = false;
										break;
									
									default:
										return 'invoice_content_error unknown_field';
										break;
								}
							}
						}
						else
							return 'invoice_content_error produkt not_array'; // Feilmelding må genereres av sjekk_inndata()
						
						if(isset($value2[$i]['topay_each']) && isset($value2[$i]['amount']))
						{
							$value2[$i]['topay_total_net']	= $value2[$i]['topay_each'] * $value2[$i]['amount'];
							$value2[$i]['tax_total']		= $value2[$i]['tax'] * $value2[$i]['topay_total_net'];
							$value2[$i]['topay_total']		= $value2[$i]['topay_total_net'] + $value2[$i]['tax_total'];
						} else
						{
							$value2[$i]['topay_total_net'] = 0;
							$value2[$i]['tax_total'] = 0;
							$value2[$i]['topay_total'] = 0;
						}
						
						
						$i++;
					}
				}
				
				// Return $invoice_innhold
				return $value2;
				break;
			
			case 'invoice_created_by_id':
			case 'invoice_to_customer_id':
			case 'invoice_to_address_id':
				return (int)$value;
				break;
			
			case 'invoice_electronic':
				if($value == '1')
					return 1;
				else
					return 0;
				break;
			
			case 'invoice_to_line1':
			case 'invoice_to_line2':
			case 'invoice_to_line3':
			case 'invoice_to_line4':
			case 'invoice_to_line5':
			case 'invoice_to_line6':
			case 'invoice_to_line7':
			case 'invoice_to_email':
			case 'invoice_comment':
			case 'invoice_internal_comment':
			case 'invoice_ref_your':
				// Tekst
				return htmlspecialchars($value,ENT_QUOTES);
				break;
			
			case 'invoice_idlinks':
				if(!is_array($value) && $value == '')
					return array();
				else
					return $value;
				break;
				
			default:
				return $value;
		}
	}
	
	function doDaChecking (
		$invoice_time_due,
		$invoice_time,
		$invoice_content,
		$invoice_extracontent = array()
		)
	{
		//foreach ($invoice_extracontent as $var => $value)
		//{
		//	$$var = $value;
		//}
		
		$this->insert_this = array(
			'invoice_time',
			'invoice_time_due',
			'invoice_content',
			'invoice_idlinks',
			'invoice_created_by_id',
			'invoice_to_customer_id',
			'invoice_to_address_id',
			'invoice_to_line1',
			'invoice_to_line2',
			'invoice_to_line3',
			'invoice_to_line4',
			'invoice_to_line5',
			'invoice_to_line6',
			'invoice_to_line7',
			'invoice_to_email',
			'invoice_electronic',
			'invoice_comment',
			'invoice_internal_comment',
			'invoice_ref_your');
		
		/* ### SJEKKER INNDATA ### */
		
		/*
			Følgende trenger inndata:
			invoice_time_due
			invoice_time
			invoice_content
		*/
		
		$this->inndata_var = array (
			'invoice_time',
			'invoice_time_due',
			'invoice_content');
		
		$this->invoice_time		= $invoice_time;
		$this->invoice_time_due	= $invoice_time_due;
		$this->invoice_content	= $invoice_content;
		if(isset($invoice_extracontent['invoice_idlinks']))
		{
			$this->invoice_idlinks = $invoice_extracontent['invoice_idlinks'];
			$this->inndata_var[] = 'invoice_idlinks';
		}
		else
			$this->invoice_idlinks = array();
		/*
		if(isset($invoice_extracontent['invoice_idlinks']) && is_array($invoice_extracontent['invoice_idlinks']))
		{
			$this->invoice_idlinks = splittalize($invoice_extracontent['invoice_idlinks']);
		}
		else
			$this->invoice_idlinks = splittalize(array());
		*/
		
		// invoice_to_customer_id
		if(isset($invoice_extracontent['invoice_to_customer_id']))
		{
			$this->invoice_to_customer_id = $invoice_extracontent['invoice_to_customer_id'];
			$this->inndata_var[] = 'invoice_to_customer_id';
		}
		else
			$this->invoice_to_customer_id = '0';
		
		// invoice_to_address_id
		if(isset($invoice_extracontent['invoice_to_address_id']))
		{
			$this->invoice_to_address_id = $invoice_extracontent['invoice_to_address_id'];
			$this->inndata_var[] = 'invoice_to_address_id';
		}
		else
			$this->invoice_to_address_id = '0';
		
		// invoice_to_line1-7
		if(isset($invoice_extracontent['invoice_to_line1']))
		{
			$this->invoice_to_line1 = $invoice_extracontent['invoice_to_line1'];
			$this->inndata_var[] = 'invoice_to_line1';
		}
		else
			$this->invoice_to_line1 = '';
		if(isset($invoice_extracontent['invoice_to_line2']))
		{
			$this->invoice_to_line2 = $invoice_extracontent['invoice_to_line2'];
			$this->inndata_var[] = 'invoice_to_line2';
		}
		else
			$this->invoice_to_line2 = '';
		if(isset($invoice_extracontent['invoice_to_line3']))
		{
			$this->invoice_to_line3 = $invoice_extracontent['invoice_to_line3'];
			$this->inndata_var[] = 'invoice_to_line3';
		}
		else
			$this->invoice_to_line3 = '';
		if(isset($invoice_extracontent['invoice_to_line4']))
		{
			$this->invoice_to_line4 = $invoice_extracontent['invoice_to_line4'];
			$this->inndata_var[] = 'invoice_to_line4';
		}
		else
			$this->invoice_to_line4 = '';
		if(isset($invoice_extracontent['invoice_to_line5']))
		{
			$this->invoice_to_line5 = $invoice_extracontent['invoice_to_line5'];
			$this->inndata_var[] = 'invoice_to_line5';
		}
		else
			$this->invoice_to_line5 = '';
		if(isset($invoice_extracontent['invoice_to_line6']))
		{
			$this->invoice_to_line6 = $invoice_extracontent['invoice_to_line6'];
			$this->inndata_var[] = 'invoice_to_line6';
		}
		else
			$this->invoice_to_line6 = '';
		if(isset($invoice_extracontent['invoice_to_line7']))
		{
			$this->invoice_to_line7 = $invoice_extracontent['invoice_to_line7'];
			$this->inndata_var[] = 'invoice_to_line7';
		}
		else
			$this->invoice_to_line7 = '';
		if(isset($invoice_extracontent['invoice_to_email']))
		{
			$this->invoice_to_email = $invoice_extracontent['invoice_to_email'];
			$this->inndata_var[] = 'invoice_to_email';
		}
		else
			$this->invoice_to_email = '';
		if(isset($invoice_extracontent['invoice_to_electronic']))
		{
			$this->invoice_to_electronic = $invoice_extracontent['invoice_to_electronic'];
			$this->inndata_var[] = 'invoice_to_electronic';
		}
		else
			$this->invoice_to_electronic = '1';
		
		// invoice_comment
		if(isset($invoice_extracontent['invoice_comment']))
		{
			$this->invoice_comment = $invoice_extracontent['invoice_comment'];
			$this->inndata_var[] = 'invoice_comment';
		}
		else
			$this->invoice_comment = '';
		
		// invoice_internal_comment
		if(isset($invoice_extracontent['invoice_internal_comment']))
		{
			$this->invoice_internal_comment = $invoice_extracontent['invoice_internal_comment'];
			$this->inndata_var[] = 'invoice_internal_comment';
		}
		else
			$this->invoice_internal_comment = '';
		
		// invoice_ref_your
		if(isset($invoice_extracontent['invoice_ref_your']))
		{
			$this->invoice_ref_your = $invoice_extracontent['invoice_ref_your'];
			$this->inndata_var[] = 'invoice_ref_your';
		}
		else
			$this->invoice_ref_your = '';
		
		// invoice_created_by_id
		if(isset($invoice_extracontent['invoice_created_by_id']))
		{
			$this->invoice_created_by_id = $invoice_extracontent['invoice_created_by_id'];
			$this->inndata_var[] = 'invoice_created_by_id';
		}
		else
			$this->invoice_created_by_id = 0;
		
		//$invoice_log_txt = '';
		//$this->log_data = array();
		$inndata_error = array();
		foreach ($this->inndata_var as $variabelen)
		{
			// Sjekker om dataene er OK
			$this->$variabelen = $this->inndata_behandling($variabelen, $this->$variabelen);
			
			if(!$this->check_inndata($variabelen, $this->$variabelen))
			{
				$inndata_error[$variabelen] = $this->TEMP_inndata_error;
			}
			else
			{
				//if($variabelen == 'invoice_content')
				//	$this->log_data[] = "$variabelen = ".serialize($$variabelen);
				//else
				//	$this->log_data[] = "$variabelen = ".$$variabelen;
			}
		}
		
		// Feil med inndata?
		if(count($inndata_error) > 0)
		{
			$this->error_code = '100'; // Det oppsto feil med dataene sendt til server.
			$this->inndata_error = $inndata_error;
			return FALSE;
		}
		
		/* 
			Disse trenger ikke inndata:
		*/
		$this->insert_this[] = 'invoice_time_created';
		$this->insert_this[] = 'invoice_time_payed';
		$this->insert_this[] = 'invoice_topay_total';
		$this->insert_this[] = 'invoice_topay_total_net';
		$this->insert_this[] = 'invoice_topay_total_tax';
		$this->insert_this[] = 'invoice_payed';
		$this->insert_this[] = 'invoice_payed_amount';
		
		$this->invoice_time_created		= time();
		$this->invoice_time_payed		= '0';
		
		$this->invoice_topay_total		= $this->hent_sum ($this->invoice_content, 'topay_total');
		$this->invoice_topay_total_net	= $this->hent_sum ($this->invoice_content, 'topay_total_net');
		$this->invoice_topay_total_tax	= $this->hent_sum ($this->invoice_content, 'tax_total');
		$this->invoice_payed			= '0';
		$this->invoice_payed_amount		= '0';
		/* Slutt-ikke_inndata */
		
		if($this->invoice_topay_total <= 0)
		{
			$this->error_code = '100'; // Det oppsto feil med dataene sendt til server.
			$this->inndata_error = array('invoice_topay_total' => 'Fakturaen ble på 0 eller mindre. Kun positive tall blir akseptert.');
			return FALSE;
		}
		
		$this->updateNonDBFields();
		
		return true;
	}
	
	function create (
		$invoice_time_due,
		$invoice_time,
		$invoice_content,
		$invoice_extracontent = array()
		)
	{
		if(!$this->doDaChecking($invoice_time_due,
			$invoice_time,
			$invoice_content,
			$invoice_extracontent))
		{
				return FALSE;
		}
		// Serialize
		$this->invoice_content = serialize($this->invoice_content);
		$this->invoice_idlinks = splittalize($this->invoice_idlinks);
		
		/* ### SQL, inndata sjekket ### */
		$SQL = "INSERT INTO `invoice` 
			(";
		$i = 0;
		foreach ($this->insert_this as $denne)
		{
			$i++;
			$SQL .= "`$denne`";
			if(count($this->insert_this) != $i)
				$SQL .= ' , ';
		}
		$SQL .= ")
		VALUES (";
		$i = 0;
		$this->log_data = array();
		foreach ($this->insert_this as $denne)
		{
			$i++;
			$value = $this->$denne;
			$SQL .= "'".$value."'";
			if(count($this->insert_this) != $i)
				$SQL .= ' , ';
			$this->log_data[$denne] = $value;
		}
		$SQL .= ");";
		
		mysql_query($SQL);
		if(mysql_error())
		{
			$this->error_code = '1';
			return FALSE;
		}
		
		$this->invoice_id		= mysql_insert_id();
		
		newInvoiceLog($this->invoice_id, 'add', '', 1, $this->log_data);
		
		$this->invoice_content = unserialize($this->invoice_content);
		$this->invoice_idlinks = splittString($this->invoice_idlinks);
		$this->updateNonDBFields ();
		
		$this->createPDF();
		
		/* Linked to any entries? */
		foreach ($this->invoice_idlinks as $link)
		{
			list($idtype, $id) = explode('=', $link);
			switch ($idtype)
			{
				case 'e': // Entry
					$entry = getEntry ($id);
					if(count($entry))
					{
						/*
						 * Set new status
						 * Set new rev_num, time of edit, etc
						 */
						$rev_num = $entry['rev_num']+1;
						mysql_query("UPDATE `entry` SET `invoice_status` = '3', `user_last_edit` = '".$this->invoice_created_by_id."', `time_last_edit` = '".time()."', `rev_num` = '$rev_num' WHERE `entry_id` = '".$entry['entry_id']."' LIMIT 1 ;");
						
						$log_data = array();
						if(!newEntryLog($entry['entry_id'], 'edit', 'invoice_made', $rev_num, $log_data))
						{
						}
					}
					break;
				
				default:
					// Do nothing
				break;
			}
		}
		
		$this->getCreatedBy();
		
		return TRUE;
	}
	
	function get()
	{
		// Henter en invoice
		$this->gotten = FALSE;
		
		// hentes via $this->invoice_id
		if(!isset($this->invoice_id) || !is_numeric($this->invoice_id))
		{
			$this->error_code = '101'; // ID til fakturaen er ikke oppgitt eller den er ikke et tall.
			return FALSE;
		}
		
		$Q_invoice = mysql_query("select * from `invoice` where invoice_id = '".$this->invoice_id."'");
		if(mysql_error())
		{
			$this->error_code = '1';
			return FALSE;
		}
		
		if(!mysql_num_rows($Q_invoice))
		{
			$this->error_code = '102'; // Finner ikke fakturaen.
			return FALSE;
		}
		
		$array_hent = array
		(
			'invoice_id',
			'invoice_time_created',
			'invoice_time',
			'invoice_time_due',
			'invoice_time_payed',
			'invoice_content',
			'invoice_topay_total',
			'invoice_topay_total_net',
			'invoice_topay_total_tax',
			'invoice_payed',
			'invoice_payed_amount',
			'invoice_idlinks',
			'invoice_to_customer_id',
			'invoice_to_address_id',
			'invoice_to_line1',
			'invoice_to_line2',
			'invoice_to_line3',
			'invoice_to_line4',
			'invoice_to_line5',
			'invoice_to_line6',
			'invoice_to_line7',
			'invoice_to_email',
			'invoice_electronic',
			'invoice_comment',
			'invoice_internal_comment',
			'invoice_ref_your',
			'invoice_created_by_id'
		);
		
		// Flytter resultatet inn i klassen
		foreach ($array_hent as $henter)
		{
			if($henter == 'invoice_content')
				$this->$henter = unserialize(mysql_result($Q_invoice, '0', $henter));
			elseif($henter == 'invoice_idlinks')
				$this->$henter = splittString(mysql_result($Q_invoice, '0', $henter));
			else
				$this->$henter = mysql_result($Q_invoice, '0', $henter);
		}
		
		$this->updateNonDBFields ();
		$this->populateIdlinks();
		$this->getCreatedBy();
		$this->getCustomerName();
		
		$this->invoice_log = getInvoiceLog($this->invoice_id, true);
		
		$this->invoice_payment_comment = '';
		$this->invoice_payment_time = '';
		foreach ($this->invoice_log as $log)
		{
			if($log['log_action'] == 'edit' && $log['log_action2'] == 'invoice_payed')
			{
				if(array_key_exists('invoice_payment_comment', $log['log_data']))
				{
					if($this->invoice_payment_comment == '')
						$this->invoice_payment_comment = $log['log_data']['invoice_payment_comment'];
					else
						$this->invoice_payment_comment = $this->invoice_payment_comment.chr(10).
							$log['log_data']['invoice_payment_comment'];
				}
				if(array_key_exists('invoice_time_payed', $log['log_data']))
				{
					if($this->invoice_payment_time == '')
						$this->invoice_payment_time = date('Y-m-d', $log['log_data']['invoice_time_payed']);
					else
						$this->invoice_payment_time = $this->invoice_payment_time.chr(10).
							date('Y-m-d', $log['log_data']['invoice_time_payed']);
				}
			}
		}
		
		$this->gotten = TRUE;
		return TRUE;
	}
	
	function sjekk_hentstatus()
	{
		if(!$this->gotten)
		{
			if(!$this->get())
			{
				return FALSE; // Feil oppsto
			}
			else
				return TRUE; // Ingen feil...
		}
		else
			return TRUE; // Allerede hentet...
	}

	function makeToLines ()
	{
		$tmp = array();
		$addline = 0;
		if($this->invoice_to_line1 != '')
			$tmp[] = $this->invoice_to_line1;
		else
			$addline++;
		if($this->invoice_to_line2 != '')
			$tmp[] = $this->invoice_to_line2;
		else
			$addline++;
		if($this->invoice_to_line3 != '')
			$tmp[] = $this->invoice_to_line3;
		else
			$addline++;
		if($this->invoice_to_line4 != '')
			$tmp[] = $this->invoice_to_line4;
		else
			$addline++;
		if($this->invoice_to_line5 != '')
			$tmp[] = $this->invoice_to_line5;
		else
			$addline++;
		if($this->invoice_to_line6 != '')
			$tmp[] = $this->invoice_to_line6;
		else
			$addline++;
		if($this->invoice_to_line7 != '')
			$tmp[] = $this->invoice_to_line7;
		else
			$addline++;
		$this->invoice_to_lines = implode(chr(10), $tmp);
		
		$this->invoice_to_lines2 = 
			$this->invoice_to_line1.chr(10).
			$this->invoice_to_line2.chr(10).
			$this->invoice_to_line3.chr(10).
			$this->invoice_to_line4.chr(10).
			$this->invoice_to_line5.chr(10).
			$this->invoice_to_line6.chr(10).
			$this->invoice_to_line7.chr(10)
			;
		
		/*	$addline defines if there are any empty lines
			Empty lines will be added at the bottom so that
			the invoice_to_lines3 always keeps the same amount of lines */
		$this->invoice_to_lines3 = $this->invoice_to_lines;
		for ($i = 0; $i <= $addline; $i++)
			$this->invoice_to_lines3 .= chr(10);
	}
	
	function updateNonDBFields () {
		$this->makeToLines();
		if($this->invoice_payed == '1')
			$this->invoice_payed2 = true;
		else
			$this->invoice_payed2 = false;
		$this->invoice_time2		= $this->hent_ddmmyyyy($this->invoice_time);
		$this->invoice_time_due2	= $this->hent_ddmmyyyy($this->invoice_time_due);
		if($this->invoice_electronic == '1')
			$this->invoice_electronic2 = true;
		else
			$this->invoice_electronic2 = false;
		
		$this->invoice_payment_left = $this->invoice_topay_total - $this->invoice_payed_amount;
	}
	
	function register_payment ($payment_time, $payment_amount, $payment_comment) 
	{
		if(!$this->sjekk_hentstatus())
		{
			return FALSE; // Feil oppsto
		}
		
		/* ## SJEKKER ULIKE TING ## */
		
		// Behandler tidspunktet
		$str2 = array();
		$str = '';
		for ($i = 0; $i < strlen($payment_time); $i++)
		{
			$char = $payment_time{($i)};
			if(is_numeric($char))
			{
				$str .= $char;
			}
			else
			{
				$str2[] = $str;
				$str = '';
			}
		}
		if($str != '')
			$str2[] = $str;
		
		if(count($str2) != 3)
		{
			$this->error_code = '106'; // Ugyldig dato for betalingstidspunktet.
			return FALSE;
		}
		
		$payment_amount = str_replace(',', '.', $payment_amount);
		$payment_amount = round((float)$payment_amount,2);
		
		if($payment_amount < 0)
		{
			$this->error_code = '107'; // Det kan ikke registeres minus-betalinger
			return FALSE;
		}
		
		$this->invoice_time_payed = mktime(0, 0, 0, $str2[1], $str2[0], $str2[2]);
		$this->invoice_payed_amount += $payment_amount;
		
		if($this->invoice_payed_amount >= $this->invoice_topay_total)
			$this->invoice_payed = 1;
		else
			$this->invoice_payed = 0;
		
		$SQL = "UPDATE `invoice` SET 
			`invoice_time_payed`	= '".$this->invoice_time_payed."',
			`invoice_payed`			= '".$this->invoice_payed."',
			`invoice_payed_amount`	= '".$this->invoice_payed_amount."'
			WHERE `invoice_id` ='".$this->invoice_id."' LIMIT 1 ;";
		
		$log_data = array();
		$log_data['invoice_time_payed']					= $this->invoice_time_payed;
		$log_data['invoice_payed']						= $this->invoice_payed;
		$log_data['invoice_payment_amount']				= $payment_amount;
		$log_data['invoice_payment_comment']			= htmlspecialchars($payment_comment,ENT_QUOTES);
		
		mysql_query($SQL);
		if(mysql_error())
		{
			$this->error_code = '1';
			return FALSE;
		}
		
		newInvoiceLog($this->invoice_id, 'edit', 'invoice_payed', 1, $log_data);
		
		/* Linked to any entries? */
		foreach ($this->invoice_idlinks as $link)
		{
			list($idtype, $id) = explode('=', $link);
			switch ($idtype)
			{
				case 'e': // Entry
					$entry = getEntry ($id);
					if(count($entry))
					{
						/*
						 * Set new status
						 * Set new rev_num, time of edit, etc
						 */
						$rev_num = $entry['rev_num']+1;
						mysql_query("UPDATE `entry` SET `invoice_status` = '4', `user_last_edit` = '".$this->invoice_created_by_id."', `time_last_edit` = '".time()."', `rev_num` = '$rev_num' WHERE `entry_id` = '".$entry['entry_id']."' LIMIT 1 ;");
						
						$log_data = array();
						if(!newEntryLog($entry['entry_id'], 'edit', 'invoice_payed', $rev_num, $log_data))
						{
						}
					}
					break;
				
				default:
					// Do nothing
				break;
			}
		}
		return TRUE;
		
	}
	
	// Henter sum
	function hent_sum ($invoice_innhold, $var)
	{
		$sum = 0;
		foreach ($invoice_innhold as $linje)
		{
			$sum += $linje[$var];
		}
		return $sum;
	}
	
	// Slitter opp og returnerer array
	function hent_ddmmyyyy ($yyyymmdd)
	{
		$return = array();
		$return['year']		= substr($yyyymmdd, 0, 4);
		$return['month']	= substr($yyyymmdd, 4, 2);
		$return['day']		= substr($yyyymmdd, 6, 2);
		return $return;
	}
	
	function populateIdlinks() {
		$this->invoice_idlinks2 = array();
		foreach($this->invoice_idlinks as $link)
		{
			list($idtype, $id) = explode('=', $link);
			$tmp = array();
			switch ($idtype)
			{
				case 'e': // Entry
					$thisentry = getEntry ($id);
					if(!count($thisentry))
					{
						$tmp['link'] = '';
						$tmp['name'] = '(BID'.$id.') UKJENT BOOKING (ikke funnet i databasen)';
					}
					else
					{
						$tmp['link'] = 'entry.php?entry_id='.$thisentry['entry_id'].'';
						$tmp['name'] = '(booking) '.$thisentry['entry_name'];
					}
					break;
				
				default:
					$tmp['link'] = '';
					$tmp['name'] = _('Unknown sourcetype');
				break;
			}
			$this->invoice_idlinks2[] = $tmp;
		}
	}

	function getCreatedBy() {
		$this->invoice_created_by_name = '';
		if($this->invoice_created_by_id != 0) {
			$user = getUser($this->invoice_created_by_id);
			if(count($user)){
				$this->invoice_created_by_name = $user['user_name'];
			}
		}
	}

	function getCustomerName() {
		$this->invoice_to_customer_name = '';
		if($this->invoice_to_customer_id != 0) {
			$customer = getCustomer($this->invoice_to_customer_id);
			if(count($customer)){
				$this->invoice_to_customer_name = $customer['customer_name'];
			}
		}
	}
	
	function createPDF ()
	{
		global $smarty;
		
		$smarty = new Smarty;
	
		templateAssignInvoice('smarty', $this);
		templateAssignSystemvars('smarty');
		
		$smarty->assign('invoice_heading', 'Faktura');
		$pdf = new HTML2FPDF();
		$pdf->DisplayPreferences('HideWindowUI');
		$pdf->AddPage();
		$pdf->WriteHTML($smarty->fetch('file:invoice.tpl'));
		$pdf->Output('invoice/invoice'.$this->invoice_id.'.pdf');
		
		$smarty->assign('invoice_heading', 'Fakturakopi');
		$pdf = new HTML2FPDF();
		$pdf->DisplayPreferences('HideWindowUI');
		$pdf->AddPage();
		$pdf->WriteHTML($smarty->fetch('file:invoice.tpl'));
		$pdf->Output('invoice/invoice'.$this->invoice_id.'_copy.pdf');
	}
	
	var $error_code = 0;
	function error()
	{
		// Returnerer siste error
		
		include "config/errors.php"; // Hent inn alle error meldinger
		
		if(array_key_exists($this->error_code,$error))
		{
			// Erroren som denne klassen har lagt finnes
			
			if($this->error_code == '1')
			{
				// Spesial error for MySQL
				return 'MySQL error nr '.mysql_errno().': '.mysql_error();
			}
			else	return $error[$this->error_code];
		}
		else
		{
			// Erroren eksisterer ikke i konfigrasjonen
			return 'Ukjent error';
		}
	}
}

function getInvoiceLog($id, $invoice=false)
{
	if($invoice)
		$id_type = 'invoice_id';
	else
		$id_type = 'log_id';
	
	if(!is_numeric($id) || $id == '0')
	{
		return array();
	}
	else
	{
		$id = (int)$id;
		$Q = mysql_query("select * from `invoice_log` where $id_type = '".$id."'");
		if(!mysql_num_rows($Q))
		{
			return array();
		}
		else
		{
			if(!$invoice)
			{
				$return = array (
					'log_id'			=> mysql_result	($Q, '0', 'log_id'),
					'invoice_id'		=> mysql_result	($Q, '0', 'invoice_id'),
					'user_id'			=> mysql_result	($Q, '0', 'user_id'),
					'log_action'		=> mysql_result	($Q, '0', 'log_action'),
					'log_action2'		=> mysql_result	($Q, '0', 'log_action2'),
					'log_time'			=> mysql_result	($Q, '0', 'log_time'),
					'rev_num'			=> mysql_result	($Q, '0', 'rev_num'),
					'log_data'			=> unserialize(mysql_result	($Q, '0', 'log_data'))
				);
			}
			else
			{
				while ($R = mysql_fetch_assoc($Q))
				{
					$return[] = array (
						'log_id'			=> $R['log_id'],
						'invoice_id'			=> $R['invoice_id'],
						'user_id'			=> $R['user_id'],
						'log_action'		=> $R['log_action'],
						'log_action2'		=> $R['log_action2'],
						'log_time'			=> $R['log_time'],
						'rev_num'			=> $R['rev_num'],
						'log_data'			=> unserialize($R['log_data'])
					);
				}
			}
			return $return;
		}
	}
}

function printInvoiceLog($log, $printData = FALSE)
{
	// Prints out, in text, what the log contains for this element
	
	if($printData)
	{
		if($log['log_action'] == 'add')
			$middlestring = _('set to');
		else
			$middlestring = _('changed to');
			
		
		foreach ($log['log_data'] as $index => $value)
		{
			if($index != 'rev_num') // Ignore some...
			{
				echo '    <li>';
				switch($index)
				{
					case 'invoice_time':
						echo _('Invoice time').' '.$middlestring.' "'.$value.'"';
						break;
						
					case 'invoice_time_due':
						echo _('Invoice time due').' '.$middlestring.' "'.$value.'"';
						break;
						
					case 'invoice_time_payed':
						echo _('Start time').' '.$middlestring.' <i>'.date('d-m-Y', $value).'</i>';
						break;
					
					default:
						echo $index.' = ';
						if(is_array($value))
						{
							echo '<code>'; print_r($value); echo '</code>';
						}
						else
							echo $value;
						
						break;
				}
				echo '</li>'.chr(10);
			}
		}
	}
	else
	{
		if($log['log_action'] == 'add')
			echo _('Invoice was created.');
		elseif($log['log_action'] == 'edit')
		{
			switch ($log['log_action2'])
			{
				case 'invoice_sent':
					echo _('Invoice is registered as sent.'); break;
				case 'invoice_payed':
					echo _('Invoice is registered as payed.'); break;
				case '':
					echo _('Invoice was edited.');
					break;
				default:
					break;
			}
		}
	}
}

function newInvoiceLog($invoice_id, $log_action, $log_action2, $rev_num, $log_data)
{
	global $login;
	
	if(!is_array($log_data))
		return FALSE;
	
	// Checking log_action
	switch ($log_action)
	{
		case 'add':
			$log_action2 = ''; // No log_action2 for add...
			break;
		case 'edit':
			switch ($log_action2)
			{
				case 'invoice_sent':
				case 'invoice_payed':
					break;
				case '': // Normal edit, not allowed
				default:
					return FALSE;
			}
			break;
		default:
			return FALSE;
	}
	
	if(!is_numeric($rev_num))
		return FALSE;
	$rev_num = (int)$rev_num;
	if(!is_numeric($invoice_id))
		return FALSE;
	$invoice_id = (int)$invoice_id;
	
	// Inserting into database
	mysql_query("INSERT INTO `invoice_log` (
			`log_id` ,
			`invoice_id` ,
			`user_id` ,
			`log_action` ,
			`log_action2` ,
			`log_time` ,
			`rev_num` ,
			`log_data`
		)
		VALUES (
			NULL , 
			'$invoice_id', 
			'".$login['user_id']."', 
			'$log_action', 
			'$log_action2', 
			'".time()."', 
			'$rev_num', 
			'".serialize($log_data)."'
		);");
	
	return TRUE;
}