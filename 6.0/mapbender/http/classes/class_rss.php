<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_administration.php";
require_once dirname(__FILE__) . "/../classes/class_rss_item.php";

/**
 * Creates an RSS Feed.
 */
class Rss {
    var $filename;
    var $channel_url;
    var $channel_title;
    var $channel_description;
    var $channel_lang;
    var $channel_copyright;
    var $channel_date;
    var $channel_creator;
    var $channel_subject;   
    var $image_url;
    //var $maxEntries;

    //$this->maxEntries = 10;

    protected $items = array();
    protected $nritems;
	
	//const MAX_ENTRIES = 10;
	
	protected function createItem () {
		return new RssItem();
	}
	//function to append item at the end of the item list
	public function append () {
		if (func_num_args() === 0) {
			$item = $this->createItem();
		}
		else {
			$item = func_get_arg(0);
		};
		$this->items[]= $item;
		$this->nritems++;
		$this->deleteLast(15+3);//offset of 3 for channel: title/link/description
		return $item;
	}
	//function to append item at the top of the item list - maybe most recent !
	public function appendTop () {
		if (func_num_args() === 0) {
			$item = $this->createItem();
		}
		else {
			$item = func_get_arg(0);
		}
		array_unshift($this->items, $item);
		$this->nritems++;
		$this->deleteLast(15+3);
		return $item;
	}

	public function deleteLast ($maxEntries) {
		for ($k=$maxEntries-1; $k<$this->nritems; $k++){
			$this->items[$k] = null;
			$e = new mb_notice("delete georss items: items[".$k."]=null");
		}
		if ($this->nritems > ($maxEntries-3)){
			//reduce to maxEntries-3
			$this->nritems = $maxEntries-3;
		}
		return true;
	}
	
	public function saveAsFile() {
		if (func_num_args() === 1) {
			$pathAndFilename = func_get_arg(0);
		}
		else {
			if ($this->filename) {
				$pathAndFilename = $this->filename;
			}
			else {
				new mb_exception(__FILE__ . 
					": saveAsFile(): must specify a filename!");
				return false;
			}
		}
		//delete all entries which are more than MAX_ENTRIES from RSS!
		
		return administration::saveFile($pathAndFilename, $this->__toString());
	}	
	
    public function __construct() {
        $this->nritems=0;
        $this->channel_url=LOGIN;
        $this->channel_title="Mapbender GeoRSS";
        $this->channel_description="New and updated WMS";
        $this->channel_lang='';
        $this->channel_copyright='';
        $this->channel_date='';
        $this->channel_creator='';
        $this->channel_subject='';
        $this->image_url='';
    }   
	
	public function setTitle ($title) {
        $this->channel_title=$title;
	}
	
	public function setDescription ($description) {
        $this->channel_description=$description;
	}
	
	public function setUrl ($url) {
        $this->channel_url=$url;
	}
	
    public function setChannel($url, $title, $description, $lang, $copyright, $creator, $subject) {
        $this->channel_url=$url;
        $this->channel_title=$title;
        $this->channel_description=$description;
        $this->channel_lang=$lang;
        $this->channel_copyright=$copyright;
        //$this->channel_date=date("Y-m-d").'T'.date("H:i:s").'+01:00';
	$timestamp = ($timestamp==null) ? time() : $timestamp;
        /*** Mon, 02 Jul 2009 11:36:45 +0000 ***/
        $this->channel_date = date(DATE_RSS, $timestamp);

        $this->channel_creator=$creator;
        $this->channel_subject=$subject;
    }

    public function setImage($url) {
        $this->image_url=$url;  
    }

    public function setItem($rssItem) {
    	if (is_a($rssItem, "RssItem")) {
		#array_unshift($this->items, $rssItem);
    		$this->items[]= $rssItem;
	        $this->nritems++;   
			return true;
    	}
		new mb_exception(__FILE__ . 
			": setItem(): parameter is not an RSS item!");
    }
	
	public function getItem ($index) {
		if (!is_numeric($index)) {
			return null;
		}
		$i = intval($index);
		if ($i >= 0 && $i < count($this->items)) {
			return $this->items[$i];
		}
		return null;
	}

    public function __toString () {
        $output =  '<?xml version="1.0" encoding="' . CHARSET . '"?>'."\n";
        $output .= '<rss ' . $this->getNamespaceString() . ' version="2.0">'."\n";
//        $output .= '<rdf:RDF ' . $this->getNamespaceString() . '>'."\n";
//        $output .= '<channel rdf:about="'.htmlentities($this->channel_url, ENT_QUOTES, CHARSET)	.'">'."\n";
        $output .= '<channel>'."\n";
        $output .= '<title>'.$this->channel_title.'</title>'."\n";
        $output .= '<link>'.htmlentities(
				$this->channel_url,
				ENT_QUOTES,
				CHARSET
			).'</link>'."\n";
        $output .= '<description>'.$this->channel_description.'</description>'."\n";
#        $output .= '<dc:language>'.$this->channel_lang.'</dc:language>'."\n";
#        $output .= '<dc:rights>'.$this->channel_copyright.'</dc:rights>'."\n";
#        $output .= '<dc:date>'.$this->channel_date.'</dc:date>'."\n";
#        $output .= '<dc:creator>'.$this->channel_creator.'</dc:creator>'."\n";
#        $output .= '<dc:subject>'.$this->channel_subject.'</dc:subject>'."\n";

#        $output .= '<items>'."\n";
#        $output .= '<rdf:Seq>';
#        for($k=0; $k<$this->nritems; $k++) {
#            $output .= '<rdf:li rdf:resource="'.
#				htmlentities(
#					$this->items[$k]->getUrl(),
#					ENT_QUOTES,
#					CHARSET
#				)
#				.'"/>'."\n"; 
#        };    
#        $output .= '</rdf:Seq>'."\n";
#        $output .= '</items>'."\n";
#        $output .= '<image rdf:resource="'.$this->image_url.'"/>'."\n";
        for($k=0; $k<$this->nritems; $k++) {
            $output .= $this->items[$k];  
        };
        $output .= '</channel>'."\n";
//        $output .= '</rdf:RDF>'."\n";
        $output .= '</rss>'."\n";
        return $output;
    }
	
	protected function getNamespaceString () {
		return "";
#		return 'xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" ' . 
#			'xmlns="http://purl.org/rss/1.0/" ' . 
#			'xmlns:slash="http://purl.org/rss/1.0/modules/slash/" ' . 
#			'xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" ' . 
#			'xmlns:dc="http://purl.org/dc/elements/1.1/" ' . 
#			'xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" ' . 
#			'xmlns:admin="http://webns.net/mvcb/" ' . 
#			'xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0"';
	}
}

?>
