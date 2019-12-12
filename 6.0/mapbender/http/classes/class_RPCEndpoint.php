<?php
require_once dirname(__FILE__)."/../../lib/class_Filter.php";

interface RPCObject{

  public function create();
  public function change($changes);
  public function commit();
  public function remove();
  public function load();
  public function getFields();
  public static function getList($filter);
  public static function byName($name);

}

class RPCEndpoint {
  
  var $ObjectConf;
  var $ajaxResponse;

  var $method;

  public function __construct ($ObjectConf,$ajaxResponse){
    $this->ObjectConf = $ObjectConf;
    $this->ajaxResponse = $ajaxResponse;
    $this->method = $this->ajaxResponse->getMethod();
  }

  // When we upgrade to 5.3.0 this needs to desperately upgraded to
  // "Added support for dynamic access of static members using $foo::myFunc(). (Etienne Kneuss)"
  // from http://php.net/ChangeLog-5.php
  // so we can get rid of this sillyness here
  public function RPCObjectByName($name){

    switch($this->ObjectConf['ClassName']){
      case 'User':
        return User::byName($name);

      case 'Group':
        return Group::byName($name);
      
      case 'gui':
        return gui::byName($name);
      
      default:
        // or throw exception ?
        return null;
    }

  }

  public function RPCObjectGetList(){
    
    switch($this->ObjectConf['ClassName']){

      case 'User':
        return User::getList("");

      case 'Group':
        return Group::getList("");

      case 'gui':
        return gui::getList("");

      default:
        return null;
    }
  }

  public function run(){

    switch($this->method)
    {
      case 'create':
        try{
          $this->rpc_create($this->ajaxResponse->getParameter('fields'));
        }catch(Exception $E){
          $this->ajaxResponse->setMessage("Create failed. Error: " . $E);
        }
        break;

      case 'update':
        try{
          $this->rpc_update($this->ajaxResponse->getParameter('name'),$this->ajaxResponse->getParameter('fields'));
        }catch(Exception $E){
          $this->ajaxResponse->setMessage("Update failed. Error: " . $E);
        }
          
        break;

      case 'remove':
        try{
          $this->rpc_remove($this->ajaxResponse->getParameter('name'));
        }catch(Exception $E){
          $this->ajaxResponse->setMessage("Delete failed. Error: " . $E);
        }
        break;

      case 'load':
        try{
          $this->rpc_load($this->ajaxResponse->getParameter('name'));
        }catch(Exception $E){
          $this->ajaxResponse->setMessage("Load failed. Error: " . $E);
        }
        break;

      case 'list':
        try{
          $this->rpc_list();
        }catch(Exception $E){
          $this->ajaxResponse->setMessage("List failed. Error: " + $E);
        }
        break;

      default:
          $this->ajaxResponse->setSuccess(false);
          $this->ajaxResponse->setMessage(_mb("invalid method"));
    }

    $this->ajaxResponse->send();
  
    
  }

  public function rpc_create($fields){
    // test if an obect of that name already exists
    $instance = $this->RPCObjectByName($fields->name);
    if($instance){

      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Already Exists "). $this->ObjectConf['internalName'] . "  Error: " . $E);
      return;
    }

    // create an empty one
    $instance = $this->RPCObjectByName(null);
    $instance->name = $fields->name;
    
    try{
        $instance->create();
    }
    catch(Exception $E)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Could not create instance "). $this->ObjectConf['internalName'] . " Error: ". $E );
    }
    
    try {
        $instance->change($fields);
    }
    catch (Exception $E)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Could not create "). $this->ObjectConf['internalName'] . "  Error: " . $E);
    }
    
  
    try{
        $instance->commit();
    }
    catch(Exception $E)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Could not change "). $this->ObjectConf['internalName'] . " Error: " . $E);
    }
  }

  public function rpc_update($name,$fields){
    $instance = $this->RPCObjectByName($name);
    if($instance == null)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage($this->ObjectConf['internalName'] . _mb(" does not exist: ". $name));
      return;
    }
    try{
        $instance->change($fields);
    }
    catch(Exception $E)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Could not change "). $this->ObjectConf['internalName'] . " Error: " .$E);
    }

    try{
        $instance->commit();
    }
    catch(Exception $E)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Could not change "). $this->ObjectConf['internalName'] . " Error: " . $E);
    }
    $this->ajaxResponse->setResult('name',$instance->name);

  }


  public function rpc_remove($name){
    $instance = $this->RPCObjectByName($name);
    if($instance == null)
    {
      $this->ajaxResponse->setSuccess(true);
      $this->ajaxResponse->setMessage(_mb("No such "). $this->ObjectConf['internalName']);
      break;
    }
    try{
        $instance->remove();
    }
    catch(Exception $E)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Could not remove "). $this->ObjectConf['internalName'] . " Error: ". $E);
    }

  }

  public function rpc_load($name){
    $instance = $this->RPCObjectByName($name);
    if($instance == null)
    {
      $this->ajaxResponse->setSuccess(true);
      $this->ajaxResponse->setMessage(_mb("No such ". $this->ObjectConf['internalName'] .": $name"));
      $this->ajaxResponse->setResult("data",array("error"=>true ));
      return;
    }
  try{
    $instance->load();
    $result = $instance->getFields();
  }
  catch(Exception $E)
  {
      $this->ajaxResponse->setSuccess(true);
      $this->ajaxResponse->setMessage(_mb("Could not load data: "). $this->ObjectConf['internalName'] ." Error: " . $E);
      $this->ajaxResponse->setResult("error",true);
  }
  $this->ajaxResponse->setResult("data",$result);

  }

  public function rpc_list(){
    $result = array();
    $instances = array();
    $instances = $this->RPCObjectGetList('');
    if(!$instances)
    {
      $this->ajaxResponse->setSuccess(false);
      $this->ajaxResponse->setMessage(_mb("Error fetching list of "). $this->ObjectConf['internalName']);
      return;
    }
    
    foreach( $instances as $instance)
    {
      $result[] = array("name" =>  $instance->name, "value" => $instance->name);
    }
    $this->ajaxResponse->setResult("list",$result);
    $this->ajaxResponse->setResult("type", array("display" => $this->ObjectConf['DisplayName'], 
                                           "internal" => $this->ObjectConf['InternalName']));

  }

}
?>
