<?php 
	/*
	 * General functions for webrw
	 * 
	 * This file contains general functions used with webrw. It contains the functions for garbage collection.
	 * 
	 * @name: inc.general.php
	 * @author: Bade Iriabho <ebade@yahoo.com>
	 * @copyright: 2011-12 Bade Iriabho
	 * @license: Free to use, just remember the first law of sharing "Give credit where it is due". Author is not liable for any damages that results from using this code.
	 * @version: See VERSION
	 * @requires: inc.config.php
	 * 
	 */


	/*
	 * @function: cleanWorkspace
	 * @description: Function called to clean the workspace folder
	 * @arguments: None
	 * 
	 */
	function cleanWorkspace() {
		$workspace_path = WORKSPACE_DIRECTORY;
		$workspace_exclude = explode("|", WORKSPACE_EXCLUDE);
		$current_time = time();
		
		$handle = opendir($workspace_path);
		while($tmp=readdir($handle)) {
			if($tmp!='..' && $tmp!='.' && $tmp!='' && !in_array($tmp, $workspace_exclude)) {
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
	 * @function: getStatTime
	 * @description: Using PHP stat function, this function returns the unix timestamp values
	 * @arguments: $arg($statArr) - 1(mtime) last modified unixtime
	 * 								2(ctime) created unixtime
	 * 								3(atime) last accessed unixtime
	 * 
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
	 * @function: timeDifference
	 * @description: Takes unix timestamp integer values for $start and $end and computes the time difference between both
	 * @arguments:	$start - Start Time
	 * 				$end   - End Time
	 * 				$mode  - Mode to return answer (h-hours, m-minutes, s-seconds)
	 * 
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
	 * @function: deleteFile
	 * @description: Deletes a specific file
	 * @arguments: $tmp_path - path to file on the server meant for deletion
	 * 
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
	 * @function: deleteFolder
	 * @description: Deletes a specific folder or directory
	 * @arguments: $tmp_path - path to folder on the server meant for deletion
	 * 
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