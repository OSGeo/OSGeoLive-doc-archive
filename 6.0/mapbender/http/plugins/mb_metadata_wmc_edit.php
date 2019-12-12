<?php
	require_once dirname(__FILE__) . "/../../core/globalSettings.php";
	
	function displayCategories ($sql) {
		if (Mapbender::session()->get("mb_lang") === "de") {
			$sql = str_replace("category_code_en", "category_code_de", $sql);
		}
		
		$str = "";
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			$str .= "<option value='" . $row["id"] . "'>" . 
				htmlentities($row["name"], ENT_QUOTES, CHARSET) . 
				"</option>";
		}
		return $str;
	}
?>
<div id="wmc">
<fieldset>
	<input name="wmc_id" id="wmc_id" type="hidden"/>

	<legend><?php echo _mb("Service Level Metadata");?>: <img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Possibility to adapt and add informations in the separate WMC-Featuretype Metadata. The modified Metadata is stored in the database of the GeoPortal.rlp, outwardly these metadata overwrite the original Service-Metadata.");?>'}" src="../img/questionmark.png"></img></legend>
<!-- <p>
		<label><?php #echo _mb("Show original Service Metadata from last update");?></label>
		<img class="original-metadata-wmc" src="../img/book.png"></img>
		<img class="help-dialog" title="<?php #echo _mb("Help");?>" help="{text:'<?php #echo _mb("The original WMC-Metadata from the last update could be recovered or updated, so that the original Service-Metadata will be shown outward again.");?>'}" src="../img/questionmark.png"></img>
	</p>
-->	
	<p>
		<a id='wmc_showMetadata' class='cancelClickEvent' target='_blank' href=''><?php echo _mb("Metadata");?></a>
	</p>
	<p>
		<label for="wmc_title"><?php echo _mb("WMC Title (OWS)");?>:</label>
		<input name="wmc_title" id="wmc_title" class="required"/>
		<img class="metadata_img" title="<?php echo _mb("Inspire");?>" src="../img/misc/inspire_eu_klein.png"></img>
	</p>
	<p>
    	<label for="wmc_abstract"><?php echo _mb("WMC Abstract (OWS)");?>:</label>
    	<input name="wmc_abstract" id="wmc_abstract"/>
    	<img class="metadata_img" title="<?php echo _mb("Inspire");?>" src="../img/misc/inspire_eu_klein.png"></img>
	</p>
	<p>
		<label for="wmc_keyword"><?php echo _mb("WMC Keywords (OWS)");?>:</label>
    	<input name="wmc_keyword" id="wmc_keyword"/>
    	<img class="metadata_img" title="<?php echo _mb("Inspire");?>" src="../img/misc/inspire_eu_klein.png"></img>
	</p>
	<p>
		<label for="public"><?php echo _mb("WMC is public");?>:</label>
    	<input name="public" id="public" type="checkbox"/>
    </p>
</fieldset>
</div>

<div id="preview">
	<fieldset class="">
		<legend><?php echo _mb("Preview");?></legend>
		<div id="map"></div>
		<div id="toolbar_upper"></div>
		<div id="toolbar_lower"></div>
	</fieldset>
</div>

<div id="classification">
	<fieldset class="">
		<legend><?php echo _mb("Classification");?></legend>
		<p>
		    <label for="isoTopicCats" class="label_classification"><?php echo _mb("ISO Topic Category");?>:</label>
		    
			<img class="metadata_img" title="<?php echo _mb("Inspire");?>" src="../img/misc/inspire_eu_klein.png"></img>
			<select class="metadata_selectbox" id="isoTopicCats" name="isoTopicCats" size="2" multiple="multiple">
<?php
	$sql = "SELECT md_topic_category_id AS id, md_topic_category_code_en AS name FROM md_topic_category";
	echo displayCategories($sql);
?>
			</select>
			<img id="resetIsoTopicCats" title="<?php echo _mb("Reset selection");?>" src="../img/cross.png" style="cursor:pointer;"/>
		</p>
		<p>
		    <label for="inspireCats" class="label_classification"><?php echo _mb("INSPIRE Category");?>:</label>
			<img class="metadata_img" title="<?php echo _mb("Inspire");?>" src="../img/misc/inspire_eu_klein.png"></img>
			<select class="metadata_selectbox" id="inspireCats" name="inspireCats" size="2" multiple="multiple">
<?php
	$sql = "SELECT inspire_category_id AS id, inspire_category_code_en AS name FROM inspire_category";
	echo displayCategories($sql);
?>
			</select>
			<img id="resetInspireCats" title="<?php echo _mb("Reset selection");?>" src="../img/cross.png" style="cursor:pointer;"/>
		</p>
		<p>
		    <label for="customCats" class="label_classification"><?php echo _mb("Custom Category");?>:</label>
			<span class="metadata_span"></span>
			<select class="metadata_selectbox" id="customCats" name="customCats" size="2" multiple="multiple">
<?php
	$sql = "SELECT custom_category_id AS id, custom_category_code_en AS name FROM custom_category";
	echo displayCategories($sql);
?>
			</select>
			<img id="resetCustomCats" title="<?php echo _mb("Reset selection");?>" src="../img/cross.png" style="cursor:pointer;"/>
		</p>
	</fieldset>
</div>
