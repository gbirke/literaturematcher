<?php

require __DIR__ . '/vendor/autoload.php';

$db = \Doctrine\DBAL\DriverManager::getConnection([ 'url' => 'sqlite://./literature.db']);

$zoteroCount = $db->executeQuery("SELECT COUNT(*) from zotero_entry")->fetchOne();

$fulltextQuery = $db->prepare( "SELECT data from zotero_entry ze JOIN zotero_titles zt ON ze.id=zt.rowid WHERE zt.title MATCH :title" );

printf("%d eintraege in Zotero\n", $zoteroCount);

$manualLit = [];
$foundFulltext = 0;
foreach(file('Literaturverzeichnis_Diss.txt') as $line) {
	$line = trim($line);
	if (!$line) continue;
	try {
		$entry = \Birke\LiteratureMatcher\LiteratureParser::parseEntry($line);
	} catch (TypeError) {
		printf("Could not parse '%s'\n", $line);
		continue;
	}
	if ( !empty($entry['title'])) {
		$titleForQuery = preg_replace("/[^\\p{L} ]/u", '', $entry['title']);
		if (!trim($titleForQuery)) {
			echo "\n Empty search title: $line, {$entry['title']} \n";
			continue;
		}
		$zoteroEntry = $fulltextQuery->executeQuery(['title' => $titleForQuery])->fetchOne();
		if ( $zoteroEntry ) {
			$foundFulltext++;
			$json = json_decode($zoteroEntry,true);
			//printf("Found zotero '%s' for '%s' (%s)\n", $json['title'], $line, $titleForQuery);
		}

	} else {
		printf("No title found for '%s'\n", $line);
	}

	$manualLit[] = $entry;
}

printf("%d eintraege in LibreOffice\n", count($manualLit));
printf("%d eintraege in Zotero gefunden\n", $foundFulltext);

