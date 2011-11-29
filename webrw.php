<?php
	include_once 'includes/class.webrw.php';
	
	$action ='';
	
	if(isset($_GET['action'])) { $action = trim($_GET['action']); }
	elseif(isset($_POST['action'])) { $action = trim($_POST['action']); }
	
	$my_webrw = new webrw();
	switch($action) {
		case 'read':
			$my_webrw->read();
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