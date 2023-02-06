<?php

final class DegewoBridge extends BridgeAbstract
{
	public const NAME = 'Degewo bridge';
	public const URI = 'https://www.degewo.de';
	public const DESCRIPTION = 'This bridge creates a Feed of Degewo non WBS rental offers';
	public const MAINTAINER = '3OW';
	public const CACHE_TIMEOUT = 1800;

	public const PARAMETERS = [
		'main' => [
			'json_url' => [
				'name' => 'JSON url',
				'type' => 'text',
				'defaultValue' => 'https://immosuche.degewo.de/de/search.json?utf8=%E2%9C%93&property_type_id=1&categories%5B%5D=1&property_number=&address%5Braw%5D=&address%5Bstreet%5D=&address%5Bcity%5D=&address%5Bzipcode%5D=&address%5Bdistrict%5D=&district=&price_switch=false&price_switch=on&price_from=&price_to=&price_from=&price_to=&price_radio=null&price_from=&price_to=&qm_radio=null&qm_from=&qm_to=&rooms_radio=null&rooms_from=&rooms_to=&features%5B%5D=&wbs_required=0&order=rent_total_without_vat_asc&',
			],
			'items' => [
				'name' => 'Items key',
				'type' => 'text',
				'defaultValue' => 'immos',
			],
			'title' => [
				'name' => 'Title key',
				'type' => 'text',
				'defaultValue' => 'address',
			],
			'url' => [
				'name' => 'Url key',
				'type' => 'text',
				'defaultValue' => 'property_path',
			],
			'uid' => [
				'name' => 'UID key',
				'type' => 'text',
				'defaultValue' => 'id',
			],
			'image' => [
				'name' => 'Image key',
				'type' => 'text',
				'defaultValue' => 'mobile_thumb_url',
			],
			'rent' => [
				'name' => 'Rent key',
				'type' => 'text',
				'defaultValue' => 'rent_total_with_vat',
			],
			'rooms' => [
				'name' => 'Rooms key',
				'type' => 'text',
				'defaultValue' => 'number_of_rooms',
			],
			'area' => [
				'name' => 'Area key',
				'type' => 'text',
				'defaultValue' => 'living_space',
			],
			'district' => [
				'name' => 'District key',
				'type' => 'text',
				'defaultValue' => 'address',
			],
			'notice' => [
				'name' => 'Notice key',
				'type' => 'text',
				'defaultValue' => 'full_address',
			],
		],
	];
	

	public function collectData()
	{
		$baseUrl = "https://immosuche.degewo.de";
		$jsonUrl = $this->getInput('json_url');
		$data = json_decode(getContents($jsonUrl), true);
		if (! $data) {
			throw new \Exception('Unable to decode json');
		}
		if (! isset($data[$this->getInput('items')])) {
			throw new \Exception('Unable to find any items');
		}

		foreach ($data[$this->getInput('items')] as $item) {
			$feedItem = new FeedItem();

			$feedItem->setTitle($item[$this->getInput('title')]." - ".$item[$this->getInput('area')]."m²" ?? $item[$this->getInput('notice')]);
			$feedItem->setURI($baseUrl.$item[$this->getInput('url')] ?? '');
			$feedItem->setUid((string)$item[$this->getInput('uid')] ?? '');
			//$feedItem->setEnclosures(["url" => $item[$this->getInput('image')]]);
			
			// get title
			$title = "<p><strong>Titel:</strong> ".($item[$this->getInput('title')] ?? '')."</p>";
			// get district
			$district = "<p><strong>Bezirk:</strong> ".($item[$this->getInput('district')] ?? '')."</p>";
			// get area
			$area = "<p><strong>Fläche:</strong> ".($item[$this->getInput('area')] ?? '')." qm</p>";
			// get rent
			$rent = "<p><strong>Miete:</strong> ".($item[$this->getInput('rent')] ?? '')."</p>";
			// get rooms
			$rooms = "<p><strong>Zimmer:</strong> ".($item[$this->getInput('rooms')] ?? '')."</p>";
			//embedd image
			$image = "<img src=\"".$item[$this->getInput('image')]."\" >";

			//put all into content
			$feedItem->setContent($title.$area.$rent.$rooms.$notice.$image);

			$this->items[] = $feedItem;
		}
	}
}
