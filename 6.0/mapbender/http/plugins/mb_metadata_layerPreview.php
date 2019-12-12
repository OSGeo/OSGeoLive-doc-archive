<?php
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../../http/classes/class_connector.php");
require_once(dirname(__FILE__)."/../../http/classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_weldMaps2JPEG.php");
//define("LAYER_PREVIEW_BASE","../tmp/layerpreviews/");
//define("LAYER_PREVIEW_BASE","../tmp/");



$ajaxResponse  = new AjaxResponse($_REQUEST);
$mapurl =  $ajaxResponse->getParameter("mapurl");
$layerName =  $ajaxResponse->getParameter("layerName");
$legendUrl =  $ajaxResponse->getParameter("legendUrl");
$wmsId =  $ajaxResponse->getParameter("wmsId");
$layerId = $ajaxResponse->getParameter("layerId");

$layerPreviewMapFileName = PREVIEW_DIR ."/".$layerId."_layer_map_preview.jpg";
$layerPreviewLegendFileName = PREVIEW_DIR ."/".$layerId."_layer_legend_preview.jpg";



if(!$mapurl){
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage('mapURL not set');
	$ajaxResponse->send();
}

/*
if(!$legendurl){
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage('legendURL not set');
	$ajaxResponse->send();
}
*/
if (!$wmsId) {
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage('wmsId not set');
	$ajaxResponse->send();
}
if (!$layerName) {
	$ajaxResponse->setSuccess(false);
	$ajaxResponse->setMessage('layerName not set');
	$ajaxResponse->send();
}

switch ($ajaxResponse->getMethod()) {
	case "saveLayerPreview":
		$mapImg = new weldMaps2JPEG($mapurl, $layerPreviewMapFileName);
		if(!$mapImg) {
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage('Map preview could not be created');
			$ajaxResponse->send();
		} 
		if ($legendUrl) {
			$legendImg = new weldMaps2JPEG($legendUrl, $layerPreviewLegendFileName);
			if(!$legendImg) {
				$ajaxResponse->setSuccess(false);
				$ajaxResponse->setMessage('Legend preview could not be created');
				$ajaxResponse->send();
			} 
			/*else {
				$ajaxResponse->setSuccess(true);
				$ajaxResponse->setMessage('Preview saved');
				$ajaxResponse->send();
			}*/
		}
		else {
			$legendUrl = null;
		}
		$ajaxResponse->setSuccess(true);
		$ajaxResponse->setMessage('Preview saved');
		$ajaxResponse->send();
/*
		$updateSQL = <<<SQL

UPDATE layer_preview SET 
	layer_map_preview_filename = $1,
	layer_legend_preview_filename = $2
WHERE layer_preview.fkey_layer_id IN (
	SELECT layer_id FROM layer 
	WHERE fkey_wms_id = $wmsId AND layer_name = $3
)
SQL;
		$insertSQL = <<<SQL
		
INSERT INTO layer_preview (
	fkey_layer_id, 
	layer_map_preview_filename, 
	layer_legend_preview_filename
) 
	SELECT layer_id, $1, $2 	
	FROM layer 
	WHERE fkey_wms_id = $wmsId AND layer_name = $3 
		AND NOT  EXISTS (
			SELECT fkey_layer_id FROM layer JOIN layer_preview 
			ON fkey_layer_id = layer_id 
			WHERE fkey_wms_id = $wmsId AND layer_name = $4
		)
SQL;
		$updateResult = db_prep_query(
			$updateSQL,
			array($layerPreviewMapFileName, $layerPreviewLegendFileName, $layerName), 
			array("s", "s", "s")
		);
		$insertResult = db_prep_query(
			$insertSQL,
			array($layerPreviewMapFileName, $layerPreviewLegendFileName, $layerName, $layerName), 
			array("s", "s", "s", "s")
		);
		if (!$insertResult && !$updateResult) {
			new mb_exception("could not insert/update layerPreview into db");
			$ajaxResponse->setSuccess(false);
			$ajaxResponse->setMessage("could not insert/Update layerPreview into db: ". pg_last_error());
		}*/
		break;

	default:
		$ajaxResponse->setSuccess(false);
		$ajaxResponse->setMessage("invalid method");
}
$ajaxResponse->send();
?>
