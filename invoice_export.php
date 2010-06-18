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

require "include/invoice_top.php";

/*
 * Export of invoice data to Visma - Komfakt
 * 
 * Input: entry_id[], entry_id[], etc
 */

/*
 * Format - export (in norwegian, some english translation in comments below):
            

FORMAT FOR OVERFØRING AV FAKTURAGRUNNLAG TIL FAKTURERING VIA FIL.            
ascii /må være i windows-format (ikke dos)

POSTTYPER
ST = Startpost
FL = Fakturalinje     
LT = Linjetekst  (er mulig å knytte fritekst til fakturalinjen)                 
SL = Sluttpost

M/K                                
M = Må angis                                                         
K = Kan angis


Type Felt    Lengde Posisjon Beskrivelse             M/K Merknader
---- ------- ------ -------- ----------------------- --- ----------    
ST   POSTTYPE   2   001-002  Posttype                 M  Verdi 'ST'
ST             60   003-062  Referanse                K  ST01

FL   POSTTYPE   2   001-002  Posttype                 M  Verdi 'FL'    
FL   KUNDENR   11   003-013  Kundenummer              M                    
FL   NAVN      30   014-043  Kundens navn             K
FL   ADRESSE1  30   044-073  Adresselinje 1           K
FL   ADRESSE2  30   074-103  Adresselinje 2           K
FL   POSTNR     4   104-107  Postnummer               K
FL   BETFORM    2   108-109  Betalingstype (BG,PG)    M  MRK01        
FL   OPPDRGNR   3   110-112  Oppdragsgivernummer      M  MRK02
FL   VARENR     4   113-116  Varenummer               M  MRK02
FL   LØPENR     2   117-118  Løpenummer               M  MRK03
FL   PRIS       9   119-127  Varens pris              M  MRK04
FL   GRUNNLAG   9   128-136  Antall av varen          M  MRK05
FL   BELØP     11   137-147  Utregnet beløp           M  MRK04
FL   SAKSNR    16   148-163  Saksnr                   K  

LT   POSTTYPE   2   001-002  Posttype                 M  Verdi 'LT'
LT   KUNDENR   11   003-013  Kundenummer              M
LT   OPPDRGNR   3   014-016  Oppdragsgivernummer      M
LT   VARENR     4   017-020  Varenummer               M
LT   LØPENR     2   021-022  Løpenummer               M
LT   LINJENR    2   023-024  Linjenummer              M  MRK06
LT   TEKST     50   025-074  Fritekstlinje            K          

SL   POSTTYPE   2   001-002  Posttype                 M  Verdi 'SL'
SL   ANTPOST    8   003-010  Antall poster            M  Inkl. Start/Sluttpost

          
MERKNADER   

ST01  -  Teksten i dette feltet kommer ut på kvitteringslisten og kan 
         brukes som referanse på overføringen.
MRK01 -  BG = Bankgiro, PG = Postgiro.
MRK02 -  Må være opprettet i Kommfakt.
MRK03 -  Fortløpende nummerering hvis flere forekomster av samme vare på kunden.
MRK04 -  De 2 nest siste posisjoner er desimaler, siste posisjon angir
         fortegn. (f.eks. 10000- er lik 100.00-)
MRK05 -  De 2 siste posisjoner er desimaler.                        
MRK06 -  Fortløpende nummerering av fritekst. Start på 1 for hvert 
         fakturagrunnlag.


Eksempel på fil:
STOverføring fra MUSIKKSKOLEN periode 0101   - produsert 12.02.01
FL21026799999KNUTSEN, ANNE                 ULVEDALEN 3                                                 2266BG01301000100234000 000000001 000234000
LT2102679999901301000101ANDERS U15/0910/01 19990816-20000615
LT2102679999901301000102PETTER U15/0910/01 19990816-20000615
SL00000005


Kommentar til filen over:

Eksemplet over gjelder medfor en kunde med følgende verdier:
Personnummer (felt kundenummer) 21026799999
Oppdragsgiver: 013
Varenummer: 0100
Løpenummer: 01  (fylles ut med 01 for alle oppdrag pr. kunde/oppdragsgiver/vare.  Dersom en kunde
                 skal ha samme vare 2 ganger må løpenummer varieres med 02 osv.)
Pris: 2340,00  NB! pris skal alltid oppgis i øre, ingen desimaltegn må benyttes i filen. Dersom positivt beløp kan posisjon 127 være blank.  
Grunnlag: 1.  Dersom en har behov for desimal i grunnlag skal f.eks. 12,5 stk settes verdien i feltet slik: 000001250.  Dersom desimal benyttes
              må bruken være konsekvent, dvs. verdien 10 settes til 000001000.
Beløp: 2340,00  Samme regler som for pris, men feltet er på 11 posisjoner.

 */
if(!isset($_GET['entry_id']) || !is_array($_GET['entry_id']))
{
	echo 'Entry id is in wrong format. I can\'t do much about that.';
	exit;
}

$entries = array();
foreach($_GET['entry_id'] as $id)
{
	$tmp_entry = getEntry($id);
	$id = $tmp_entry['entry_id'];
	$entries[$id] = $tmp_entry;
}

function integerToString ($length, $int)
{
	while($length >= strlen($int))
	{
		$int = "0".$int;
	}
	return $int;
}

function stringToString ($length, $string, $cut = false)
{
	if($cut && strlen($string) > $length)
		$string = substr($string, 0, $length);
	
	while($length >= strlen($string))
	{
		$string = " ".$string;
	}
	return $string;
}

// Text to be printed
$the_text = '';

$number_of_posts = 0;
$product_counter = array();
function invoice_export_start ($entry_id)
{
	global $number_of_posts, $the_text;
	
	// Make sure the invoiceend has been executed
	if($number_of_posts > 0)
	{
		// System error
		echo '<h1>EXPORT FAILED!</h1>';
		echo 'No invoiceend printed.';
	}
	
	// Posttype, character 001-002: ST
	$the_text .= 'ST';
	$number_of_posts++;
	
	// Referance, character 003-062 - max length 60: using "BOOKING<ID>"
	$the_text .= 'BOOKING'.$entry_id;
	
	// End of line
	$the_text .= chr(10);
}

function invoice_export_invoiceheading (
	$customer_id,
	$customer_name, // max 30 characters
	$customer_adr1, // max 30 characters
	$customer_adr2, // max 30 characters
	$customer_adr_postalnum,
	$payment_type, // BG or PG
	$oppdragsgivernummber, // Must be created in Komfakt
	$product_id, // Must be created in Komfakt
	// $product_counter
	$product_topay_each,
	$product_amount,
	$product_topay_total,
	$saksnummer
)
{
	global $number_of_posts, $product_counter, $the_text;
	
	// Posttype, character 001-002: FL
	$the_text .= 'FL';
	$number_of_posts++;
	
	// Customer number/id, character 003-013 - max length 11: using customer_id
	$the_text .= integerToString(11, $customer_id);
	
	// Customer name, char 014-043 - length 30: using customer_name
	$the_text .= stringToString(30, $customer_name, true);
	
	// Address line 1, character 044-073 - length 30: using customers address line 1
	$the_text .= stringToString(30, $customer_adr1, true);
	
	// Address line 2, character 074-103 - length 30: using customers address line 2
	$the_text .= stringToString(30, $customer_adr1, true);
	
	// Postal number, character 104-107 - length 4: using customers postal number
	$the_text .= stringToString(4, $customer_adr_postalnum, true);
	
	// Payment type (BG, PG), character 108-109 - length 2:
	$the_text .= stringToString(2, $payment_type, true);
	
	// Oppdragsgivernummer, character 110-112 - length 3: using 0
	// Must exist in Komfakt
	$the_text .= integerToString(3, $oppdragsgivernummber);
	
	// Product number, character 113-116 - length 4: using 0
	// Must exist in Komfakt
	$the_text .= integerToString(4, $product_id);
	
	// Product counter, character 117-118 - length 2: using ???
	if(!isset($product_counter[$customer_id.'-'.$product_id]))
		$product_counter[$customer_id.'-'.$product_id] = 0;
	$product_counter[$customer_id.'-'.$product_id]++;
	$the_text .= integerToString(2, $product_counter[$customer_id.'-'.$product_id]);
	
	// Product price topay, character 119-127 - length 9: using product_topay_each
	$the_text .= integerToString(9, $product_topay_each);
	
	// Product amount, character 128-136 - length 9: using product_amount
	$the_text .= integerToString(9, $product_amount);
	
	// Product topay total, character 137-147 - length 11: using product_topay_total
	$the_text .= integerToString(11, $product_topay_total);
	
	// Saksnummer, character 148-163 - length 16: not in use
	$the_text .= integerToString(16, $saksnummer);
	
	// End of line
	$the_text .= chr(10);
}


function invoice_export_invoicetextline (
	$customer_id,
	$oppdragsgivernummer,
	$product_id,
	$textline_num,
	$text
)
{
	global $number_of_posts, $product_counter, $the_text;
	
	// Posttype, character 001-002: LT
	$the_text .= 'LT';
	$number_of_posts++;
	
	// Customer number/id, character 003-013 - max length 11: using customer_id
	$the_text .= integerToString(11, $customer_id);
	
	// Oppdragsgivernummer, character 014-016 - length 3: using 0
	// Must exist in Komfakt
	$the_text .= integerToString(3, $oppdragsgivernummer);
	
	// Product number, character 017-020 - length 4: using 0
	// Must exist in Komfakt
	$the_text .= integerToString(4, $product_id);
	
	// Product counter, character 021-022 - length 2: using ???
	$the_text .= integerToString(2, $product_counter[$customer_id.'-'.$product_id]);
	
	// Text line number, character 023-024 - length 2: using $textline_num
	$the_text .= integerToString(2, $textline_num);
	
	// Text, character 025-074 - length 50: using $text
	$the_text .= stringToString(50, $text);
	
	// End of line
	$the_text .= chr(10);
}


function invoice_export_end ($end_of_line)
{
	global $number_of_posts, $product_counter, $the_text;
	
	// Posttype, character 001-002: SL
	$the_text .= 'SL';
	$number_of_posts++;
	
	// Number of posts including start and end
	// character 003-010 - max length 8:
	// using $number_of_posts
	$the_text .= integerToString(8, $number_of_posts);
	$number_of_posts = 0; // reset
	
	$product_counter = array(); // reset
	
	// End of line
	$the_text .= $end_of_line;
}

$i = 0;
foreach($entries as $entry)
{
	// Printing this entry
	$i++;
	invoice_export_start($entry['entry_id']);
	
	$customer = getCustomer($entry['customer_id']);
	if(!count($customer))
	{
		// TODO: error handling
		echo 'Can\'t find customer for entry '.$entry['entry_id'];
		exit();
	}
	$address = getAddress($entry['invoice_address_id']);
	if(!count($address))
	{
		// TODO: error handling
		echo 'Can\'t find address for entry '.$entry['entry_id'];
		exit();
	}
	
	// Invoice lines
	foreach($entry['invoice_content'] as $line)
	{
		invoice_export_invoiceheading(
			$customer['customer_id'],
			$customer['customer_name'],
			$address['address_line_1'],
			$address['address_line_2'],
			$address['address_postalnum'],
			'BG',
			0,
			0,
			$line['belop_hver'],
			$line['antall'],
			$line['belop_sum'],
			0
		);
		
		$text_line_num = 0;
		$text_split = explode(chr(10), $line['name']);
		foreach($text_split as $this_text)
		{
			$this_text = trim($this_text);
			if($this_text != '')
			{
				$text_line_num++;
				invoice_export_invoicetextline(
					$customer['customer_id'], 
					0, 
					0, 
					$text_line_num, 
					$this_text
				);
			}
		}
	}
	
	if($i >= count($entries))
		invoice_export_end('');
	else
		invoice_export_end(chr(10));
}


foreach($entries as $entry)
{
	if(count($entry))
	{
		/*
		 * Set new status
		 * Set new rev_num, time of edit, etc
		 */
		$rev_num = $entry['rev_num']+1;
		mysql_query("UPDATE `entry` SET `invoice_status` = '3', ".
		"`user_last_edit` = '".$login['user_id']."', `time_last_edit` = '".time()."', ".
		"`rev_num` = '$rev_num' WHERE `entry_id` = '".$entry['entry_id']."' LIMIT 1 ;");
		
		$log_data = array();
		if(!newEntryLog($entry['entry_id'], 'edit', 'invoice_exported', $rev_num, $log_data))
		{
		}
	}
}

// Print text that is made during the process above
echo $the_text;
?>