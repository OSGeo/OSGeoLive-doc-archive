<?php
# $Id:database-pgsql.php 2619 2008-07-08 15:46:11Z christoph $
# http://www.mapbender.org/index.php/database-pgsql.php
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

require_once(dirname(__FILE__) . "/../http/classes/class_json.php");
require_once(dirname(__FILE__) . "/class_Mapbender.php");

/**
 * Represents an incoming JSON-RPC. It will NOT send a response to the client!
 * (Use AjaxResponse if you also want to send a response.)
 * 
 * Usually called like this
 * 
 * $incomingAjax = new AjaxRequest($_REQUEST)
 * 
 * get parameters of request via
 * 
 * echo $incomingAjax->getParameter("someAttribute");
 */
class AjaxRequest {

	protected $method = "";
	protected $json = null;
	protected $id = null;
	protected $paramObject = array();
	
	public function __construct ($requestArray) {
		$this->json = new Mapbender_JSON();
		
		if (is_array($requestArray)) {
			$this->initializeFromArray($requestArray);
		}
	}

	protected function initializeFromArray ($requestArray) {
		if ($requestArray["id"]) {
			$this->id = intval($requestArray["id"]);
		}
		
		if ($requestArray["method"]) {
			$this->method = $requestArray["method"];
			
			if ($requestArray["params"]) {
				$obj = $this->json->decode($requestArray["params"]);
				$this->paramObject = $obj;
			}
		}
	}

	public function getMethod () {
		return $this->method;
	}

	public function getParameter ($key) {
		if (is_object($this->paramObject) && $this->paramObject->$key) {
			return $this->paramObject->$key;
		}
		return null;
	}
	
	public function getId () {
		return $this->id;
	}
}

/**
 * Represents an incoming JSON-RPC, which will send a response to the client.
 * 
 * Usually called like this
 * 
 * $ajaxResponse = new AjaxResponse($_REQUEST)
 * 
 * 
 * get parameters of request via
 * 
 * echo $ajaxResponse->getParameter("someAttribute");
 * 
 * 
 * set data to be sent back to the client
 * 
 * $ajaxResponse->setResult("key", "value");
 * 
 * or
 * 
 * $ajaxResponse->setResult($assocArray);
 * 
 * 
 * set the status of this RPC
 * 
 * $ajaxResponse->setSuccess(false);
 * 
 * and supply a message
 * 
 * $ajaxResponse->setMessage("I didn't do it.");
 * 
 * 
 * Finally send the response
 * 
 * $ajaxResponse->send();
 */
class AjaxResponse extends AjaxRequest {
	private $data = array();
	private $success = true;
	private $error = null;
	private $message = "";
	
	public function __construct ($ajaxRequest) {
		$this->json = new Mapbender_JSON();

		if (is_array($ajaxRequest)) {
			$this->initializeFromArray($ajaxRequest);
		}		

		// in addition to AjaxRequest, immediately send an
		// error message to the client, if the request
		// could not be identified
		if ($this->id === null) {
			$this->success = false;
			$this->message = _mb("Fatal error: Could not detect ID of AJAX request.");
			$this->send();
		}

		if (!Mapbender::session()->get("mb_user_id") || 
			!Mapbender::session()->get("mb_user_ip") || 
			Mapbender::session()->get("mb_user_ip") != $_SERVER['REMOTE_ADDR']) {
			$this->setSuccess(false);
			$this->error = array(
				"code" => -2,
				"message" => _mb("The session has expired. Please log in again.")
			);
		}
	}
	
	/**
	 * Set a message to be sent back to the client.
	 * 
	 * @param $aMessage String
	 */
	public function setMessage ($aMessage) {
		$this->message = $aMessage;
	}
	
	/**
	 * Set status of the RPC.
	 * 
	 * @param $trueOrFalse Boolean
	 */
	public function setSuccess ($trueOrFalse, $code = null) {
		$this->success = $trueOrFalse;
		
		if (!$this->success && is_numeric($code)) {
			$this->error = array(
				"code" => intval($code)
			);
		}
	}
	
	/**
	 * Compose data to be sent back to the client.
	 * Either by key and value, or by passing the complete associative array.
	 */
	public function setResult () {
		if (func_num_args() == 1) {
			$this->data = func_get_arg(0);
		}
		else if (func_num_args() == 2) {
			$key = func_get_arg(0);
			$value = func_get_arg(1);
			$this->data[$key] = $value;
		}
	}
	
	/**
	 * Send the response to the client.
	 */
	public function send () {
		header("Content-type:application/json; charset=" . CHARSET);
		echo $this->getData();
		die;
	}
	
	private function getData () {
		$dataObject = array();
		$dataObject["data"] = $this->data;
		if ($this->success) {
			$dataObject["success"] = true;
			$dataObject["message"] = $this->message;
		}
		else {
			if (is_null($this->error)) {
				$this->error = array(
					"code" => -1,
					"message" => $this->message
				);
			}
			else if (is_array($this->error) 
				&& is_numeric($this->error["code"]) 
				&& !$this->error["message"]
			) {
				$this->error["message"] = $this->message;
			}
		}
		$obj = array(
			"result" => $dataObject,
			"error" => $this->error,
			"id" => $this->id
		);
		return $this->json->encode($obj);
	}
}
?>