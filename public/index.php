<?php

use Birke\LiteratureMatcher\LiteratureMatcher;
use Birke\LiteratureMatcher\ManualLiteratureFile;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ . '/../src/container.php';
$app = \DI\Bridge\Slim\Bridge::create($container);

$app->get('/entries', function (Request $request, Response $response, ManualLiteratureFile $sourceFile, LiteratureMatcher $matcher ) {
	$entries = [];
	foreach( $sourceFile->getLines() as $lineNumber => $line ) {
		$line = trim($line);
		if (!$line) continue;

		$entries[] = $matcher->getEntryForLine( $line, $lineNumber );
	}
	$response->getBody()->write(json_encode($entries));
	return $response->withHeader('Content-Type','application/json');
});


$app->run();
