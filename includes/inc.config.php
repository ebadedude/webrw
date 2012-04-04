<?php 
	/*
	 * Configuration settings for webrw
	 * 
	 * This file contains configuration settings that can be changed to suite your needs so feel free to make changes here as needed.
	 * 
	 * @name: inc.config.php
	 * @author: Bade Iriabho <ebade@yahoo.com>
	 * @copyright: 2011-12 Bade Iriabho
	 * @license: Free to use, just remember the first law of sharing "Give credit where it is due". Author is not liable for any damages that results from using this code.
	 * @version: See VERSION
	 * 
	 */

	define('DEBUG_ON', FALSE);										// Turns on or off debugging
	define('WORKSPACE_DIRECTORY', 'doc');							// Directory name to use for server manipulated files, sits on root
	define('WORKSPACE_DELETE_AGE', 84600);							// How old files have to be before you delete them. Value is in seconds.
	define('WORKSPACE_EXCLUDE', '.htaccess|index.html');			// File(s) to exclude from the deletion process 		
	define('DS', DIRECTORY_SEPARATOR);								// Directory seperator
	define('DEFAULT_CONTENT_TYPE', 'text/javascript');				// 'text/javascript', 'text/html'
	define('DEFAULT_CALLBACK', 'callback');							// default callback function name
	
?>