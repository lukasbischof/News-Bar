<?php
	error_reporting(0);
	date_default_timezone_set('Europe/Zurich');
	
	include_once 'rss.php';
	
	$maxAge = '1d';
	if (isset($_GET['maxAge'])) {
		$maxAge = $_GET['maxAge'];
	}
	
	$oneDay = 60 * 60 * 24;
	switch ($maxAge) {
		case '1d':
			$maxAge = $oneDay;
			break;
			
		case '2d':
			$maxAge = 2 * $oneDay;
			break;
			
		case '3d':
			$maxAge = 3 * $oneDay;
			break;
			
		case '1w':
			$maxAge = 7 * $oneDay;
			break;
			
		default:
			$maxAge = PHP_INT_MAX;
			break;
	}
	
	$ret = array();
	
	function iconName($name) {
		return 'http://' . $_SERVER['SERVER_NAME'] . explode('?', $_SERVER['REQUEST_URI'])[0] . "icons/$name";
	}
	
	// NZZ
	function getNZZ() {
		global $ret, $maxAge;
		
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
		
		$timestamp = strtotime($date);
		if ((time() - $maxAge) > $timestamp) {
			return;
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
		global $ret, $maxAge;
		
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
		
		loadRSS($ret, 'https://www.newsd.admin.ch/newsd/feeds/rss?lang=de&org-nr=1&topicword=&offer-nr=&catalogueElement=&kind=M,R&start_date=2017-02-02&end_date=', $info, $maxAge);
	}
	
	// NBC
	function getCNBC() {
		global $ret, $maxAge;
		
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
		
		loadRSS($ret, 'http://www.cnbc.com/id/100727362/device/rss/rss.html', $info, $maxAge);
	}
	
	// Tagi
	function getTagesanzeiger() {
		global $ret, $maxAge;
		$info = array(
			'src' => 'Tagesanzeiger',
			'icon' => array(
				'url' => iconName('tagi.png'),
				'url2' => iconName('tagi@2x.png'),
				'width' => 55,
				'height' => 11,
				'alt' => 'Tagesanzeiger'
			)
		);
		
		loadRSS($ret, 'http://www.tagesanzeiger.ch/rss_ticker.html', $info, $maxAge);
	}
	
	getNZZ();
	getAdmin();
	getCNBC();
	getTagesanzeiger();
	
	echo json_encode($ret);
?>

