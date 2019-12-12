<?php
ob_start();
$e_id="CustomizeTree";
require_once(dirname(__FILE__) . "/../php/mb_validatePermission.php");
require_once(dirname(__FILE__) . "/../classes/class_json.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../css/customTree.css">
		<style type="text/css">
			.ui-selecting {
			  color:red;
			}
			.ui-selected {
			  border-width:thin;
			  border-style:solid;
			  border-color:red;
			  background-color:transparent;
			  font-size:9px;
			}
			.ui-draggable {
			}
			.div-border {
			  border-width:thin;
			  border-style:solid;
			  border-color:black;
			  background-color:transparent;
			  font-size:9px;
			}
			.contextMenu
			{
				display:none;
			}
		</style>
		<link rel='stylesheet' type='text/css' href='../css/popup.css'>
		<script type='text/javascript'>
<?php

	require_once(dirname(__FILE__) . "/../extensions/jquery-ui-1.8.1.custom/js/jquery-1.4.2.min.js");
	require_once(dirname(__FILE__) . "/../extensions/jquery-ui-1.8.1.custom/development-bundle/ui/jquery-ui-1.8.1.custom.js");
	//require_once(dirname(__FILE__) . "/../extensions/jquery-ui-personalized-1.5.2.js");
	require_once(dirname(__FILE__) . "/../extensions/jquery.contextmenu.r2.js");
	require_once(dirname(__FILE__) . "/../extensions/jqjson.js");
	require_once(dirname(__FILE__) . "/../javascripts/popup.js");
	require_once(dirname(__FILE__) . "/../javascripts/core.php");
	require_once(dirname(__FILE__) . "/../../lib/customTreeModel.js");
	require_once(dirname(__FILE__) . "/../../lib/customTreeController.js");
	require_once(dirname(__FILE__) . "/../../lib/buttonNew.js");
	header('Content-type: text/html');
?>
			var myTree = new CustomTree();

			var applicationId;


			var saveTreeOnUnloadOrChange = function (myTree) {
				if (myTree.hasChanged) {
					var saveChanges = confirm("<?php echo _mb("You have changed the tree. All changes will be lost. Save changes?");?>");

					if (saveChanges) {
						Save.updateDatabase(function () {
						});
					}

				}
			};


			var selectApplication = function (applicationName) {

				applicationId = applicationName;

				myTree = new CustomTree({
					loadFromApplication: applicationName,
					draggable: true,
					droppable: true,
					id: "myTree",
					contextMenu: true
				});
			}
			
			var getApplications = function () {
				var queryObj = {
					command:"getApplications",
					"sessionName": parent.Mapbender.sessionName,
					"sessionId": parent.Mapbender.sessionId
				};				
				$.post("../php/mod_customTree_server.php", {
					queryObj:$.toJSON(queryObj)
				}, function (json, status) {
					var replyObj = typeof json == "string" ?
						eval('(' + json + ')') : json;

					$select = $("#applicationSelect");
					$select.change(function () {
						saveTreeOnUnloadOrChange(myTree);
						selectApplication(this.options[this.selectedIndex].value);
					});

					for (var index in replyObj.data.applicationArray) {
						var currentApplication = replyObj.data.applicationArray[index];
						$currentOption = $("<option id='application" + index + "' value='" + currentApplication + "'>" + currentApplication + "</option>");
						$select.append($currentOption);
					}
				});
			};
			
			var Save = {
				buttonParameters : {
					on:"../img/button_blink_red/wmc_save_on.png",
					over:"../img/button_blink_red/wmc_save_over.png",
					off:"../img/button_blink_red/wmc_save_off.png",
					type:"toggle"
				},
				updateDatabase : function (callback) {
					if (!applicationId || applicationId == "...") {
						callback();
						return;
					}

					var data = {
						"applicationId": applicationId,
						"folderArray": myTree.exportNestedSets()
					};
					var queryObj = {
						command:"update",
						parameters:{
							data: data
						},
						"sessionName": parent.Mapbender.sessionName,
						"sessionId": parent.Mapbender.sessionId
					};
					$.post("mod_customTree_server.php", {
						queryObj:$.toJSON(queryObj)	
					}, function (json, status) {
						var replyObj = typeof json == "string" ?
							eval('(' + json + ')') : json;
						alert(replyObj.success);
						myTree.hasChanged = false;
						callback();
					});
				}
			};

			var Restart = {
				buttonParameters : {
					on:"../img/button_blink_red/exit_on.png",
					over:"../img/button_blink_red/exit_over.png",
					off:"../img/button_blink_red/exit_off.png",
					type:"singular"
				},
				removeAllFolders: function(){
					if (!applicationId || applicationId == "...") {
						return;
					}
					
					var confirmDelete = confirm("<?php echo _mb("You are about to delete your customized folder structure. Proceed?");?>");

					if (confirmDelete) {
						var queryObj = {
							"command": "delete",
							"parameters": {
								"applicationId": applicationId
							},
							"sessionName": parent.Mapbender.sessionName,
							"sessionId": parent.Mapbender.sessionId
						};
						
						$.post("../php/mod_customTree_server.php", {
							queryObj: $.toJSON(queryObj)
						}, function(json, status){
							myTree = new CustomTree({
								loadFromApplication: applicationName,
								draggable: true,
								droppable: true,
								id: "myTree",
								contextMenu: true
							});
						});
						
					}
				}
			}
						
			$(function () {

				getApplications();

				var toolbox = new ButtonGroup("controls");

				// save tool
				var saveButton = new Button(Save.buttonParameters);
				toolbox.add(saveButton);

				saveButton.registerPush(function () {
					Save.updateDatabase(saveButton.triggerStop);	
				});

				// restart tool
				var restartButton = new Button(Restart.buttonParameters);
				toolbox.add(restartButton);
				
				restartButton.registerPush(function () {
					Restart.removeAllFolders();
				});

				// dialogue: save changes on unload
				$(window).unload(function () {
//					saveTreeOnUnloadOrChange(myTree);
				})
			});
			
		</script>
	</head>
	<body>
	<div style="border:solid gray 1px;padding:5px;">
		<h2><?php echo _mb("Konfigure tree");?></h2>
		<p><b><?php echo _mb("Please select a gui which its tree should be configured.");?></b></p>
		<select id='applicationSelect'>
			<option>...</option>
		</select>
		<div id="controls"></div>
		<div style="border-top: solid gray 1px; border-bottom: solid gray 0px;">
		<div class="contextMenu" id="folderMenu">
			<ul>
				<li id="addFolder"><?php echo _mb("Add");?></li>
				<li id="deleteFolder"><?php echo _mb("Delete");?></li>
				<li id="editFolder"><?php echo _mb("Edit");?></li>
			</ul>
		</div>
		<div class="contextMenu" id="rootMenu">
			<ul>
				<li id="addFolder"><?php echo _mb("Add");?></li>
			</ul>
		</div>
		<div id='myTree'></div>
		<div id='wmsTree'></div>
		</div>
	</div>
	</body>
</html>
