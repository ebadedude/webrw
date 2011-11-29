<?php 
	/*
	 * Name: inc.config.php
	 * Type: Config File
	 * Written by: Bade Iriabho (c) 2011
	 * 
	 * Description:
	 * Hold the configuration variables. Make changes here as needed.
	 * 
	 */
	define('READ_MODE_JSON', 'READ_MODE_JSON');				//when using webrw->read, reads and returns the specified source line by line 
	define('READ_MODE_EXACT', 'READ_MODE_EXACT');			//when using webrw->read, reads and the specified source and return it in the specified mime type
	
	define('DEBUG_ON', TRUE);								//Turns on or off debugging
	define('WORKSPACE_DIRECTORY', 'uploadz');				//Directory name to use for server manipulated files, sits on root
	define('WORKSPACE_DELETE_AGE', 84600);					//How old files have to be before you delete them. Value is in seconds.
	define('DS', DIRECTORY_SEPARATOR);						//Directory seperator
	define('DEFAULT_CONTENT_TYPE', 'text/javascript');		//'text/javascript', 'text/html'
	define('DEFAULT_CALLBACK', 'callback');					//default callback function name
	define('DEFAULT_KEYVALUE_FILENAME', 'content');			//default name to use for key/value file names
	define('DEFAULT_READ_MODE', READ_MODE_JSON);			//select the default read mode
	
?>