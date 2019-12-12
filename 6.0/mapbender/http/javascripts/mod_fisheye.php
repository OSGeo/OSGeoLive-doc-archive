<?php
# $Id: mod_fisheye.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/index.php/mod_center1.php
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
include(dirname(__FILE__)."/../include/dyn_js.php");
?>

var fisheyeElementArray = fisheyeElements.split(",");
var fisheyeString = "#" + fisheyeElementArray.join(",#");

eventInit.register(function() {
	var $newOuterContainer = $('<div id="fisheyeOuter">');
	var $newInnerContainer = $('<div class="fisheyeContainer">');
	$(fisheyeString)
	  .attr('style', '')
	  .wrapAll($newOuterContainer)
	  .wrapAll($newInnerContainer)
	  .wrap('<a href="#" class="fisheyeItem">')
	  .each(function() {
	  	//$( this ).before('<span>' + $( this ).attr('title') + '</span>');
	  	$( this ).before('<span></span>');
	  })
	
	$newOuterContainer.appendTo( '#<?php echo $e_id; ?>' );
	
	$('#fisheyeOuter').Fisheye(
		{
			maxWidth: 32,
			items: 'a',
			itemsText: 'span',
			container: '.fisheyeContainer',
			itemWidth: 28,
			proximity: 20,
			halign : 'center',
			valign : 'bottom'
		}
	);
	
});



