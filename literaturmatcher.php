<?php

require __DIR__ . '/vendor/autoload.php';

$entries = [];
$foundFulltext = 0;

$sourceFile = new \Birke\LiteratureMatcher\ManualLiteratureFile('Literaturverzeichnis_Diss.txt');
$matcher = new \Birke\LiteratureMatcher\LiteratureMatcher( new \Birke\LiteratureMatcher\ZoteroRepository([ 'url' => 'sqlite://./literature.db'] ) );
foreach( $sourceFile->getLines() as $lineNumber => $line ) {
	$line = trim($line);
	if (!$line) continue;

	$entries[] = $matcher->getEntryForLine( $line, $lineNumber );
}

file_put_contents( 'app/src/literature.json', json_encode($entries, JSON_PRETTY_PRINT));
