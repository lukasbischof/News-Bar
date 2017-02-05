<?php
	function loadRSS(&$ret, $url, $info) {
		$xmlDoc = new DOMDocument();
		$xmlDoc->load($url);
		
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
			$item_date = $x->item($i)->getElementsByTagName('pubDate')->item(0)->childNodes->item(0)->nodeValue;
			
			$ret[] = array(
			    'src' => $info['src'],
			    'title' => $item_title,
			    'url' => $item_link,
			    'icon' => $info['icon'],
			    'date' => $item_date
			);
		}
	}	
?>

