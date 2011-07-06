<?php

/*

Plugin for MediaWiki
- Authentication for Jærmuseet

- Made by Hallvard Nygård <hn@jaermuseet.no>

License: GNU GPL

Some of the code is based on IPBAuth version 1.1 and Auth_phpBB 3.0.3.

*/

require_once("AuthPlugin.php");

class AuthPluginJaermuseet extends AuthPlugin 
{
	
	
	function AuthPluginJaermuseet () {
		global $wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname;
		
		
            // Set some MediaWiki Values
            // This requires a user be logged into the wiki to make changes.
            $GLOBALS['wgGroupPermissions']['*']['edit'] = false;

            // Specify who may create new accounts:
            $GLOBALS['wgGroupPermissions']['*']['createaccount'] = false;

		
		/*
		 * set your settings here
		 */
		$dbhost = $wgDBserver; //$wgDBserver
		$dbusername = $wgDBuser; //$wgDBuser
		$dbpassword = $wgDBpassword; //$wgDBpassword
		//$dbname = $wgDBname; //$wgDBname
		$dbname = 'jm-booking';
		// set the usergroups for the administrators
		//$this->admin_usergroups = Array(4);
		//$this->user_rights = Array("sysop");
		// set the usergroups for those who can edit the wiki
		//$this->allowed_usergroups = Array(4,3,9);
		/*
		 * end user settings
		 */
		$this->database = mysql_connect($dbhost, $dbusername, $dbpassword);
		
		if(mysql_error($this->database)) {
			echo 'Ingen kontakt med database.<br>';
			echo mysql_error();
			exit();
		}
		mysql_select_db($dbname, $this->database);
		
		if(mysql_error($this->database)) {
			echo 'Ingen kontakt med database.<br>';
			echo mysql_error();
			exit();
		}
		 mysql_query("SET NAMES 'utf8'", $this->database);
		
		$this->debug = false;
	}
	
	/**
	 * Check whether there exists a user account with the given name.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @return bool
	 * @public
	 */
	function userExists( $username ) {
		if($this->debug)
			echo 'userExists';
		return true;
		$username = addslashes($username);
		$find_user_query = "SELECT user_id FROM users WHERE lower(user_name_short)=lower('{$username}') and deactivated = '0'";
		$find_result = mysql_query($find_user_query, $this->database);
		// make sure that there is only one person with the username
		if (mysql_num_rows($find_result) == 1) {
			//$userinfo = mysql_fetch_assoc($ipb_find_result);
			//mysql_free_result($ipb_find_result);
			// Only registered and admins. Banned and unregistered don't belong here.
			//if (in_array($ipb_userinfo['mgroup'], $this->allowed_usergroups)) {
				return true;
			//}
		}
		// if no one is registered with that username, or there are more than 1 entries
        	// or they have illegal characters return FALSE (they do not exist)
		return false;
	}
 
	/**
	 * Check if a username+password pair is a valid login.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @param $password String: user password.
	 * @return bool
	 * @public
	 */
	function authenticate( $username, $password ) {
		if($this->debug)
			echo 'authenticate<br>';
		$username = addslashes($username);
		$password = addslashes($password);
		
		if(isset($GLOBALS['authpluginjmTillatteBrukere']) && !in_array(strtolower($username), $GLOBALS['authpluginjmTillatteBrukere'])) {
			return false;
		}
		
		$find_user_query = "SELECT user_id FROM users WHERE lower(user_name_short)=lower('{$username}') AND user_password = MD5('{$password}')";
		$find_result = mysql_query($find_user_query, $this->database);
		if (mysql_num_rows($find_result) == 1) {
			//$ipb_userinfo = mysql_fetch_assoc($ipb_find_result);
			//mysql_free_result($ipb_find_result);
			// Only registered and admins. Banned and unregistered don't belong here.
			//if (in_array($ipb_userinfo['mgroup'], $this->allowed_usergroups)) {
			//	$this->passwordchange = true;
			echo 'auth=true<br>';
				return true;
			//}
		}
		return false;
	}
 
 
	/**
	 * When a user logs in, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param User $user
	 * @public
	 */
	function updateUser( &$user ) {
		if($this->debug)
			echo 'updateUser';
		$username = addslashes($user->getName());
		
		$find_user_query = "SELECT
			user_id,
			user_accesslevel, user_email, 
			user_name_short, user_name
			FROM users WHERE lower(user_name_short)=lower('{$username}')";
		$find_result = mysql_query($find_user_query, $this->database);
		// make sure that there is only one person with the username
		if (mysql_num_rows($find_result) == 1) {
			$userinfo = mysql_fetch_assoc($find_result);
			mysql_free_result($find_result);
			$user->setEmail($userinfo['user_email']);
			$user->confirmEmail();
			$user->setRealName($userinfo['user_name']);
			
			// Accessrights
			if($userinfo['user_accesslevel'] > 2)
				$user->addGroup('sysop');
			
			$user->saveSettings();
			return true;
		}
		return false;
	}
 
 
	/**
	 * Return true if the wiki should create a new local account automatically
	 * when asked to login a user who doesn't exist locally but does in the
	 * external auth database.
	 *
	 * If you don't automatically create accounts, you must still create
	 * accounts in some way. It's not possible to authenticate without
	 * a local account.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return bool
	 * @public
	 */
	function autoCreate() {
		if($this->debug)
			echo 'autoCreate';
		return true;
	}
 
	/**
	 * Can users change their passwords?
	 *
	 * @return bool
	 */
	function allowPasswordChange() {
		//return $this->passwordchange;
		if($this->debug)
			echo 'allowPasswordChange';
		
		return false;
		//return true;
	}
 
	/**
	 * Set the given password in the authentication database.
	 * As a special case, the password may be set to null to request
	 * locking the password to an unusable value, with the expectation
	 * that it will be set later through a mail reset or other method.
	 *
	 * Return true if successful.
	 *
	 * @param $user User object.
	 * @param $password String: password.
	 * @return bool
	 * @public
	 */
	function setPassword( $user, $password ) {
		if($this->debug)
			echo 'setPassword';
		return true;
	}
 
	/**
	 * Update user information in the external authentication database.
	 * Return true if successful.
	 *
	 * @param $user User object.
	 * @return bool
	 * @public
	 */
	function updateExternalDB( $user ) {
		if($this->debug)
			echo 'updateExternalDB';
		return false;
	}
 
	/**
	 * Check to see if external accounts can be created.
	 * Return true if external accounts can be created.
	 * @return bool
	 * @public
	 */
	function canCreateAccounts() {
		if($this->debug)
			echo 'canCreateAccounts';
		return false;
	}
	
	/**
	 * Add a user to the external authentication database.
	 * Return true if successful.
	 *
	 * @param User $user
	 * @param string $password
	 * @return bool
	 * @public
	 */
	function addUser( $user, $password ) {
		if($this->debug)
			echo 'addUser<br>';
		return false;
		//return true;
	}
 
 
	/**
	 * Return true to prevent logins that don't authenticate here from being
	 * checked against the local database's password fields.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return bool
	 * @public
	 */
	function strict() {
		if($this->debug)
			echo 'strict';
		return true;
	}
	 function strictUserAuth() {
		if($this->debug)
			echo 'strictUserAuth<br>';
		return true;
	}
	/**
	 * When creating a user account, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param $user User object.
	 * @public
	 */
	function initUser( &$user , $autocreate = false) {
		if($this->debug) {
			echo 'initUser<br>';
			echo 'autocreate='.$autocreate.'<br>';
		}
		$username = addslashes($user->getName());
		$find_user_query = "SELECT user_id, user_email, user_name FROM users WHERE lower(user_name_short)=lower('{$username}') and deactivated = '0'";
		$find_result = mysql_query($find_user_query, $this->database);
		// make sure that there is only one person with the username
		if (mysql_num_rows($find_result) == 1) {
			$userinfo = mysql_fetch_assoc($find_result);
			mysql_free_result($find_result);
			$user->setEmail($userinfo['user_email']);
			$user->confirmEmail();
			$user->setRealName($userinfo['user_name']);
			$user->mPassword = '';
			$user->saveSettings();
		}
	}
	
	/**
	 * If you want to munge the case of an account name before the final
	 * check, now is your chance.
	 */
	function getCanonicalName( $username ) {
		if($this->debug)
			echo 'getCanonicalName<br>';
		return strtoupper($username);
	}
	function validDomain ($domain) {
		return true;
	}
}