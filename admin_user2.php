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
	Administration of users
*/

require __DIR__ . '/libs/editor.class.php';
$section = 'users';

include "include/admin_top.php";

if(isset($_GET['editor']))
{
	$id = 0;
	if(isset($_GET['id']) && is_numeric($_GET['id']))
		$id = (int)$_GET['id'];
	if(isset($_POST['id']) && is_numeric($_POST['id']))
		$id = (int)$_POST['id'];
	
	if($id <= 0)
	{
		$editor = new editor('users', $_SERVER['PHP_SELF'].'?editor=1');
		$editor->setHeading('Ny bruker');
		$editor->setSubmitTxt(__('Add'));
		if(!$login['user_access_useredit'])
		{
			showAccessDenied($day, $month, $year, $area, true);
			exit ();
		}
	}
	else
	{
		$editor = new editor('users', $_SERVER['PHP_SELF'].'?editor=1', $id);
		$editor->setHeading('Endre bruker');
		$editor->setSubmitTxt(__('Change'));
		
		if(!$login['user_access_useredit'] && $id != $login['user_id'])
		{
			showAccessDenied($day, $month, $year, $area, true);
			exit ();
		}
	}
	
	$editor->setDBFieldID('user_id');
	$editor->showID (TRUE);
	
	// Login name, only for people with access to editing users can change this
	if(!$login['user_access_useredit'])
		$user_name_short_txt = ', kan ikke endres av deg';
	else
		$user_name_short_txt = '';
	$editor->makeNewField('user_name_short', 'Innloggingsnavn / initialer'.$user_name_short_txt, 'text');
	if(!$login['user_access_useredit'])
	{
		$editor->vars['user_name_short']['disabled'] = true;
		$editor->vars['user_name_short']['DBQueryPerform'] = false;
	}
	
	$editor->makeNewField('user_name', 'Navn', 'text');
	$editor->makeNewField('user_email', __('E-mail'), 'text');
	$editor->makeNewField('user_phone', __('Phone'), 'text');
	$editor->makeNewField('user_position', 'Stilling', 'text');
	
	$editor->makeNewField('user_area_default', __('Default area'), 'select');
	$Q_area = db()->prepare("select id as area_id, area_name from `mrbs_area` order by `area_name`");
    $Q_area->execute();
	while($R_area = $Q_area->fetch()) {
        $editor->addChoice('user_area_default', $R_area['area_id'], $R_area['area_name']);
    }
	
	$editor->makeNewField('user_ews_sync', _h('Syncronize with Exchange'), 'boolean');
	$editor->makeNewField('user_ews_sync_email', _h('Main Exchange e-mail'), 'text');
	
	if($login['user_access_userdeactivate'])
	{
		$editor->makeNewField('deactivated', 'Er brukeren deaktivert', 'boolean');
	}
	
	if($login['user_access_changerights'])
	{
		$editor->makeNewField('user_access_changerights', 'Tilgang til &aring; endre brukeres rettigheter', 'boolean');
		$editor->vars['user_access_changerights']['before'] = 
			"\t<tr>\n\t\t<td><h2>".__('Userrights')."</h2></td>\n\t</tr>". // Added heading
			"\t<tr>\n\t\t<td>";
		$editor->makeNewField('user_access_useredit', 'Tilgang til &aring; endre brukere', 'boolean');
		$editor->makeNewField('user_access_userdeactivate', 'Tilgang til &aring; deaktivere brukere', 'boolean');
		$editor->makeNewField('user_access_areaadmin', __('Access to edit area and room'), 'boolean');
		$editor->makeNewField('user_access_entrytypeadmin', 'Tilgang til &aring; endre bookingtyper', 'boolean');
		$editor->makeNewField('user_access_importdn', 'Tilgang til &aring; importere tall fra Datanova kassesystem', 'boolean');
		$editor->makeNewField('user_access_productsadmin', 'Tilgang til &aring; endre i produktsregister', 'boolean');
		$editor->makeNewField('user_access_programadmin', 'Tilgang til &aring; endre faste program', 'boolean');
		$editor->makeNewField('user_access_templateadmin', 'Tilgang til &aring; endre p&aring; systemmaler', 'boolean');
		$editor->makeNewField('user_invoice_setready', 'Tilgang til &aring; sette bookinger faktureringsklar', 'boolean');
		$editor->makeNewField('user_invoice', 'Tilgang til &aring; merke bookinger som sendt til regnskap', 'boolean');
	}
	
	if($login['user_access_useredit'])
	{
		$Q_groups = db()->prepare('select * from `groups` order by group_name');
        $Q_groups->execute();
		$first = true;
		while($R_group = $Q_groups->fetch())
		{
			$editor->makeNewField('group_'.$R_group['group_id'], 
				$R_group['group_name'], 'boolean', 
				array(
					'noDB' => true,
				));
			$editor->vars['group_'.$R_group['group_id']]['DBQueryPerform'] = false;
			if($first)
			{
				$editor->vars['group_'.$R_group['group_id']]['before'] = 
					"\t<tr>\n\t\t<td><h2>".__('Groups')."</h2></td>\n\t</tr>". // Added heading
					"\t<tr>\n\t\t<td>";
				$first = false;
			}
			
			// Adding value
			$gusers = splittIDs($R_group['user_ids']);
			$editor->vars['group_'.$R_group['group_id']]['value'] = in_array($id,$gusers);
		}
	}
	
	
	/* Disabled until implementet
	
	// TODO: Implement
	$editor->makeNewField('user_areas', 'Tilgang til', 'checkbox', array('defaultValue' => -1));
	$Q_area = db()->prepare("select id as area_id, area_name from `mrbs_area` order by `area_name`");
    $Q_area->execute();
	$editor->addChoice('user_areas', -1, _('All areas'));
	while($R_area = $Q_area->fetch())
		$editor->addChoice('user_areas', $R_area['area_id'], $R_area['area_name']);
	*/
	
	$editor->getDB();
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Edit of groups
				$Q_groups = db()->prepare("select * from `groups` order by group_name");
				$Q_groups->execute();
				$first = true;
				while($R_group = $Q_groups->fetch())
				{
					$gusers = splittIDs($R_group['user_ids']); // Users in group
					if(
						$editor->vars['group_'.$R_group['group_id']]['value'] && // Wants to be in group 
						!in_array($id, $gusers) // Are not in group
					)
					{
						// Update
						$gusers_new = $R_group['user_ids'].';'.$id.';';
						$Q = db()->prepare("UPDATE `groups` SET `user_ids` = '".$gusers_new."'
							WHERE `group_id` = '".$R_group['group_id']."' LIMIT 1 ;");
                        $Q->execute();
					}
					elseif(
						!$editor->vars['group_'.$R_group['group_id']]['value'] && // Don't want to be in group 
						in_array($id, $gusers) // Are in group
					)
					{
						// Update
						$gusers_new = str_replace(';'.$id.';', '', $R_group['user_ids']);
						$Q = db()->prepare("UPDATE `groups` SET `user_ids` = '".$gusers_new."'
							WHERE `group_id` = '".$R_group['group_id']."' LIMIT 1 ;");
                        $Q->execute();
					}
				}
				
				// Redirect
				header('Location: '.$_SERVER['PHP_SELF']);
				exit();
			}
			else
			{
				echo 'Error occured while performing query on database:<br>'.chr(10),
				//echo '<b>Error:</b> '.$editor->error();
				exit();
			}
		}
	}
	
	include "include/admin_middel.php";
	$editor->printEditor();
	//echo '* = '._('Password won\'t be changed unless you type in a new one.');
	echo '* = Passordet blir bare endret hvis det blir skrevet inn ett nytt ett';
}
else
{
	include "include/admin_middel.php";
	
	echo '<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>'.chr(10);
	echo '<script src="js/hide_unhide.js" type="text/javascript"></script>'.chr(10);
	
	echo '<h1>'.__('Users').'</h1>';
	// Add
	if($login['user_access_useredit']) {
        echo iconHTML('user_add') . ' <a href="' . $_SERVER['PHP_SELF'] . '?editor=1">' . __('New user') . '</a><br>' . chr(10);
    }
	
	echo iconHTML('phone').' <a href="telefonliste.php">Telefonliste</a><br><br>'.chr(10);
	
	$rights = array(
	
			'1' => 'Rettighet til &aring; administrere brukeres rettigheter',
			'2' => 'Rettighet til &aring; administrere brukere',
			'3' => 'Rettighet til &aring; endre p&aring; anlegg og rom',
			'4' => 'Rettighet til &aring; endre p&aring; bookingtyper',
			'5' => 'Rettighet til &aring; importere data fra Datanovas kassesystem',
			'6' => 'Rettighet til &aring; endre vareregisteret',
			'7' => 'Rettighet til &aring; endre program',
			'8' => 'Rettighet til &aring; endre systemmaler',
			'9' => 'Rettighet til &aring; sette faktureringsklar',
			'10' => 'Rettighet til &aring; sette bookinger som sendt til regnskap',
			'11' => 'Rettighet til &aring; deaktivere brukere',
			'sync' => 'Synkronisering mot Exchange / Outlook p&aring;sl&aring;tt',
			'external' => 'Passord tilfredstiller krav til p&aring;logging eksternt',
		);
	echo '<script src="js/jquery.hoverbox.min.js" type="text/javascript"></script>';
	echo '<script type="text/javascript">
	$(document).ready(function(){
		$(\'.rightsHover\').hoverbox();
		
		$(\'.prettytable tr\').hover(
			function() {
				$(\'td\', this).addClass(\'green\');
			},
			function() {
				$(\'td\', this).removeClass(\'green\');
			}
		);
	});
	</script>
	';
	// List of users
	echo '<h2>'.__('List of users').'</h2>'.chr(10);
	$Q_users = db()->prepare("select user_id from `users` order by `user_name`");
	$Q_users->execute();
	if($Q_users->rowCount() <= 0)
		echo __('No users found.');
	else
	{
		echo '<a href="javascript:void();" class="showAll">Vis info p&aring; alle / Ikke vis info p&aring; alle</a>';
		echo '<table class="prettytable">'.chr(10);
		echo '	<tr>'.chr(10);
		echo '		<th>ID</th>'.chr(10);
		echo '		<th>Bruker</th>'.chr(10);
		echo '		<th>Login</th>'.chr(10);
		echo '		<th>Info</th>'.chr(10);
		echo '		<th>Valg</th>'.chr(10);
		echo '		<th>Grupper som brukeren er medlem av</th>'.chr(10);
		echo '		<th>1</th>'.chr(10);
		echo '		<th>2</th>'.chr(10);
		echo '		<th>3</th>'.chr(10);
		echo '		<th>4</th>'.chr(10);
		echo '		<th>5</th>'.chr(10);
		echo '		<th>6</th>'.chr(10);
		echo '		<th>7</th>'.chr(10);
		echo '		<th>8</th>'.chr(10);
		echo '		<th>9</th>'.chr(10);
		echo '		<th>10</th>'.chr(10);
		echo '		<th>11</th>'.chr(10);
		echo '		<th>Synk</th>'.chr(10);
		echo '		<th>Ekstern tilgang</th>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
		while($R_user = $Q_users->fetch())
		{
			$user = getUser($R_user['user_id'], true);
			echo '	<tr>'.chr(10);
			
			if($user['deactivated'])
			{
				$deactivated = 'strike graytext';
				$deactivated2 = 'graytext';
			}
			else
			{
				$deactivated = '';
				$deactivated2 = '';
			}
			
			echo '		<td class="'.$deactivated.'">'.$user['user_id'].'</td>';
			
			echo '		<td class="'.$deactivated.'">'.
					'<a href="user.php?user_id='.$user['user_id'].'" class="'.$deactivated2.'">'.
					iconHTML('user').' '.
					$user['user_name'].'</a>'.
				'</td>'.chr(10);
			
			echo '		<td class="'.$deactivated.'">'.
					$user['user_name_short'].
				'</td>'.chr(10);
			
			echo '		<td class="'.$deactivated.'">'.
					'<div class="showButton" id="buttonId'.$user['user_id'].'"><a href="javascript:void();" class="'.$deactivated2.'">Vis / Ikke vis</a></div>'.
					'<div class="showField" id="fieldId'.$user['user_id'].'" style="display:none;">'.
					'Telefon: '.$user['user_phone'].'<br>'.
					'E-post: '.$user['user_email'].'<br>'.
					'Stilling: '.$user['user_position'].'<br>';
				$area_user = getArea($user['user_area_default']);
				if(!count($area_user))
					$area_user['area_name'] = 'IKKE FUNNET'; 
				echo __('Default area').': '.$area_user['area_name'];
				'</div></td>'.chr(10);
			
			echo '		<td class="'.$deactivated.'">';
			
			if($login['user_access_useredit'] || $login['user_id'] == $user['user_id'])
			{
				echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1&amp;id='.$user['user_id'].'" class="'.$deactivated2.'">'.
					iconHTML('user_edit').' '.
					'Endre&nbsp;bruker</a><br />';
				echo '<a href="admin_user_password.php?id='.$user['user_id'].'" class="'.$deactivated2.'">'.
					iconHTML('lock_edit').' '.
					'Endre&nbsp;passord</a>';
			}
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td>';
			if(count($user) && !$user['deactivated'] && count($user['groups']))
			{
				echo '<ul style="margin: 0;">'.chr(10);
				foreach($user['groups'] as $gid)
				{
					$group = getGroup($gid);
					if(count($group))
						echo '<li>'.$group['group_name'].'</li>'.chr(10);
				}
				echo '</ul>'.chr(10);
			}
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[1].'">';
			if($user['user_access_changerights'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[2].'">';
			if($user['user_access_useredit'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[3].'">';
			if($user['user_access_areaadmin'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[4].'">';
			if($user['user_access_entrytypeadmin'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[5].'">';
			if($user['user_access_importdn'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[6].'">';
			if($user['user_access_productsadmin'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[7].'">';
			if($user['user_access_programadmin'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[8].'">';
			if($user['user_access_templateadmin'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[9].'">';
			if($user['user_invoice_setready'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[10].'">';
			if($user['user_invoice'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights[11].'">';
			if($user['user_access_userdeactivate'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights['sync'].'">';
			if($user['user_ews_sync'])
				echo 'X';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10);
			
			echo '		<td class="rightsHover" title="'.$rights['external'].'">';
			try
			{
				if($user['user_password_complex'] != '1') {
                    throw new Exception('');
                }
				
				loginPWcheckAge($user);
				
				echo 'X';
			}
			catch (Exception $e)
			{
				echo '&nbsp;';
			}
			echo '</td>'.chr(10);
			
			echo '	</tr>'.chr(10).chr(10);
			//echo '- <br>'.chr(10);
		}
		echo '</table>'.chr(10);
		
		echo '<ul>'.
				'<li>1, '.$rights[1].'</li>'.
				'<li>2, '.$rights[2].'</li>'.
				'<li>3, '.$rights[3].'</li>'.
				'<li>4, '.$rights[4].'</li>'.
				'<li>5, '.$rights[5].'</li>'.
				'<li>6, '.$rights[6].'</li>'.
				'<li>7, '.$rights[7].'</li>'.
				'<li>8, '.$rights[8].'</li>'.
				'<li>9, '.$rights[9].'</li>'.
				'<li>10, '.$rights[10].'</li>'.
				'<li>11, '.$rights[11].'</li>'.
			'</ul>';
	}
}

echo '</td>
</tr>
</table>
</HTML>';