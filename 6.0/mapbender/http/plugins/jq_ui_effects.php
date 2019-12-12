<?php
	$uiPath = dirname(__FILE__) . '/' . 
		"../extensions/jquery-ui-1.7.2.custom/development-bundle/ui/";

	include '../include/dyn_php.php';

	include 'jq_ui_effects.js';
	
	if ($blind) {
		include $uiPath . "min.effects.blind.js";
	}
	
	if ($bounce) {
		include $uiPath . "min.effects.bounce.js";
	}
	
	if ($clip) {
		include $uiPath . "min.effects.clip.js";
	}
	
	if ($drop) {
		include $uiPath . "min.effects.drop.js";
	}
	
	if ($explode) {
		include $uiPath . "min.effects.explode.js";
	}
	
	if ($fold) {
		include $uiPath . "min.effects.fold.js";
	}
	
	if ($highlight) {
		include $uiPath . "min.effects.highlight.js";
	}
	
	if ($pulsate) {
		include $uiPath . "min.effects.pulsate.js";
	}
	
	if ($scale) {
		include $uiPath . "min.effects.scale.js";
	}
	
	if ($shake) {
		include $uiPath . "min.effects.shake.js";
	}
	
	if ($slide) {
		include $uiPath . "min.effects.slide.js";
	}
	
	if ($transfer) {
		include $uiPath . "min.effects.transfer.js";
	}
	
?>