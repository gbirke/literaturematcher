<?php

require __DIR__ . '/vendor/autoload.php';

$db = \Doctrine\DBAL\DriverManager::getConnection([ 'url' => 'sqlite://./literature.db']);

$zoteroCount = $db->executeQuery("SELECT COUNT(*) from zotero_entry")->fetchOne();

$fulltextQuery = $db->prepare( "SELECT data from zotero_entry ze JOIN zotero_titles zt ON ze.id=zt.rowid WHERE zt.title MATCH :title" );

$entries = [];
$foundFulltext = 0;
foreach(file('Literaturverzeichnis_Diss.txt') as $lineNumber => $line) {
	$line = trim($line);
	if (!$line) continue;
	try {
		$entry = \Birke\LiteratureMatcher\LiteratureParser::parseEntry($line);
	} catch (TypeError) {
			$entries[] = [
				'lineNumber' => $lineNumber,
				'line' => $line,
				'error' => printf("Parse Error" ),
				'manualEntry' => [],
				'zoteroEntry' => []
		];
		continue;
	}
	$zoteroEntry = [];
	if ( !empty($entry['title'])) {
		$titleForQuery = preg_replace("/[^\\p{L} ]/u", '', $entry['title']);
		if (!trim($titleForQuery)) {
			echo "\n Empty search title: $line, {$entry['title']} \n";
			continue;
		}
		$zoteroEntryData = $fulltextQuery->executeQuery(['title' => $titleForQuery])->fetchOne();
		if ( $zoteroEntryData ) {
			$foundFulltext++;
			$zoteroEntry = json_decode($zoteroEntryData,true);
			//printf("Found zotero '%s' for '%s' (%s)\n", $json['title'], $line, $titleForQuery);
		}

	} else {
		$entries[] = [
			'lineNumber' => $lineNumber,
			'line' => $line,
			'error' => 'No title found',
			'manualEntry' => [],
			'zoteroEntry' => []
		];
		continue;
	}

	$entries[] = [
		'lineNumber' => $lineNumber,
		'line' => $line,
		'manualEntry' => $entry,
		'zoteroEntry' => $zoteroEntry
	];
}

file_put_contents( 'app/src/literature.json', json_encode($entries, JSON_PRETTY_PRINT));
