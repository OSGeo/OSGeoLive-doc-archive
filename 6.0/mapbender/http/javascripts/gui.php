<?php
require_once(dirname(__FILE___)."/../classes/class_mb_exception.php");
require_once(dirname(__FILE___)."/../classes/class_gui.php");
require_once(dirname(__FILE___)."/../classes/class_RPCEndpoint.php");
require_once(dirname(__FILE___)."/../classes/class_json.php");

$ajaxResponse  = new AjaxResponse($_REQUEST);

$ObjectConf = array("DisplayName"   => "Gui",
                    "internalName"  => "gui",
                    "ClassName"     => "gui");

$rpc = new RPCEndpoint($ObjectConf,$ajaxResponse);
$rpc->run();

?>
