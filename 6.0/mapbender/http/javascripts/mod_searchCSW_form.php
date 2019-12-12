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

require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
//ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<?php printf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\" />",CHARSET);	?>
	<title>Search CSW</title>
	
	<script type="javascript">
	function mod_searchCSWSimple()
	{
		alert('Call');
	}

	function show_advanced()
	{
		document.container_advanced.InnerHTML;
	}

	function build_query_from_search()
	{
	}


	</script>
</head>
<body>
	<h1><?php echo _mb("Search CSW"); ?></h1>
	<p><?php echo _mb("Search catalog metadata"); ?></p>

	<form id="cswSimpleSearchForm" name="addURLForm" method="post" action="">
		<fieldset id="container_simple">
		<legend>Basic Search</legend>
		<p>
			<label for="basic_search"><?php echo _mb("Search for "); ?>:</label> 
			<input type="text" id="basic_search" name="basic_search" />
			<br /> 
			<input type="button" id="basic_search_submit" name="addCapURL" value="<?php echo _mb("Search CSW"); ?>" onclick="mod_searchCSWSimple();" />
		</p>
		</fieldset>
		<a href="#" onclick='show_advanced()'>+ Advanced</a>
		<!-- Show via inner HTML -->
		<fieldset id="container_advanced">
		<legend>Advanced Search</legend>
			<fieldset id="cont_adv_summary">
				<label for="adv_title"><?php echo _mb("Title "); ?>:</label> 
				<input type="text" id="adv_title" name="adv_title" />
				<label for="adv_abstract"><?php echo _mb("Abstract"); ?>:</label> 
				<input type="text" id="adv_abstract" name="adv_abstract" />
				<label for="adv_keywords"><?php echo _mb("Keywords"); ?>:</label> 
				<input type="text" id="adv_keywords" name="adv_keywords" />
				<br /> 
			</fieldset>
			<fieldset id="cont_adv_bbox">
				<label for="adv_title"><?php echo _mb("Lat-Min"); ?>:</label> 
				<input type="text" id="adv_title" name="adv_title" />
				<label for="adv_abstract"><?php echo _mb("Lat-Maz"); ?>:</label> 
				<input type="text" id="adv_abstract" name="adv_abstract" />
				<label for="adv_keywords"><?php echo _mb("Lon-Min"); ?>:</label> 
				<input type="text" id="adv_keywords" name="adv_keywords" />
				<label for="adv_keywords"><?php echo _mb("Lon-Max"); ?>:</label> 
				<input type="text" id="adv_keywords" name="adv_keywords" />
				<br /> 
			</fieldset>
			<input type="button" id="basic_search_submit" name="addCapURL" value="<?php echo _mb("Search CSW"); ?>" onclick="mod_searchCSWSimple();" />
		</fieldset>
	</form>
</body>