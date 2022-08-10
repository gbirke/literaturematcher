<?php

require __DIR__ . '/vendor/autoload.php';

$container = include __DIR__ . '/src/container.php';

$client = $container->get('zotero.client');

// TODO determine end page from LINK header
for($page=0; $page<=2450; $page+=100) {
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

