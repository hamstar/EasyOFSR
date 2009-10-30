<?php
 
	/*
	* Hotfile.com module for the EasyOFSR class
	*
	* This class extends OFS_Base and contains the
	* code to login to, and get data from, hotfile.com
	*
	* @author Robert McLeod
	* @copyright Copyright 2009 Robert McLeod
	* @licence http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
	* @version 0.4b
	*/
 
	/* Class for hotfile.com */
	class OFS_hotfile_com extends OFS_Base {
	
		/**
		 * Set all the login info for this module
		 */
		protected $loginTestUrl = 'http://hotfile.com';
		protected $loginTestCodePresence = '<a href="/logout.php';
		protected $loginUrl = 'http://hotfile.com/login.php';
		protected $loginMethod = 'post';
		protected $loginString = "user={USER}&pass={PASS}";
		
		/**
		 * Check the hotfile links with hotfiles own link checker.  Puts
		 * all the file details into the livefiles array.
		 * 
		 * @return void
		 */
		private function checkLinks() {
		
			// Start curl and get the data
			$postData = 'but= Check Urls &files='.implode("\n", $this->urls);			
			$html = str_get_html($this->c->post('http://hotfile.com/checkfiles.html', $postData)->body);
			
			// Get the table from the html
			$table = $html->find('table.tbl', 1);
			
			// Run through all the rows in tbody
			foreach($table->find('tr') as $tr) {
				
				// Skip the header row
				if($tr->bgcolor == '#dddddd') {
					continue;
				}
			
				// Get the status
				$status = trim($tr->find('td', 2)->plaintext);
				
				// Get all the file information into livefiles
				$this->livefiles[] = array(
					'size' => $tr->find('td', 1)->innertext,
					'live' => ($status == 'Existent') ? true : false,
					'link' => trim($tr->find('td', 0)->plaintext)
				);
				
			} // end the table rows foreach
			
			// Clear memory
			$html->clear();
			$table->clear();
			$html = null;
			$table = null;
		}
		
		/**
		 * Extracts the direct links from the pages obtained from the
		 * checkLinks() method.  Updates the livefiles varray.
		 * 
		 * @return void
		 */
		private function getDirectFiles() {
			
			// Run through all the livefiles
			foreach ( $this->livefiles as &$lf ) {
				
				// Add the host to lf
				$lf['host'] = 'hotfile.com';
				
				// Add an error message if the live is false
				if ( $lf['live'] == false ) {
					$lf['error_message'] = 'File was removed.';
					continue;
				}
				
				// Get the HTML and get the URL
				$html = str_get_html($this->c->get($lf['link'])->body);
				
				// Get all the '/get' links
				foreach ( $html->find('a[href^=http://hotfile.com/get]') as $a ) {
					$links[] = $a->href;
				}
				
				// Run through all the files
				$count = 0;
				foreach ( $links as $l ) {
					
					// Watch for multiple links on one page
					if($count == 0) {
						// Update the link in the livefiles with the direct one
						$lf['link'] = $l;
					} else {
						// Spawn a new livefile entry if there
						// are multiple get links on the page
						$_lf = $lf;
						$_lf['link'] = $l;
						$this->livefiles[] = $_lf;
					}
					
					// Increment
					++$count;
				} // end foreach of files
				
				// Empty the links array to avoid infinite loop
				$links = array();
				
			}// end foreach all the livefiles

			// Clear memory
			$html->clear();
			$html = null;

		}
		
		/**
		* Get the links that the current links redirect to
		*
		* @return void
		*/
		private function getRedirectLinks() {
		
			// Set curl to not follow location
			$this->c->option['followlocation'] = false;
			
			// Get the real link for each redir link
			foreach ( $this->livefiles as &$lf ) {
				// Get the location into the array
				$lf['link'] = $this->c->get($lf['link'])->headers['location'];
				
			}
		
			// Reset the curl option
			$this->c->option['followlocation'] = true;
		
		}
		
		/**
		 * This function calls the other functions to do their thing
		 * and then returns an array of link objects
		 * 
		 * @return array
		 */
		public function returnObjects() {
			
			// Run the functions to get the links
			$this->checkLinks();
			$this->getDirectFiles();
			$this->getRedirectLinks();
			
			// Run through the livefiles array
			foreach ( $this->livefiles as $lf ) {
				
				// Create the link object
				$objects[] = new OFS_LinkObject($lf);
				
			}
			
			// Return an array of objects
			return $objects;
			
		}
	}
?>
