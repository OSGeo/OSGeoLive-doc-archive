<?php
# $Id$
# Copyright (C) 2002 CCGIS 
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<?php printf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\" />",CHARSET);	?>
	<title><?php echo _mb("Search Catalog"); ?></title>
	<style type="text/css">
	<!--
	div.form-container form { padding: 5px; background-color: #FFF; border: #EEE 1px solid; background-color: #FbFbFb; }
	div.form-container p { margin: 0.5em 0 0 0; }
	div.form-container form p { margin: 0; }
	div.form-container form p.note { font-style: italic; margin-left: 18em; font-size: 80%; color: #666; }
	div.form-container form fieldset { margin:0 0 10px 0; padding: 10px; border: #DDD 1px solid; background-color:#FFF;}
	div.form-container form fieldset input:focus,
	div.form-container form fieldset input.errorfield:focus,
	div.form-container form fieldset textarea:focus { background-color: #FFC; border-color: #FC6;  }
	div.form-container form fieldset label{ position:relative; margin-right: 10px; padding-right: 10px; width: 15em; display: block; float: left; text-align: right;min-height:1em;top:0.25em;}
	div.form-container form fieldset label.errorfield,
	div.form-container form fieldset span.errorfield { color: #C00; }
	div.form-container form fieldset label.value{display:block;text-align:left;width:auto;}
	
	
	
	-->
	</style>
	<script type="text/javascript">
	<!--
	// Set default for element variables if they are undefined
	/*
	option_dball      = (typeof(option_dball) !== 'undefined')      ? option_dball      : '1';
	option_dbgroup    = (typeof(option_dbgroup) !== 'undefined')    ? option_dbgroup    : '0';
	option_dbgui      = (typeof(option_dbgui) !== 'undefined')      ? option_dbgui      : '0';
	capabilitiesInput = (typeof(capabilitiesInput) !== 'undefined') ? capabilitiesInput : '1';
	gui_list          = (typeof(gui_list) !== 'undefined')          ? gui_list          : '';
	*/

	/*
	var guis = gui_list.split(',');
	
	if(gui_list === '') {
		guis = [];
	}
	*/

	var global_source = 'capabilities';  // [capabilities,db]

	
	var global_is_advanced = false;

	//defaults
	var getrecords_media = 'GET';
	var getrecords_query = 'CQL'; //CQL FILTER
	
	//set server side URL for query builder
	var phpUrl  = '../php/mod_searchCatQueryBuilder_server.php?<?php echo $urlParameters;?>';



	function hide_advanced_form(){

		global_is_advanced = false;
		var html='';
		html = html + "<fieldset>";
		html = html + "<input type='button' id='adv_search_show' name='adv_search_show' value='<?php echo _mb('+ Advanced'); ?>' onclick='show_advanced_form();' />";
		html = html + "</fieldset>";

		document.getElementById('advanced_div').innerHTML=html;
	}

	// Form fields

	function show_advanced_form(){
		
		global_is_advanced = true;
		var html = '';
		html = html + "<fieldset>";
		html = html + "<input type='button' value='<?php echo _mb("- Advanced"); ?>' onclick=hide_advanced_form() />";
		html = html + "</fieldset>";
		html = html + "<fieldset>";
		html = html + "<legend><?php echo _mb("Advanced Search"); ?></legend>";
		html = html + "<fieldset id='cont_adv_summary'>";
		html = html + "<label for='adv_title'><?php echo _mb('Title '); ?>:</label>";
		html = html + "<input type='text' id='adv_title' name='adv_title' /> <br /><br />";
		html = html + "<label for='adv_abstract'><?php echo _mb('Abstract'); ?>:</label>";
		html = html + "<input type='text' id='adv_abstract' name='adv_abstract' /><br /><br />";
		html = html + "<label for='adv_keywords'><?php echo _mb('Keywords'); ?>:</label>";
		html = html + "<input type='text' id='adv_keywords' name='adv_keywords' /><br /><br />";
		html = html + "</fieldset>";

		
		html = html + "<fieldset>";
		html = html + "<table>";
		html = html + "<tr>";
		html = html + "<td></td>";
		html = html + "<td>";
		html = html + "<label for='latmin'><?php echo _mb('Lat-Min'); ?>:</label>";
		html = html + "<input type='text' id='latmin' name='latmin' size=8/>";
		html = html + "</td>";
		html = html + "<td></td>";
		html = html + "</tr>";
		html = html + "<tr>";
		html = html + "<td>";
		html = html + "<label for='lonmin'><?php echo _mb('Lon-Min'); ?>:</label>";
		html = html + "<input type='text' id='lonmin' name='lonmin' />";
		html = html + "</td>";
		html = html + "<td></td>";
		html = html + "<td>";
		html = html + "<label for='latmax'><?php echo _mb('Lat-Max'); ?>:</label>";
		html = html + "<input type='text' id='latmax' name='latmax' />";
		html = html + "</td>";
		html = html + "</tr>";
		html = html + "<tr>";
		html = html + "<td></td>";
		html = html + "<td>";
		html = html + "<label for='lonmax'><?php echo _mb('Lon-Max'); ?>:</label>";
		html = html + "<input type='text' id='lonmax' name='lonmax' />";
		html = html + "<td></td>";
		html = html + "</tr>";
		html = html + "</table>";
		html = html + "</fieldset>";
		
		document.getElementById('advanced_div').innerHTML=html;			
			
	}

	function show_options_form(){
		var html = '';
		html = html + "<fieldset>";
		html = html + "<input type='button' value='<?php echo _mb("- Options"); ?>' onclick=hide_options_form() />";
		html = html + "</fieldset>";
		html = html + "<fieldset>";
		html = html + "<legend>Search Options</legend>";
		html = html + "<fieldset id='cont_options'>";
		html = html + "<label for='opt_result_cont'><?php echo _mb('Number of Hits'); ?>:</label>";
		html = html + "<input type='text' id='opt_result_cont' name='opt_result_cont' /> <br /><br />";
		html = html + "<label for='opt_getrecords_media'><?php echo _mb('Getrecords Medium'); ?>:</label>";
		html = html + "<select id='opt_getrecords_media' name='opt_getrecords_media'>";
		html = html + "<option value='get'>GET</option>";
		html = html + "<option value='post'>POST</option>";
		html = html + "<option value='post-soap' disabled>SOAP</option>";//must be enabled if soap should be used in future
		html = html + "</select>";
		html = html + "<br /><br />";

		html = html + "<label for='opt_getrecords_query'><?php echo _mb('Query Language'); ?>:</label>";
		html = html + "<select id='opt_getrecords_query' name='opt_getrecords_query'>";
		html = html + "<option value='filter'><?php echo _mb("Filter"); ?></option>";
		html = html + "<option value='CQL'><?php echo _mb("CQL"); ?></option>";
		html = html + "</select>";
		html = html + "<br /><br />";
		
		html = html + "</fieldset>";
		document.getElementById('options_div').innerHTML=html;
	}

	function hide_options_form(){

		var html='';
		html = html + "<fieldset>";
		html = html + "<input type='button' id='options_show' name='options_show' value='<?php echo _mb('+ Options'); ?>' onclick='show_options_form();' />";
		html = html + "</fieldset>";

		document.getElementById('options_div').innerHTML=html;
	}

	// End of Form fields
	


	// -----------------  Display results --------------------

	function removeChildNodes(node) {
		while (node.childNodes.length > 0) {
			var childNode = node.firstChild;
			node.removeChild(childNode);
		}
	}

	function setTableHeader(text,titleLeft,titleRight,titleRecord) {
		document.getElementById('resultString').innerHTML = text;
		document.getElementById('titleLeft').innerHTML    = titleLeft;
		document.getElementById('titleRight').innerHTML   = titleRight;
		document.getElementById('titleRecord').innerHTML   = titleRecord;
		
		removeChildNodes(document.getElementById('resultTableBody'));
	}

	function addTableRow(leftText,rightText,idText,onClick) {
		var resultTableBoy        = document.getElementById('resultTableBody');
		var leftTableCell         = document.createElement('td');
		var rightTableCell        = document.createElement('td');
		var idTableCell        = document.createElement('td');
		
		var leftTableCellContent  = document.createElement('strong');
		var rightTableCellContent = document.createElement('em');
		var idTableCellContent = document.createElement('em');
		
		var tableRow              = document.createElement('tr');
				
		leftTableCellContent.innerHTML  = leftText;
		rightTableCellContent.innerHTML = rightText;
		idTableCellContent.innerHTML = idText;
		
		leftTableCell.appendChild(leftTableCellContent);
		rightTableCell.appendChild(rightTableCellContent);
		idTableCell.appendChild(idTableCellContent);
		
		tableRow.appendChild(leftTableCell);
		tableRow.appendChild(rightTableCell);
		tableRow.appendChild(idTableCell);
		
		tableRow.onclick = function () {
			eval(onClick);
		}

		if(resultTableBoy.childNodes.length % 2 !== 0) {
			tableRow.className += tableRow.className + ' alternate';
		}

		resultTableBoy.appendChild(tableRow);
	}

	function imageOn() {
		document.getElementById("progressIndicator").style.visibility = "visible";
		document.getElementById("progressIndicator").style.display = "block";
		document.getElementById("resultTable").style.visibility = "hidden";
		document.getElementById("resultTable").style.display = "none";
		document.getElementById("resultString").style.visibility = "hidden";
		document.getElementById("resultString").style.display = "none";
	}

	function imageOff() {
		document.getElementById("progressIndicator").style.visibility = "hidden";
		document.getElementById("progressIndicator").style.display = "none";
		document.getElementById("resultTable").style.visibility = "visible";
		document.getElementById("resultTable").style.display = "block";
		document.getElementById("resultString").style.visibility = "visible";
		document.getElementById("resultString").style.display = "block";
	}

	function noResult() {
		document.getElementById("resultTable").style.visibility = 'hidden';
		document.getElementById("resultString").innerHTML = noResultText;
	}

	function noResultV(val) {
		document.getElementById("resultTable").style.visibility = 'hidden';
		document.getElementById("resultString").innerHTML = noResultText+val;
	}



	// Display Catalog records returned by getrecords
	function displayRecords(catarray){
	
		if (catarray.length > 0) {
			setTableHeader(CatTitle, CatName, CatAbstract, RecId);

			for (var i = 0; i < catarray.length; i++) {
				var recordLink = '<a href=\''+catarray[i].url+'?request=GetRecordById&service=CSW&version=2.0.2&ElementSetName=full&Id=';
				//var recordLink = '<a href=\''+catarray[i].url+'?request=GetRecordById&id=';

				recordLink = recordLink+catarray[i].identifier+'';
				recordLink = recordLink+'>'+catarray[i].identifier+'<a>';
				
				addTableRow(catarray[i].title, catarray[i].abstractt, recordLink);
			}
		}
		else {
			noResultV(catarray[0]);
		}
		
	}
	


	//Build CSW query and search
	function mod_searchCSW(){
		imageOn();
		
		var simplesearchterm = document.getElementById('basic_search').value;
		try{
			getrecords_media = document.getElementById('opt_getrecords_media').value;
		}
		catch(err){
			//getrecords_media = 'get';
		}
		try{
			getrecords_query = document.getElementById('opt_getrecords_query').value;
		}
		catch(err){
			//getrecords_query = 'cql';
		}
		//check for simple or advanced
		if(global_is_advanced){
			//handle advanced search
			try{
				advanced_title = document.getElementById('adv_title').value;
				advanced_abstract = document.getElementById('adv_abstract').value;
				advanced_keywords = document.getElementById('adv_keyword').value;
				advanced_bb_latmin = document.getElementById('latmin').value;
				advanced_bb_latmax = document.getElementById('latmax').value;
				advanced_bb_lonmin = document.getElementById('lonmin').value;
				advanced_bb_lonmax = document.getElementById('lonmax').value;
			}
			catch(err){
				
			}
			
			parent.mb_ajax_json(phpUrl, {"command":"getrecordsadvanced","adv_title":adv_title,"adv_abstract":adv_abstract,"adv_keyword":adv_keyword,"latmin":latmin,"latmax":latmax,"lonmin":lonmin,"lonmax":lonmax}, function (json, status) {
				
				displayRecords(json.cats);
				
			});
				
		}
		else{
			//handle simple search
			
			parent.mb_ajax_json(phpUrl, {"command":"getrecordssimple", "search":simplesearchterm, "getrecordsmedia":getrecords_media,"getrecordsquery":getrecords_query }, function (json, status) {
			
			displayRecords(json.cats);
			imageOff();
			});
				
		}	
		
	}

		
	-->
	</script>
	<?php include("../include/dyn_css.php"); ?>
	<script type="text/javascript">

	var CatName = '<?php echo _mb("Record Title");?>';
	var CatAbstract = '<?php echo _mb("Record Abstract");?>';
	var CatTitle = '<?php echo _mb("Returned Results...");?>';
	var RecId = '<?php echo _mb("Record ID");?>';


	</script>

</head>

<body onLoad="imageOff();">
<h1><?php echo _mb("Catalog Search"); ?></h1>
<div class='note'>
	<?php echo _mb("Do a Basic or Advanced Search of Catalogs in the system. Please visit the Catalog Admin to add a Catalog"); ?>
	<?php new mb_notice("MM:TEST EXCEPTION2");?>
</div>

<div class="form-container">
<form id="capabilitiesForm" name="addURLForm" method="post" action="">
	<fieldset id="container_capabilities">
		<legend><?php echo _mb("Capabilities"); ?></legend>
			<p> 
				<label for="basic_search"><?php echo _mb("Search for "); ?>:</label> 
				<input type="text" id="basic_search" name="basic_search" />
				<br /> 
			</p>
	</fieldset>
		
		<!-- Show via inner HTML -->
		<div id="advanced_div">
		<fieldset>
			<input type="button" id="adv_search_show" name="adv_search_show" value="<?php echo _mb("+ Advanced"); ?>" onclick="show_advanced_form();" />
		</fieldset>
		</div>
		
		<div id="options_div">
		<fieldset>
			<input type="button" id="options_show" name="options_show" value="<?php echo _mb("+ Options"); ?>" onclick="show_options_form();" />
		</fieldset>
		</div>
		
		
		<input type="button" id="basic_search_submit" name="addCapURL" value="<?php echo _mb("Search CSW"); ?>" onclick="mod_searchCSW();" />
</form>
</div>
 
  
<p id="progressIndicator" name="progressIndicator">
	<img src="../img/indicator_wheel.gif" />
	<?php echo _mb("Loading"); ?> ... 
</p>


<h2 id="resultString" name="resultString"></h2>

<table id="resultTable" name="resultTable">
	<thead>
		<tr>
			<th id="titleLeft" name="titleLeft"></th>
			<th id="titleRight" name="titleRight"></th>
			<th id="titleRecord" name="titleRecord"></th>
		</tr>
	</thead>
	<tbody id="resultTableBody" name="resultTableBody">
	</tbody>
</table>
</body>

</html>
