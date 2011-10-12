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
	define('DEBUG_ON', TRUE);								//Turns on or off debugging
	define('WORKSPACE_DIRECTORY', 'uploads');				//Directory name to use for server manipulated files, sits on root. Note: Apache needs to be able to write to this folder.
	define('DS', DIRECTORY_SEPARATOR);						//Directory seperator
	define('DEFAULT_CONTENT_TYPE', 'text/javascript');		//'text/javascript', 'text/html'
	define('DEFAULT_CALLBACK', 'callback');					//default callback function name
	define('DEFAULT_KEYVALUE_FILENAME', 'content');			//default name to use for key/value file names
	
?>