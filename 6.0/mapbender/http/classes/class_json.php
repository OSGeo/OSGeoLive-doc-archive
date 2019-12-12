<?php
# $Id:class_json.php 2406 2008-04-23 15:59:31Z christoph $
# http://www.mapbender.org/index.php/JSON
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

require_once(dirname(__FILE__)."/../../core/system.php");

define("JSON_PEAR", "json_pear");
define("JSON_NATIVE", "json_native");

if (!function_exists("json_encode")) {
	require_once(dirname(__FILE__)."/../extensions/JSON.php");
}

/**
 * A wrapper class for PHP JSON encoding/decoding.
 * Uses native PHP functions if available.
 * 
 * @class
 */
class Mapbender_JSON {
	
	/**
	 * Either JSON_PEAR or JSON_NATIVE
	 */
	private $library = JSON_PEAR;

	/**
	 * Check whether Mapbender will use the native PHP JSON functions
	 * or the PEAR library
	 * 
	 * @return Boolean 
	 */
	public static function usesNative () {
		return function_exists("json_encode");
	}
	
	/**
	 * Determines which JSON lib to use.
	 * @constructor
	 */
	public function __construct(){
		if (function_exists("json_encode")) {
			$this->library = JSON_NATIVE;
		}
	}
	
        /**
        * Converts incoming object to UTF-8 for json_encode
        *
        */

        private function json_fix_charset($var)
        {
            if (is_array($var)) {
                $new = array();
                foreach ($var as $k => $v) {
                    $new[$this->json_fix_charset($k)] = $this->json_fix_charset($v);
                }
                $var = $new;
            } elseif (is_object($var)) {
                $vars = get_object_vars(get_class($var));
                foreach ($vars as $m => $v) {
                    $var->$m = $this->json_fix_charset($v);
                }
            } elseif (is_string($var)) {
				if(mb_detect_encoding($var) != "UTF-8"){
                	$var = utf8_encode($var);
				}
            }
            return $var;
        }

        /**
        * savely encode object to JSON,
        * converting to UTF-8
        */
        public function save_encode($anObject){
                $e = new mb_notice("converting to UTF-8 before using native JSON");
                return $this->encode($this->json_fix_charset($anObject));
        }

	/**
	 * Encodes an object to JSON
	 */
	public function encode($anObject) {
		if ($this->library == JSON_PEAR) {
			$pear = new Services_JSON();
			$e = new mb_notice("using PEAR JSON");
			return $pear->encode($anObject);
		}
		$e = new mb_notice("using native JSON");
		return json_encode($anObject);
	}

	/**
	 * Decodes a JSON string
	 */
	public function decode($aString) {
		if ($this->library == JSON_PEAR) {
			$pear = new Services_JSON();
			$e = new mb_notice("using PEAR JSON");
			return $pear->decode($aString);
		}
		$e = new mb_notice("using native JSON");
		return json_decode($aString);
	}
}
?>
