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
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * 
 * This file contains lots of old and used variables. Be carefull when editing
 * 
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */















/* ******************************************************************
 * NOTICE
 ********************************************************************
 * Please change at least the non-indented Parts of this config-file.
 * 
 * The indented blocks aren't *that* important (but you should have
 * a look at them, too.
 *******************************************************************/


###########################################################################
#   ARBS Configuration File
#   Configure this file for your site.
#   You shouldn't have to modify anything outside this file.
###########################################################################


################################
#Mailing rules
#note: there is no sendmail_adm_on_update/delete. most likely the administrator knows when he has changed something
################################
$mrbs_robot_email = "hn@jaermuseet.no"; #adress in the "FROM" header of auto-mails
define('EMAIL_FROM', $mrbs_robot_email);
$mrbs_admin_email = "hallvard.nygaard@jaermuseet.no"; #adress where notifications are beeing send to


###################
# Calendar settings
###################
#set this false to show the name instead of a booking block on the calendar where only one entry is present
$showSingleEntrysAsBlock=false;
# Resolution - what blocks can be booked, in seconds.
# Default is half an hour: 1800 seconds.
$resolution = 60*15;

# Start and end of day, NOTE: These are integer hours only, 0-23, and
# morningstarts must be < eveningends. See also eveningends_minutes.
$morningstarts = 8;
$eveningends   = 20;
$lastBookingHour=16;

$allowStartBookingAtWeekend=false;#if this is false, a user may book a device from friday to monday, but not from sunday to monday.
# Minutes to add to $eveningends hours to get the real end of the day.
# Examples: To get the last slot on the calendar to be 16:30-17:00, set
# eveningends=16 and eveningends_minutes=30. To get a full 24 hour display
# with 15-minute steps, set morningstarts=0; eveningends=23;
# eveningends_minutes=45; and resolution=900.
$eveningends_minutes = 0;

# Start of week: 0 for Sunday, 1 for Monday, etc.
$weekstarts = 1;

# Trailer date format: 0 to show dates as "Jul 10", 1 for "10 Jul" 
$dateformat = 1;