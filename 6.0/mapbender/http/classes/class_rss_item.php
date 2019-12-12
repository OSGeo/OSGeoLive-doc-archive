<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_rss.php");

class RssItem {
	protected $title;
	protected $description;
	protected $url;
	protected $pubDate;
	
	public function __construct () {
		//$this->title = "dummytitle";
		//$this->description = "dummydescription";
		//$this->url = "dummyurl";
		//$this->pubDate = "dummypubdate";
	}
	
	public function setUrl ($url) {
        $this->url = $url;
	}

	public function getUrl () {
        return $this->url;
	}

	public function setTitle ($title) {
        $this->title = $title;
	}

	
	public function setPubDate ($pubDate) {
        $this->pubDate = $pubDate;
	}
	
	public function setDescription ($description) {
        $this->description = $description;
	}
	
	public function __toString () {
        return '<item>'."\n" . 
        	$this->getItemString() . '</item>'."\n";  
#        return '<item rdf:about="' . $this->url . '">'."\n" . 
#        	$this->getItemString() . '</item>'."\n";  
	}
	
	protected function getItemString () {
        return '<title>' . $this->title . '</title>' . "\n" . 
			'<link>' . htmlentities($this->url, ENT_QUOTES, CHARSET) . '</link>' . "\n" . 
			'<description>' . $this->description . '</description>' . "\n" . '<pubDate>' . $this->pubDate . '</pubDate>' . "\n";
//			'<feedburner:origLink>' . $this->url . '</feedburner:origLink>' . 
//			"\n";
	}
}
?>
