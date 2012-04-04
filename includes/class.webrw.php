<?php 
/*
* webrw class file
*
* webrw is a class that houses two main functions "get" and "set", the get serves 
* as a proxy that allows a user to remotely get content fron any valid URL. This 
* can be used to circumvent the same domain origin policy issues usually encountered 
* with some web applications. With set, the user can temporarily save information 
* for later retreival. There is a delete function but it is setup to work internally 
* and will be invisible to the user. With a successful set operation the user gets a 
* key (a UUID) that allows for later retreival of the saved data. See README.md 
* for usage examples.
*
* @name: class.webrw.php
* @author: Bade Iriabho <ebade@yahoo.com>
* @copyright: 2011-12 Bade Iriabho
* @license: Free to use, just remember the first law of sharing "Give credit where it is due". Author is not liable for any damages that results from using this code.
* @version: 1.3.0. Also see VERSION
* @requires: inc.config.php, class.uuid.php, inc.general.php
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
	private $webrw_url;					//Resource URL
	private $webrw_value;				//Value to be written
	private $webrw_key;					//Key-value key string
	private $webrw_callback;			//Callback function. Defaults to DEFAULT_CALLBACK if nothing is specified.
	private $webrw_onload;				//onload function to be added to read calls
	private $webrw_messages;
	
	/*
	 * Public Set/Add Functions
	 */
	public function setUrl($url='') { $this->webrw_url = trim($url); }
	public function setValue($str='') { $this->webrw_value = trim($str); }
	public function setKey($str='') { $this->webrw_key = str_replace('-', '', trim($str)); }
	public function setCallback($str='') { 
		$str = trim($str);
		if(strlen($str) > 0) {
			$this->webrw_callback = trim($str);
		} else {
			$this->webrw_callback = "callback";
		}
	}
	public function setOnload($str='') {
		$str = trim($str);
		$this->webrw_onload = ';';
		if(strlen($str) > 0) {
			$this->webrw_onload .= $str.';';
		}
	}
	
	/*
	 * Public Get Functions
	 */
	public function getUrl() { return $this->webrw_url; }
	public function getValue() { return $this->webrw_value; }
	public function getKey() { return $this->webrw_key; }
	public function getCallback() { return $this->webrw_callback; }
	public function getOnload() { return $this->webrw_onload; }
	public function getMessages() { return $this->webrw_messages; }
	
	/*
	 * Other functions
	 */
	private function addMessage($str='') {
		if(strlen(trim($str)) > 0) {
			array_push($this->webrw_messages, '"'.$str.'"');
		}
	}
	public function clearMessages() { $this->webrw_messages = array(); }
	
	
	/*
	 * Constructor Function
	 */
	public function __construct() {
		//get
		if(isset($_GET['get'])) { $this->setUrl(trim($_GET['get'])); } 
		elseif(isset($_POST['get'])) { $this->setUrl(trim($_POST['get'])); }
		else { $this->setUrl(''); }
		if($this->isDoc()) {
			if(isset($_GET['doc'])) { $this->setUrl(trim($_GET['doc'])); } 
			elseif(isset($_POST['doc'])) { $this->setUrl(trim($_POST['doc'])); }
		}
		
		//set
		if(isset($_GET['set'])) { $this->setValue(trim($_GET['set'])); }
		elseif(isset($_POST['set'])) { $this->setValue(trim($_POST['set'])); }
		else { $this->setValue(''); }
		
		//key
		if(isset($_GET['key'])) { $this->setKey(trim($_GET['key'])); }
		elseif(isset($_POST['key'])) { $this->setKey(trim($_POST['key'])); }
		else { $this->setKey(''); }

		//callback
		if(isset($_GET['callback'])) { $this->setCallback(trim($_GET['callback'])); } 
		elseif(isset($_POST['callback'])) { $this->setCallback(trim($_POST['callback'])); }
		else { $this->setCallback(DEFAULT_CALLBACK); }

		//onload
		if(isset($_GET['onload'])) { $this->setOnload(trim($_GET['onload'])); }
		elseif(isset($_POST['onload'])) { $this->setOnload(trim($_POST['onload'])); }
		else { $this->setOnload(''); }

		$this->webrw_messages = array();
		
		//Garbage Collection
		cleanWorkspace();
	}
	
	/*
	 *  Destructor Function
	 */
	public function __destruct() {
		$this->webrw_url = null;
		$this->webrw_value = null;
		$this->webrw_key = null;
		$this->webrw_callback = null;
		$this->webrw_onload = null;
		$this->webrw_messages = null;
	}
	
	/*
	 * @function: get
	 * @arguments:	$arg - URL for resource
	 * 				$dispRes - Echo the results
	 *  			$contentType - Content Type to use if display results is true
	 */
	public function get($arg='', $dispRes=TRUE, $contentType=DEFAULT_CONTENT_TYPE) {
		$readOK = TRUE;
		$localFlag = FALSE;
		$filePath = '';
		$final_rtn = '';
		$arg = trim($arg);
		
		//Set Page Header Content Type
		if($dispRes === TRUE && !$this->isDoc()) {
			$this->setHtmlHeader($contentType);
		}
		
		//create JSON framework
		$result1 = $this->getCallback().'(||GET_CONTENT||)';
		$result2 = $this->getCallback().'({"content":||GET_CONTENT||, "date":"'.date('M-d-Y H:i:s').'", "success":false, "messages": [||GET_MESSAGE||]})||GET_ONLOAD||';

		$result2 = str_replace('||GET_ONLOAD||', $this->getOnload(), $result2);
		
		//analyse arguments
		if(strlen($arg) > 0) { $this->setUrl($arg); }
		if(strlen($this->getUrl()) < 1) {
			$readOK = FALSE;
			$this->addMessage('You did not supply any agruments for "get".');
		} elseif($this->keyValid($this->getUrl())) {
			$localFlag = TRUE;
		}

		if($readOK === TRUE) {
			if($localFlag === TRUE) {
				$dir_host = $_SERVER['HTTP_HOST'];
				
				$dir_path_arr = explode('/', $_SERVER['SCRIPT_NAME']);
				$count = count($dir_path_arr);
				unset($dir_path_arr[$count-1]);
				$dir_path = implode('/', $dir_path_arr);
				$dir_path = (strlen(trim($dir_path)) > 1)?$dir_path:'';
								
				$dir_prot = (isset($_SERVER['HTTPS']))?"https://":"http://";
				$filePath = $dir_prot.$dir_host.$dir_path.DS.WORKSPACE_DIRECTORY.DS.$this->getUrl();
				if(!is_file(WORKSPACE_DIRECTORY.DS.$this->getUrl())) {
					$this->addMessage('The specified key does not exist.');
					$readOK = FALSE;
				}
			} else {
				$filePath = $this->getUrl();
			}

			$crl = curl_init();
			curl_setopt($crl, CURLOPT_URL, $filePath);
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($crl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, 5);
			$rtn = curl_exec($crl);
			curl_close($crl);
		
			if($this->isDoc()) {
				echo $rtn;
			} else {
				$search = array("\r\n", "\n\r", "\n", "\r");
				$rtn = str_replace($search, '||GET_DIV||', $rtn);
		
				$arr_rtn = explode('||GET_DIV||', $rtn);
				$counter = 0;
				foreach($arr_rtn as $val) {
					$final_rtn .= (($counter > 0)?",":"").json_encode($val);
					$counter++;
				}
				$result1 = str_replace('||GET_CONTENT||', "[".$final_rtn."]", $result1);
				if($dispRes === TRUE) {
					echo $result1;
				}
			}
			return TRUE;
		}
		if($this->isDoc()) {
			if($dispRes === TRUE) {
				echo implode("<br />", $this->getMessages());
			}
		} else {
			$result2 = str_replace('||GET_MESSAGE||', implode(",", $this->getMessages()), $result2);
			$result2 = str_replace('||GET_CONTENT||', "[".$final_rtn."]", $result2);
			if($dispRes === TRUE) {
				echo $result2;
			}
		}
		return FALSE;
	}
	
	/*
	 * @function: set
	 * @arguments: 	$val - Value to be written to file
	 *  			$key - Key to append values to
	 *
	 */
	public function set($val='', $key='') {
		$uniqueFound = FALSE;
		$keyExists = FALSE;
		
		//Set Page Header Content Type
		$this->setHtmlHeader(DEFAULT_CONTENT_TYPE);
		
		//create JSON framework
		$result1 = $this->getCallback().'("||SET_KEY||")';
		$result2 = $this->getCallback().'({"success":||SET_SUCCESS||, "key":"||SET_KEY||", "messages": [||SET_MESSAGE||]})';

		if(strlen(trim($val)) > 0) { $this->setValue($val); }
		if(strlen(trim($key)) > 0) { $this->setKey($key); }
		
		if(strlen($this->getKey()) < 1) {
			//get universal unique ID
			$count = 0;
			$this->setKey(uuid::get());
			while($uniqueFound === FALSE && $count < 1000) {
				if(!is_file(WORKSPACE_DIRECTORY.DS.$this->getKey())) {
					$uniqueFound = TRUE;
					$result1 = str_replace('||SET_KEY||',$this->getKey(), $result1);
					$result2 = str_replace('||SET_KEY||',$this->getKey(), $result2);
				} else {
					$this->setKey(uuid::get());
				}
				$count++;
			}
			if($count >= 1000) {
				$this->addMessage('Could not create key. Exceeded number of tries.');
			}
		} else {
			$result1 = str_replace('||SET_KEY||',$this->getKey(), $result1);
			$result2 = str_replace('||SET_KEY||',$this->getKey(), $result2);
			if(is_file(WORKSPACE_DIRECTORY.DS.$this->getKey())) {
				$keyExists = TRUE;
			} else {
				$this->addMessage("Supplied key does not exist.");
			}
		}
				
		//Check that writing is possible
		if($uniqueFound === TRUE || $keyExists === TRUE) {
			$fh = @fopen(WORKSPACE_DIRECTORY.DS.$this->getKey(),'a');
			if(!$fh) {
				$this->addMessage("Could not create value for key ('{$this->getKey()}').");
		
				$result2 = str_replace('||SET_SUCCESS||','false', $result2);
				$result2 = str_replace('||SET_MESSAGE||',implode(",", $this->getMessages()), $result2);
				echo $result2;
		
				return FALSE;
			} else {
				fwrite($fh,$this->getValue()."\r\n");
				$this->addMessage("Successfully created key value pair.");
				fclose($fh);
				echo $result1;
		
				return TRUE;
			}
		}
		$result2 = str_replace('||SET_SUCCESS||','false', $result2);
		$result2 = str_replace('||SET_KEY||','', $result2);
		$result2 = str_replace('||SET_MESSAGE||',implode(",", $this->getMessages()), $result2);
		echo $result2;
		
		return FALSE;
	}
	
	/**************************************************************************
	 *************************** Private functions ****************************
	 **************************************************************************
	 */
	
	/*
	 * @function: keyValid
	 * @type: private
	 * @desciption: Determines if a supplied key is valid.
	 * @argumments: $key - UUID key
	 * 
	 */
	private function keyValid($key='') {
		if (!preg_match("/^[:a-zA-Z0-9]+$/",$key)) {
			return FALSE;
		} else {
			return TRUE;
		}		
	}
	
	/*
	 * @function: isDoc
	 * @type: private
	 * @desciption: Determines if the user is using the doc query request versus the get.
	 * @argumments: None
	 * 
	 */
	private function isDoc() {
		$get = FALSE;
		$doc = FALSE;
		
		if(isset($_GET['get']) || isset($_POST['get'])) { $get = TRUE; }
		if(isset($_GET['doc']) || isset($_POST['doc'])) { $doc = TRUE; }
		
		if($get === TRUE) {
			return FALSE;
		} else {
			return $doc;
		}
	}
	
	/*
	 * @function: urlValid
	 * @type: private
	 * @desciption: Determines if a supplied URL is valid.
	 * @argumments: $url - URL string
	 * 
	 */
	private function urlValid($url='') {
		if (!preg_match("/^([a-zA-Z][a-zA-Z0-9\+\-\.]*:((((\/\/((((([a-zA-Z0-9\-_\.!\~\*'\(\);:\&=\+$,]|(%[a-fA-F0-9]{2}))*)\@)?((((([a-zA-Z0-9](([a-zA-Z0-9\-])*[a-zA-Z0-9])?)\.)*([a-zA-Z](([a-zA-Z0-9\-])*[a-zA-Z0-9])?)(\.)?)|([0-9]+((\.[0-9]+){3})))(:[0-9]*)?))?|([a-zA-Z0-9\-_\.!\~\*'\(\)$,;:\@\&=\+]|(%[a-fA-F0-9]{2}))+)(\/(([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*(;([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*)*)(\/(([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*(;([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*)*))*)?)|(\/(([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*(;([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*)*)(\/(([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*(;([a-zA-Z0-9\-_\.!\~\*'\(\):\@\&=\+$,]|(%[a-fA-F0-9]{2}))*)*))*))(\?([a-zA-Z0-9\-_\.!\~\*'\(\);/\?:\@\&=\+$,]|(%[a-fA-F0-9]{2}))*)?)|(([a-zA-Z0-9\-_\.!\~\*'\(\);\?:\@\&=\+$,]|(%[a-fA-F0-9]{2}))([a-zA-Z0-9\-_\.!\~\*'\(\);/\?:\@\&=\+$,]|(%[a-fA-F0-9]{2}))*)))?(\#([a-zA-Z0-9\-_\.!\~\*'\(\);/\?:\@\&=\+$,]|(%[a-fA-F0-9]{2}))*)?$/i",$url)) {
			return FALSE;
		} else {
			return TRUE;
		}		
	}
	
	/*
	 * @function: setHtmlHeader
	 * @type: private
	 * @desciption: Sets the content type for the resulting page. Remember to call this 
	 * 				first before any other content is written to the page.
	 * @argumments: $str - Document Header Content Type e.g. "text/Javascript"
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