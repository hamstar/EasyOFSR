<?php

	require 'curl.php';
	require 'htmldom.php';
	require 'ofsr.hotfile.inc';
	require 'ofsr.rapidshare.inc';

	/* Main class */
	class OFSR {

		function __construct() {}

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
			
			// Start curl
			$c = new Curl;
			
			// Run through each URL
			foreach ($urls as $u) {

				// Get the host and html from the url
				$host = parse_url($u, PHP_URL_HOST);
				$html = $c->get($u)->body;
				
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
