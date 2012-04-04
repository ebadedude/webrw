<?php
	/*
	 * Index file
	 * 
	 * Stick this file in the root of your application.
	 * 
	 * @name: index.php
	 * @author: Bade Iriabho <ebade@yahoo.com>
	 * @copyright: 2011-12 Bade Iriabho
	 * @license: Free to use, just remember the first law of sharing "Give credit where it is due". Author is not liable for any damages that results from using this code.
	 * @version: See VERSION
	 * 
	 */

	include_once 'includes/class.webrw.php';
	
	if(isset($_GET['get']) || isset($_POST['get'])) {
		$my_webrw = new webrw();
		$my_webrw->get();
	} elseif(isset($_GET['set']) || isset($_POST['set'])) {
		$my_webrw = new webrw();
		$my_webrw->set();
	} else {
		echo "Invalid action!!!";
	}
?>
