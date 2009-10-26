<?php

	/**
	* EasyOFSR class
	*
	* Retrieves 'premium' links from online file storage websites
	*
	* The host class files (i.e. osfr.rapidshare.inc) must be kept in
	* the same directory as this file.
	*
	* @author	Robert McLeod
	* @copyright	Copyright 2009 Robert McLeod
	* @licence	GNU GPL
	* @version	0.1a
	*/

	// Include our requires
	require 'curl.php';
	require 'htmldom.php';

	// Include all our host classes
	foreach(scandir(getcwd()) as $fn) {
		if (substr($fn, 0, 4) == 'osfr' && substr($fn, -3) == 'inc') {
			require $fn;
		}
	}
	
	// Check for the hosts classes so that
	// we may add the host names to our known hosts
	foreach ( get_declared_classes() as $class) {
		if( substr($class, 0, 4) == 'OFS_') ) {
			if($class != 'OFS_Object') {
				$known_hosts[] = str_replace('OFS_', '', $class);
			}
		}
	}
	
	// Define this so we can use the constant in the class
	define('KNOWN_HOSTS', serialize($known_hosts));

	/* Main class */
	class OFSR {

		function __construct() {
			$this->known_hosts = unserialize(KNOWN_HOSTS);
		}

		/**
		 * Checks if the host is known to the class, I.E. if we have
		 * a host module that can handle the host given.
		 * 
		 * @param string $dsn Username/password pair for a host
		 * 
		 * @return object
		 */
		public function dsn($dsn) {
			
			if ( preg_match('#(?<user>.*):(?<pass>.*)@(?<host>.*)#', $dsn, $m) ) {
				if( array_search($m['host'], $this->known_hosts) === FALSE ) {
					die('Not a known host.');
				} else {
					$this->user = $m['user'];
					$this->pass = $m['pass'];
					$this->host = $m['host'];
				}
			}
			
			return $this;
		
		}

		/**
		* Gets the object for urls given to it
		*
		* @param mixed $urls A string or array of URLs
		*
		* @return mixed
		*/
		public function get($urls) {
		
			// If it is a string add it to an array
			if (is_string($urls)) {
				$urls[] = $urls;
			}
			
			// Run through each URL
			foreach ($urls as $u) {

				// Get the host and html from the url
				$host = parse_url($u, PHP_URL_HOST);
				
				// If the class for this host exists run it
				// or return the default class
				if( class_exists( OFS_{$host} ) ) {
				
					$object = new OFS_{$host}($html, $u, $host);
					
				} else {
				
					$object = new OFS_Object(false, $u, $host);
					
				}
				
				// Add the objects into files
				$files[] = $object;

			}
			
			// If there is only one file
			// drop the object
			if(count($files) == 1) {
				return $files[0];
			}
			
			// Else drop the array
			return $files;
			
		}

	}
	
	/* Base class for host classes to work off */
	class OFS_Object {
	
		/**
		* Constructor. Starts the simplehtmldom element
		* runs the html processor, and then clears the html
		* object after.
		*
		* @param string $html The HTML of the page to get data from
		* @param string $url The URL of the HTML page that was gotten
		* @param string $host The host of the HTML page that was gotten
		*
		* @return void
		*/
		function __construct($html = false, $url, $host) {
		
			$this->url = $url;
			$this->host = $host;
		
			if ($html) {
			
				// Get the html into a simplehtmldom object
				$this->$html = str_get_html($html);
				
				// Get the data
				// (this function is in the host classes)
				$this->processHTML();
				
				// Clear the HTML to save memory
				$this->html->clear();
				$this->html = null;
				
			} else {
			
				protected $error = true;
				protected $error_message = 'The host '. $this->host .' is not supported.';
			
			}
		
		}
		
		/**
		* Returns either the output of error()
		* or the output of link() depending on
		* if there is an error or not
		*
		* @return string
		*/
		public function result() {
		
			if($error) {
				return $this->error();
			} else {
				return $this->link();
			}
		
		}
		
		/**
		* Returns the host of the object
		*
		* @return string
		*/
		public function host() {
		
			return $this->host;
		
		}
		
		/**
		* Returns the URL of the page
		* that was curled.
		*
		* @return string
		*/
		public function original() {
		
			return $this->url;
		
		}
		
		/**
		* Returns the link to the file
		*
		* @return string
		*/
		public function link() {
			
			return $this->link;
		
		}
		
		/**
		* Returns an error message or false
		* if there is no error message
		*
		* @return mixed
		*/
		public function error() {
		
			if($this->error) {
				return $this->error_message;
			} else {
				return false;
			}
			
		}
		
		/**
		* Returns whether the link is live or
		* has been removed.
		*
		* @return bool
		*/
		public function live() {
		
			return $this->live;
			
		}
		
		/**
		* Returns the size of the file
		*
		* @return string
		*/
		public function size() {
		
			return $this->size;
		
		}
	
	}

?>