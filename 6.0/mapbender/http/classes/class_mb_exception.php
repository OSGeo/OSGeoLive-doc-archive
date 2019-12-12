<?php

# $Id: class_mb_exception.php 8077 2011-08-28 14:35:04Z astrid_emde $
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

require_once(dirname(__FILE__)."/../classes/class_mb_log.php");

/**
 * Logs messages to a specified file, for example "../log/mb_error_log_<date>.log". Define LOG_DIR in mapbender.conf
 * 
 * @package exceptionHandling
 */
class mb_exception extends mb_log {

	/**
	 * @param	string $message		message that is being logged
	 */
	public function __construct ($message) {
		if ($message == Exception) {
			return $this->mb_log("ERROR: " . $e->getMessage(), $this->level);
		}
		return $this->mb_log("ERROR: " . $message, $this->level);
	}

	/**
	 * @var string a description of the log level
	 */
	private $level = "error";
}
?>
