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

function curl_execute (array $options, $ch) {
    curl_setopt_array($ch, $options);
    if ($options[CURLOPT_POST] === 1) {
        echo 'POST ' . $options[CURLOPT_URL].chr(10);
    }
    else {
        echo 'GET ' . $options[CURLOPT_URL].chr(10);
    }
    return curl_exec($ch);
}

function datanova_webreport_login($baseurl, $username, $password, $shop, &$ch, $viewstategenerator, $eventvalidation, $viewstate)
{
    $url = $baseurl . '/';
    $options = array();
    $options[CURLOPT_URL] = $url;
    $options[CURLOPT_POST] = 1;
    $options[CURLOPT_POSTFIELDS] =
        '__VIEWSTATE='.$viewstate.'&'.
        '__EVENTARGUMENT=&' .
        '__EVENTTARGET=&' .
        '__VIEWSTATEGENERATOR='.$viewstategenerator.'&' .
        '__EVENTVALIDATION='.$eventvalidation.'&' .
        '__LASTFOCUS=&' .
        'ucUserLogin$btnLogin.x=14&' .
        'ucUserLogin$btnLogin.y=16&' .
        'ucUserLogin$txtUID=' . $username .'&' .
        'ucUserLogin$txtPWD=' . $password;
    $result = curl_execute($options, $ch);

    $wrong_usernameorpassword = '<script type=\'text/javascript\'>alert(\'Feil brukernavn og/eller passord\')</script></form>';
    $login_failedorsomething_message = '<span id="lblMessage"><font face="Arial Narrow" color="Red" size="5">Vennligst legg inn riktig informasjon</font></span>';

    if (strpos($result, $wrong_usernameorpassword) !== FALSE) {
        throw new Exception ('Login failed for Datanova Web reports. Username or password might be wrong. ' .
            'Please check with a login to ' . $baseurl . ' in a browser and check the configuration.');
    }
}

function datanova_webreport_logout ($baseurl, &$ch)
{
	$url = $baseurl.'/ajax/UserControl_LoggedIn,App_Web_yvfx3v1s.ashx?_method=updateLogout&_session=nodefault.aspx';
	$result = curl_execute (array(CURLOPT_URL => $url, CURLOPT_POST => 0), $ch);
}

function datanova_webreport_getreport($baseurl, $username, $password, $shop, $year, $varegruppe_fra, $varegruppe_til)
{
    /* Init */
    $cookie = 'datanova_webreport.txt';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);

    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    // Set headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //'User-Agent: JM-Booking',
        'Accept: image/gif, image/jpeg, image/pjpeg, image/pjpeg, application/x-ms-application, application/x-ms-xbap, application/vnd.ms-xpsdocument, application/xaml+xml, */*', 
'Accept-Language: no', 
'User-Agent: Mozilla/4.0 (compatible; JM-Booking)', 
 'Host: 192.168.114.43',
'Connection: Keep-Alive',
    ));

    $url = $baseurl . '/';

    $result = curl_execute(array(CURLOPT_URL => $url, CURLOPT_POST => 0), $ch);

    // Extract viewstate and eventvalidation
    preg_match_all("#<input.*?name=\"__viewstate\".*?value=\"(.*?)\".*?>.*?<input.*?name=\"__viewstategenerator\".*?value=\"(.*?)\".*?>.*?<input.*?name=\"__eventvalidation\".*?value=\"(.*?)\".*?>#mis", $result, $arr_viewstate);
    $viewstate = $arr_viewstate[1][0];
    $viewstategenerator = $arr_viewstate[2][0];
    $eventvalidation = $arr_viewstate[3][0];


    /* Login */
    datanova_webreport_login($baseurl, $username, $password, $shop, $ch, $viewstategenerator, $eventvalidation, $viewstate);


    /* Generate report */
    $url = $baseurl . '/MainReport.aspx?nodeid=6.4';
    $result = curl_execute(array(CURLOPT_URL => $url, CURLOPT_POST => 0), $ch);

    // Get viewstate and eventvalidation (needed in this ASP.NET application to verify the origin of the form)
    preg_match_all("#<input.*?name=\"__viewstate\".*?value=\"(.*?)\".*?>.*?<input.*?name=\"__viewstategenerator\".*?value=\"(.*?)\".*?>#mis", $result, $arr_viewstate);
    if (!isset($arr_viewstate[1][0]) || !isset($arr_viewstate[2][0])) {
        throw new Exception ('Viewstate/Eventvalidation not found in result from MainReport.aspx in first request after login. HTML fetching failed.' .
            chr(10) . 'HTML body: ' . chr(10) . $result);
    }
    $viewstate = $arr_viewstate[1][0];
    $viewstategenerator = $arr_viewstate[2][0];
	
    // Set up post data
    $post =
        '__EVENTTARGET=ctl00$Content$ucParam$btnSend&'.
        '__EVENTARGUMENT=&'.
        '__VIEWSTATE=' . rawurlencode($viewstate).'&'.
        '__VIEWSTATEGENERATOR=' . $viewstategenerator.'&'.
        rawurlencode('ctl00$ucLoggedIn$hdnUserid').'=' . $username.'&'.
        rawurlencode('ctl00$Content$txtSearch').'=Søk&'.
        rawurlencode('ctl00$Content$ucParam$selddl1').'=' . $year.'&'.
        rawurlencode('ctl00$Content$ucParam$dt71').'=&'.
        rawurlencode('ctl00$Content$ucParam$dt81').'=&'.
        // Garborgsenteret
        rawurlencode('ctl00$Content$ucParam$selddl11').'=1&'.
        // Vitenfabrikken
        rawurlencode('ctl00$Content$ucParam$selddl11').'=3&'.
        // Vitengarden
        rawurlencode('ctl00$Content$ucParam$selddl11').'=4&'.
        // Garborgstova
        rawurlencode('ctl00$Content$ucParam$selddl11').'=5&'.
        rawurlencode('ctl00$Content$ucParam$txt131').'=' . $varegruppe_fra.'&'.
        rawurlencode('ctl00$Content$ucParam$txt141').'=' . $varegruppe_til.'&'.
        rawurlencode('ctl00$Content$ucParam$txt151').'=&'.
        rawurlencode('ctl00$Content$ucParam$txt161').'=&'.
        rawurlencode('ctl00$Content$ucParam$txt171').'=&'.
        rawurlencode('ctl00$Content$ucParam$txt181').'=&'.
        rawurlencode('ctl00$Content$ucParam$txt191').'=&'.
        rawurlencode('ctl00$Content$ucParam$txt201').'=&'.
        rawurlencode('ctl00$Content$ucParam$txt341').'=&'.
        rawurlencode('ctl00$Content$ucParam$txt351').'=&'.
        rawurlencode('ctl00$Content$ucParam$ddlXaxis').'=coshopno&'.
        rawurlencode('ctl00$Content$ucParam$ddlYaxis').'=salesqty&'.
        rawurlencode('ctl00$Content$ucParam$ddlGraph').'=Bar&'.
        rawurlencode('ctl00$Content$ucParam$hdnTextId').'=&'.
        rawurlencode('ctl00$Content$ucParam$hdnNodeid').'=6.4&'.
        rawurlencode('ctl00$Content$ucParam$hdnstock').'=&'.
        rawurlencode('ctl00$Content$ucParam$hdnXaxis').'=&'.
        rawurlencode('ctl00$Content$ucParam$hdnYaxis').'=&'.
        rawurlencode('ctl00$Content$ucParam$hdnGraphType').'=&'.
        rawurlencode('ctl00$Content$ucParam$hdnorderby').'=0&'.
        rawurlencode('ctl00$Content$ucParam$hdnsort').'=';

    $options = array(CURLOPT_URL => $url, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $post);
    $result = curl_execute($options, $ch);

    if($varegruppe_fra == $varegruppe_til && strpos($result, '302 Found') !== FALSE && strpos($result, '500 Internal Server Error') !== FALSE) {
         return 'Not found.';
    }

    //$info = curl_getinfo($ch);
    //echo '<h3>'.nl2br($info['request_header']).'</h3>';
    //echo '<div style="width: 500px; height: 250px; display: inline-block; clear: none; overflow: scroll;">'.$result.'</div><br>';

    // Extract viewstate and eventvalidation
    preg_match_all("#<input.*?name=\"__viewstate\".*?value=\"(.*?)\".*?>".
        ".*?<input.*?name=\"__viewstategenerator\".*?value=\"(.*?)\".*?>".
        ".*?<input.*?name=\"__eventvalidation\".*?value=\"(.*?)\".*?>".
        ".*?<input.*?name=\"ctl00\\\$Content\\\$ucParam\\\$grid\\\$CallbackState\".*?value=\"(.*?)\".*?>".
        "#mis", $result, $arr_viewstate);
    if (!isset($arr_viewstate[1][0])) {
        echo "-------------------------------\n\r";
        echo "------ NO VIEW STATE\n\r";
        echo "-------------------------------\n\r";
        echo $result;
        echo "-------------------------------\n\r";
        throw new Exception('No viewstate');
    }

    $viewstate = $arr_viewstate[1][0];
    $viewstategenerator = $arr_viewstate[2][0];
    $eventvalidation = $arr_viewstate[3][0];  
    $callbackstate = $arr_viewstate[4][0];

//echo base64_decode($viewstate).chr(10).chr(10);
//echo base64_decode($viewstategenerator).chr(10).chr(10);
//echo base64_decode($eventvalidation).chr(10).chr(10);
//echo $callbackstate;
//echo base64_decode($callbackstate).chr(10).chr(10);
//exit;

    /* Download report */
    $url = $baseurl . '/ShowReport.aspx?childid=6.4&childtext=6.04+Salg+pr.+vare+pr.+dag+(+spesifisert+)';
    $url = $baseurl . '/ShowReport.aspx?nodeid=6.4';

    $post = array(
        '__EVENTTARGET='.rawurlencode('ctl00$Content$ucParam$btnXmlExport'),
        '__EVENTARGUMENT=Click',
        '__VIEWSTATE=' . rawurlencode(html_entity_decode($viewstate, ENT_QUOTES)),
        '__VIEWSTATEGENERATOR=' . rawurlencode($viewstategenerator),
        '__EVENTVALIDATION=' . rawurlencode(html_entity_decode($eventvalidation, ENT_QUOTES)),
        rawurlencode('ctl00$ucLoggedIn$hdnUserid').'=' . $username,
        rawurlencode('ctl00$Content$ucParam$grid$DXSelInput').'=',
        rawurlencode('ctl00$Content$ucParam$grid$CallbackState').'='.rawurlencode(html_entity_decode($callbackstate, ENT_QUOTES)),
        rawurlencode('ctl00$Content$ucParam$grid$DXColResizedInput').'=',
        rawurlencode('ctl00$Content$ucParam$grid$DXSyncInput').'=',
        rawurlencode('ctl00$Content$ucParam$hdnGroupBy').'=',
        rawurlencode('ctl00$Content$ucParam$hdnGroupBy2').'=',
        rawurlencode('ctl00$Content$ucParam$hdnHiddenCol1').'=',
        rawurlencode('ctl00$Content$ucParam$hdnHiddenCol2').'=',
        rawurlencode('ctl00$Content$ucShowGraph$ddlXaxis').'=coshopno',
        rawurlencode('ctl00$Content$ucShowGraph$ddlYaxis').'=salesqty',
        rawurlencode('ctl00$Content$ucShowGraph$hdnGraphOption').'=bar',
        rawurlencode('ctl00$Content$ucShowGraph$hdnPrev').'=1',
        rawurlencode('ctl00$Content$ucShowGraph$hdnNext').'=1',
        rawurlencode('ctl00$Content$ucShowGraph$hdnNodeId').'=6.4',
        rawurlencode('ctl00$Content$ucShowGraphLine$ddlXaxis').'=coshopno',
        rawurlencode('ctl00$Content$ucShowGraphLine$ddlYaxis').'=salesqty',
        rawurlencode('ctl00$Content$ucShowGraphLine$hdnGraphOption').'=line',
        rawurlencode('ctl00$Content$ucShowGraphLine$hdnPrev').'=1',
        rawurlencode('ctl00$Content$ucShowGraphLine$hdnNext').'=1',
        rawurlencode('ctl00$Content$ucShowGraphLine$hdnNodeId').'=6.4',
        rawurlencode('ctl00$Content$ucShowGraphArea$ddlXaxis').'=coshopno',
        rawurlencode('ctl00$Content$ucShowGraphArea$ddlYaxis').'=salesqty',
        rawurlencode('ctl00$Content$ucShowGraphArea$hdnGraphOption').'=area',
        rawurlencode('ctl00$Content$ucShowGraphArea$hdnPrev').'=1',
        rawurlencode('ctl00$Content$ucShowGraphArea$hdnNext').'=1',
        rawurlencode('ctl00$Content$ucShowGraphArea$hdnNodeId').'=6.4',
        rawurlencode('ctl00$Content$ucShowGraphPie$ddlXaxis').'=coshopno',
        rawurlencode('ctl00$Content$ucShowGraphPie$ddlYaxis').'=salesqty',
        rawurlencode('ctl00$Content$ucShowGraphPie$hdnGraphOption').'=pie',
        rawurlencode('ctl00$Content$ucShowGraphPie$hdnPrev').'=1',
        rawurlencode('ctl00$Content$ucShowGraphPie$hdnNext').'=1',
        rawurlencode('ctl00$Content$ucShowGraphPie$hdnNodeId').'=6.4',
        rawurlencode('ctl00$Content$hdnReportId').'=6.4',
        rawurlencode('ctl00$Content$ucDownload$Emailsubmit').'=',
        rawurlencode('ctl00$Content$ucDownload$hdnMemberId').'=' . $username,
    );

    $options = array(CURLOPT_URL => $url, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => implode($post, '&'));
    $result = curl_execute($options, $ch);
    //$info = curl_getinfo($ch);
    //echo '<h3>'.nl2br($info['request_header']).'</h3>';
    //echo implode('&', $post).'&'.http_build_query($validations).'&'.implode('&', $post_last).chr(10).chr(10);

    /* Logout */
    datanova_webreport_logout($baseurl, $ch);
    unlink($cookie);

    /* Clean up */
    curl_close($ch);

    /* Return HTML (+ headers) */
    return $result;
}

function str_starts_with($content, $needle) {
    return substr($content, 0, strlen($needle)) == $needle;
}

/**
 * Parse HTML retrived from Datanova Back office web reporter
 *
 * @param String  The whole HTML (can include header etc)
 * @return Array  Data found (array with 'Butikknr', 'Varenavn', 'Varenr', 'Antsolgt' and 'Transdato')
 */
function datanova_webreport_parser($result, $current_date_dd_mm_yyyy)
{
    $result = utf8_decode($result);
    $lines = explode("\n", $result);
    if (!str_starts_with(trim($lines[0]), '<?xml version="1.0" encoding="utf-8"')) {
        echo '---------'.chr(10);
        var_dump($lines[0]);
        echo '---------'.chr(10);
        throw new Exception('Unknown result. Did not return XML: '.chr(10) .$result);
    }


    if (!str_starts_with($lines[1], '<ivxml version="1.0.0" createdate="'. $current_date_dd_mm_yyyy . '" delimiter="|" parameters="')) {
        throw new Exception('Unknown result. Unknown line 2: '.chr(10) .$result);
    }

    $a = '<report name="6.04 Salg pr. vare pr. dag ( spesifisert )" allrows="true" columns="';
    if (!str_starts_with(trim($lines[2]), $a)) {
        throw new Exception('Unknown result. Unknown line 3: '.chr(10) .$result);
    }

    $columns = substr(trim($lines[2]), strlen($a));
    $columns = substr($columns, 0, strlen($columns)-2);
    $columns = explode('|', $columns);

    $heading = array();
    $data = array();
    $number_of_fields = count($columns);
    $varenavn_column_num = -1;

    foreach($columns as $i => $column) {
        // Cleaning
        $td = $column;
        $td = trim(str_replace('.', ' ', $td));
        $td = trim(str_replace('-', ' ', $td));
        $td = str_replace('  ', ' ', $td);
        //$td = str_replace(' ', '_', $td);
        $td = str_replace(' ', '', $td);
        $td = str_replace('%', 'prosent', $td);
        $td = str_replace('ø', 'o', $td);

        // Translate to old column names (not make the rest of the logic work)
        if ($td == 'Antallsolgt') {
            $td = 'Antsolgt';
        }
        if ($td == 'Dato') {
            $td = 'Transdato';
        }

        // Adding to list
        if (
            // Ignoring a lot for fields
            $td == 'Butikknr' ||
            $td == 'Varenavn' ||
            $td == 'Varenr' ||
            $td == 'Antsolgt' ||
            $td == 'Transdato'
        ) {
            $heading[$i] = $td;
            if ($td == 'Varenavn') {
                $varenavn_column_num = $i;
            }
        }
        else {
            //echo 'Ignoring '. $i .': '.$td.chr(10);
        }
    }

    //var_dump($heading);

    // Getting table content
    $controlamount = 0;
    $a = 0;
    // Iterate over the rows (not the first lines and not the 2 last lines)
    for ($j = 3; $j < count($lines) - 2; $j++) {
        $line = explode('|',
            trim(
                str_replace('<row values="', '',
                str_replace('" />', '', $lines[$j]))
            ));
        if (count($line) != $number_of_fields) {
            //echo 'Line with funky column number: '. $lines[$j] . "\r\n";
            //echo "- Merging varenavn...\r\n";
            $columns_to_merge = count($line) - $number_of_fields;
            $new_line = array();
            foreach ($line as $i => $item) {
               if ($i <= $varenavn_column_num) {
                   // -> Before varenavn
                   $new_line[$i] = $item;
               }
               elseif ($i <= ($varenavn_column_num + $columns_to_merge)) {
                   // -> Varenavn, merge
                   $new_line[$varenavn_column_num] .= '|' . $item;
               }
               else {
                   // -> After. Adjust column num.
                   $new_line[$i - $columns_to_merge] = $item;
               }
            }
            $line = $new_line;
            //print_r($line);
            if (count($line) != $number_of_fields) {
                throw new Exception('Faulty merge: ' . print_r($line, true));
            }
        }
        $row = array();
        foreach ($line as $i => $item) {
            // Cleaning / fixing
            $td = $item;
            $td = str_replace('å', '�', $td);
            $td = str_replace('æ', '�', $td);
            $td = str_replace('ø', '�', $td);
            $td = str_replace('Å', '�', $td);

            // Adding to array
            if (
                // A lot for fields are ignored
            isset($heading[$i])
            ) {
                if ($heading[$i] == 'Antsolgt') {
                    $td = (int)$td;
                    if ($row['Butikknr'] != 'Totalt :')
                        $controlamount += $td;
                }

                if ($heading[$i] == 'Transdato' && $td != '') {
                    // 6/7/2016 12:00:00 AM => 12.03.2016
                    // M/D/YYYY ----------- => dd.mm.YYYY
                    $td = explode(' ', $td);
                    $td = explode('/', $td[0]);
                    if (count($td) != 3) {
                        echo 'HEADING: '.$lines[2]."\n\r";
                        throw new Exception('Unknown date: ' . $item . '. Line: '. $lines[$j]);
                    }
                    $td = str_pad($td[1], 2, '0', STR_PAD_LEFT)
                        . '.' . str_pad($td[0], 2, '0', STR_PAD_LEFT)
                        . '.' . $td[2];
                }

                $row[$heading[$i]] = $td;
            }
        }

        $data[$a] = $row;
        $a++;
    }

    if ($data[$a - 1]['Butikknr'] != 'Totalt :') {
        throw new Exception('Faulty read of table or bogus data.. No sum found.');
    }

    if ($data[$a - 1]['Antsolgt'] != $controlamount) {
        throw new Exception('Faulty read of table or bogus data. Control amount is not correct. Could also have been sold half a ticket. Should be: ' . $data[$a - 1]['Antsolgt'] . '. Was: ' . $controlamount);
    }

    unset($data[$a - 1]);

    /*
    echo '<table>'.chr(10);
    echo '<tr>'.chr(10);
    foreach($heading as $cell)
    {
        echo '	<th>'.$cell.'</th>'.chr(10);
    }
    echo '</tr>'.chr(10).chr(10);

    foreach($data as $row)
    {
        echo '<tr>'.chr(10);
        foreach($row as $cell)
        {
            echo '	<td>'.$cell.'</td>'.chr(10);
        }
        echo '</tr>'.chr(10).chr(10);
    }
    echo '</table>';
    */

    return $data;
}

/**
 * Analyze the Datanova data
 *
 * Format $data_rows:
 * array(
 *      'Butikknr'  => '3',
 *      'Varenavn'  => 'ABC',
 *      'Varenr'    => '123321123',
 *      'Antsolgt'  => '123',
 *      'Transdato' => '12.03.4567'
 * )
 *
 * @param  Array  Rows of data
 * @param  Array  Shop-area translation (shop_id => area_id)
 * @return Array  Final analyzed data in different categories
 */
function datanova_analyze_data($data_rows, $shops)
{
    $Q_varer = mysql_query("SELECT varereg.*, kat.kat_navn AS kat_navn
	FROM import_dn_vareregister varereg LEFT JOIN import_dn_kategori kat
	ON varereg.kat_id = kat.kat_id
	");
    //where varereg.area_id = '$area';");
    $areavarer = array(); // vare_nr => array()
    while ($R_vare = mysql_fetch_assoc($Q_varer))
        $areavarer[$R_vare['area_id'] . '_' . $R_vare['vare_nr']] = $R_vare;

    $unknowns = array();
    $found = array();
    $tall_nye = array();
    $tall_update = array();
    $tall_ignore = array();
    $tall_ignore2 = array();
    $tall_allerede = array();
    $varer_nye = array();
    $varer_update = array();
    foreach ($data_rows as $key => $val) {
        $vare = array();
        $vare['vare_nr'] = slashes(htmlspecialchars($val['Varenr'], ENT_QUOTES));
        $vare['vare_navn'] = slashes(htmlspecialchars($val['Varenavn'], ENT_QUOTES));
        $vare['vare_antall'] = (int)$val['Antsolgt'];
        if (strlen($val['Transdato']) != strlen('11.06.2008')) {
            throw new Exception('Problemer med tolking av dato. Dato er ikke i rett format for ' . $vare['vare_nr'] . ' (dato: ' . $val['Transdato'] . '). Vare: ' . print_r($vare, true));
        } else {
            $vare['dag'] = getTime($val['Transdato'], array('d', 'm', 'y'));
            if ($vare['dag'] == 0)
                throw new Exception('Problemer med tolking av dato. Dato er ikke i rett format for ' . $vare['vare_nr'] . ' (dato: ' . $val['Transdato'] . '). Vare: ' . print_r($vare, true));
        }

        $vare['shop_id'] = $val['Butikknr'];
        if (isset($shops[$vare['shop_id']])) {
            $vare['area_id'] = $shops[$vare['shop_id']];
        } else {
            $vare['area_id'] = 0;
        }

        $vare_id_primary = $vare['area_id'] . '_' . $vare['vare_nr'];

        /* Determine import */
        if (!isset($areavarer[$vare_id_primary])) {
            if (!isset($unknowns[$vare_id_primary])) {
                $unknowns[$vare_id_primary] = $vare;
                unset($unknowns[$vare_id_primary]['dag']);
                $unknowns[$vare_id_primary]['vare_dager'] = 1;
            } else {
                $unknowns[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                $unknowns[$vare_id_primary]['vare_dager'] += 1;
            }
            $tall_ignore[] = $vare;
        } else {
            // Varer funnet
            if (!isset($found[$vare_id_primary])) {
                $found[$vare_id_primary] = $vare;
                unset($found[$vare_id_primary]['dag']);
                $found[$vare_id_primary]['vare_dager'] = 1;
            } else {
                $found[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                $found[$vare_id_primary]['vare_dager'] += 1;
            }

            $vare_med_kat = $areavarer[$vare_id_primary];
            $vare['kat_id'] = $areavarer[$vare_id_primary]['kat_id'];
            if ($areavarer[$vare_id_primary]['barn'] == 0) {
                $vare['antall_barn'] = 0;
                $vare['antall_voksne'] = $vare['vare_antall'];
            } else {
                $vare['antall_barn'] = $vare['vare_antall'];
                $vare['antall_voksne'] = 0;
            }

            if ($vare_med_kat['kat_id'] == 0) {
                $tall_ignore2[] = $vare;
            } else {
                // Sjekker mot database
                $Q_dbsjekk = mysql_query("SELECT * FROM `import_dn_tall` WHERE
					vare_nr = '" . $vare['vare_nr'] . "' AND
					area_id = '" . $vare['area_id'] . "' AND
					dag = '" . $vare['dag'] . "'
					LIMIT 1;");
                if (!mysql_num_rows($Q_dbsjekk)) {
                    $tall_nye[] = $vare;

                    // Nye varer
                    if (!isset($varer_nye[$vare_id_primary])) {
                        $varer_nye[$vare_id_primary] = $vare;
                        unset($varer_nye[$vare_id_primary]['dag']);
                        $varer_nye[$vare_id_primary]['vare_dager'] = 1;
                    } else {
                        $varer_nye[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                        $varer_nye[$vare_id_primary]['vare_dager'] += 1;
                    }
                } else {
                    $tall = mysql_fetch_assoc($Q_dbsjekk);
                    if (
                        $tall['kat_id'] != $vare['kat_id'] ||
                        $tall['antall_barn'] != $vare['antall_barn'] ||
                        $tall['antall_voksne'] != $vare['antall_voksne']
                    ) {
                        $tall_update[] = $vare;

                        // Update av varer
                        if (!isset($varer_update[$vare_id_primary])) {
                            $varer_update[$vare_id_primary] = $vare;
                            unset($varer_update[$vare_id_primary]['dag']);
                            $varer_update[$vare_id_primary]['vare_dager'] = 1;
                        } else {
                            $varer_update[$vare_id_primary]['vare_antall'] += $vare['vare_antall'];
                            $varer_update[$vare_id_primary]['vare_dager'] += 1;
                        }
                    } else
                        $tall_allerede[] = $vare;
                }
            }
        }
    }

    return array(
        'unknowns' => $unknowns,
        'numbers_new' => $tall_nye,
        'numbers_update' => $tall_update,
        'numbers_ignored_notreged' => $tall_ignore,
        'numbers_ignored_reged' => $tall_ignore2,
        'numbers_alreadyimported' => $tall_allerede,
    );
}

/**
 * Insert new numbers in database
 *
 * @throws Exception on MySQL errors
 * @param  array   New numbers to be inserted
 * @return int     Amount of new numbers inserted
 */
function datanova_databaseinsert($tall_nye)
{
    if (count($tall_nye)) {
        $tall_nye2 = array();
        foreach ($tall_nye as $vare) {
            // Insert
            $tall_nye2[] =
                '\'' . $vare['vare_nr'] . '\',' .
                '\'' . $vare['area_id'] . '\',' .
                '\'' . $vare['dag'] . '\',' .
                '\'' . $vare['kat_id'] . '\',' .
                '\'' . $vare['antall_barn'] . '\',' .
                '\'' . $vare['antall_voksne'] . '\',' .
                '\'' . $vare['shop_id'] . '\'';
        }


        // Make batches of 100 and 100 inserts
        $tall_nye3 = array(0 => array());
        $i = 0;
        foreach ($tall_nye2 as $tall) {
            if (count($tall_nye3[$i]) >= 100) {
                $i++;
                $tall_nye3[$i] = array();
            }
            $tall_nye3[$i][] = $tall;
        }

        $total_count = 0;
        foreach ($tall_nye3 as $tall_nye4) {
            if (count($tall_nye4)) {
                // -> There are numbers in this batch
                $total_count += count($tall_nye4);

                $query =
                    'INSERT INTO `import_dn_tall` (
						`vare_nr`,
						`area_id`,
						`dag`,
						`kat_id`,
						`antall_barn`,
						`antall_voksne`,
						`shop_id`
					) VALUES (' . implode('),(', $tall_nye4) . ');';
                mysql_query($query);

                if (mysql_error()) {
                    throw new Exception(
                        'MySQL error when inserting new numbers to database: ' .
                        mysql_error() . '. ' .
                        'Query: ' . $query);
                }
            }
        }

        // Make sure the splitting has gone okey
        if (count($tall_nye2) != $total_count) {
            throw new Exception('$tall_nye2 (' . $tall_nye2 . ') is not the same as $total_count (' . $total_count . ')');
        }

        return $total_count;
    } else {
        return 0;
    }
}

function datanova_databaseupdate($numbers_update)
{
    if (count($numbers_update)) {
        foreach ($numbers_update as $vare) {
            mysql_query("DELETE FROM `import_dn_tall` WHERE
					`vare_nr` = '" . $vare['vare_nr'] . "' AND
					`shop_id` = '" . $vare['shop_id'] . "' AND
					`dag`     = '" . $vare['dag'] . "'
				LIMIT 1;");

            if (mysql_error()) {
                throw new Exception('MySQL error when deleting numbers (' . print_r($vare, true) . ') from database: ' . mysql_error());
            }
        }

        return datanova_databaseinsert($numbers_update);
    } else {
        return 0;
    }
}

function datanova_databaseupdate_notimported($numbers_update_notimported)
{
    if (count($numbers_update_notimported)) {
        $tall_nye2 = array();
        foreach ($numbers_update_notimported as $vare) {
            mysql_query("DELETE FROM `import_dn_tall_ikkeimportert` WHERE
					`vare_nr` = '" . $vare['vare_nr'] . "' AND
					`shop_id` = '" . $vare['shop_id'] . "'
				LIMIT 1;");

            if (mysql_error()) {
                throw new Exception('MySQL error when deleting not-imported numbers (' . print_r($vare, true) . ') from database: ' . mysql_error());
            }

            // Insert
            $tall_nye2[] =
                '\'' . $vare['vare_nr'] . '\',' .
                '\'' . $vare['shop_id'] . '\',' .
                '\'' . $vare['vare_navn'] . '\',' .
                '\'' . $vare['vare_antall'] . '\',' .
                '\'' . $vare['area_id'] . '\',' .
                '\'' . $vare['vare_dager'] . '\'';
        }

        mysql_query('INSERT INTO `import_dn_tall_ikkeimportert` (
			`vare_nr`,
			`shop_id`,
			`vare_navn`,
			`vare_antall`,
			`area_id`,
			`vare_dager`
		) VALUES (' . implode('),(', $tall_nye2) . ');');

        if (mysql_error()) {
            throw new Exception('MySQL error when inserting not-imported data to database: ' . mysql_error());
        } else {
            return count($tall_nye2);
        }
    } else {
        return 0;
    }
}