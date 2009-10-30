<?php

	/*
	* Rapidshare.com module for the EasyOFSR class
	*
	* This class extends OFS_Base and contains the
	* code to login to, and get data from, rapidshare.com
	*
	* @author Robert McLeod
	* @copyright Copyright 2009 Robert McLeod
	* @licence http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
	* @version 0.2a
	*/
 
	// Need to include the RSGet class
	require '../libs/PHP_RSGet.php';
 
	/* Class for hotfile.com */
	class OFS_rapidshare_com extends OFS_Base {
	
		/**
		 * Set all the login info for this module
		 */
		protected $loginTestUrl = 'https://ssl.rapidshare.com/cgi-bin/premiumzone.cgi';
		protected $loginTestCodePresence = 'Overview of the Account:';
		protected $loginUrl = 'https://ssl.rapidshare.com/cgi-bin/premiumzone.cgi';
		protected $loginMethod = 'post';
		protected $loginString = "login={USER}&password={PASS}&uselandingpage=1";
		
		private function returnObjects() {
			
			// Start our RS Get object
			$rs = new PHP_RSGet;
			
			// Get the links
			$links = $rs->getLinks($this->urls);
			
			// Run through all the link data
			foreach ( $links as $lo ) {
			
				// No link is an indicator of erroneous file
				if ( !is_string( $lo->link ) ) {
					
					// Build our array
					$to = array(
						'live' => false,
						'error_message' => $lo->status_msg,
						'link' => 'http://www.rapidshare.com/files/' . $lo->fid . '/' . $lo->fn
					);
					
					// Submit the array to the object
					$objects[] = new OFS_LinkObject( $to );
					
					// Next item in the array
					continue;
				}
				
				// The url was good build our array
				$to = array(
					'live' => true,
					'link' => $lo->link,
					'size' => $lo->size,
					'host' => 'rapidshare.com'
				);
				
				// Drop the array into the object
				$objects[] = new OFS_LinkObject( $to );
				
			} // End the foreach
			
			// Drop all the objects
			return $objects;
			
		} // End the function
		
	} // end the class

?>
