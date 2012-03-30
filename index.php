<?php
	include_once 'includes/class.webrw.php';
	
	$action = '';
	$mode = '';
	$mimetype = '';
	
	if(isset($_GET['action'])) 			{ $action = trim($_GET['action']); }
	elseif(isset($_POST['action'])) 	{ $action = trim($_POST['action']); }
	
	if(isset($_GET['mode'])) 			{ $mode = trim($_GET['mode']); }
	elseif(isset($_POST['mode'])) 		{ $mode = trim($_POST['mode']); }
	
	if(isset($_GET['mimetype'])) 		{ $mimetype = trim($_GET['mimetype']); }
	elseif(isset($_POST['mimetype'])) 	{ $mimetype = trim($_POST['mimetype']); }
	
	$my_webrw = new webrw();
	
	switch($action) {
		case 'read':
			if(strcmp('exact',strtolower($mode)) == 0) {
				if(strlen($mimetype) < 1) {
					$mimetype = DEFAULT_CONTENT_TYPE;
				}
				$my_webrw->read($mimetype, READ_MODE_EXACT);
			} else {
				$my_webrw->read();
			}
			break;
		case 'write':
			$my_webrw->write();
			break;
		case 'kvread':
			$my_webrw->kvread();
			break;
		case 'kvwrite':
			$my_webrw->kvwrite();
			break;
		case 'kvupdate':
			$my_webrw->kvupdate();
			break;
		default:
			if(isset($_GET['get']) || isset($_POST['get'])) {
			$my_webrw->get();
		} elseif(isset($_GET['set']) || isset($_POST['set'])) {
			$my_webrw->set();
		} else {
			echo "Invalid action!!!";
		}
		break;
	}
?>
