
<!-- TODO: Need my header! -->
<html>
<head>


<script src="../extensions/jquery-ui-1.7.2.custom/js/jquery-1.3.2.min.js" type="text/javascript"></script>
<script src="../extensions/jquery-ui-1.7.1.w.o.effects.min.js" type="text/javascript"></script>
<script src="../extensions/jqjson.js" type="text/javascript"></script>
<script src="mod_admin.js" type="text/javascript"></script>
<script src="Object.js" type="text/javascript"></script>
<script src="Editor.js" type="text/javascript"></script>
<script src="List.js" type="text/javascript"></script>

<script type="text/javascript">
//<![CDATA[
var Mapbender = {};

<?php
echo "var global_mb_log_js = '".LOG_JS."';";
echo "var global_mb_log_level = '".LOG_LEVEL."';";
echo "var global_log_levels = '".LOG_LEVEL_LIST."';";

require_once "../../lib/ajax.js";
require_once "../../lib/exception.js";
require_once "../../lib/event.js";

?>
//]]>
</script>
<script src="mod_admin.js" type="text/javascript" ></script>
<script type="text/javascript" >
//<![CDATA[

function body_onload(evt){
  Mapbender.Admin.scan();
}
//]]>
</script>
</head>
<body onload="body_onload(event)">
<!--

Check which adminGUI elements are defined for this GUI and put them on the page
like this:

require_once("mod_user.php");

echo mod_user_editor();

which would output something like this:
bla
<div class="MBUserEditor">
</div>

The javascript on the clientside then scans for each of these modules, loads
their javascript executes:

$(".MBUserEditor").Mapbender.Admin.UserEditor();


-->

<?php

include_once("mod_user.php");
echo $mod_user_html;

?>


</body>
</html>
