<?php

require __DIR__ . '/vendor/autoload.php';

$db = \Doctrine\DBAL\DriverManager::getConnection([ 'url' => 'sqlite://./literature.db']);

$zoteroCount = $db->executeQuery("SELECT COUNT(*) from zotero_entry")->fetchOne();

$titleQuery = $db->prepare( "SELECT data from zotero_entry WHERE title=:title" );

printf("%d eintraege in Zotero\n", $zoteroCount);

$manualLit = [];
$found = 0;
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
		$zoteroEntry = $titleQuery->executeQuery(['title' => $entry['title']])->fetchOne();
		if ($zoteroEntry) {
			$found++;
			// TODO compare entries
		}
	} else {
		printf("No title found for '%s'\n", $line);
	}


	$manualLit[] = $entry;
}

printf("%d eintraege in LibreOffice\n", count($manualLit));
printf("%d eintraege in Zotero gefunden", $found);
