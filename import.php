<?php
$db = new PDO("mysql:host=localhost;dbname=fever", "fever", "fever");

$json = json_decode(file_get_contents("starred.json"), true);
$items = $json['items'];
var_dump($json);
foreach($items as $item){
	$feedUrl = substr($item['origin']['streamId'], 5);
	$stmt = $db->prepare("select id from fever_feeds where url=?");
	$stmt->execute([$feedUrl]);
	$feedId = (int)$stmt->fetchColumn(0);
	if($feedId > 0){
		$stmt = $db->prepare("insert into fever_items set feed_id = ?, uid = ?, title = ?, author = ?, description = ?, link = ?, url_checksum = ?, read_on_time = 0, is_saved = 1, created_on_time = ?, added_on_time = ?");
		$stmt->execute([
			$feedId, $item['id'], $item['title'], $item['author'], $item['content']['content'], $item['alternate']['href'], sprintf('%u', crc32($item['alternate']['href'])), $item['published'], $item['published'],
		]);		
	}
}