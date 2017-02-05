<?php
	error_reporting(0);
	
	include_once 'rss.php';
	
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
		
		$date = $main->getElementsByTagName('time');
		if ($date->length > 0) {
			$date = $date[0];
			$date = $date->getAttribute('datetime');
		} else {
			$date = date('D M d Y H:i:s O');
		}
		
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
							'url2' => iconName('NZZ@2x.png'),
							'width' => 22,
							'height' => 11,
							'alt' => 'NZZ'
						),
						'date' => $date
					);
					break;
				}
			}
		}
	}
	
	
	// admin
	function getAdmin() {
		global $ret;
		
		$info = array(
			'src' => 'Admin',
			'icon' => array(
				'url' => iconName('admin.png'),
				'url2' => iconName('admin@2x.png'),
				'width' => 10,
				'height' => 11,
				'alt' => 'CH Admin'
			)
		);
		
		loadRSS($ret, 'https://www.newsd.admin.ch/newsd/feeds/rss?lang=de&org-nr=1&topicword=&offer-nr=&catalogueElement=&kind=M,R&start_date=2017-02-02&end_date=', $info);
	}
	
	// NBC
	function getCNBC() {
		global $ret;
		
		$info = array(
			'src' => 'CNBC',
			'icon' => array(
				'url' => iconName('CNBC.png'),
				'url2' => iconName('CNBC@2x.png'),
				'width' => 30,
				'height' => 11,
				'alt' => 'CNBC'
			)
		);
		
		loadRSS($ret, 'http://www.cnbc.com/id/100727362/device/rss/rss.html', $info);
	}
	
	getNZZ();
	getAdmin();
	getCNBC();
	
	echo json_encode($ret);
?>

