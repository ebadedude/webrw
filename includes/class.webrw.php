<?php
/*
 * Name: class.webrw.php
 * Type: Class
 * Version: 1.1.0
 * Written by: Bade Iriabho (c) 2011
 * 
 * Description:
 *  Houses several functions that allows a paricular abstraction of a "web read and write" package
 * 
 * Querystrings
 * //General
 * 	action 	- Action you want the class to perform (read, write, keyval)
 * 	callback- Callback function name
 * 
 * //read
 * 	url 	- URL for resource
 *  arg		- Additional arguments
 *  onl		- Onload function
 *  
 * //write
 *  name	- Name of file to be written to
 *  value	- Value to be written to file
 *  mode	- Mode to use "w" overwrite, "a" append. In either case, if file does not exist, a new file is created 
 * 
 * //kvread
 *  key		- key used to retrieve content
 *  arg		- Additional arguments
 *  onl		- Onload function
 *  
 * //kvwrite
 *  value	- Value to be written to a new key
 *  
 * //kvupdate
 *  key		- key used to update content
 *  value	- Value to be written to a new key
 * 
 * 
 * REQUIRES:
 * - inc.config.php
 * - class.uuid.php
 * - inc.general.php
 * 
 */

//debug
if(DEBUG_ON) {
	ini_set("display_errors", 1);
	error_reporting(E_ALL);
}

//Includes and Requires
require_once 'inc.config.php';
require_once 'inc.general.php';
require_once 'class.uuid.php';

class webrw {
	private $webrw_action;				//Action to be performed
	private $webrw_url;					//Resource URL
	private $webrw_filename;			//Filename for writing
	private $webrw_writemode;			//Mode for making writes "w" overwrite, "a" append 
	private $webrw_writevalue;			//Value to be written
	private $webrw_callback;			//Callback function. Defaults to DEFAULT_CALLBACK if nothing is specified.
	private $webrw_argument;			//Additional argument used for reads
	private $webrw_onload;				//onload function to be added to read calls
	private $webrw_key;				//Key-value key string
	private $webrw_action_flag;
	private $webrw_read_flag;
	private $webrw_write_flag;
	private $webrw_messages;
	
	/*
	 * Public Set/Add Functions
	 */
	public function setAction($str='') {
		$this->webrw_action = trim($str);
	}
	public function setUrl($url='') {
		$this->webrw_url = trim($url);
	}
	public function setFileName($str='') {
		$this->webrw_filename = trim($str);
	}
	public function setWriteMode($str='') {
		$this->webrw_writemode = trim(strtolower($str));
	}
	public function setWriteValue($str='') {
		$this->webrw_writevalue = trim($str);
	}
	public function setCallback($str='') {
		$str = trim($str);
		if(strlen($str) > 0) {
			$this->webrw_callback = trim($str);
		} else {
			$this->webrw_callback = "callback";
		}
	}
	public function setArgument($str='') {
		$str = trim($str);
		if(strlen($str) > 0) {
			$this->webrw_argument = ','.trim($str);
		} else {
			$this->webrw_argument = '';
		}
	}
	public function setOnload($str='') {
		$str = trim($str);
		$this->webrw_onload = ';';
		if(strlen($str) > 0) {
			$this->webrw_onload .= $str.';';
		}
	}
	public function setKey($str='') {
		$this->webrw_key = str_replace('-', '', trim($str));
	}
	private function addMessage($str='') {
		if(strlen(trim($str)) > 0) {
			array_push($this->webrw_messages, '"'.$str.'"');
		}
	}
	
	/*
	 * Public Get Functions
	 */
	public function getAction() {
		return $this->webrw_action;
	}
	public function getUrl() {
		return $this->webrw_url;
	}
	public function getFileName() {
		return $this->webrw_filename;
	}
	public function getWriteMode() {
		return $this->webrw_writemode;
	}
	public function getWriteValue() {
		return $this->webrw_writevalue;
	}
	public function getCallback() {
		return $this->webrw_callback;
	}
	public function getArgument() {
		return $this->webrw_argument;
	}
	public function getOnload() {
		return $this->webrw_onload;
	}
	public function getKey() {
		return $this->webrw_key;
	}
	public function getMessages() {
		return $this->webrw_messages;
	}
	
	/*
	 * public other functions
	 */
	public function clearMessages() {
		$this->webrw_messages = array();
	}
	
	
	/*
	 * Constructor Function
	 */
	public function __construct() {
		//action
		if(isset($_GET['action'])) { $this->setAction(trim($_GET['action'])); } 
		elseif(isset($_POST['action'])) { $this->setAction(trim($_POST['action'])); }
		else { $this->setAction(''); }

		//url
		if(isset($_GET['url'])) { $this->setUrl(trim($_GET['url'])); } 
		elseif(isset($_POST['url'])) { $this->setUrl(trim($_POST['url'])); }
		else { $this->setUrl(''); }

		//filename
		if(isset($_GET['name'])) { $this->setFileName(trim($_GET['name'])); }
		elseif(isset($_POST['name'])) { $this->setFileName(trim($_POST['name'])); }
		else { $this->setFileName(''); }

		//write mode
		if(isset($_GET['mode'])) { $this->setWriteMode(trim($_GET['mode'])); }
		elseif(isset($_POST['mode'])) { $this->setWriteMode(trim($_POST['mode'])); }
		else { $this->setWriteMode(''); }
		
		//content or value to be written
		if(isset($_GET['value'])) { $this->setWriteValue(trim($_GET['value'])); }
		elseif(isset($_POST['value'])) { $this->setWriteValue(trim($_POST['value'])); }
		else { $this->setWriteValue(''); }
		
		//callback
		if(isset($_GET['callback'])) { $this->setCallback(trim($_GET['callback'])); } 
		elseif(isset($_POST['callback'])) { $this->setCallback(trim($_POST['callback'])); }
		else { $this->setCallback(DEFAULT_CALLBACK); }

		//arguments
		if(isset($_GET['arg'])) { $this->setArgument(trim($_GET['arg'])); }
		elseif(isset($_POST['arg'])) { $this->setArgument(trim($_POST['arg'])); }
		else { $this->setArgument(''); }
		
		//onload
		if(isset($_GET['onload'])) { $this->setOnload(trim($_GET['onload'])); }
		elseif(isset($_POST['onload'])) { $this->setOnload(trim($_POST['onload'])); }
		else { $this->setOnload(''); }

		//key
		if(isset($_GET['key'])) { $this->setKey(trim($_GET['key'])); }
		elseif(isset($_POST['key'])) { $this->setKey(trim($_POST['key'])); }
		else { $this->setKey(''); }
		
		$this->webrw_action_flag = FALSE;
		$this->webrw_read_flag = FALSE;
		$this->webrw_write_flag = FALSE;
		$this->webrw_messages = array();
	}
	
	/*
	 *  Destructor Function
	 */
	public function __destruct() {
		$this->setAction('');
		$this->setUrl('');
		$this->setFileName('');
		$this->setWriteMode('');
		$this->setWriteValue('');
		$this->setCallback('');
		$this->setArgument('');
		$this->setOnload('');
		$this->setKey('');
		$this->webrw_action_flag = FALSE;
		$this->webrw_read_flag = FALSE;
		$this->webrw_write_flag = FALSE;
		$this->webrw_messages = array();
	}
	
	/*
	 * read
	 */
	public function read($contentType=DEFAULT_CONTENT_TYPE) {
		//Set Page Header Content Type
		$this->setHtmlHeader($contentType);
			
		//create JSON framework
		$result = $this->getCallback().'({"content":||READ_CONTENT||, "url":"'.$this->getUrl().'", "date":"'.date('M-d-Y H:i:s').'", "success":||READ_SUCCESS||, "messages": [||READ_MESSAGE||]}||READ_ARG||)||READ_ONLOAD||';
		
		//Check that reading is possible
		if($this->actionValid() && $this->readValid()) { 
			$crl = curl_init();
			curl_setopt($crl, CURLOPT_URL, $this->getUrl());
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, 5);
			$rtn = curl_exec($crl);
			curl_close($crl);
		
			$final_rtn = '';
			$result = str_replace('||READ_ARG||', $this->getArgument(), $result);
			$result = str_replace('||READ_ONLOAD||', $this->getOnload(), $result);
			
			$search = array("\r\n", "\n\r", "\n", "\r");
			$rtn = str_replace($search, '||READ_DIV||', $rtn);
		
			$arr_rtn = explode('||READ_DIV||', $rtn);
			$counter = 0;
			foreach($arr_rtn as $val) {
				$final_rtn .= (($counter > 0)?",":"").json_encode($val);
				$counter++;
			}
			$result = str_replace('||READ_SUCCESS||', 'true', $result);
			$result = str_replace('||READ_MESSAGE||', implode(",", $this->getMessages()), $result);
			$result = str_replace('||READ_CONTENT||', "[".$final_rtn."]", $result);
			echo $result;
			
			return TRUE;
		}
		$result = str_replace('||READ_CONTENT||', '', $result);
		$result = str_replace('||READ_SUCCESS||', 'false', $result);
		$result = str_replace('||READ_MESSAGE||', implode(",", $this->getMessages()), $result);
		$result = str_replace('||READ_ARG||', '', $result);
		$result = str_replace('||READ_ONLOAD||', '', $result);
		echo $result;
		
		return FALSE;
	}
	
	/*
	 * write
	 */
	public function write($content='', $mode='', $contentType=DEFAULT_CONTENT_TYPE) {
		//Set Page Header Content Type
		$this->setHtmlHeader($contentType);
		
		//Check supplied content and mode
		if(strlen(trim($content)) > 0) { $this->setWriteValue($content); }
		if(strlen(trim($mode)) > 0) { $this->setWriteMode($mode); }
		
		//create JSON framework
		$result = $this->getCallback().'({"success":||WRITE_SUCCESS||, "messages": [||WRITE_MESSAGE||]})';

		//Check that writing is possible
		if($this->actionValid() && $this->writeValid()) {
			$fh = @fopen(WORKSPACE_DIRECTORY.DS.$this->getFileName(),$this->getWriteMode());
			if(!$fh) {
				$this->addMessage("Could not open specified file ('{$this->getFileName()}') for writing.");

				$result = str_replace('||WRITE_SUCCESS||','false', $result);
				$result = str_replace('||WRITE_MESSAGE||',implode(",", $this->getMessages()), $result);
				echo $result;
						
				return FALSE;
			} else {
				fwrite($fh,$this->getWriteValue());
				$this->addMessage("Successfully write to the specified file ('{$this->getFileName()}').");
				fclose($fh);

				$result = str_replace('||WRITE_SUCCESS||','true', $result);
				$result = str_replace('||WRITE_MESSAGE||',implode(",", $this->getMessages()), $result);
				echo $result;
	
				return TRUE;
			}
		}
		$result = str_replace('||WRITE_SUCCESS||','false', $result);
		$result = str_replace('||WRITE_MESSAGE||',implode(",", $this->getMessages()), $result);
		echo $result;
		
		return FALSE;
	}
	
	public function kvread($key='', $contentType=DEFAULT_CONTENT_TYPE, $dispRes=TRUE) {
		$readOK = TRUE;

		//Set Page Header Content Type
		if($dispRes === TRUE) { $this->setHtmlHeader($contentType); }
		
		//create JSON framework
		$result = $this->getCallback().'({"content":||KVREAD_CONTENT||, "key":"||KVREAD_KEY||", "date":"'.date('M-d-Y H:i:s').'", "success":||KVREAD_SUCCESS||, "messages": [||KVREAD_MESSAGE||]}||KVREAD_ARG||)||KVREAD_ONLOAD||';
		
		//check supplied key
		if(strlen($key) > 0) { $this->setKey($key); }
		if(strlen($this->getKey()) < 1) {
			$this->addMessage('You have not supplied any key.');
			$readOK = FALSE;
		}
		$result = str_replace('||KVREAD_KEY||', $this->getKey(), $result);
		
		$dir_host = $_SERVER['HTTP_HOST'];
		$dir_prot = (isset($_SERVER['HTTPS']))?"https://":"http://";
		if(!is_dir(WORKSPACE_DIRECTORY.DS.$this->getKey())) {
			$this->addMessage('The specified key does not exist.');
			$readOK = FALSE;
		}
		
		//Check that reading is possible
		if($readOK === TRUE && $this->actionValid()) { 
			$crl = curl_init();
			curl_setopt($crl, CURLOPT_URL, $dir_prot.$dir_host.DS.WORKSPACE_DIRECTORY.DS.$this->getKey());
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, 5);
			$rtn = curl_exec($crl);
			curl_close($crl);
		
			$final_rtn = '';
			$result = str_replace('||KVREAD_ARG||', $this->getArgument(), $result);
			$result = str_replace('||KVREAD_ONLOAD||', $this->getOnload(), $result);
			
			$search = array("\r\n", "\n\r", "\n", "\r");
			$rtn = str_replace($search, '||KVREAD_DIV||', $rtn);
		
			$arr_rtn = explode('||KVREAD_DIV||', $rtn);
			$counter = 0;
			foreach($arr_rtn as $val) {
				$final_rtn .= (($counter > 0)?",":"").json_encode($val);
				$counter++;
			}
			$result = str_replace('||KVREAD_SUCCESS||', 'true', $result);
			$result = str_replace('||KVREAD_MESSAGE||', implode(",", $this->getMessages()), $result);
			$result = str_replace('||KVREAD_CONTENT||', "[".$final_rtn."]", $result);
			if($dispRes === TRUE) { echo $result; }
			
			return TRUE;
		}
		$result = str_replace('||KVREAD_CONTENT||', '', $result);
		$result = str_replace('||KVREAD_SUCCESS||', 'false', $result);
		$result = str_replace('||KVREAD_MESSAGE||', implode(",", $this->getMessages()), $result);
		$result = str_replace('||KVREAD_ARG||', '', $result);
		$result = str_replace('||KVREAD_ONLOAD||', '', $result);
		if($dispRes === TRUE) { echo $result; }
		
		return FALSE;
	}
	
	public function kvwrite($content='', $contentType=DEFAULT_CONTENT_TYPE) {
		//Set Page Header Content Type
		$this->setHtmlHeader($contentType);
		
		//create JSON framework
		$result = $this->getCallback().'({"success":||KVWRITE_SUCCESS||, "key":"||KVWRITE_KEY||", "messages": [||KVWRITE_MESSAGE||]})';
		
		//get universal unique ID
		$uniqueFound = FALSE;
		$count = 0;
		$this->setKey(uuid::get());
		while($uniqueFound === FALSE || $count < 1000) {
			if(!is_file(WORKSPACE_DIRECTORY.DS.$this->getKey())) {
				$uniqueFound = TRUE;
				$result = str_replace('||KVWRITE_KEY||',$this->getKey(), $result);
			} else {
				$this->setKey(uuid::get());
			}
			$count++;
		}
		if($count >= 1000) {
			$this->addMessage('Could not create key. Exceeded number of tries.');
		}
		
		//Check that writing is possible
		if($uniqueFound === TRUE && $this->actionValid()) {
			$fh = @fopen(WORKSPACE_DIRECTORY.DS.$this->getKey(),'w');
			if(!$fh) {
				$this->addMessage("Could not create value for key ('{$this->getKey()}').");
		
				$result = str_replace('||KVWRITE_SUCCESS||','false', $result);
				$result = str_replace('||KVWRITE_MESSAGE||',implode(",", $this->getMessages()), $result);
				echo $result;
		
				return FALSE;
			} else {
				fwrite($fh,$this->getWriteValue());
				$this->addMessage("Successfully created key value pair.");
				fclose($fh);
		
				$result = str_replace('||KVWRITE_SUCCESS||','true', $result);
				$result = str_replace('||KVWRITE_MESSAGE||',implode(",", $this->getMessages()), $result);
				echo $result;
		
				return TRUE;
			}
		}
		$result = str_replace('||KVWRITE_SUCCESS||','false', $result);
		$result = str_replace('||KVWRITE_KEY||','', $result);
		$result = str_replace('||KVWRITE_MESSAGE||',implode(",", $this->getMessages()), $result);
		echo $result;
		
		return FALSE;
	}
	
	public function kvupdate($key='', $content='', $contentType=DEFAULT_CONTENT_TYPE) {
		$keyOK = TRUE;
		
		//Set Page Header Content Type
		$this->setHtmlHeader($contentType);
		
		//create JSON framework
		$result = $this->getCallback().'({"success":||KVUPDATE_SUCCESS||, "key":"||KVUPDATE_KEY||", "messages": [||KVUPDATE_MESSAGE||]})';

		//check supplied key
		if(strlen($key) > 0) { $this->setKey($key); }
		if(strlen($this->getKey()) < 1) {
			$this->addMessage('You have not supplied any key.');
			$keyOK = FALSE;
		}
		//check key exists
		if(!is_dir(WORKSPACE_DIRECTORY.DS.$this->getKey())) {
			$this->addMessage('The supplied key does not exist.');
			$keyOK = FALSE;
		}
		$result = str_replace('||KVUPDATE_KEY||', $this->getKey(), $result);
		
		//Check that writing is possible
		if($keyOK === TRUE && $this->actionValid()) {
			$fh = @fopen(WORKSPACE_DIRECTORY.DS.$this->getKey(),'w');
			if(!$fh) {
				$this->addMessage("Could not store value for key ('{$this->getKey()}').");
		
				$messages = $this->getMessages();
				array_map($this->jsStrPad, $messages);
				$result = str_replace('||KVUPDATE_SUCCESS||','false', $result);
				$result = str_replace('||KVUPDATE_MESSAGE||',implode(",", $messages), $result);
				echo $result;
		
				return FALSE;
			} else {
				fwrite($fh,$this->getWriteValue());
				$this->addMessage("Successfully updated key value pair.");
				fclose($fh);
		
				$messages = $this->getMessages();
				array_map($this->jsStrPad, $messages);
				$result = str_replace('||KVUPDATE_SUCCESS||','true', $result);
				$result = str_replace('||KVUPDATE_MESSAGE||',implode(",", $messages), $result);
				echo $result;
		
				return TRUE;
			}
		}
		$messages = $this->getMessages();
		array_map($this->jsStrPad, $messages);
		$result = str_replace('||KVUPDATE_SUCCESS||','false', $result);
		$result = str_replace('||KVUPDATE_MESSAGE||',implode(",", $messages), $result);
		echo $result;
		
		return FALSE;
	}
	
	/*
	 * actionValid
	 * 
	 * Desc: Checks the action to see if it is correct
	 */
	public function actionValid() {
		$actions = array('read', 'write', 'kvread', 'kvwrite', 'kvupdate');
		$my_action = strtolower($this->webrw_action);
		
		if(in_array($my_action, $actions)) {
			$this->webrw_action_flag = TRUE;
		} else {
			$this->webrw_action_flag = FALSE;
		}
		return $this->webrw_action_flag;
	}
	
	/**************************************************************************
	 *************************** Private functions ****************************
	 **************************************************************************
	 */
	
	/*
	 * readValid
	 * 
	 * Desc: Checks to see if the "read" action is possible
	 */
	private function readValid() {
		//All we are doing is checking the length to see it does not have a zero-length which indicates that some URL has been entered
		//Thought about do REGEX but the user can enter relative URLs, so no need.
		if(strlen($this->getUrl()) > 0) {
			$this->webrw_read_flag = TRUE;
		} else {
			$this->webrw_read_flag = FALSE;
			$this->addMessage('Please specify a valid URL.');
		}
		
		//set callback if it is not specified
		if(strlen($this->getCallback()) < 1) {
			$this->setCallback(DEFAULT_CALLBACK);
		}
		
		return $this->webrw_read_flag;
	}

	/*
	 * writeValid
	 * 
	 * Desc: Checks to see if the "write" action is possible
	 */
	private function writeValid() {
		//All we are doing is checking the length to see it does not have a zero-length which indicates that some URL has been entered
		//Thought about do REGEX but the user can enter relative URLs, so no need.
		if(strlen($this->getFileName()) > 0) {
			$tmp_name = str_replace(" ","",$this->getFileName());	//remove spaces
			$tmp_name = str_replace("/","",$tmp_name);				//remove forward slashes "/"
			$tmp_name = str_replace("\\","",$tmp_name);				//remove back slashes "\"

			if(strlen(trim($tmp_name)) > 0) {
				$this->setFileName($tmp_name);
				$this->webrw_write_flag = TRUE;
			} else {
				$this->webrw_write_flag = FALSE;
				$this->addMessage('Please specify a valid file name.');
			}
		} else {
			$this->webrw_write_flag = FALSE;
			$this->addMessage('Please specify a valid file name.');
		}
		
		//write mode
		if(strlen($this->getWriteMode()) == 1) {
			$file_modes = array('a','w');
			if(!in_array($this->getWriteMode(), $file_modes)) {
				$this->webrw_write_flag = FALSE;
				$this->addMessage("Please specify a valid write mode ('a' or 'w').");
			}
		} else {
			$this->webrw_write_flag = FALSE;
			$this->addMessage("Please specify a valid write mode ('a' or 'w').");
		}
		
		//set callback if it is not specified
		if(strlen($this->getCallback()) < 1) {
			$this->setCallback(DEFAULT_CALLBACK);
		}
		
		return $this->webrw_write_flag;
	}

	/*
	 * setHtmlHeader
	 * Arg:  $str - Document Header Content Type e.g. "text/Javascript"
	 * 
	 * Desc: Sets the content type for the resulting page. Remember to call this first before any other content is written to the page.
	 * 
	 */
	private function setHtmlHeader($str='') {
		$str = trim($str);
		if(strlen($str) > 0) {
			header('Content-type: '.$str);
		} else {
			header('Content-type: text/javascript');
		}
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	}
}
?>