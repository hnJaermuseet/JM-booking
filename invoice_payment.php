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

if(!isset($_GET['invoice_id']) || !is_numeric($_GET['invoice_id']))
{
	echo _('Can\'t find invoice.');
	exit();
}

$invoice = new invoice ($_GET['invoice_id']);
if(!$invoice->get())
{
	echo _('Can\'t find invoice.');
	exit();
}

if(isset($_GET['return']) && $_GET['return'] == 'returnToInvoice') {
	$return = 'invoice_view.php?invoice_id='.$invoice->invoice_id;
	$return2 = 'returnToInvoice';
}
else {
	$return = 'invoice_payed_not.php';
	$return2 = '';
}

if($invoice->invoice_payed == '1')
{
	echo 'Allerede registert som betalt.';
	exit();
}

if(isset($_GET['reg_payment']) && isset($_POST['payment_amount']) && isset($_POST['payment_time']))
{
	if(isset($_POST['payment_comment']))
		$payment_comment = $_POST['payment_comment'];
	
	if(!$invoice->register_payment($_POST['payment_time'], $_POST['payment_amount'], $payment_comment))
	{
		echo 'Problemer med å registere betalingen.<br><br>';
		echo '<b>Feilmelding:</b><br>'.$invoice->error();
		exit();
	}
	header ('Location: '.$return);
	exit();
}

$section = 'payed_not';
require "include/invoice_menu.php";

$smarty = new Smarty;

templateAssignInvoice('smarty', $invoice);
templateAssignSystemvars('smarty');

$smarty->assign('return', $return);
$smarty->assign('return2', $return2);

$smarty->display('file:invoice_payment.tpl');

/*
require "topp.php";
$aa_betale = $faktura->faktura_belop_sum;
echo '<h1>Register betaling for faktura</h1>'.chr(10).chr(10);
echo '- <a href="admin_faktura_list.php?visning='.$visning.'">Tilbake til fakturaliste</a><br><br>'.chr(10);
echo '<i>Tast inn beløp som ble betalt og når det ble betalt under.</i><br><br>'.chr(10);
echo '<form action="'.$_SERVER['PHP_SELF'].'?id='.$faktura->id.'&amp;visning='.$visning.'&amp;reg_bet=1" method="post">'.chr(10);
echo '<table class="fakturainfo">'.chr(10);
echo '<tr class="fakturainfo"><td class="fakturainfo"><b>BETALT:</b></td></tr>'.chr(10);
echo '<tr class="fakturainfo"><td class="fakturainfo"><b>Beløp:</b></td><td class="fakturainfo"><input type="text" name="belop_bet" value="'.$aa_betale.'"></td></tr>'.chr(10);
echo '<tr class="fakturainfo"><td class="fakturainfo"><b>Dato:</b></td><td class="fakturainfo"><input type="text" name="dato_bet" value="'.date('d-m-Y').'"> (format: dd-mm-yyyy)</td></tr>'.chr(10);
echo '<tr class="fakturainfo"><td class="fakturainfo">&nbsp;</td><td class="fakturainfo"><input type="submit" value="Register betaling"></td></tr>'.chr(10);
echo '</table></form><br><br>'.chr(10);
/**/
?>