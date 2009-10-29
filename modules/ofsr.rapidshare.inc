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
	* @version 0.1a
	*/
 
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
		
		private function getLinks() {
			
			// Set the api url
			$apiUrl = 'https://api.rapidshare.com/cgi-bin/rsapi.cgi?sub=checkfiles_v1&';
			
			$file_ids = '';
			$file_names = '';
			
			// Run through each URL given
			foreach($this->urls as $u) {
				
				// Run a preg_match to get the file id and name
				if ( preg_match( '#/(?<fid>\d+)/(?<fn>.+)$#', $u, $m ) ) {
			
					// Set up a linear array
					$file_ids .= $m['fid'] . ',';
					$file_names .= $m['fn'] . ',';
					
				} else {
					// Some kind of fail message here
				}
			
			} // end foreach
			
			// Cut off the extra commas
			$file_ids = substr($file_ids, -1);
			$file_names = substr($file_names, -1);
			
			// Build the URL
			$url = 'files=' . $file_ids . '&filenames=' . $file_names;
			
			// Get the CSV output of the rsapi
			$csv = $this->c->get($url)->body;
			
			// Split the lines by newline character
			$lines = explode("\n", $csv);
			
			foreach($lines as &$l) {
				
				$mini = explode(',', $l);
				
				
				$url = 'http://rs'
				. $mini[3] . $mini[5]
				. '.rapidshare.com/files/'
				. $mini[0] .'/' . $mini[1];
			
		}
	}

?>
