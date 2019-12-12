<?php
# $Id: class_gui.php 7969 2011-07-19 13:02:47Z astrid_emde $
# http://www.mapbender.org/index.php/class_gui.php
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

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_element.php");
require_once(dirname(__FILE__)."/../classes/class_RPCEndpoint.php");

/**
 * GUI is a set of GUI elements and services. 
 */
class gui implements RPCObject{

	var $id;
    var $name = "";
    var $description = "";
    var $public = 1;
	var $elementArray = array();
    
    static $displayName = "Gui";
    static $internalName = "gui";
	
	public function __construct ($guiId) {
        $this->id = $guiId;
		if (func_num_args() == 1) {
			$id = func_get_arg(0);
			if ($this->guiExists($id))	{
				$this->id = $id;
				$this->elementArray = $this->selectElements();
			}
            //FIXME: is this a good compromise between the two constructors?
            try{
              $this->load();
            }
            catch(Exception $E)
            {
              new mb_exception($E->getMessage()); 
            }
		}
	}
    
    /*
    * @return Assoc Array containing the fields to send to the user
    */
    public function getFields() {
        $result = array(
                            "name" => $this->name,
							"description" => $this->description, 
                            "public" => $this->public

        );
		return $result;
	}

	public function getElementByName ($id) {
		for ($i = 0; $i < count($this->elementArray); $i++) {
			$e = $this->elementArray[$i];
			if ($e->id === $id) {
				return $e;
			}
		}
		throw new Exception ("Element " . $id . " does not exist in application " . $this->name);
	}

	public function toSql () {
		$insert =  "INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) VALUES ";
		$insert .= "('" . $this->id . "','" . $this->name . "','" . 
			$this->description . "'," . $this->public . ");\n";
	
		//gui_element
		foreach ($this->elementArray as $element) {
			$insert .= $element->toSql();
		}
		$insert = preg_replace("/,,/",",NULL,",$insert);
		$insert = preg_replace("/, ,/", ",NULL,",$insert);	
		return $insert;	
	}

	public function  create() {
		if($this->name == ""){ $e = new Exception("Can't create user  without name");}
	    
        //NOTE: gui_id, is not autocrated in the database
		$sql_gui_create = "INSERT INTO gui (gui_id,gui_name) VALUES ($1,$2);";
		$v = array($this->name,$this->name);
		$t = array("s","s");
	
		db_begin();
		
		$insert_result = db_prep_query($sql_gui_create,$v,$t);
		if($insert_result == false)
		{
			db_rollback();
			$e = new Exception("Could not insert new gui");
		}

		$id = db_insertid($insert_result,'gui','gui_id');
		if($id != 0)
		{
			$this->id = $id;
		}
	
		$commit_result = $this->commit();
		if($commit_result == false)
		{
			try {
				db_rollback();
			}
			catch(Exception $E)
			{
				$newE = new Exception("Could not set inital values of new gui");
				throw $newE;
			}
		}


		db_commit();


	}

    /*
	*	@param	$changes JSON  keys and their values of what to change in the object
	*/
	public function change($changes) {
        //FIXME: validate input
		$this->name = isset($changes->name) ? $changes->name : $this->name;
		$this->description = isset($changes->description) ? $changes->description : $this->description;
		$this->id = isset($changes->id) ? $changes->id : $this->id;
		$this->public = isset($changes->public) ? $changes->public : $this->public;

        return true;
	}
	
    public function commit() {

		$sql_update = "UPDATE gui SET ".
			"gui_name = $1, ".
			"gui_description = $2, ".
			"gui_public = $3 ".
			"WHERE gui_id = $4;";


			$v = array($this->name,
									$this->description,
									$this->public,
									$this->id);

			$t = array("s", "s", "i", "s");

			$update_result = db_prep_query($sql_update,$v,$t);
			if(!$update_result)
			{
				throw new Exception("Database error updating User");
			}

		return true;
	}

    public function remove(){
      // this functions exists, in a sliglty differnt form, so we 
      // can reuse it
      $this->deleteGui($this->id);
    }
    
	public function load() {
		$sql_gui = "SELECT * FROM gui WHERE gui_id = $1; ";
		$v = array($this->id);
		$t = array("s");
		$res_gui = db_prep_query($sql_gui,$v,$t);
		if($row = db_fetch_array($res_gui)){

			$this->name = $row['gui_name'];
			$this->description	= $row['gui_description'];
			$this->public = $row['gui_public'];

		}else{
			 throw new Exception("no such GUI");
		}
		return true;
	}

    /*
    * @return Array of GUIs
    * @param $filter UNUSED! string that must be contained in the guiname 
    */
    public static function getList($filter) {
    //FIXME: optimize
      $guis = Array();
      $sql_guilist = "SELECT gui_id FROM gui ORDER BY gui_name";
      $res_guis = db_query($sql_guilist);

      while($row = db_fetch_array($res_guis))
      {
        try{
          $guis[] = new gui($row['gui_id']);
        }
        catch(Exception $E)
        {
          //FIXME: should catch some errors here
          throw($E);
        }
      }
      return $guis;
      
    }

    /*
    * tries to initialize a guiobject by Name
    * @return A gui Object
    * @param $name the name of the gui to find
    */

    public static function byName($name) {
    
      if($name == null) { return new gui(null); }

      $sql_gui = "SELECT gui_id FROM gui WHERE gui_name = '$name'";
      $res_gui = db_query($sql_gui);
      if($row = db_fetch_array($res_gui))
      {
        return  new gui($row['gui_id']);
      }
      return null;

    }

	
	public function addWfs ($aWfs) {
		$sql ="INSERT INTO gui_wfs (fkey_gui_id, fkey_wfs_id)";
		$sql .= "VALUES ($1, $2);";
		$v = array($this->id, $aWfs->id);
		$t = array("s", "i");
		$res = db_prep_query($sql, $v, $t);

		if (!$res) {
			$e = new mb_exception("Error while saving WFS to DB. Rollback performed.");
			return false;
		}
		return true;
	}
	
	public function selectElements () {
		$sql = "SELECT e_id FROM gui_element WHERE fkey_gui_id = $1 " . 
				"ORDER BY e_pos";
		$v = array($this->id);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		$elementArray = array();
		while ($row = db_fetch_array($res)) {
			array_push($elementArray, $row[0]);
		}

		$this->elementArray = array();
		for ($i = 0; $i < count($elementArray); $i++) {
			$currentElement = new Element();
			$currentElement->select($elementArray[$i], $this->id);
			array_push($this->elementArray, $currentElement);
		}
		return $this->elementArray;
	}

	public function toHtml () {
		$htmlString = "";
		$htmlString .= $this->elementsToHtml();
		return $htmlString;
	}

	public function getJavaScriptModules () {
		$jsArray = array();
		for ($i = 0; $i < count($this->elementArray); $i++) {
			$currentElement = $this->elementArray[$i];
			array_merge($jsArray, $currentElement->getJavaScriptModules());			
		}
		return $jsArray;
	}
	
 	/**
 	 * Checks if a GUI with a given ID exists in the database
 	 * 
 	 * @param integer $gui_id the ID of the GUI that is being checked
 	 * @return boolean true if a gui '$gui_id' exists; else false
 	 */
 	public function guiExists ($gui_id){
		$sql = "SELECT * FROM gui WHERE gui_id = $1";
		$v = array($gui_id);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		$row = db_fetch_array($res);
		if ($row) {
			return true;	
		}
		return false;
 	}

	
	/**
	 * Deletes a GUI $guiId and all its links to users, layers etc.
	 * 
	 * @param Integer $guiId the GUI that is going to be deleted
	 * @return boolean true if the deletion succeded, else false
	 */
	public function deleteGui ($guiId) {
		$guiList = $guiId;

		$sql = array();
		$v = array();			
		$t = array();

		array_push($sql, "BEGIN");
		array_push($v, array());
		array_push($t, array());
		
		array_push($sql, "DELETE FROM gui WHERE gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_element WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_element_vars WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_layer WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_mb_group WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_mb_user WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_treegde WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_wfs WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

    array_push($sql, "DELETE FROM gui_wfs_conf WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "DELETE FROM gui_wms WHERE fkey_gui_id = $1");
		array_push($v, array($guiList));
		array_push($t, array('s'));

		array_push($sql, "COMMIT");
		array_push($v, array());
		array_push($t, array());

		// execute all SQLs
		for ($i = 0; $i < count($sql); $i++) {
			$res = db_prep_query($sql[$i], $v[$i], $t[$i]);
			// if an SQL fails, send a ROLLBACK and return false
			if (!$res) {
				db_query("ROLLBACK");
				return false;
			}
		}
		return true;
	}

	/** Renames the GUI $guiID to $newGUIName
	 * 
	 * @param Integer $guiId ID of the GUI
	 * @param String $newGuiName the new name of the GUI
	 * @return boolean true if the renaming succeded, else false
	 */
	public function renameGui ($guiId, $newGuiName) {
		if ($this->copyGui($guiId, $newGuiName, true)) {
			$this->deleteGui($guiId);
			return true;
		}
		return false;
	}

	/**
	 * 
 	 * Copies a GUI $guiId and all its links to users, layers etc. to GUI $newGuiName
 	 * 
	 * @param Integer $guiId ID of the GUI
	 * @param String $newGuiName the new name of the GUI
	 * @param boolean $withUsers true if the users, that may access the GUI $guiId, shall have access to the new GUI; else false.
	 * 
	 * @return boolean true if the renaming succeded, else false
	 */ 
 	public function copyGui ($guiId, $newGuiName, $withUsers) {
		$guiList = $guiId;
		if (!$this->guiExists($newGuiName)) {
			
			$sql = array();
			$v = array();			
			$t = array();
						
			array_push($sql, "BEGIN");
			array_push($v, array());
			array_push($t, array());

			array_push($sql, "INSERT INTO gui (gui_id, gui_name, gui_description, gui_public) SELECT $1, $2, gui_description, gui_public FROM gui WHERE gui_id = $3;");
			array_push($v, array ($newGuiName, $newGuiName, $guiList));
			array_push($t, array ("s", "s", "s"));

			array_push($sql, "INSERT INTO gui_gui_category (fkey_gui_id, fkey_gui_category_id) SELECT $1, fkey_gui_category_id FROM gui_gui_category WHERE fkey_gui_id = $2;");
			array_push($v, array ( $newGuiName, $guiList));
			array_push($t, array ("s", "s"));
			
			array_push($sql, "INSERT INTO gui_element (fkey_gui_id, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url) SELECT $1, e_id, e_pos, e_public, e_comment, e_title, e_element, e_src, e_attributes, e_left, e_top, e_width, e_height, e_z_index, e_more_styles, e_content, e_closetag, e_js_file, e_mb_mod, e_target, e_requires, e_url FROM gui_element WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));

			array_push($sql, "INSERT INTO gui_element_vars (fkey_gui_id, fkey_e_id, var_name, var_value, context, var_type) SELECT $1, fkey_e_id, var_name, var_value, context, var_type FROM gui_element_vars WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));

			array_push($sql, "INSERT INTO gui_layer (fkey_gui_id, fkey_layer_id, gui_layer_wms_id, gui_layer_status, gui_layer_selectable, gui_layer_visible, gui_layer_queryable, gui_layer_querylayer, gui_layer_minscale, gui_layer_maxscale, gui_layer_priority, gui_layer_style, gui_layer_wfs_featuretype,gui_layer_title) SELECT $1, fkey_layer_id, gui_layer_wms_id, gui_layer_status, gui_layer_selectable, gui_layer_visible, gui_layer_queryable, gui_layer_querylayer, gui_layer_minscale, gui_layer_maxscale, gui_layer_priority, gui_layer_style, gui_layer_wfs_featuretype,gui_layer_title FROM gui_layer WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));

			array_push($sql, "INSERT INTO sld_user_layer (fkey_mb_user_id,fkey_layer_id,fkey_gui_id,sld_xml,use_sld) SELECT fkey_mb_user_id,fkey_layer_id, $1,sld_xml,use_sld FROM sld_user_layer  WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));
			
			if ($withUsers == true) {
				/* group of original gui is copied as well */
				array_push($sql, "INSERT INTO gui_mb_group (fkey_gui_id, fkey_mb_group_id, mb_group_type) SELECT $1, fkey_mb_group_id, mb_group_type FROM gui_mb_group WHERE fkey_gui_id = $2;");
				array_push($v, array($newGuiName, $guiList));
				array_push($t, array("s", "s"));

				/* users of original gui are copied as well */
				array_push($sql, "INSERT INTO gui_mb_user (fkey_gui_id, fkey_mb_user_id, mb_user_type) SELECT $1, fkey_mb_user_id, mb_user_type FROM gui_mb_user WHERE fkey_gui_id = $2;");
				array_push($v, array($newGuiName, $guiList));
				array_push($t, array("s", "s"));
			}
			else {
				// users of original gui are not copied, the current user is set as owner 
				array_push($sql, "INSERT INTO gui_mb_user (fkey_gui_id, fkey_mb_user_id, mb_user_type) VALUES ($1, $2, 'owner')");
				array_push($v, array($newGuiName, Mapbender::session()->get("mb_user_id")));
				array_push($t, array('s', 'i'));
			}
			array_push($sql, "INSERT INTO gui_treegde (fkey_gui_id, fkey_layer_id, id, lft, rgt, my_layer_title, layer, wms_id) SELECT $1, fkey_layer_id, id, lft, rgt, my_layer_title, layer, wms_id FROM gui_treegde WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));

			array_push($sql, "INSERT INTO gui_wfs (fkey_gui_id, fkey_wfs_id) SELECT $1, fkey_wfs_id FROM gui_wfs WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));
			
			array_push($sql, "INSERT INTO gui_wfs_conf (fkey_gui_id, fkey_wfs_conf_id) SELECT $1, fkey_wfs_conf_id FROM gui_wfs_conf WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));


			array_push($sql, "INSERT INTO gui_wms (fkey_gui_id, fkey_wms_id, gui_wms_position, gui_wms_mapformat, gui_wms_featureinfoformat, gui_wms_exceptionformat, gui_wms_epsg, gui_wms_visible) SELECT $1, fkey_wms_id, gui_wms_position, gui_wms_mapformat, gui_wms_featureinfoformat, gui_wms_exceptionformat, gui_wms_epsg, gui_wms_visible FROM gui_wms WHERE fkey_gui_id = $2;");
			array_push($v, array($newGuiName, $guiList));
			array_push($t, array("s", "s"));
			
			array_push($sql, "COMMIT");
			array_push($v, array());
			array_push($t, array());

			// execute all SQLs
			for ($i = 0; $i < count($sql); $i++) {
				$res = db_prep_query($sql[$i], $v[$i], $t[$i]);
				// if an SQL fails, send a ROLLBACK and return false
				if (!$res) {
					db_query("ROLLBACK");
					return false;
				}
			}
			return true;
		}
		else {
	      echo "<script type='text/javascript'>";
	      echo "alert('This gui name " . $newGuiName . " is taken!');";
	      echo "</script>";
	      return false;
		}
	}

	private function elementsToHtml () {
		$bodyStringArray = array();
		$elementString = "";
		for ($i = 0; $i < count($this->elementArray); $i++) {
			$currentElement = $this->elementArray[$i];
			if ($currentElement->id != "body") {
				$elementString .= $currentElement->toHtml();
			}
			else {
				$bodyStringArray = $currentElement->toHtmlArray();
			}
		}
		$elementString .= "<form id='sendData' name='sendData' action='' " .
						  "method='POST' target='loadData' " .
						  "style='position:absolute;left:800px'>" .
						  "<input type='hidden' name='data'></form>";

		if (count($bodyStringArray) == 3) {
			$elementString = $bodyStringArray[0] . 
				$bodyStringArray[1] .
				$elementString . 
				$bodyStringArray[2];
			
		}
		return $elementString;			
	}
	
}
?>
