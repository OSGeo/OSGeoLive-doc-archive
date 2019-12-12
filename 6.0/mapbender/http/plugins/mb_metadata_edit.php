<?php
	require_once dirname(__FILE__) . "/../../core/globalSettings.php";
	require_once dirname(__FILE__) . "/../classes/class_user.php";
?>

<fieldset>
	<input name="wms_id" id="wms_id" type="hidden"/>

	<legend><?php echo _mb("Service Level Metadata");?>: <img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Possibility to adapt and add informations in the separate WMS-Layer Metadata. The modified Metadata is stored in the database of the GeoPortal.rlp, outwardly these metadata overwrite the original Service-Metadata.");?>'}" src="../img/questionmark.png" alt="" /></legend>
	<p>
		<label for="wms_title"><?php echo _mb("Show original Service Metadata from last update");?></label>
		<img class="original-metadata-wms" src="../img/book.png" alt="" />
		<img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("The original WMS-Metadata from the last update could be recovered or updated, so that the original Service-Metadata will be shown outward again.");?>'}" src="../img/questionmark.png" alt="" />
	</p>
	<p>
		<label for="wms_title"><?php echo _mb("WMS Title (OWS)");?>:</label>
		<input name="wms_title" id="wms_title" class="required"/>
		<img class="metadata_img" title="<?php echo _mb("INSPIRE 1.1: resource title");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
    	<label for="wms_abstract"><?php echo _mb("WMS Abstract (OWS)");?>:</label>
    	<input name="wms_abstract" id="wms_abstract"/>
    	<img class="metadata_img" title="<?php echo _mb("INSPIRE 1.2: resource abstract");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	<img class="help-dialog" title="<?php echo _mb("Help for INSPIRE Abstract");?>" help="{text:'<?php echo _mb("INSPIRE demands some information about the spatial resolution in the abstract tag of the capabilities document. Please insert some words about it.");?>'}" src="../img/questionmark.png" alt="" />
	</p>
	<p>
		<label for="wms_keywords"><?php echo _mb("WMS Keywords (OWS)");?>:</label>
    	<input readonly="readonly" name="wms_keywords" id="wms_keywords"/>
    	<img class="metadata_img" title="<?php echo _mb("INSPIRE 3: keyword");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="fees"><?php echo _mb("WMS Fees (OWS)");?>:</label>
    	<input name="fees" id="fees"/>
    	<img class="metadata_img" title="<?php echo _mb("INSPIRE 8.1: conditions applying to access and use");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="accessconstraints"><?php echo _mb("WMS AccessConstraints (OWS)");?>:</label>
		<input name="accessconstraints" id="accessconstraints"/>
    	<img class="metadata_img" title="<?php echo _mb("INSPIRE 8.2: limitations on public access");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
<?php
	$sql = "SELECT termsofuse_id, name FROM termsofuse";
	$res = db_query($sql);
	$termsofuse = array();
	while ($row = db_fetch_assoc($res)) {
		$termsofuse[$row["termsofuse_id"]] = $row["name"];
	}
?>
	<p>
		<label for="wms_termsofuse"><?php echo _mb("WMS Predefined Licence (Registry)");?>:</label>
    	<select name="wms_termsofuse" id="wms_termsofuse">
			<option>...</option>
<?php
	foreach ($termsofuse as $key => $value) {
		echo "<option value='" . $key . "'>" . htmlentities($value, ENT_QUOTES, CHARSET) . "</option>";
	}
?>
		</select>
    	<img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Auswahl von vordefinierten Lizenzen hinsichtlich Gebühren und Zugriffsbeschränkungen.");?>'}" src="../img/questionmark.png" alt="" />
	</p>
	<p>
		<label for="wms_network_access"><?php echo _mb("Restricted Network Access (Registry)");?>:</label>
      		<input name="wms_network_access" id="wms_network_access" type="checkbox"/>
	</p>
</fieldset>
<fieldset>
	<legend><?php echo _mb("WMS Provider Section (OWS)");?></legend>
	<p>
		<label for="contactperson"><?php echo _mb("Contact Individual Name (OWS)");?>:</label>
    	<input name="contactperson" id="contactperson"/>
	</p>
	<p>
		<label for="contactposition"><?php echo _mb("Contact Position Name (OWS)");?>:</label>
    	<input name="contactposition" id="contactposition"/>
	</p>
	<p>
		<label for="contactvoicetelephone"><?php echo _mb("Contact Phone Voice (OWS)");?>:</label>
    	<input name="contactvoicetelephone" id="contactvoicetelephone"/>
	</p>
	<p>
		<label for="contactfacsimiletelephone"><?php echo _mb("Contact Phone Fax (OWS)");?>:</label>
    	<input name="contactfacsimiletelephone" id="contactfacsimiletelephone"/>
	</p>
	<p>
		<label for="contactorganization"><?php echo _mb("Contact Organisation (WMS)");?>:</label>
    	<input name="contactorganization" id="contactorganization"/>
    	<img  class="metadata_img" title="<?php echo _mb("INSPIRE 9.1: responsible party name");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="address"><?php echo _mb("Contact Address (WMS)");?>:</label>
    	<input name="address" id="address"/>
	</p>
	<p>
		<label for="city"><?php echo _mb("Contact City (WMS)");?>:</label>
     	<input name="city" id="city"/>
	</p>
	<p>
	  	<label for="stateorprovince"><?php echo _mb("Contact State or Province (WMS) - ISO 3166-II");?>:</label>
      	<input name="stateorprovince" id="stateorprovince"/>
	</p>
	<p>
	  	<label for="postcode"><?php echo _mb("Contact Post Code (WMS)");?>:</label>
      	<input name="postcode" id="postcode"/>
	</p>
	<p>
	  	<label for="country"><?php echo _mb("Contact Post Country (WMS) - ISO 3166");?>:</label>
      	<input name="country" id="country"/>
	</p>
	<p>
	  	<label for="contactelectronicmailaddress"><?php echo _mb("Contact Electronic Mail Address (WMS)");?>:</label>
      	<input name="contactelectronicmailaddress" id="contactelectronicmailaddress" class="required email"/>
     	<img class="metadata_img" title="<?php echo _mb("INSPIRE 9.1: responsible party email");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
	  	<label for="wms_timestamp_create"><?php echo _mb("Date of first registration (Registry)");?>:</label>
      	<input readonly="readonly" name="wms_timestamp_create" id="wms_timestamp_create"/>
      	<img class="metadata_img" title="<?php echo _mb("INSPIRE 5.2: date of publication");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
	  	<label for="wms_timestamp"><?php echo _mb("Date of last revision (Registry)");?>:</label>
      	<td><input readonly="readonly" name="wms_timestamp" id="wms_timestamp"/>
      	<img class="metadata_img" title="<?php echo _mb("INSPIRE 10.2: metadata date");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
</fieldset>
<fieldset>
	<legend><?php echo _mb("Metadata Point of contact (registry)");?>: <img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Information about the organization which is responsible for contributing the metadata. The information will be automaticcaly generated from the mapbender database mb_group object. The information came from the primary group of the service owner or from a group which has authorized the owner to publish metadata in their name.");?>'}" src="../img/questionmark.png" alt="" /></legend>

<?php
//selectbox for organizationswhich allows the publishing of metadatasets for the specific user
	$sql = "SELECT fkey_mb_group_id, mb_group_name FROM (SELECT fkey_mb_group_id FROM mb_user_mb_group WHERE fkey_mb_user_id = $1 AND (mb_user_mb_group_type = 3 OR mb_user_mb_group_type = 2)) AS a LEFT JOIN mb_group ON a.fkey_mb_group_id = mb_group.mb_group_id";
	$user = new User();
	$userId = $user->id;
	$v = array($userId);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$metadataGroup = array();
	while ($row = db_fetch_assoc($res)) {
		$metadataGroup[$row["fkey_mb_group_id"]] = $row["mb_group_name"];
	}
?>
	<p>
		<label for="mb_group_name"><?php echo _mb("Organization responsible for metadata");?>:</label>
    	<select name="fkey_mb_group_id" id="fkey_mb_group_id" onChange="var chosenoption=this.options[this.selectedIndex];$('#mb_md_edit').mapbender().fillMdContact(chosenoption.value);">
			<option value="0">...</option>
<?php
	foreach ($metadataGroup as $key => $value) {
		echo "<option value='" . $key . "'>" . htmlentities($value, ENT_QUOTES, CHARSET) . "</option>";
	}
?>
		</select>
    	<img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Selection of different organizations which authorized you to publish metadata in their name.");?>'}" src="../img/questionmark.png" alt="" />
	</p>
<!-- end of selection for the different organizations -->

	<p>
		<label for="mb_group_title"><?php echo _mb("Title");?>:</label>
		<input readonly="readonly" name="mb_group_title" id="mb_group_title"/>
		<img class="metadata_img" title="<?php echo _mb("INSPIRE 10.1: metadata point of contact name");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="mb_group_address"><?php echo _mb("Address");?>:</label>
		<input readonly="readonly" name="mb_group_address" id="mb_group_address"/>
	</p>
	<p>
		<label for="mb_group_postcode"><?php echo _mb("Postcode");?>:</label>
		<input readonly="readonly" name="mb_group_postcode" id="mb_group_postcode"/>
	</p>
	<p>
		<label for="mb_group_city"><?php echo _mb("City");?>:</label>
		<input readonly="readonly" name="mb_group_city" id="mb_group_city"/>
	</p>
	<p>
		<label for="mb_group_voicetelephone"><?php echo _mb("Telephone");?>:</label>
		<input readonly="readonly" name="mb_group_voicetelephone" id="mb_group_voicetelephone"/>
	</p>
	<p>
		<label for="mb_group_email"><?php echo _mb("Email");?>:</label>
		<input readonly="readonly" name="mb_group_email" id="mb_group_email"/>
		<img class="metadata_img" title="<?php echo _mb("INSPIRE 10.1: metadata point of contact email");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="mb_group_logo_path"><?php echo _mb("Logo url");?>:</label>
		<input readonly="readonly" name="mb_group_logo_path" id="mb_group_logo_path"/>
	</p>

</fieldset>
