<?php

require "html2fpdf.php";

/*
$html = '<html>
<head>
<style>
#rightbox
{
position:fixed;
top:30px;
right:5px;
}
</style>
</head>

<body>

<div style="font-size: x-small;"><b>Jærmuseet</b></div>
<div style="font-size: xx-small; position:fixed; top:55px;"><b>Adresse:</b>&nbsp;Postboks&nbsp;250,&nbsp;4367&nbsp;NÆRBØ</b><br><b>Org.nr.:</b>&nbsp;NO 971 098 767 MVA<br><b>Banknr:</b>&nbsp;123</div>
<div style="font-size: xx-small; position:fixed; top:55px; left:250px;"><b>Telefon:&nbsp;</b> (+47) 51 79 94 20<br><b>Telefaks:&nbsp;</b> (+47) 51 79 94 21<br><b>Nettside:&nbsp;</b> http://www.jaermuseet.no/</div>
<br>
<br>
<br>
<br>
Hallvard Nygård<br>
Vitenfabrikken<br>
Postboks 366<br>
4303 SANDNES<br>
linje 5<br>
linje 6<br>
<br>
Mobilnr.: 45442095<br>
E-post: hn@jaermuseet.no

<div style="position:fixed; right:10px; top:20px; font-size: xx-large;" align="right"><b>Faktura</b></div>
<div style="position:fixed; right:10px; top:60px;" align="right">
<b>Fakturanr:&nbsp;</b> 14<br>
<b>Kundenr:&nbsp;</b> 41<br>
<b>Fakturadato:&nbsp;</b> 07.03.2008<br>
<b>Forfallsdato:&nbsp;</b> 21.03.2008<br>
</div><br>
<i>Alle beløp er i NOK. Denne fakturaen er bare et eksempel på en data-generert faktura.</i>

<br><br><h2>Produkter i faktura:</h2>
<table>
 <tr>
  <th bgcolor="#E0E0E0" align="center" style="font-size:SMALL;"><b>Linjenr</b></td>
  <th bgcolor="#E0E0E0" align="center" style="font-size:SMALL;" width="100px"><b>Beskrivelse</b></td>
  <th bgcolor="#E0E0E0" align="right" style="font-size:SMALL;"><b>Stk.pris</b></td>
  <th bgcolor="#E0E0E0" align="center" style="font-size:SMALL;"><b>Antall</b></td>
  <th bgcolor="#E0E0E0" align="right" style="font-size:X-SMALL;"><b>Sum&nbsp;eks.mva</b></td>
  <th bgcolor="#E0E0E0" align="center" style="font-size:X-SMALL;"><b>MVA-sats</b></td>
  <th bgcolor="#E0E0E0" align="right" style="font-size:SMALL;"><b>Sum&nbsp;ink.mva</b></td>
 </tr>
 <tr>
  <td align="center" style="font-size: small">1</td>
  <td align="center" style="font-size: small" width="250px">Bookingnr. 52<br>Utleie - Kafé - Vitengarden, 07.03.2008.</td>
  <td align="right" style="font-size: small">kr&nbsp;1800</td>
  <td align="center" style="font-size: small">1</td>
  <td align="right" style="font-size: small">kr&nbsp;1800</td>
  <td align="center" style="font-size: small">0&nbsp;%</td>
  <td align="right" style="font-size: small"><b>kr&nbsp;1800</b></td>
 </tr>
</table>
<br><br>
		<div align="right" style="font-size: large;">
			<b>Å BETALE:&nbsp;kr&nbsp;1800</b><br>
			(av dette er kr 0 MVA)
		</div>
<br><br>
<h2>Betaling</h2>
<i>Ved overføring til bankkonto må du huske å merkere betalingen med fakturaid. Dette må til for at vi skal klare å identifisere betalingen.</i><br>
<b><i>Beløp:</i></b> 1800 NOK<br>
<b><i>Til konto:</i></b> 123<br>
<b><i>Merker med:</i></b> 14<br>
</body></html>
';*/


$pdf = new HTML2FPDF();
$pdf->DisplayPreferences('HideWindowUI');
$pdf->AddPage();
$pdf->WriteHTML($html);
$pdf->Output('test.pdf');
//*/
echo $html;

?>