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
 * This is a list of municipals in Norway.
 * The list is originated from 
 * http://www.ssb.no/vis/kommuner/komkatfy.html
 * (sdv-file)
 * 
 * Including this file will result
 * - $municipals = array ('mun_num' => 'mun_name');
 * - $county = array ('county_num' => array ('mun_num', 'mun_num'));
 * 
 * To update the list please just edit the $municipals
 * This variabel is made into the arrays at the bottom.
 * 
 * 
 */

$municipals_sdv = 'Kommunekatalog per 1. januar 2008;

Kommunenr.;Kommunenavn
0101;Halden
0104;Moss
0105;Sarpsborg
0106;Fredrikstad
0111;Hvaler
0118;Aremark
0119;Marker
0121;R�mskog
0122;Tr�gstad
0123;Spydeberg
0124;Askim
0125;Eidsberg
0127;Skiptvet
0128;Rakkestad
0135;R�de
0136;Rygge
0137;V�ler
0138;Hob�l
0211;Vestby
0213;Ski
0214;�s
0215;Frogn
0216;Nesodden
0217;Oppeg�rd
0219;B�rum
0220;Asker
0221;Aurskog-H�land
0226;S�rum
0227;Fet
0228;R�lingen
0229;Enebakk
0230;L�renskog
0231;Skedsmo
0233;Nittedal
0234;Gjerdrum
0235;Ullensaker
0236;Nes
0237;Eidsvoll
0238;Nannestad
0239;Hurdal
0301;Oslo
0402;Kongsvinger
0403;Hamar
0412;Ringsaker
0415;L�ten
0417;Stange
0418;Nord-Odal
0419;S�r-Odal
0420;Eidskog
0423;Grue
0425;�snes
0426;V�ler
0427;Elverum
0428;Trysil
0429;�mot
0430;Stor-Elvdal
0432;Rendalen
0434;Engerdal
0436;Tolga
0437;Tynset
0438;Alvdal
0439;Folldal
0441;Os
0501;Lillehammer
0502;Gj�vik
0511;Dovre
0512;Lesja
0513;Skj�k
0514;Lom
0515;V�g�
0516;Nord-Fron
0517;Sel
0519;S�r-Fron
0520;Ringebu
0521;�yer
0522;Gausdal
0528;�stre Toten
0529;Vestre Toten
0532;Jevnaker
0533;Lunner
0534;Gran
0536;S�ndre Land
0538;Nordre Land
0540;S�r-Aurdal
0541;Etnedal
0542;Nord-Aurdal
0543;Vestre Slidre
0544;�ystre Slidre
0545;Vang
0602;Drammen
0604;Kongsberg
0605;Ringerike
0612;Hole
0615;Fl�
0616;Nes
0617;Gol
0618;Hemsedal
0619;�l
0620;Hol
0621;Sigdal
0622;Kr�dsherad
0623;Modum
0624;�vre Eiker
0625;Nedre Eiker
0626;Lier
0627;R�yken
0628;Hurum
0631;Flesberg
0632;Rollag
0633;Nore og Uvdal
0701;Horten
0702;Holmestrand
0704;T�nsberg
0706;Sandefjord
0709;Larvik
0711;Svelvik
0713;Sande
0714;Hof
0716;Re
0719;Andebu
0720;Stokke
0722;N�tter�y
0723;Tj�me
0728;Lardal
0805;Porsgrunn
0806;Skien
0807;Notodden
0811;Siljan
0814;Bamble
0815;Krager�
0817;Drangedal
0819;Nome
0821;B�
0822;Sauherad
0826;Tinn
0827;Hjartdal
0828;Seljord
0829;Kviteseid
0830;Nissedal
0831;Fyresdal
0833;Tokke
0834;Vinje
0901;Ris�r
0904;Grimstad
0906;Arendal
0911;Gjerstad
0912;Veg�rshei
0914;Tvedestrand
0919;Froland
0926;Lillesand
0928;Birkenes
0929;�mli
0935;Iveland
0937;Evje og Hornnes
0938;Bygland
0940;Valle
0941;Bykle
1001;Kristiansand
1002;Mandal
1003;Farsund
1004;Flekkefjord
1014;Vennesla
1017;Songdalen
1018;S�gne
1021;Marnardal
1026;�seral
1027;Audnedal
1029;Lindesnes
1032;Lyngdal
1034;H�gebostad
1037;Kvinesdal
1046;Sirdal
1101;Eigersund
1102;Sandnes
1103;Stavanger
1106;Haugesund
1111;Sokndal
1112;Lund
1114;Bjerkreim
1119;H�
1120;Klepp
1121;Time
1122;Gjesdal
1124;Sola
1127;Randaberg
1129;Forsand
1130;Strand
1133;Hjelmeland
1134;Suldal
1135;Sauda
1141;Finn�y
1142;Rennes�y
1144;Kvits�y
1145;Bokn
1146;Tysv�r
1149;Karm�y
1151;Utsira
1160;Vindafjord
1201;Bergen
1211;Etne
1216;Sveio
1219;B�mlo
1221;Stord
1222;Fitjar
1223;Tysnes
1224;Kvinnherad
1227;Jondal
1228;Odda
1231;Ullensvang
1232;Eidfjord
1233;Ulvik
1234;Granvin
1235;Voss
1238;Kvam
1241;Fusa
1242;Samnanger
1243;Os
1244;Austevoll
1245;Sund
1246;Fjell
1247;Ask�y
1251;Vaksdal
1252;Modalen
1253;Oster�y
1256;Meland
1259;�ygarden
1260;Rad�y
1263;Lind�s
1264;Austrheim
1265;Fedje
1266;Masfjorden
1401;Flora
1411;Gulen
1412;Solund
1413;Hyllestad
1416;H�yanger
1417;Vik
1418;Balestrand
1419;Leikanger
1420;Sogndal
1421;Aurland
1422;L�rdal
1424;�rdal
1426;Luster
1428;Askvoll
1429;Fjaler
1430;Gaular
1431;J�lster
1432;F�rde
1433;Naustdal
1438;Bremanger
1439;V�gs�y
1441;Selje
1443;Eid
1444;Hornindal
1445;Gloppen
1449;Stryn
1502;Molde
1504;�lesund
1505;Kristiansund
1511;Vanylven
1514;Sande
1515;Her�y
1516;Ulstein
1517;Hareid
1519;Volda
1520;�rsta
1523;�rskog
1524;Norddal
1525;Stranda
1526;Stordal
1528;Sykkylven
1529;Skodje
1531;Sula
1532;Giske
1534;Haram
1535;Vestnes
1539;Rauma
1543;Nesset
1545;Midsund
1546;Sand�y
1547;Aukra
1548;Fr�na
1551;Eide
1554;Aver�y
1557;Gjemnes
1560;Tingvoll
1563;Sunndal
1566;Surnadal
1567;Rindal
1571;Halsa
1573;Sm�la
1576;Aure
1601;Trondheim
1612;Hemne
1613;Snillfjord
1617;Hitra
1620;Fr�ya
1621;�rland
1622;Agdenes
1624;Rissa
1627;Bjugn
1630;�fjord
1632;Roan
1633;Osen
1634;Oppdal
1635;Rennebu
1636;Meldal
1638;Orkdal
1640;R�ros
1644;Holt�len
1648;Midtre Gauldal
1653;Melhus
1657;Skaun
1662;Kl�bu
1663;Malvik
1664;Selbu
1665;Tydal
1702;Steinkjer
1703;Namsos
1711;Mer�ker
1714;Stj�rdal
1717;Frosta
1718;Leksvik
1719;Levanger
1721;Verdal
1723;Mosvik
1724;Verran
1725;Namdalseid
1729;Inder�y
1736;Sn�sa
1738;Lierne
1739;R�yrvik
1740;Namsskogan
1742;Grong
1743;H�ylandet
1744;Overhalla
1748;Fosnes
1749;Flatanger
1750;Vikna
1751;N�r�y
1755;Leka
1804;Bod�
1805;Narvik
1811;Bindal
1812;S�mna
1813;Br�nn�y
1815;Vega
1816;Vevelstad
1818;Her�y
1820;Alstahaug
1822;Leirfjord
1824;Vefsn
1825;Grane
1826;Hattfjelldal
1827;D�nna
1828;Nesna
1832;Hemnes
1833;Rana
1834;Lur�y
1835;Tr�na
1836;R�d�y
1837;Mel�y
1838;Gildesk�l
1839;Beiarn
1840;Saltdal
1841;Fauske
1845;S�rfold
1848;Steigen
1849;Hamar�y
1850;Tysfjord
1851;L�dingen
1852;Tjeldsund
1853;Evenes
1854;Ballangen
1856;R�st
1857;V�r�y
1859;Flakstad
1860;Vestv�g�y
1865;V�gan
1866;Hadsel
1867;B�
1868;�ksnes
1870;Sortland
1871;And�y
1874;Moskenes
1901;Harstad
1902;Troms�
1911;Kv�fjord
1913;Sk�nland
1915;Bjark�y
1917;Ibestad
1919;Gratangen
1920;Lavangen
1922;Bardu
1923;Salangen
1924;M�lselv
1925;S�rreisa
1926;Dyr�y
1927;Tran�y
1928;Torsken
1929;Berg
1931;Lenvik
1933;Balsfjord
1936;Karls�y
1938;Lyngen
1939;Storfjord
1940;G�ivuotna K�fjord
1941;Skjerv�y
1942;Nordreisa
1943;Kv�nangen
2002;Vard�
2003;Vads�
2004;Hammerfest
2011;Guovdageaidnu Kautokeino
2012;Alta
2014;Loppa
2015;Hasvik
2017;Kvalsund
2018;M�s�y
2019;Nordkapp
2020;Porsanger Pors�ngu Porsanki
2021;K�r�sjohka Karasjok
2022;Lebesby
2023;Gamvik
2024;Berlev�g
2025;Deatnu Tana
2027;Unj�rga Nesseby
2028;B�tsfjord
2030;S�r-Varanger';

$municipals2	= explode("\n", $municipals_sdv);
$municipals		= array();
$county			= array();
foreach ($municipals2 as $municipal) {
	$explode = explode (";", $municipal, 2);
	if(count($explode) > 1)
	{
		list($municipal_num, $municipal_name) =  $explode;
		if(is_numeric($municipal_num))
		{
			$municipals[$municipal_num] = trim($municipal_name);
			$county_num = substr($municipal_num, 0, 2);
			$county[$county_num][] = $municipal_num;
		}
	}
}
unset($municipals2, $municipals_sdv);
?>