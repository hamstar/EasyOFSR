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
	* @licence	http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
	* @version	0.3b
	*/

	// Include our wrappers
	require 'libs/curl.php';
	require 'libs/htmldom.php';

	// Include our subclasses
	require 'classes/OFS_Base.php';
	require 'classes/OFS_LinkObject.php';

	// Include all our host classes (in the modules directory
	foreach(scandir(getcwd() . '/modules') as $fn) {
		// Check that the module starts with ofsr and ends with inc
		if (substr($fn, 0, 4) == 'ofsr' && substr($fn, -3) == 'inc') {
			// Require
			require 'modules/' . $fn;
		}
	}
	
	// Get all our known hosts
	foreach( get_declared_classes() as $class ) {
		// Filter by classes which start with OFS_
		if ( substr($class, 0, 4) == 'OFS_' ) {
			// Filter out these two
			if( $class != 'OFS_Base' && $class != 'OFS_LinkObject' ) {
				// Get the known host from the class name
				$known_hosts[] = str_replace( array('OFS_', '_'), array('', '.'), $class);	
			}
		}
	}

	// Serialize our known hosts into a constant
	define( 'KNOWN_HOSTS', serialize($known_hosts) );

	/* Main class */
	class OFSR {

		function __construct() {
			
			$this->known_hosts = unserialize( KNOWN_HOSTS );
			
		}

		/**
		 * Check if the host is known to us in the known_hosts array
		 * 
		 * @param string $host The host to check for familiarity
		 * 
		 * @return bool
		 */
		private function checkHost($host) {
			// Search the array and look for a real false
			if ( array_search( $host, $this->known_hosts ) === false ) {
				return false;
			}
			
			// Return true if we know it
			return true;
		}

		/**
		 * Adds user credentials to the credentials array
		 * 
		 * @param string $dsn Username password pair for a given host
		 * 
		 * @return object
		 */
		public function dsn($dsn) {
			
			// Extract the fields from the given dsn
			if ( preg_match('#(?<user>.*):(?<pass>.*)@(?<host>.*)#', $dsn, $m) ) {
				
				// Check that we know this host by searching the known hosts array
				if ( $this->checkHost($m['host']) ) {
					// If we do set the credentials array
					$this->credentials[$m['host']] = array( 'user' => $m['user'], 'pass' => $m['pass'] );
					
					return $this;
					
				} else {
					// Otherwise die
					die('Credentials given to the dsn() method are for an unknown host!');
				}
				
			} else {
				// Die if preg_match failed
				die('Invalid DSN given to the dsn() method.');
			}
			
		}

		/**
		* Gets the object for urls given to it
		*
		* @param mixed $urls A string or array of URLs
		*
		* @return array
		*/
		public function getLinks($urls) {
		
			// Start the link objects array
			$linkObjects = array();
		
			// If it is a string add it to an array
			if (is_string($urls)) {
				$urls[] = $urls;
			}
			
			// Run through each URL
			foreach ($urls as $u) {

				// Get the host and html from the url
				$host = str_replace('www.', '', parse_url($u, PHP_URL_HOST));
				
				// Throw each url in the sorted urls array
				$sortedUrls[$host][] = $u;

			}
			
			// Unset urls
			$urls = null;
			
			// Run through each of the host sorted array
			foreach ( $sortedUrls as $host => $urls ) {
				
				// Check that this is a known host
				if ( $this->checkHost($host) ) {
					
					$module = 'OFS_' . str_replace( '.', '_', $host );
					
					// Start up our host module class
					// Giving our url array and user credentials for this host
					$HostOFS = new $module($urls, $this->credentials[$host]);
					
					// Get the objects
					$_linkObjects = $HostOFS->returnObjects();
					
					// Merge the latest returned objects with the array
					$linkObjects = array_merge($linkObjects, $_linkObjects);
					
				} else {
					
					// Output error if we don't know the host
					echo "<p>Unknown host $host given in the getLinks() method.</p>";
				
				}
				
			}
			
			// Return the array of objects
			return $linkObjects;
			
		}

	}

?>
