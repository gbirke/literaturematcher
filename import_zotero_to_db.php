<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$db = \Doctrine\DBAL\DriverManager::getConnection([ 'url' => 'sqlite://./literature.db']);

$db->executeStatement('DELETE FROM zotero_creators_entries' );
$db->executeStatement('DELETE FROM zotero_creator');
$db->executeStatement('DELETE FROM zotero_entry' );

$authorQuery = $db->createQueryBuilder();
$authorQuery->select('id')
	->from('zotero_creator')
	->where('lastname=:lastName')
	->andWhere('firstName=:firstName');

foreach(glob("zotero_*.json") as $zoteroFile) {
	$json = json_decode(file_get_contents($zoteroFile), true);
	printf("\nReading file %s", $zoteroFile);
	foreach( $json as $entry ) {
		// ignore library, links and metadata
		$entry = $entry['data'];
		// Skip attachments and highlights for now
		if ( $entry['itemType'] === 'attachment' || $entry['itemType'] === 'highlight' || $entry['itemType'] === 'annotation') continue;

		if (empty($entry['title'])) {
			echo "\nEmpty title\n";
			var_export($entry);
			continue;
		}

		//printf("Importing '%s' ... ", $entry['title']);

		$db->insert('zotero_entry', ['title' => $entry['title'], 'key' => $entry['key'], 'data' => json_encode( $entry, JSON_PRETTY_PRINT ) ]);
		$entryId = intval( $db->lastInsertId() );

		foreach( $entry['creators'] as $creator ) {
			if ( !isset($creator['firstName']) ) {
				if (isset($creator['name'])) {
					$creatorData = [
						'firstName' => '',
						'lastName' => $creator['name']
					];
				} else {
					echo "\nNo valid names for creator found";
					var_export($entry);
					continue 2;
				}

			} else {
				$creatorData = [
					'firstName' => $creator['firstName'] ?? '',
					'lastName' => $creator['lastName']
				];
			}
			$authorId = $authorQuery->setParameter('firstName', $creatorData['firstName'])
				->setParameter('lastName', $creatorData['lastName'])
				->fetchOne();
			if (intval($authorId) === 0 ) {
				$db->insert('zotero_creator', $creatorData);
				$authorId = $db->lastInsertId();
			}
			$db->insert( 'zotero_creators_entries', [
				'author_id' => $authorId,
				'entry_id' => $entryId,
				'creatorType' => $creator['creatorType'],
				'entry_key' => $entry['key']
			]);
		}

		// echo " done\n";
	}
	echo "\n------------------\n";
}
