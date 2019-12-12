<?php
	ob_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../extensions/dataTables-1.5/media/css/demo_table_jui.css">
		<link rel="stylesheet" type="text/css" href="../extensions/jquery-ui-1.7.2.custom/css/ui-lightness/jquery-ui-1.7.2.custom.css">
		<link rel="stylesheet" type="text/css" href="../css/popup.css">
		<script type='text/javascript'>
			var loadFromSession = false;
<?php
	$e_id = "wmcPublic";
	require_once(dirname(__FILE__) . "/../php/mb_validatePermission.php");
	require_once(dirname(__FILE__) . "/../classes/class_json.php");
	require_once(dirname(__FILE__) . "/../extensions/jquery-1.3.2.min.js");
	require_once(dirname(__FILE__) . "/../extensions/jqjson.js");
	require_once(dirname(__FILE__) . "/../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.core.js");
	require_once(dirname(__FILE__) . "/../javascripts/core.php");
	require_once(dirname(__FILE__) . "/../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/min.ui.tabs.js");
	require_once(dirname(__FILE__) . "/../plugins/jq_upload.js");
	require_once(dirname(__FILE__) . "/../extensions/dataTables-1.5/media/js/jquery.dataTables.min.js");

echo "var global_mb_log_js = '".LOG_JS."';";
echo "var global_mb_log_level = '".LOG_LEVEL."';";
echo "var global_log_levels = '".LOG_LEVEL_LIST."';";

echo "Mapbender.sessionId = '".session_id()."';\n";
echo "var mb_nr = Mapbender.sessionId;\n";
echo "Mapbender.sessionName = '".session_name()."';\n";
echo "var mb_session_name = Mapbender.sessionName;\n";
	
	require_once(dirname(__FILE__) . "/../../lib/exception.js");
	require_once(dirname(__FILE__) . "/../../lib/ajax.js");
	require_once(dirname(__FILE__) . "/../../lib/basic.js");
	require_once(dirname(__FILE__) . "/../javascripts/popup.js");
	header('Content-type: text/html');
?>
	$(function () {
		
		$.fn.loadwmc = function (options) {
			return this.each(function () {
				var options = {
					id: this.id,
					loadWmc: 0,
					mergeWmc: 0,
					appendWmc: 0,
					publishWmc: 1,
					showWmc: 1,
					openLayers: 1,
					deleteWmc: 1,
					uploadWmc: 0,
					listWmc: 1
				};

<?php
	require_once(dirname(__FILE__) . "/../javascripts/mod_loadwmc.js");
	
?>
			});
		}
		$("#loadwmc_container").loadwmc();
		$("#loadwmc_container_tabs").show();
	});

		</script>
	</head>
	<body>
		<div id='loadwmc_container'></div>
	</body>
</html>
