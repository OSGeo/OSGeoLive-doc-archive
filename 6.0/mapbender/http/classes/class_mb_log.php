<?php

# $Id: class_mb_log.php 7134 2010-11-16 10:06:01Z christoph $
# http://www.mapbender.org/index.php/class_mb_exception.php
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require_once dirname(__FILE__) . "/../../conf/mapbender.conf";
require_once(dirname(__FILE__)."/../classes/class_mb_notice.php");
require_once(dirname(__FILE__)."/../classes/class_mb_warning.php");
require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");

/**
 * @package exceptionHandling
 */
abstract class mb_log {

	/**
	 * Appends a message to a given log file.
	 * 
	 * @param	string $n		the message that is being logged.
	 * @param	string $level	the log level of the message.
	 * @return	bool			true if the logging succeded; else false.
	 */
	protected function mb_log ($n, $level) {
		if (!isset($this->mb_log_level)) {
			$n = "class_mb_exception: please set LOG_LEVEL in mapbender.conf" . $n;
		}
		if ($this->isValidLevel($level)) {
			if (defined("LOG_PHP_WITH_FIREPHP") && LOG_PHP_WITH_FIREPHP === "on") {
				$firephp = FirePHP::getInstance(true);
				switch ($level) {
					case "error":
						$firephp->error($n);
						break;
					case "warning":
						$firephp->warn($n);
						break;
					case "notice":
						$firephp->log($n);
						break;
				}
			}
			if (is_dir($this->dir)) {
				$logfile = $this->dir . $this->filename_prefix . date("Y_m_d") . ".log";
				if ($h = fopen($logfile,"a")) {
					$content = date("Y.m.d, H:i:s") . "," . $n .chr(13).chr(10);
					if(!fwrite($h,$content)){
						$this->result = false;
						$this->message = "Unable to write " . $logfile;
						return false;
					}
					fclose($h);
					$this->result = true;
					$this->message = "Successful.";
					return true;
				}
				else {
					$this->result = false;
					$this->message = "Unable to open or generate " . $logfile;
					return false;
				}
			}
			else {
				$this->result = false;
				$this->message = "Directory " . $this->dir . " is not valid.";
				return false;
			}
		}
		else {
			$this->result = false;
			$this->message = "Log level '" . $level . "' is not valid or logging is disabled in mapbender.conf.";
			return false; 
		}
	}

	/**
	 * Retrieves the index of the level in the array of available levels.
	 * By this, we can find out if the message is eligable for logging.
	 * 
	 * @param	string $level			the log level of the message
	 * @param	string[] $levelArray	an array of available levels
	 * @return	mixed					false, if the level is not available; else 
	 * 									the index of the level in the log level array
	 */
	protected function indexOf ($level, $levelArray) {
		$index = false;
		for ($i=0; $i < count($levelArray); $i++) {
			if ($levelArray[$i] == $level) {
				$index = $i;
			}
		}
		return $index;
	}

	/**
	 * Checks if the message will be logged. Example: Log level of the message is "warning",
	 * but the log level set in 
	 * {@link http://www.mapbender.org/index.php/Mapbender.conf#Mapbender_error_logging mapbender.conf}
	 * is "off", then the message will not be logged.
	 * 
	 * @param	string $level	the log level of the message that is being logged.
	 * @return	bool			true if the message will be logged; else false.
	 */
	protected function isValidLevel ($level) {
		$log_level_array = explode(",", $this->log_levels);
		$isValid = in_array($level, $log_level_array);
		$isAppropriate = ($this->indexOf($level, $log_level_array) <= $this->indexOf($this->mb_log_level, $log_level_array));
		return $isValid && $isAppropriate;
	}
	
	/**
	 * @var	string	a comma-separated list of available log levels, see 
	 * 				{@link http://www.mapbender.org/index.php/Mapbender.conf#Mapbender_error_logging mapbender.conf}.
	 */
	protected $log_levels = LOG_LEVEL_LIST;

	/**
	 * @var	string	the selected log level, see 
	 * 				{@link http://www.mapbender.org/index.php/Mapbender.conf#Mapbender_error_logging mapbender.conf}.
	 */
	protected $mb_log_level = LOG_LEVEL;	

	/**
	 * @var	string	the path to the log directory
	 */
	protected $dir = LOG_DIR;

	/**
	 * @var	string	the prefix of the logs' file name
	 */
	protected $filename_prefix = "mb_error_";

	/**
	 * @var	bool	true if the logging succeeded; else false.
	 */
	public $result = false;

	/**
	 * @var	string	if the logging did not succeed, this contains an error message.
	 */
	public $message = "";
}
?>