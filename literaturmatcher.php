<?php

require __DIR__ . '/vendor/autoload.php';

$entries = [];

$container = require_once __DIR__ . '/src/container.php';

$sourceFile = $container->get(\Birke\LiteratureMatcher\ManualLiteratureFile::class);
$matcher = $container->get(\Birke\LiteratureMatcher\LiteratureMatcher::class);

foreach( $sourceFile->getLines() as $lineNumber => $line ) {
	$line = trim($line);
	if (!$line) continue;

	$entries[] = $matcher->getEntryForLine( $line, $lineNumber );
}

file_put_contents( 'app/src/literature.json', json_encode($entries, JSON_PRETTY_PRINT));
