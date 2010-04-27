{*
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
*}{literal}<script type="text/php">

if ( isset($pdf) )
{
	$font = Font_Metrics::get_font("verdana");
	// If verdana isn't available, we'll use sans-serif.
	if (!isset($font)) { Font_Metrics::get_font("sans-serif"); }
	$size = 8;
	$color = array(0,0,0);
	$text_height = Font_Metrics::get_font_height($font, $size);
	
	$foot = $pdf->open_object();
	
	$w = $pdf->get_width();
	$h = $pdf->get_height();
	
	// Draw a line along the bottom
	$y = $h - 2 * $text_height - 24;
	$pdf->line(16, $y, $w - 16, $y, $color, 1);
	
{/literal}
	$text1 = "{$tittel}";
{literal}
	
	// Tittel - Side X av Y
	$y += $text_height;
	$text2 = $text1 . " - Side {PAGE_NUM} av {PAGE_COUNT}";  
	$width = Font_Metrics::get_text_width(html_entity_decode($text1). " - Side 1 av 2", $font, $size); // Center the text
	$pdf->page_text($w / 2 - $width / 2, $y, $text2, $font, $size, $color);
	
	
	$pdf->close_object();
	$pdf->add_object($foot, "all");
}
</script>{/literal}