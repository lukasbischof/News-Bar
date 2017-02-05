<?php
	error_reporting(0);
	
	$ret = array();
	
	function iconName($name) {
		return 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "icons/$name";
	}
	
	// NZZ
	function getNZZ() {
		global $ret;
		
		$site = file_get_contents('https://www.nzz.ch/briefing/');
		if ($site === false) {
			die('{error: "can\'t load NZZ site"}');
		}
		
		$doc = new DOMDocument();
		$doc->loadHTML($site);
		
		$main = $doc->getElementsByTagName('main');
		
		if ($main->length == 0) {
			die('{error: "can\'t load main"}');
		}
		
		$main = $main[0];
		
		$content = $main->getElementsByTagName('p');
		if ($content->length == 0) {
			die('{error: "can\'t load messages"}');
		}
		
		foreach ($content as $p) {
			$strong = $p->getElementsByTagName('strong');
			
			if ($strong->length == 0) {
				$strong = $p->getElementsByTagName('b');
			}
			
			if ($strong->length > 0) {
				foreach ($strong as $title) {
					$val = $title->nodeValue;
					if (empty($val)) {
						continue;
					}
					
					$ret[] = array(
						"src" => "NZZ",
						"title" => $val,
						'url' => 'https://www.nzz.ch/briefing/',
						'icon' => array(
							'url' => iconName('NZZ.png'),
							'width' => 22,
							'height' => 11,
							'alt' => 'NZZ'
						)
					);
					break;
				}
			}
		}
	}
	
	
	// admin
	function getAdmin() {
		global $ret;
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->load('https://www.newsd.admin.ch/newsd/feeds/rss?lang=de&org-nr=1&topicword=&offer-nr=&catalogueElement=&kind=M,R&start_date=2017-02-02&end_date=');
		
		//get elements from "<channel>"
		$channel = $xmlDoc->getElementsByTagName('channel')->item(0);
		$channel_title = $channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
		$channel_link = $channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
		$channel_desc = $channel->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
		
		//get and output "<item>" elements
		$x = $xmlDoc->getElementsByTagName('item');
		for ($i = 0; $i < $x->length; $i++) {
			$item_title = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
			$item_link = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
			$item_desc = $x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
			
			$ret[] = array(
			    'src' => "Admin",
			    'title' => $item_title,
			    'url' => 'http://www.admin.ch',
			    'icon' => array(
					'url' => iconName('admin.png'),
					'width' => 35,
					'height' => 11,
					'alt' => 'CH Admin'
				)
			);
		}
	}
	
	// NBC
	function getCNBC() {
		global $ret;
		$xmlDoc = new DOMDocument();
		$xmlDoc->load('http://www.cnbc.com/id/100727362/device/rss/rss.html');
		
		//get elements from "<channel>"
		$channel = $xmlDoc->getElementsByTagName('channel')->item(0);
		$channel_title = $channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
		$channel_link = $channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
		$channel_desc = $channel->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
		
		//get and output "<item>" elements
		$x = $xmlDoc->getElementsByTagName('item');
		for ($i = 0; $i < $x->length; $i++) {
			$item_title=$x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
			$item_link=$x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
			$item_desc=$x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
			
			$ret[] = array(
			    'src' => "CNBC",
			    'title' => $item_title,
			    'url' => $item_link,
			    'icon' => array(
					'url' => iconName('CNBC.png'),
					'width' => 30,
					'height' => 11,
					'alt' => 'CNBC'
				)
			);
		}
	}
	
	getNZZ();
	getAdmin();
	getCNBC();
	
	echo json_encode($ret);
?>
