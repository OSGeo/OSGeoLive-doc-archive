<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_RPCEndpoint.php");

/**
 * A Mapbender user as described in the table mb_user.
 */
class ElementVar implements RPCObject{
	/**
	 * @var Integer The User ID
	 */
	var $applicationId;
	var $elementId;
	var $name;  
	var $value;
	var $context;
	var $type;
	
	/**
	 * Constructor
	 * @param $application String 	name of the application
	 * @param $element String		name of the element
	 * @param $name String			name of the element var
	 */
	public function __construct () {
		if (func_num_args() === 3) {
			try {
				$this->applicationId = func_get_arg(0);
				$this->elementId = func_get_arg(1);
				$this->name = func_get_arg(2);
				$this->load();
			}
			catch (Exception $e) {
				throw new Exception ("Could not initialize element var, because: " . $e);
			}
		}
	}	

	
	/**
	 * @return String the name of this element var
	 */
	public function __toString () {
		return (string) $this->name;	
	}


    /*
    * @return Assoc Array containing the fields to send to the user
    */
    public function getFields() {
        $result = array(
        );
		return $result;
	}

	public function create() {
	}


	/*
	*	@param	$changes JSON  keys and their values of what to change in the object
	*/
	public function change($changes) {
	}

	public function commit() {
	}

	public function remove() {
	}
	
	public function toSql () {
		$insert .= "INSERT INTO gui_element_vars(fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type";
		$insert .= ") VALUES (";
		$insert .= "'" . $this->applicationId . "',";
		$insert .= "'" . $this->elementId . "',";
		$insert .= "'" . $this->name . "',";
		$insert .= "'" . db_escape_string($this->value) . "',";
		$insert .= "'" . db_escape_string($this->context) . "',";
		$insert .= "'" . $this->type . "'";
		$insert.=");\n";
		return $insert;
	}

	public function load() {
		$sql = "SELECT * from gui_element_vars WHERE fkey_gui_id = $1 AND fkey_e_id = $2 AND var_name = $3; ";
		$v = array($this->applicationId, $this->elementId, $this->name);
		$t = array("s", "s", "s");
		$res = db_prep_query($sql, $v, $t);

		if ($row = db_fetch_array($res)) {
			$this->value = $row["var_value"];
			$this->context = $row["context"];
			$this->type = $row["var_type"];
		}
		else {
			 throw new Exception("no such element var");
		}
	}

	public static function getList($filter) {
      return array();
    }

    public static function byName($name) {
      return null;
    }
	
	public function isValid () {
		return true;
	}
}
?>
