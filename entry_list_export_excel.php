<?php

/*
JM-booking
Copyright (C) 2007-2012  Jaermuseet <http://www.jaermuseet.no>
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

$noprint = true;
require 'include/entry_list.php';
require 'libs/Excelklasse/Writer.php';

/* ## Lager Excel-fil ## */
$navn = 'booking-liste-' . date( 'Ymd_His' );

// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send( $navn . '.xls' );

$formatHeading =& $workbook->addFormat( array( 'bold' => 1, 'align' => 'right' ) );
$formatHeadingBig =& $workbook->addFormat( array( 'bold' => 1, 'size' => '16' ) );
$formatBold =& $workbook->addFormat( array( 'bold' => 1 ) );
$formatRight =& $workbook->addFormat( array( 'align' => 'right' ) );
$formatSum =& $workbook->addFormat( array( 'bold' => 1 ) );
$formatSum->setBottom( 1 );
$formatSum->setTop( 1 );


$fields = array(
    'entry_id'             => 'Bookingid',
    'entry_name'           => 'Bookingnavn',
    'entry_title'          => 'Tittel',
    'entry_type_name'      => 'Type',
    'area'                 => 'Anlegg',
    'room'                 => 'Rom',
    'entry_type_id'        => 'Typeid',
    'area_id'              => 'Anleggid',
    'room_id'              => 'Rom - ider',

    'time_start'           => 'Starter',
    'time_end'             => 'Slutter',
    'time_start_time'      => 'Starter - klokkeslett',
    'time_end_time'        => 'Slutter - klokkeslett',
    'time_start_date'      => 'Starter - dag',
    'time_end_date'        => 'Slutter - dag',

    'user_assigned_any'    => 'Verter',
    'user_assigned'        => 'Verter - ider',
    'user_assigned2'       => 'Verter - ikke brukere',
    'confirm_email'        => 'Bekreftelse sendt',
    'num_person_child'     => 'Antall barn',
    'num_person_adult'     => 'Antall voksne',
    'num_person_count'     => 'Tell i bookingsystemet',
    'program_name'         => 'Fast program',
    'program_id'           => 'Fast program - id',
    'program_description'  => 'Programbeskrivelse',
    'service_alco'         => 'Alkohol',
    'service_description'  => 'Serveringsbeskrivelse',
    'comment'              => 'Kommentar',
    'infoscreen_txt'       => 'Tekst på infoskjerm',
    'rev_num'              => 'Revisjonsnummer',
    'time_created'         => 'Opprettet',
    'created_by'           => 'Opprettet av - id',
    'created_by_name'      => 'Opprettet av - navn',
    'user_last_edit'       => 'Sist endret av - id',
    'user_last_edit_name'  => 'Sist endret av - navn',

    'customer_id'              => 'Kundeid',
    'customer_name'            => 'Kundenavn',
    'customer_municipal_num'   => 'Kommunenr',
    'customer_municipal'       => 'Kommune',
    'contact_person_name'      => 'Kontaktpersons navn',
    'contact_person_phone'     => 'Kontaktpersons telefonnr',
    'contact_person_email'     => 'Kontaktpersons epost',

    'invoice'                  => 'Faktura',
    'invoice_ref_your'         => 'Kundens referanse',
    'resourcenum'              => 'Ressursnummer',
    'invoice_comment'          => 'Fakturakommentar',
    'invoice_internal_comment' => 'Intern fakturakommentar',
    'invoice_address_id'       => 'Fakturaadresse - id',
    'invoice_address'          => 'Fakturaadresse',
    //'invoice_content'          => '',
    'invoice_status'           => 'Fakturastatus',
    //'invoice_locked'           => 'Låst for endringer i fakturadelen',
    'invoice_electronic'       => 'Ønsker elektronisk faktura',
    'invoice_email'            => 'Faktura-epost',
    //'invoice_exported_time'    => 'Eksportert faktura',
);
$special_fields = array(
    'entry_type_name'  => 'getEntryTypeName',
    'confirm_email'    => 'getBooleanFromEntry',
    'num_person_count' => 'getBooleanFromEntry',
    'service_alco'     => 'getBooleanFromEntry',
    'user_assigned_any'   => 'getUserAssignedAny',
    'user_assigned'       => 'arrayToString',
    'area'                => 'getAreaFromEntry',
    'room'                => 'getRoomFromEntry',
    'room_id'             => 'arrayToString',
    'program_name'        => 'getProgramNameFromEntry',
    'created_by_name'     => 'getUserNameFromEntry',
    'user_last_edit_name' => 'getUserNameFromEntry',
    'invoice'             => 'getBooleanFromEntry',
    'invoice_electronic'  => 'getBooleanFromEntry',
    'invoice_address'     => 'getAddressFromEntry',
    'time_start'          => 'getTimeAndDateFromEntry',
    'time_end'            => 'getTimeAndDateFromEntry',
    'time_created'        => 'getTimeFromEntry',
    'time_start_time'     => 'getTimeFromEntry',
    'time_end_time'       => 'getTimeFromEntry',
    'time_start_date'     => 'getDateFromEntry',
    'time_end_date'       => 'getDateFromEntry',
);

function arrayToString ( $entry, $key ) {
    return implode( $entry[$key], ', ' );
}

function getEntryTypeName ( $entry, $key ) {
    $key = str_replace( '_name', '_id', $key );
    $entry_type = getEntryType( $entry[$key] );
    if ( !count( $entry_type ) ) {
        return '';
    }
    else {
        return $entry_type['entry_type_name'];
    }
}

function getBooleanFromEntry ( $entry, $key ) {
    if ( (boolean)($entry[$key]) ) {
        return 'ja';
    }
    else {
        return 'nei';
    }
}

function getUserAssignedAny ( $entry, $key ) {
    $key = str_replace( '_any', '', $key );

    $users = array();
    foreach ( $entry[$key] as $user ) {
        $user = getUser( $user );
        if ( count( $user ) ) {
            $users[$user['user_id']] = $user['user_name'];
        }
    }

    return implode( $users, ', ' );
}

function getAreaFromEntry ( $entry, $key ) {
    $area = getArea( $entry[$key . '_id'] );

    if ( count( $area ) ) {
        return $area['area_name'];
    }
    else {
        return '';
    }
}

function getRoomFromEntry ( $entry, $key ) {
    $rooms = array();
    foreach ( $entry[$key . '_id'] as $room ) {
        $room = getRoom( $room );
        if ( count( $room ) ) {
            $rooms[] = $room['room_name'];
        }
    }

    return implode( $rooms, ', ' );
}

function getProgramNameFromEntry ( $entry, $key ) {
    $key = str_replace( '_name', '_id', $key );
    $program = getProgram( $entry[$key] );

    if ( count( $program ) ) {
        return $program['program_name'];
    }
    else {
        return '';
    }
}

function getUserNameFromEntry ( $entry, $key ) {
    $key = str_replace( '_name', '', $key );
    $user = getUser( $entry[$key] );
    if ( !count( $user ) ) {
        return '';
    }
    else {
        return $user['user_name'];
    }
}

function getAddressFromEntry ( $entry, $key ) {
    if ( $entry[$key . '_id'] == 0 ) {
        return '';
    }
    else
    {
        $address = getAddress( $entry[$key . '_id'] );
        if ( !count( $address ) ) {
            // -> No address found
            return '';
        }
        else {
            // -> Address found, returning full address
            return $address['address_full'];
        }
    }
}

function getTimeAndDateFromEntry ( $entry, $key ) {
    return date( 'H:i:s d.m.Y', $entry[$key] );
}

function getTimeFromEntry ( $entry, $key ) {
    $key = str_replace( '_time', '', $key );
    return date( 'H:i:s', $entry[$key] );
}

function getDateFromEntry ( $entry, $key ) {
    $key = str_replace( '_date', '', $key );
    return date( 'd.m.Y', $entry[$key] );
}

/* #### ENTRY LISTS #### */
$worksheet1 =& $workbook->addWorksheet( 'Bookinger' );
$worksheet1->hideGridlines();
/*
$worksheet1->setColumn(0, 0, 10);
$worksheet1->setColumn(1, 3, 100);
$worksheet1->setColumn(0, 0, 10);
$worksheet1->setColumn(4, 4, 17);  */

$linje = 0;
$i = 0;

foreach ( $fields as $field ) {
    $worksheet1->write( $linje, $i, $field, $formatHeading );
    $i++;
}

while ( $R = mysql_fetch_assoc( $Q ) )
{
    $linje++;

    $entry = getEntryParseDatabaseArray( $R );

    $i = 0;

    foreach ( $fields as $field_key => $field_name ) {
        if ( isset($special_fields[$field_key]) ) {
            $worksheet1->write( $linje, $i, $special_fields[$field_key]( $entry, $field_key ) );
            $i++;
        }
        else {
            $worksheet1->write( $linje, $i, $entry[$field_key] );
            $i++;
        }
    }
}


/* #### METADATA #### */
$worksheetMeta =& $workbook->addWorksheet('Metadata');
$worksheetMeta->hideGridlines();
$worksheetMeta->setColumn(0, 0, 20);
$worksheetMeta->setColumn(1, 1, 15);
$worksheetMeta->setColumn(2, 2, 20);
$linje = 0;
$worksheetMeta->write($linje, 0, "Om datautdrag fra Jærmuseets bookingsystemet", $formatHeadingBig);
$linje++; $linje++;

$worksheetMeta->write($linje, 0, "Dataene i dette regnearket ble hentet fra Jærmuseets bookingsystem");
$linje++;
$worksheetMeta->write($linje, 0, "Dataene ble hentet:", $formatBold);
$worksheetMeta->write($linje, 1, ' '.date('d.m.Y H:i:s'));
$linje++;
//$worksheetMeta->write($linje, 0, "Filter brukt (som tekst):");
//$worksheetMeta->write($linje, 1, strip_tags(filterToText($filters)));
$worksheetMeta->write($linje, 0, 'For å reprodusere disse regnearkene (oppdatere, få opp liste med bookinger, osv) i bookingsystem, så kan du sette opp filterne under i søkemotoren (Søk i menyene).');
$linje++;$linje++;

$worksheetMeta->write($linje, 0, "Filter brukt:",$formatBold);
$worksheetMeta->write($linje, 1, "Verdi 1:",$formatBold);
$worksheetMeta->write($linje, 2, "Verdi 2:",$formatBold);
foreach($filters as $filter)
{
	$linje++;
	$worksheetMeta->write($linje, 0, $alternatives[$filter[0]]['name']);
	//$worksheetMeta->write($linje, 1, $filter[1]);
	//$worksheetMeta->write($linje, 2, $filter[2]);

	switch ($alternatives[$filter[0]]['type']) {
		case 'bool':
		case 'select':
		case 'id':
		case 'id2':
		case 'text':
			$worksheetMeta->write($linje, 1, "");
		break;

		case 'date':
		case 'num':
			switch($filter[2]) {
				case '=':	$worksheetMeta->write($linje, 1, _('is'));							break;
				case '>';	$worksheetMeta->write($linje, 1, _('is bigger than'));				break;
				case '>=':	$worksheetMeta->write($linje, 1, _('is bigger than or same as'));	break;
				case '<':	$worksheetMeta->write($linje, 1, _('is less than'));				break;
				case '<=':	$worksheetMeta->write($linje, 1, _('is less than or same as'));		break;
			}
	}

	if($alternatives[$filter[0]]['type'] == 'date' && $filter[1] == 'current') {
		$worksheetMeta->write($linje, 2, $filter[1]);
	}
	elseif($alternatives[$filter[0]]['type'] == 'date') {
		$worksheetMeta->write($linje, 2, date('H:i d-m-Y', $filter[1]));
	}
	elseif($alternatives[$filter[0]]['type'] == 'bool') {
		if($filter[1])
			$worksheetMeta->write($linje, 2, _('true'));
		else
			$worksheetMeta->write($linje, 2, _('false'));
	}
	elseif($alternatives[$filter[0]]['type'] == 'select') {
		$worksheetMeta->write($linje, 2, $alternatives[$filter[0]]['choice'][$filter[1]]);
	}
	elseif($alternatives[$filter[0]]['type'] == 'text') {
		$worksheetMeta->write($linje, 2, '"'.$filter[1].'"');
	}
	elseif($alternatives[$filter[0]]['type'] == 'id') {
		if(isset($alternatives[$filter[0]]['table']) && count($alternatives[$filter[0]]['table']))
		{
			$table = $alternatives[$filter[0]]['table'];
			$Q_id = mysql_query('
				SELECT 
					'.$table['id_field'].' AS id,
					'.$table['value_field'].' AS value
				FROM '.$table['table'].'
				WHERE '.$table['id_field'].' = "'.$filter[1].'"');
			if(mysql_num_rows($Q_id))
				$worksheetMeta->write($linje, 2, mysql_result($Q_id, '0', 'value').' (id '.mysql_result($Q_id, '0', 'id').')');
			else
				$worksheetMeta->write($linje, 2, 'id '.$filter[1]);
		}
		else
			$worksheetMeta->write($linje, 2, 'id '.$filter[1]);
	}
	else {
		$worksheetMeta->write($linje, 2, $filter[1]);
	}
}

$workbook->close();
?>