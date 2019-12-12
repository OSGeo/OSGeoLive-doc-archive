<?php
	require_once dirname(__FILE__) . "/../../core/globalSettings.php";
?>
<fieldset class="ui-widget" id="metadataUrlEditor" name="metadataUrlEditor" type="hidden" style="display: none">
<input name="kindOfMetadataAddOn" id="kindOfMetadataAddOn" type="hidden" value="" />
	<fieldset id="addonChooser" name="addonChooser" style="display: none">
		<legend><?php echo _mb("Choose kind of coupled metadata");?></legend>
		<p>
			<table><tr><td><img src='../img/osgeo_graphics/geosilk/link.png' title='linkage' onclick='$("#addonChooser").css("display","none");$("#link_editor").css("display","block");$("#kindOfMetadataAddOn").attr("value","link");' /></td><td><?php echo _mb("Add URL to existing Metadataset");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Here someone can add a url to an existing metadata record, which is available over www. The record can either be harvested and pushed to the own catalogue service or it is only used as a link. This links are pushed into the service metadata record and the new capabilities document.");?>'}" src="../img/questionmark.png" alt="" /></td></tr>
			<tr><td><img src='../img/gnome/edit-select-all.png' title='metadata'  onclick='$("#addonChooser").css("display","none");$("#simple_metadata_editor").css("display","block");$("#kindOfMetadataAddOn").attr("value","metadataset");' /></td><td><?php echo _mb("Add a simple metadata record which is mostly generated from given layer information");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("With this option someone can generate a very simple metadata record for the data which is distributed thru the wms layer. The record fulfills only the INSPIRE Metadata Regulation! Most of the needed data is pulled from the service, layer and group information of the owner of the service. The metadate will be created on the fly. It is not stored in the database!");?>'}" src="../img/questionmark.png" alt="" /></td></tr>
			<tr><td><img src='../img/button_blue_red/up.png' id='uploadImage' title='upload' /></td><td><?php echo _mb("Add a simple metadata record from a local file");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("With this option someone can upload an existing metadata record and couple it to the layer. The uploaded data is pushed to the catalogue and will be available for searching. The uploaded data is not fully controlled and validated. It cannot be edited but must be replaced with a new record if needed!");?>'}" src="../img/questionmark.png" alt="" /></td></tr>
			</table>
		</p>
	</fieldset>
	<!--fieldset for save link form-->
	<fieldset id="link_editor" name="link_editor" type="hidden" style="display: none">
		<legend><?php echo _mb("Link Editor");?></legend>
		<input name="link" id="link" />
		<p>
			<label for="export2csw"><?php echo _mb("Harvest link target and export to CSW");?></label>
      			<input name="export2csw" id="export2csw" type="checkbox" checked="checked"/>
		</p>
		
	</fieldset>
	<!--fieldset for upload metadata form-->
	<fieldset id="metadata_upload" name="metadata_upload" type="hidden" style="display: none">
		<legend><?php echo _mb("Metadata upload");?></legend>
		<input name="metadatafile" id="metadatafile" type="file"/>
	</fieldset>
	<!--fieldset for metadata form-->
	<fieldset id="simple_metadata_editor" name="simple_metadata_editor" type="hidden"  style="display: none">
		<legend><?php echo _mb("Simple metadata editor");?></legend>
		<fieldset>
			<legend><?php echo _mb("Resource title");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("This a characteristic, and often unique, name by which the resource is known.");?>'}" src="../img/questionmark.png" alt="" /></legend>
			<input class="required" name="title" id="title"/>
		</fieldset>
		<fieldset>
			<legend><?php echo _mb("Resource abstract");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("This is a brief narrative summary of the content of the resource.");?>'}" src="../img/questionmark.png" alt="" /></legend>
			<input class="required" name="abstract" id="abstract"/>
		</fieldset>
		<fieldset>
			<legend><?php echo _mb("Lineage");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("This is a statement on process history and/or overall quality of the spatial data set. Where appropriate it may include a statement whether the data set has been validated or quality assured, whether it is the official version (if multiple versions exist), and whether it has legal validity. The value domain of this metadata element is free text.");?>'}" src="../img/questionmark.png" alt="" /></legend>
			<input class="required" name="lineage" id="lineage"/>
		</fieldset>
	<fieldset id="tempref" name="tempref">
		<legend><?php echo _mb("TEMPORAL REFERENCE");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("This metadata element addresses the requirement to have information on the temporal dimension of the data as referred to in Article 8(2)(d) of Directive 2007/2/EC. At least one of the metadata elements referred to in points 5.1 to 5.4 shall be provided. The value domain of the metadata elements referred to in points 5.1 to 5.4 is a set of dates. Each date shall refer to a temporal reference system and shall be expressed in a form compatible with that system. The default reference system shall be the Gregorian calendar, with dates expressed in accordance with ISO 8601.");?>'}" src="../img/questionmark.png" alt="" /></legend>
		<fieldset id="timespan" name="timespan">
			<legend><?php echo _mb("Temporal extent");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("The temporal extent defines the time period covered by the content of the resource. This time period may be expressed as any of the following: - an individual date, - an interval of dates expressed through the starting date and end date of the interval, - a mix of individual dates and intervals of dates.");?>'}" src="../img/questionmark.png" alt="" /></legend>
				<label for=""><?php echo _mb("from");?>:</label>
				<input class="required hasdatepicker" name="tmp_reference_1" id="tmp_reference_1"/>
				<label for=""><?php echo _mb("to");?>:</label>
				<input class="required hasdatepicker" name="tmp_reference_2" id="tmp_reference_2"/>
		</fieldset>
		<fieldset id="cyclicupdate" name="cyclicupdate">
			<legend><?php echo _mb("Maintenance and update frequency");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Frequency with which changes and additions are made to the resource after the initial resource is completed. Notice: This value may change the value of the end date of temporal extent. The end date will be computed automatically from the current timestamp if a cyclic update is defined!");?>'}" src="../img/questionmark.png" alt="" /></legend>
				<select class="required cyclic_selectbox" id='update_frequency' name='update_frequency'>
<!-- B.5.18 MD_MaintenanceFrequencyCode <<CodeList>> of ISO19115 -->					<option value="continual"><?php echo _mb("continual");?></option>
					<option value="daily"><?php echo _mb("daily");?></option>
					<option value="weekly"><?php echo _mb("weekly");?></option>
					<option value="fortnightly"><?php echo _mb("fortnightly");?></option>
					<option value="monthly"><?php echo _mb("monthly");?></option>
					<option value="quarterly"><?php echo _mb("quarterly");?></option>
					<option value="biannually"><?php echo _mb("biannually");?></option>
					<option value="annually"><?php echo _mb("annually");?></option>
					<option value="asNeeded"><?php echo _mb("as needed");?></option>
					<option value="irregular"><?php echo _mb("irregular");?></option>

					<option value="notPlanned"><?php echo _mb("not planned");?></option>

					<option value="unknown"><?php echo _mb("unknown");?></option>
				</select>
		</fieldset>
	</fieldset>
	<fieldset id="spatialres">
		<legend><?php echo _mb("Spatial resolution");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Spatial resolution refers to the level of detail of the data set. It shall be expressed as a set of zero to many resolution distances (typically for gridded data and imagery-derived products) or equivalent scales (typically for maps or map-derived products). An equivalent scale is generally expressed as an integer value expressing the scale denominator. A resolution distance shall be expressed as a numerical value associated with a unit of length.");?>'}" src="../img/questionmark.png" alt="" /></legend>
			<label for="groundDistance">
				<input class="required radioRes" name="spatial_res_type" id="groundDistance" type="radio"/ value="groundDistance" checked="checked">
			<?php echo _mb("Ground distance in [m]");?>
			</label>
			<label for="scaleDenominator">
				<input class="required radioRes" name="spatial_res_type" id="scaleDenominator" type="radio"/ value="scaleDenominator">
			<?php echo _mb("Scale denominator [1:X]");?>
			</label>
			<!--<label for="spatial_res_type" class="error"><?php echo _mb("Please set the resolution type!");?>-->
			</label>
			<label><?php echo _mb("Value of resolution");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Value of spatial resolution in [m] or scale denominator");?>'}" src="../img/questionmark.png" alt="" /></label>
			<input class="required" name="spatial_res_value" id="spatial_res_value"/>
	</fieldset>
	<!-- Dropdown List of CRS which are defined in mapbender.conf. For those CRS the layer extents will be computed by mapbender - this is relevant for INSPIRE.-->
	<fieldset id="referencesystem">
		<legend><?php echo _mb("Coordinate Reference System");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Description of the coordinate reference system(s) used in the data set.");?>'}" src="../img/questionmark.png" alt="" /></legend>
<?php
	if (defined('SRS_ARRAY')) {
		$srs_array = explode(",", SRS_ARRAY);
		echo '<select class="required ref_system_selectbox" name="ref_system" id="ref_system">';
		foreach ($srs_array as $epsg) {
			echo "<option value='" . "EPSG:" .$epsg . "'>" . _mb("EPSG:".$epsg) . "</option>";
		}
		echo "</select>";
		
	} else {
		echo '<input name="ref_system" id="ref_system"/>';
	}
?>
	</fieldset>
	<fieldset id="data_format">
		<legend><?php echo _mb("Encoding");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Description of the computer language construct(s) specifying the representation of data objects in a record, file, message, storage device or transmission channel.");?>'}" src="../img/questionmark.png" alt="" /></legend>
		<select class="required format_selectbox" id='format' name='format'>
			<option value="database"><?php echo _mb("Database");?></option>
			<option value="shapefile"><?php echo _mb("Esri Shape");?></option>
			<option value="tab"><?php echo _mb("MapInfo Tab file");?></option>
			<option value="csv"><?php echo _mb("CSV");?></option>
			<option value="gml"><?php echo _mb("GML");?></option>
		</select>
	</fieldset>
	<fieldset id="charset">
		<legend><?php echo _mb("Character Encoding");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("The character encoding used in the data set. This element is mandatory only if an encoding is used that is not based on UTF-8.");?>'}" src="../img/questionmark.png" alt="" /></legend>
		<select class="required charset_selectbox" id='inspire_charset' name='inspire_charset'>
			<option value="utf8"><?php echo _mb("utf8");?></option>
			<option value="latin1"><?php echo _mb("latin1");?></option>
		</select>
	</fieldset>
	<fieldset id="consistance">
		<legend><?php echo _mb("Topological Consistency");?><img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Correctness of the explicitly encoded topological characteristics of the data set as described by the scope. This element is mandatory only if the data set includes types from the Generic Network Model and does not assure centreline topology (connectivity of centrelines) for the network.");?>'}" src="../img/questionmark.png" alt="" /></legend>
		<input name="inspire_top_consistence" id="inspire_top_consistence" type="checkbox" checked="checked"/>
	</fieldset>
	</fieldset>
</fieldset>
