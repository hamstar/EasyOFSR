<?php

	/*
	* OFS_Base class for the EasyOFSR class
	*
	* This class provides login functions for the host
	* modules that extend off it.
	*
	* @author Robert McLeod
	* @copyright Copyright 2009 Robert McLeod
	* @licence http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
	* @version 0.3b
	*/

	class OFS_Base {
	
		protected $c;
		protected $pass;
		protected $user;
		
		function __construct($urls, $credentials) {
			
			// Set the credentials and url list
			$this->urls = $urls;
			$this->user = $credentials['user'];
			$this->pass = $credentials['pass'];
			$this->c = new Curl;
			$this->c->useragent = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.14) Gecko/2009090216 Ubuntu/9.04 (jaunty) Firefox/3.0.14';
			
			// Try to login
			$this->login();
			
		}
		
		/**
		 * Checks if the user is logged by searching for a string
		 * in a given page.
		 * 
		 * @return bool
		 */
		protected function is_logged_in() {
			
			// Get the HTML
			$html = $this->c->get($this->loginTestUrl)->body;
			
			// Check for the logout URL
			if( !strstr($html, $this->loginTestCodePresence) ) {
				return false;
			} else {
				return true;
			}
			
		}
		
		/**
		 * Logs in as the user with the given credentials, given user/pass
		 * string, login url, and login method.  Verifies if the user is
		 * logged in and dies if the login failed.
		 * 
		 * @return bool
		 */
		protected function login() {
			
			// Generate the login string
			$loginString = str_replace( array('{USER}', '{PASS}'), array( $this->user, $this->pass), $this->loginString );
			
			// Send the login data with curl
			$this->c->{$this->loginMethod}($this->loginUrl, $loginString);
			
			// Check if the login worked
			if($this->is_logged_in()) {
				return true;
			} else {
				die('Login failed');
			}
			
		}
	
	}

?>
