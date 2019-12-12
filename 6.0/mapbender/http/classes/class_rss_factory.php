<?php
# License:
# Copyright (c) 2009, Open Source Geospatial Foundation
# This program is dual licensed under the GNU General Public License 
# and Simplified BSD license.  
# http://svn.osgeo.org/mapbender/trunk/mapbender/license/license.txt

require_once dirname(__FILE__) . "/../../core/globalSettings.php";
require_once dirname(__FILE__) . "/../classes/class_rss.php";

class RssFactory {

	protected function createRss () {
		$rss = new Rss();
		return $rss;
	}

	protected function createRssItem () {
		$rssItem = new RssItem();
		return $rssItem;
	}

	public function loadOrCreate ($pathAndFilename) {
		$rss = $this->createFromUrl($pathAndFilename);
		if (is_null($rss)) {
			$rss = $this->createAt($pathAndFilename);
			if (is_null($rss)) {
				return null;
			}
		}
		return $rss;
	}
		
	public function createAt ($pathAndFilename) {
		$rss = $this->createRss();
		if ($rss->saveAsFile($pathAndFilename)) {
			$rss->filename = $pathAndFilename;
			return $rss;
		};
		return null;
	}

	public function createFromUrl ($url) {
		$rss = $this->parseDocument($url, $this->createRss());
		if (is_null($rss)) {
			return null;
		}
		$rss->filename = $url;
		return $this->parseItems($url, $rss);
	}
	
	protected function parseDocument ($url, $rss) {
		$domxpath = $this->createDomXpathFromUrl($url, $rss);
		if (is_null($domxpath)) {
			return null;
		}
		
		$nodeList = $domxpath->query("/rss/channel/title");
		if ($nodeList->length === 1) {
			$rss->channel_title = trim($nodeList->item(0)->nodeValue);
		}
		else {
			new mb_warning(__FILE__ . ": load(): Could not find title in RSS feed.");
		}

		$nodeList = $domxpath->query("/rss/channel/description");
		if ($nodeList->length === 1) {
			$rss->channel_description = trim($nodeList->item(0)->nodeValue);
		}
		else {
			new mb_warning(__FILE__ . ": load(): Could not find description in RSS feed.");
		}

		$nodeList = $domxpath->query("/rss/channel/link");
		if ($nodeList->length === 1) {
			$rss->channel_url = trim($nodeList->item(0)->nodeValue);
		}
		else {
			new mb_warning(__FILE__ . ": load(): Could not find url in RSS feed.");
		}
		return $rss;
	}

	protected function createDomXpathFromUrl ($url) {
		$dom = new DOMDocument();
		$dom->preserveWhitespace = false;
		$success = $dom->load($url);
		if (!$success) {
			new mb_exception(__FILE__ . ": load(): Could not load " . $url);
			return null;
		}

		return new DOMXPath($dom);		
	}
	
	protected function parseItems ($url, $rss) {
		$domxpath = $this->createDomXpathFromUrl($url, $rss);
		if (is_null($domxpath)) {
			return null;
		}
		
		$nodeList = $domxpath->query("/rss/channel/item");
		foreach ($nodeList as $node) {
			$item = $this->createRssItem();
			$item = $this->parseItem($node, $item);
			$rss->append($item);
		}
		return $rss;
	}
	
	protected function parseItem ($node, $item) {
		foreach ($node->childNodes as $childNode) {
			switch ($childNode->tagName) {
				case "title":
					$item->setTitle(trim($childNode->nodeValue));
					break;

				case "description":
					$item->setDescription(trim($childNode->nodeValue));
					break;

				case "link":
					$item->setUrl(trim($childNode->nodeValue));
					break;
					
				case "pubDate":
					$item->setPubDate(trim($childNode->nodeValue));
					break;	
					
			}
		}
		return $item;
	}
}

?>
