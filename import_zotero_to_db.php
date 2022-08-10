<?php
declare(strict_types=1);

use Birke\LiteratureMatcher\ZoteroRepository;

require __DIR__ . '/vendor/autoload.php';

$container = include __DIR__ . '/src/container.php';

/** @var ZoteroRepository $repo */
$repo = $container->get(ZoteroRepository::class);

foreach (glob("zotero_*.json") as $zoteroFile) {
	$json = json_decode(file_get_contents($zoteroFile), true);
	printf("\nReading file %s", $zoteroFile);
	foreach ($json as $entry) {
		// ignore library, links and metadata
		$entry = $entry['data'];
		// Skip attachments and highlights for now
		if ($entry['itemType'] === 'attachment' || $entry['itemType'] === 'highlight' || $entry['itemType'] === 'annotation') continue;

		if (empty($entry['title'])) {
			echo "\nEmpty title\n";
			var_export($entry);
			continue;
		}

		//printf("Importing '%s' ... ", $entry['title']);

		$entryId = $repo->insertEntry($entry);

		foreach ($entry['creators'] as $creator) {
			if (!isset($creator['firstName'])) {
				if (isset($creator['name'])) {
					$creatorData = [
						'firstName' => '',
						'lastName' => $creator['name']
					];
				} else {
					echo "\nNo valid names for creator found";
					var_export($entry);
					continue;
				}

			} else {
				$creatorData = [
					'firstName' => $creator['firstName'] ?? '',
					'lastName' => $creator['lastName']
				];
			}
			$authorId = $repo->getCreator( $creatorData );
			try {
				$repo->insertCreatorRelation( $authorId, $entryId, $creator['creatorType'], $entry['key']);
			} catch (\Exception $e) {
				error_log($e->getMessage());
				print_r($entry);
			}
		}

		// echo " done\n";
	}
	echo "\n------------------\n";
}

$repo->buildFulltextIndex();
