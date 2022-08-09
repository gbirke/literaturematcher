<?php

require __DIR__ . '/vendor/autoload.php';

$client = new \GuzzleHttp\Client([
	'base_uri' => 'https://api.zotero.org/users/3658329/items',
	'headers' => [
		'Accept' => 'application/json',
		// TODO use valid key from environment, this one does not work
		'Zotero-API-Key' => 'Yt3z9Wg5SP2qADqrs1wpyGKy'
	]
]);


for($page=0; $page<=2250; $page+=100) {
	$response = $client->get('', ['query' => ['limit' => 100, 'start' => $page]]);
	$json = $response->getBody()->getContents();
	$filename = sprintf("zotero_%04d.json", $page);
	printf("Downloading %s\n", $filename);
	file_put_contents($filename, $json);
	if ($response->getStatusCode() === 429 ) {
		$backoffHeader = $response->getHeader('Retry-After');
		var_export($backoffHeader);
		die();
	}
	if ( $response->hasHeader('Backoff')) {
		$backoffHeader = $response->getHeader('Backoff');
		var_export($backoffHeader);
		die();
	}
}

