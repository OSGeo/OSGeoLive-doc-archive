<?php

# $Id: class_mb_notice.php 1950 2008-01-04 16:33:40Z christoph $
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
 * @package exceptionHandling
 */
class mb_notice extends mb_log {

	/**
	 * @param	string $message		message that is being logged
	 */
	public function __construct ($message) {
		return $this->mb_log("Notice: " . $message, $this->level);
	}

	/**
	 * @var string a description of the log level
	 */
	private $level = "notice";
}
?>