<?php
# $Id: mb_validateInput.php 543 2006-06-20 07:16:54Z vera_schulze $
# http://www.mapbender.org/index.php/mb_validateInput.php
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

function mb_validateInput($text){
	$match = array(
		0 => "/drop/i",
		1 => "/--/"
	);
	for($i=0; $i<count($match);$i++){
		if( preg_match($match[$i], $text) == true){
			return null;
			exit;
		}
		else{
			return $text;
		}
	}
}
?>