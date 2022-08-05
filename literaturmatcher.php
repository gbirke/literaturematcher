<?php

$zoteroLit = [];

foreach(glob("zotero_*.json") as $zoteroFile) {
	$json = json_decode(file_get_contents($zoteroFile));
	array_push($zoteroLit, ...$json);
}


printf("%d eintraege in Zotero\n", count($zoteroLit));

$manualLit = [];
foreach(file('Literaturverzeichnis_Diss.txt') as $line) {
	if (!$line) continue;
	$manualLit[] = $line;
}

printf("%d eintraege in LibreOffice\n", count($manualLit));
