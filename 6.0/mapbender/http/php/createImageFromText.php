<?php
# $Id: createImageFromText.php 7983 2011-07-20 14:18:57Z marc $
# Modul Maintainer Christoph Baudson
# http://www.mapbender.org/index.php/createImageFromText
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

$text = $_GET["text"];
$angle = intval($_GET["angle"]);

$text_x = 4;
$text_y = 0;
$rect_w = 7 * mb_strlen($text) + $text_x;
$rect_h = 14 + $text_y;

$im = imagecreate($rect_w, $rect_h);
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
imagestring($im, 2, $text_x, $text_y, $text, $black);
$im = imagerotate($im, $angle, 0);

Header("Content-type:image/png");
imagepng($im);
imagedestroy($im);
?>
