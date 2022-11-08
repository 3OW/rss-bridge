<?php

final class HowogeBridge extends BridgeAbstract
{
	public const NAME = 'Howoge bridge';
	public const URI = 'https://www.howoge.de/wohnungen-gewerbe/wohnungssuche.html?tx_howsite_json_list%5Bpage%5D=1&tx_howsite_json_list%5Blimit%5D=12&tx_howsite_json_list%5Blang%5D=&tx_howsite_json_list%5Brent%5D=&tx_howsite_json_list%5Barea%5D=&tx_howsite_json_list%5Brooms%5D=egal&tx_howsite_json_list%5Bwbs%5D=wbs-not-necessary';
	public const DESCRIPTION = 'This bridge creates a Feed of Howoge non WBS rental offers';
	public const MAINTAINER = '3OW';
	public const CACHE_TIMEOUT = 1800;

	public const PARAMETERS = [
		'main' => [
			'json_url' => [
				'name' => 'JSON url',
				'type' => 'text',
				'defaultValue' => 'https://www.howoge.de/?type=999&tx_howsite_json_list%5Baction%5D=immoList&tx_howsite_json_list%5Blimit%5D=120&tx_howsite_json_list%5Brent%5D=&tx_howsite_json_list%5Bwbs%5D=wbs-not-necessary&tx_howsite_json_list%5Bpage%5D=',
			],
			'items' => [
				'name' => 'Items key',
				'type' => 'text',
				'defaultValue' => 'immoobjects',
			],
			'title' => [
				'name' => 'Title key',
				'type' => 'text',
				'defaultValue' => 'title',
			],
			'url' => [
				'name' => 'Url key',
				'type' => 'text',
				'defaultValue' => 'link',
			],
			'uid' => [
				'name' => 'UID key',
				'type' => 'text',
				'defaultValue' => 'uid',
			],
			'image' => [
				'name' => 'Image key',
				'type' => 'text',
				'defaultValue' => 'image',
			],
			'rent' => [
				'name' => 'Rent key',
				'type' => 'text',
				'defaultValue' => 'rent',
			],
			'rooms' => [
				'name' => 'Rooms key',
				'type' => 'text',
				'defaultValue' => 'rooms',
			],
			'area' => [
				'name' => 'Area key',
				'type' => 'text',
				'defaultValue' => 'area',
			],
			'district' => [
				'name' => 'Area key',
				'type' => 'text',
				'defaultValue' => 'district',
			],
			'notice' => [
				'name' => 'Notice key',
				'type' => 'text',
				'defaultValue' => 'notice',
			],
		],
	];
	

	public function collectData()
	{
		$baseUrl = "https://www.howoge.de";
		$jsonUrl = $this->getInput('json_url');
		
		for ($i = 1; $i <=10 ; $i++) {
  			$data = json_decode(getContents($jsonUrl.$i), true);
			if (! $data) {
				throw new \Exception('Unable to decode json');
			}
			if (! isset($data[$this->getInput('items')])) {
				break;
			}

			foreach ($data[$this->getInput('items')] as $item) {
				$feedItem = new FeedItem();

				$feedItem->setTitle($item[$this->getInput('notice')] ?? $item[$this->getInput('title')]);
				$feedItem->setURI($baseUrl.$item[$this->getInput('url')] ?? '');
				$feedItem->setUid((string)$item[$this->getInput('uid')] ?? '');
				$feedItem->setEnclosures(["url" => $baseUrl.$item[$this->getInput('image')]]);
				
				// get the different elements
				$title = "<p><strong>Titel:</strong> ".($item[$this->getInput('title')] ?? '')."</p>";
				$district = "<p><strong>Bezirk:</strong> ".($item[$this->getInput('district')] ?? '')."</p>";
				$area = "<p><strong>Fläche:</strong> ".($item[$this->getInput('area')] ?? '')." qm</p>";
				$rent = "<p><strong>Miete:</strong> ".($item[$this->getInput('rent')] ?? '')."</p>";
				$rooms = "<p><strong>Zimmer:</strong> ".($item[$this->getInput('rooms')] ?? '')."</p>";
				$image = "<img src=\"".$baseUrl.$item[$this->getInput('image')]."\" >";
				//put everything into content
				$feedItem->setContent($title.$district.$area.$rent.$rooms.$image);

				$this->items[] = $feedItem;
			}
		}
	}
}
