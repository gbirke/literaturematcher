<?php

use Birke\LiteratureMatcher\LiteratureMatcher;
use Birke\LiteratureMatcher\ManualLiteratureFile;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
$container = require __DIR__ . '/../src/container.php';
$app = \DI\Bridge\Slim\Bridge::create($container);

$app->get('/entries', function (Response $response, ManualLiteratureFile $sourceFile, LiteratureMatcher $matcher ) {
	$entries = [];
	foreach( $sourceFile->getLines() as $lineNumber => $line ) {
		$line = trim($line);
		if (!$line) continue;

		$entries[] = $matcher->getEntryForLine( $line, $lineNumber );
	}
	$response->getBody()->write(json_encode($entries));
	return $response->withHeader('Content-Type','application/json');
});

$app->get('/entry/{id}', function ($id, Response $response, ManualLiteratureFile $sourceFile, LiteratureMatcher $matcher ) {
	$lineNumber = intval($id);
	$line = $sourceFile->getLine($lineNumber);

	$entry = $matcher->getEntryForLine( $line, $lineNumber );

	$response->getBody()->write(json_encode($entry));
	return $response->withHeader('Content-Type','application/json');
});

$app->get('/bibtex-entry/{id}', function ($id, Response $response, ManualLiteratureFile $sourceFile, LiteratureMatcher $matcher ) {
	$lineNumber = intval($id);
	$line = $sourceFile->getLine($lineNumber);

	// TODO move to use case
	$rawEntry = $matcher->getEntryForLine( $line, $lineNumber );
	$factory = new \Birke\LiteratureMatcher\EntryFactory();
	$entry = $factory->build($rawEntry['manualEntry']);
	$bt = new \Geissler\Converter\Standard\BibTeX\Creator();
	$entries = new \Geissler\Converter\Model\Entries();
	$entries->setEntry($entry);
	$bt->create($entries);

	$filename = $entry->getCitationLabel() ?: 'epxort';
	$response->getBody()->write($bt->retrieve());
	return $response->withHeader('Content-Type','text/plain')
		->withHeader('Content-Disposition', sprintf('attachment; filename="%s.bib"', $filename));
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();
