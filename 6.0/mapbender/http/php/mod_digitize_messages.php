<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");

$e = new mb_notice("locale: " . Mapbender::session()->get("mb_locale") . "; lang: " . Mapbender::session()->get("mb_lang"));
$e = new mb_notice(setlocale(LC_ALL, Mapbender::session()->get("mb_locale")));

//
// Messages
//
$msg_obj = array();
$msg_obj["messageDescriptionPolygon"] = _mb("polygon");
$msg_obj["messageDescriptionLine"] = _mb("line");
$msg_obj["messageDescriptionPoint"] = _mb("point");
$msg_obj["messageErrorNotAnInteger"] = _mb("Not an integer value.");
$msg_obj["messageErrorNotAFloat"] = _mb("Not a double value.");
$msg_obj["messageErrorFieldIsEmpty"] = _mb("This field may not be empty.");
$msg_obj["messageErrorFormEvaluation"] = _mb("Failure during form evaluation.");
$msg_obj["messageErrorWfsWrite"] = _mb("An error occured.");
$msg_obj["messageErrorMergeNotApplicable"] = _mb("At least two geometries must be available. Only polygons are allowed in the geometry list.");
$msg_obj["messageErrorSplitNotApplicable"] = _mb("Exactly two geometries must be available. The first geometry shall be a polygon or a line, the second geometry shall be a line.");
$msg_obj["messageErrorDifferenceNotApplicable"] = _mb("Exactly two polygons must be available.");
$msg_obj["messageErrorMergeLineNotApplicable"] = _mb("Exactly two lines must be available.");
$msg_obj["messageSuccessWfsWrite"] = _mb("Success.");
$msg_obj["messageConfirmDeleteGeomFromDb"] = _mb("Delete geometry from database?");
$msg_obj["messageConfirmDeleteAllGeomFromList"] = _mb("Clear list of geometries?");
$msg_obj["messageSelectAnOption"] = _mb("Please select an entry.");
$msg_obj["buttonLabelSaveGeometry"] = _mb("Save");
$msg_obj["buttonLabelUpdateGeometry"] = _mb("Update");
$msg_obj["buttonLabelDeleteGeometry"] = _mb("Delete");
$msg_obj["buttonLabelAbort"] = _mb("Abort");
$msg_obj["errorMessageEpsgMismatch"] = _mb("Fatal error: EPSG mismatch. ");
$msg_obj["errorMessageNoGeometrySelected"] = _mb("No geometry selected!");
$msg_obj["errorMessageInvalidLineGeometries"] = _mb("No valid line geometries for merging! Please check start and end points of your lines.");
$msg_obj["buttonLabelPointOff"] = _mb("add point");
$msg_obj["buttonLabelPointOn"] = _mb("cancel editing");
$msg_obj["buttonLabelLineOff"] = _mb("add line");
$msg_obj["buttonLabelLineOn"] = _mb("finish line");
$msg_obj["buttonLabelLineContinueOff"] = _mb("continue line");
$msg_obj["buttonLabelLineContinueOn"] = _mb("finish line");
$msg_obj["buttonLabelPolygonOff"] = _mb("add polygon");
$msg_obj["buttonLabelPolygonOn"] = _mb("close polygon");
$msg_obj["buttonLabelMoveBasepointOff"] = _mb("move basepoint");
$msg_obj["buttonLabelMoveBasepointOn"] = _mb("move basepoint");
$msg_obj["buttonLabelInsertBasepointOff"] = _mb("Insert basepoint");
$msg_obj["buttonLabelInsertBasepointOn"] = _mb("Insert basepoint");
$msg_obj["buttonLabelDeleteBasepointOff"] = _mb("Delete basepoint");
$msg_obj["buttonLabelDeleteBasepointOn"] = _mb("Delete basepoint");
$msg_obj["buttonLabelClearListOff"] = _mb("clear list of geometries");
$msg_obj["buttonLabelClearListOn"] = _mb("clear list of geometries");
$msg_obj["buttonLabelMergeOff"] = _mb("Merge two polygons into a single polygon (will be added to the geometry list)");
$msg_obj["buttonLabelMergeOn"] = _mb("Merge two polygons into a single polygon (will be added to the geometry list)");
$msg_obj["buttonLabelSplitOff"] = _mb("Split a polygon/line by a line (the new polygons/lines will be added to the geometry list)");
$msg_obj["buttonLabelSplitOn"] = _mb("Split a polygon/line by a line (the new polygons/lines will be added to the geometry list)");
$msg_obj["buttonLabelDifferenceOff"] = _mb("Combine two polygons (to create en- and exclave or to compute the difference)");
$msg_obj["buttonLabelDifferenceOn"] = _mb("Split geometries");
$msg_obj["buttonLabelMergeLineOff"] = _mb("Merge two lines into a single line");
$msg_obj["buttonLabelMergeLineOn"] = _mb("Merge two lines into a single line");
$msg_obj["buttonDig_wfs_title"] = _mb("save / update / delete");
$msg_obj["buttonDig_remove_title"] = _mb("remove from workspace");
$msg_obj["buttonDig_removeDb_title"] = _mb("remove from database");
$msg_obj["buttonDig_clone_title"] = _mb("clone this geometry");
$msg_obj["closePolygon_title"] = _mb("click the first basepoint to close the polygon");
$msg_obj["measureTagLabelCurrent"] = _mb("Current: ");
$msg_obj["measureTagLabelTotal"] = _mb("Total: ");
$msg_obj["digitizeDefaultGeometryName"] = _mb("new");

$json = new Mapbender_JSON();
$output = $json->encode($msg_obj);

header("Content-type:application/x-json; charset=utf-8");
echo $output;
?>