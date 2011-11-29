<?php
	/*
	 * Name: inc.general.php
	 * Type: Config File
	 * Written by: Bade Iriabho (c) 2011
	 *
	 * Description:
	 * Hold the general functions.
	 *
	 */

	/*
	 * cleanWorkspace
	 * 
	 * Desc: function called to clean the workspace folder
	 */
	function cleanWorkspace() {
		$workspace_path = WORKSPACE_DIRECTORY;
		$current_time = time();
		
		$handle = opendir($workspace_path);
		while($tmp=readdir($handle)) {
			if($tmp!='..' && $tmp!='.' && $tmp!='') {
				if(is_file($workspace_path.DS.$tmp)) {
					$fileStat = stat($workspace_path.DS.$tmp);
					$fileinfo = timeDifference(getStatTime($fileStat), $current_time);
					if(intval($fileinfo['value']) >= WORKSPACE_DELETE_AGE) {
						deleteFile($workspace_path.DS.$tmp);
					}
				} elseif(is_dir($workspace_path.DS.$tmp)) {
					$fileStat = stat($workspace_path.DS.$tmp);
					$fileinfo = timeDifference(getStatTime($fileStat), $current_time);
					if(intval($fileinfo['value']) >= WORKSPACE_DELETE_AGE) {
						deleteFolder($workspace_path.DS.$tmp);
					}
				}
			}
		}
		closedir($handle);
	}
	
	/*
	 * getStatTime
	 * 
	 * Desc: using PHP stat function, this function returns the unix timestamp values
	 * 
	 * Arguments:
	 * 		$statArr -  1 (mtime) last modified unixtime
	 * 					2 (ctime) created unixtime
	 * 					3 (atime) last accessed unixtime
	 */
	function getStatTime($statArr='', $arg=1) {
		$arg = intval($arg);
		if(!is_array($statArr)) {
			return false;
		}
		
		switch($arg) {
			case 2:
				if(isset($statArr['ctime'])) {
					return $statArr['ctime'];
				} elseif (isset($statArr[10])) {
					return $statArr[10];
				} else {
					return false;
				}
				break;
			case 3:
				if(isset($statArr['atime'])) {
					return $statArr['atime'];
				} elseif (isset($statArr[8])) {
					return $statArr[8];
				} else {
					return false;
				}
				break;
			case 1:
			default:
				if(isset($statArr['mtime'])) {
					return $statArr['mtime'];
				} elseif (isset($statArr[9])) {
					return $statArr[9];
				} else {
					return false;
				}
				break;
		}
	}
	
	/*
	 * timeDifference
	 * 
	 * Desc: takes unix timestamp integer values for $start and $end and computes the time difference between both
	 */
	function timeDifference($start=0, $end=0, $mode='s') {
		$negative = false;
		$start = intval($start);
		$end = intval($end);
		$mode = strtolower(trim($mode));
		
		if($start < 1) { $start = time(); }
		if($end < 1) { $end = time(); }
		
		if($end < $start) { $negative = true; }
		$diff = abs($end - $start);
		
		$ans = array('value' => 0, 'unit' => 'second', 'negative' => $negative);
		
		switch($mode) {
			case 'm':					//minutes
				$tmp = (int) $diff/60;
				$tmp2 = sprintf('%.4f', ((float) ($diff % 60))/60.000);
				$ans['value'] = sprintf('%d%s', $tmp, substr($tmp2,1));
				$ans['unit'] = ($diff > 60)?'minutes':'minute';
				break;
			case 'h':					//hours
				$tmp = (int) $diff/3600;
				$tmp2 = sprintf('%.4f', ((float) ($diff % 3600))/3600.000);
				$ans['value'] = sprintf('%d%s', $tmp, substr($tmp2,1));
				$ans['unit'] = ($diff > 3600)?'hours':'hour';
				break;
			case 's':					//seconds
			default:					//seconds
				$ans['value'] = $diff;
				if($diff > 1) { $ans['unit'] = 'seconds'; }
				break;
		}
		return $ans;
	}
	
	/*
	 * deleteFile
	 * 
	 * Desc: Deletes a specific file
	 */
	function deleteFile($tmp_path='') {
		if(is_writeable($tmp_path) && is_file($tmp_path)) {
			unlink($tmp_path);
		} elseif(!is_writeable($tmp_path) && is_file($tmp_path)) {
			chmod($tmp_path,0666);
			unlink($tmp_path);
		}
		
		if(!is_file($tmp_path)) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * deleteFolder
	 * 
	 * Desc: Deletes a specific folder or directory
	 */
	function deleteFolder($tmp_path='') {
		if(!is_writeable($tmp_path) && is_dir($tmp_path)) {
			chmod($tmp_path,0777);
		}
		$handle = opendir($tmp_path);
		while($tmp=readdir($handle)) {
			if($tmp!='..' && $tmp!='.' && $tmp!='') {
				if(is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)) {
					unlink($tmp_path.DS.$tmp);
				} elseif(!is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)) {
					chmod($tmp_path.DS.$tmp,0666);
					unlink($tmp_path.DS.$tmp);
				}
				 
				if(is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)) {
					deleteFolder($tmp_path.DS.$tmp);
				} elseif(!is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)) {
					chmod($tmp_path.DS.$tmp,0777);
					deleteFolder($tmp_path.DS.$tmp);
				}
			}
		}
		closedir($handle);
		rmdir($tmp_path);
		if(!is_dir($tmp_path)) {
			return true;
		} else {
			return false;
		}
	}
?>