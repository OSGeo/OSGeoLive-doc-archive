<?php
/**
 * manages the session-handling and the access to session-variables 
 * @class
 */
 
 require_once(dirname(__FILE__)."/../http/classes/class_mb_exception.php");
 require_once(dirname(__FILE__)."/../http/classes/class_mb_warning.php");
 require_once(dirname(__FILE__)."/../http/classes/class_mb_notice.php");
 require_once(dirname(__FILE__)."/class_Singleton.php");

 class Mapbender_session extends Singleton{
 	
 	private $id ;
 	
 	/**
	 * @constructor
	 */
 	protected function __construct() {
 		$id = false;
 		new mb_notice("session.mapbender_session.instantiated ... ");
 	}

 	public static function singleton()
    {
        return parent::singleton(__CLASS__);
    }
 	
 	/**
	 * sets the value of the session-variable  
	 * @param String name the name of the session-variable
	 * @param Mixed value the new value of the session-variable
	 */
 	public function set($name, $value){
 		$this->start();
 		$_SESSION[$name] = $value;
 		new mb_notice("session.setSessionVariable.set: " . $name ." = ". $value);
 		session_write_close(); 
 	}
 	
	/**
	 * Unsets a session variable
	 * @param String name name of the session variable
	 */
	public function delete ($name) {
 		$this->start();
 		unset($_SESSION[$name]);
 		new mb_notice("session.setSessionVariable.unset: " . $name);
 		session_write_close(); 
	}
	
 	/* pushs the array of the session-variable  
	 * @param String name the name of the session-variable
	 * @param Mixed value the new value of the session-variable
	 */
 	public function push($name, $value){
 		//not implemented yet
 		// todo
 	}
 	
 	/**
	 * returns the value of the session-variable  
	 * @param String name the name of the session-variable
	 */
 	public function get($name){
 		$this->start();
 		if(isset($_SESSION[$name])){
			$sessionValue = $_SESSION[$name];
 		}
 		else{
 			new mb_warning("the sessionVariable: ".$name." is read but it's not set!'");
			$sessionValue = false;
 		}
 		session_write_close();
		return $sessionValue;
 	}
 	
 	/**
	 * checks if a session variable is set
	 * @param String name the name of the session variable
	 */
 	public function exists ($name){
 		$this->start();
 		if(isset($_SESSION[$name])){
			$sessionExists = true;
 		}
 		else{
 			new mb_notice("exists(): The session variable: ".$name." is not set!'");
			$sessionExists = false;
 		}
 		session_write_close();
		return $sessionExists;
 	}
 	
 	/**
	 * sets a new session-id   
	 * @param String id the new id of the session
	 */
 	public function setId($id){
		$this->id = $id;
		
	}
	
	/**
	 * starts the new session   
	 */
	private function start(){
		if(session_id() == ''){
			new mb_notice("session.sessionStart.noActiveId");
		}
		if($this->id){
			session_id($this->id);
			new mb_notice("session.sessionStart.changeId to: ". session_id());
		}
		session_start();
		new mb_notice("session.sessionStart.id: ". session_id());
	}
	
	/*
	 * kills the current session
	 */
	 public function kill(){
	 	if (isset($_COOKIE[session_name()])) {
    	setcookie(session_name(), '', time()-42000, '/');
		}
		if(session_id()){
			session_destroy();
		}
	 } 	
 }
?>
