<?php
echo "<table width='100%'><tr align='center'><td style='padding:30px'><img alt='logo' src='../img/Mapbender_logo_and_text.png'></td></tr>" . 
	"<tr align='center'><td style='padding:30px'>".MB_VERSION_NUMBER . " " . strtolower(MB_VERSION_APPENDIX) . "</strong>..." .
	"loading application '" . $this->guiId . "'</td></tr>".
	"<tr align='center'><td style='padding:30px'><img alt='indicator wheel' src='../img/indicator_wheel.gif'></td></tr>" . 
	"<tr align='center'><td style='padding:30px'><strong>please wait...</strong></td></tr></table>";
?>
