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
 * Telefonliste
 *
 */

include_once("glob_inc.inc.php");

echo 
'<link rel="stylesheet" media="screen,projection" href="wiki/skins/RoundedBlue/main.css" />
<link rel="stylesheet" type="text/css" media="print" href="wiki/skins/common/commonPrint.css" />
<link rel="stylesheet" type="text/css" media="handheld" href="wiki/skins/RoundedBlue/handheld.css" />

<body  class=" ltr">
	<div id="globalWrapper">
		<div id="column-content">
			<div id="content" style="margin:0;">
				<div class="t"><div class="b"><div class="l"><div class="r"><div class="bl"><div class="br"><div class="tl"><div class="tr">
				<div class="post">
					<div id="bodyContent">
						<div id="bodyContentInnerWrapper">
';

if(!isset($_GET['gid']))
{
	$Q_groups = db()->prepare("select * from `groups` order by `group_name`");
	$Q_groups->execute();
	echo '<h1>Telefonliste fra bookingsystemet</h1>'.chr(10);
	echo '<ul>'.chr(10);
	while($R = $Q_groups->fetch())
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?gid='.$R['group_id'].'">'.$R['group_name'].'</a></li>';
	echo '</ul>'.chr(10);
}
else
{
	$group = getGroup($_GET['gid']);
	if(count($group))
	{
		echo '<h1>Telefonliste - '.$group['group_name'].'</h1>'.chr(10);
		if(!count($group['users']))
			echo '<i>Ingen brukere p&aring; denne listen</i>';
		else
		{
			echo '<table class="wikitable">';
			echo '	<tr>'.chr(10);
			echo '		<th>Navn</th>'.chr(10);
			echo '		<th>Telefon</th>'.chr(10);
			echo '		<th>Stilling</th>'.chr(10);
			echo '	</tr>'.chr(10);
			foreach($group['users'] as $user)
			{
				$user = getUser($user);
				if(count($user) && !$user['deactivated'])
				{
					echo '	<tr>'.chr(10);
					echo '		<td>'.$user['user_name'].'</td>'.chr(10);
					echo '		<td>'.$user['user_phone'].'</td>'.chr(10);
					echo '		<td>'.$user['user_position'].'</td>'.chr(10);
					echo '	</tr>'.chr(10);
				}
			}
			echo '</table>'.chr(10);
		}
		echo '<br><br>';
		echo '<i>For redigering av liste, s&aring; m&aring; du redigere brukerne i bookingsystemet.</i>';
	}
}

echo '

</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</body>';