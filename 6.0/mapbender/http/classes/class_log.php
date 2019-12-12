<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__)."/../../core/globalSettings.php");

class log {
	var $dir = LOG_DIR;
	var $log_username = true;

	var $url = array();
	/*
	 * {'file' || 'db'}
	 */
	var $logtype = 'db';
	
	function log($module,$req,$time_client,$type = ""){

		$this->url = $req;
		if($type == "")
			$type = $this->logtype;

		if($type == "file"){
			if(is_dir($this->dir)){
				$logfile = $this->dir . "mb_access_" . date("Y_m_d") . ".log";
				if(!$h = @fopen($logfile,"a")){
					#exit;
				}
				else{
					for($i = 0; $i < count($this->url); $i++){
						$content = strtotime("now")." ";
						$content .= "[".date("d/M/Y:H:i:s O")."]";
						$content .= " " . Mapbender::session()->get("mb_user_ip");
						$content .= ' "';
						if($this->log_username == true){
							$content .= Mapbender::session()->get("mb_user_name");
						}
						$content .= '"';
						$content .= " " . Mapbender::session()->get("mb_user_id");
						$content .= " " . $module;
						$content .= ' "' . $this->url[$i] . '"';
						$content .= chr(13).chr(10);
						if(!fwrite($h,$content)){
							#exit;
						}
					}
					fclose($h);
				}
			}
		}
		else if($type == 'db'){

			for($i = 0; $i < count($this->url); $i++){
				$sql = "INSERT INTO mb_log (";
				$sql .= "time_client, time_server, time_readable, mb_session, ";
				$sql .= "gui, module, ip, username, userid, request";
				$sql .= ") VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)";

				$v = array($time_client, strtotime("now"), "[".date("d/M/Y:H:i:s O")."]", SID, Mapbender::session()->get("mb_user_gui"), $module, Mapbender::session()->get("mb_user_ip"), Mapbender::session()->get("mb_user_name"), Mapbender::session()->get("mb_user_id"), $this->url[$i]);
				$t = array("s", "s", "s", "s", "s", "s", "s", "s", "s", "s");
				$res = db_prep_query($sql, $v, $t)or die(db_error());

				if(!$res){
					include_once(dirname(__FILE__)."/class_mb_exception.php");
					$e = new mb_exception("class_log: Writing table mb_log failed.");
				}
			}
		}
	}
}
?>
