<?php
	require_once dirname(__FILE__) . "/../../core/globalSettings.php";
	require_once dirname(__FILE__) . "/../classes/class_user.php";
?>

<fieldset>
	<input name="wfs_id" id="wfs_id" type="hidden"/>

	<legend><?php echo _mb("Service Level Metadata");?>: <img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Possibility to adapt and add informations in the separate WFS-Featuretype Metadata. The modified Metadata is stored in the database of the GeoPortal.rlp, outwardly these metadata overwrite the original Service-Metadata.");?>'}" src="../img/questionmark.png" alt="" /></legend>
	<p>
		<label for="wfs_title"><?php echo _mb("Show original Service Metadata from last update");?></label>
		<img class="original-metadata-wfs" src="../img/book.png" alt="" />
		<img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("The original WFS-Metadata from the last update could be recovered or updated, so that the original Service-Metadata will be shown outward again.");?>'}" src="../img/questionmark.png" alt="" />
	</p>
	<p>
		<label for="title"><?php echo _mb("WFS Title (OWS)");?>:</label>
		<input name="title" id="title" class="required"/>
		<img class="metadata_img" title="<?php echo _mb("INSPIRE 1.1: resource title");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
    	<label for="summary"><?php echo _mb("WFS Abstract (OWS)");?>:</label>
    	<input name="summary" id="summary"/>
    	<img class="metadata_img" title="<?php echo _mb("INSPIRE 1.2: resource abstract");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="wfs_keywords"><?php echo _mb("WFS Keywords (OWS)");?>:</label>
    	<input readonly="readonly" name="wfs_keywords" id="wfs_keywords"/>
    	<img class="metadata_img" title="<?php echo _mb("INSPIRE 3: keyword");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="fees"><?php echo _mb("WFS Fees (OWS)");?>:</label>
    	<input name="fees" id="fees"/>
    	<img class="metadata_img" title="<?php echo _mb("INSPIRE 8.1: conditions applying to access and use");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="accessconstraints"><?php echo _mb("WFS AccessConstraints (OWS)");?>:</label>
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
		<label for="wfs_termsofuse"><?php echo _mb("WFS Predefined Licence (Registry)");?>:</label>
    	<select name="wfs_termsofuse" id="wfs_termsofuse">
			<option>...</option>
<?php
	foreach ($termsofuse as $key => $value) {
		echo "<option value='" . $key . "'>" . htmlentities($value, ENT_QUOTES, CHARSET) . "</option>";
	}
?>
		</select>
    	<img class="help-dialog" title="<?php echo _mb("Help");?>" help="{text:'<?php echo _mb("Selection of predefined licences.");?>'}" src="../img/questionmark.png" alt="" />
	</p>
	<p>
	  	<label for="network_access"><?php echo _mb("Restricted Network Access (Registry)");?>:</label>
      		<input name="network_access" id="network_access" type="checkbox"/>
	</p>
</fieldset>
<fieldset>
	<legend><?php echo _mb("WFS Provider Section (OWS)");?></legend>
	<p>
		<label for="individualName"><?php echo _mb("Contact Individual Name (OWS)");?>:</label>
    	<input name="individualName" id="individualName"/>
	</p>
	<p>
		<label for="positionName"><?php echo _mb("Contact Position Name (OWS)");?>:</label>
    	<input name="positionName" id="positionName"/>
	</p>
	<p>
		<label for="voice"><?php echo _mb("Contact Phone Voice (OWS)");?>:</label>
    	<input name="voice" id="voice"/>
	</p>
	<p>
		<label for="facsimile"><?php echo _mb("Contact Phone Fax (OWS)");?>:</label>
    	<input name="facsimile" id="facsimile"/>
	</p>
	<p>
		<label for="providerName"><?php echo _mb("Contact Organisation (WFS)");?>:</label>
    	<input name="providerName" id="providerName"/>
    	<img  class="metadata_img" title="<?php echo _mb("INSPIRE 9.1: responsible party name");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
		<label for="deliveryPoint"><?php echo _mb("Contact Address (WFS)");?>:</label>
    	<input name="deliveryPoint" id="deliveryPoint"/>
	</p>
	<p>
		<label for="city"><?php echo _mb("Contact City (WFS)");?>:</label>
     	<input name="city" id="city"/>
	</p>
	<p>
	  	<label for="administrativeArea"><?php echo _mb("Contact State or Province (WFS) - ISO 3166-II");?>:</label>
      	<input name="administrativeArea" id="administrativeArea"/>
	</p>
	<p>
	  	<label for="postalCode"><?php echo _mb("Contact Post Code (WFS)");?>:</label>
      	<input name="postalCode" id="postalCode"/>
	</p>
	<p>
	  	<label for="country"><?php echo _mb("Contact Post Country (WFS) - ISO 3166");?>:</label>
      	<input name="country" id="country"/>
	</p>
	<p>
	  	<label for="electronicMailAddress"><?php echo _mb("Contact Electronic Mail Address (WFS)");?>:</label>
      	<input name="electronicMailAddress" id="electronicMailAddress" class="required email"/>
     	<img class="metadata_img" title="<?php echo _mb("INSPIRE 9.1: responsible party email");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
	  	<label for="wfs_timestamp_create"><?php echo _mb("Date of first registration (Registry)");?>:</label>
      	<input readonly="readonly" name="wfs_timestamp_create" id="wfs_timestamp_create"/>
      	<img class="metadata_img" title="<?php echo _mb("INSPIRE 5.2: date of publication");?>" src="../img/misc/inspire_eu_klein.png" alt="" />
	</p>
	<p>
	  	<label for="wfs_timestamp"><?php echo _mb("Date of last revision (Registry)");?>:</label>
      	<td><input readonly="readonly" name="wfs_timestamp" id="wfs_timestamp"/>
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
    	<select name="fkey_mb_group_id" id="fkey_mb_group_id" onChange="var chosenoption=this.options[this.selectedIndex];$('#mb_md_wfs_edit').mapbender().fillMdContact(chosenoption.value);">
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
