<?php

	/*
	* OFS_LinkObject for the EasyOFSR class
	*
	* This class takes an array of link data and turns it into an object
	* providing a fluent interface for getting the link data from the
	* object instead of an array.
	*
	* @author Robert McLeod
	* @copyright Copyright 2009 Robert McLeod
	* @licence http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
	* @version 0.3b
	*/

	/* Class */
	class OFS_LinkObject {
	
		// Set some defaults
		private $error_message = false;
	
		function __construct($array) {
		
			// Put all the array data into the object
			foreach ( $array as $k => $v ) {	
				$this->{$k} = $v;
			}
		
		}
		
		/**
		* Returns the host of the object
		*
		* @return string
		*/
		public function getHost() {
		
			return $this->host;
		
		}
		
		/**
		* Returns the link to the file
		*
		* @return string
		*/
		public function getLink() {
			
			return $this->link;
		
		}
		
		/**
		* Returns an error message or false
		* if there is no error message
		*
		* @return string
		*/
		public function getError() {
		
			return $this->error_message;
			
		}
		
		/**
		* Returns whether the link is live or
		* has been removed.
		*
		* @return bool
		*/
		public function getStatus() {
		
			return $this->live;
			
		}
		
		/**
		* Returns the size of the file
		*
		* @return string
		*/
		public function getSize() {
		
			return $this->size;
		
		}
		
		/**
		* Returns either the output of error()
		* or the output of link() depending on
		* if there is an error or not
		*
		* @return string
		*/
		public function getResult() {
		
			if($this->getStatus()) {
				return "<p><a href='{$this->link}'>{$this->link}</a></p>";
			} else {
				return "<p>{$this->error_message}</p>";
			}
		
		}

	
	}

?>
