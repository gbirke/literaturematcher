<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;

class ZoteroRepository
{
	private ?Connection $connection = null;
	private ?Statement $fulltextQuery = null;
	private ?QueryBuilder $authorQuery = null;

	public function __construct( private readonly array $dbConfig )
	{
	}

	private function getConnection(): Connection {
		if ($this->connection === null ) {
			$this->connection = DriverManager::getConnection($this->dbConfig);
		}
		return $this->connection;
	}

	private function getTitleQuery(): Statement {
		if ($this->fulltextQuery === null ) {
			$this->fulltextQuery = $this->getConnection()->prepare( "SELECT data from zotero_entry ze JOIN zotero_titles zt ON ze.id=zt.rowid WHERE zt.title MATCH :title" );
		}
		return $this->fulltextQuery;
	}

	public function getEntryByTitle( string $title ): array {
		$data = $this->getTitleQuery()->executeQuery(['title' => $title])->fetchOne();
		if ( $data === false ) {
			return [];
		}
		return json_decode($data,true);
	}

	public function clearAll(): void {
		$db = $this->getConnection();
		$db->executeStatement('DELETE FROM zotero_creators_entries');
		$db->executeStatement('DELETE FROM zotero_creator');
		$db->executeStatement('DELETE FROM zotero_entry');
		$db->executeStatement('DELETE FROM zotero_title');
	}

	private function getAuthorQuery(): QueryBuilder {
		if ( $this->authorQuery === null ) {
			$this->authorQuery = $this->getConnection()->createQueryBuilder();
			$this->authorQuery->select('id')
				->from('zotero_creator')
				->where('lastname=:lastName')
				->andWhere('firstName=:firstName');
		}
		return $this->authorQuery;
	}

	public function getCreator( array $creator ): int {
		$db = $this->getConnection();
		$authorId = $this->getAuthorQuery()->setParameter('firstName', $creator['firstName'])
			->setParameter('lastName', $creator['lastName'])
			->fetchOne();
		if (intval($authorId) === 0) {
			$db->insert('zotero_creator', $creator);
			$authorId = $db->lastInsertId();
		}
		return intval($authorId);
	}

	public function insertCreatorRelation( $authorId, $entryId, $creatorType, $entryKey) {
		$this->getConnection()->insert('zotero_creators_entries', [
			'author_id' => $authorId,
			'entry_id' => $entryId,
			'creatorType' => $creatorType,
			'entry_key' => $entryKey
		]);
	}

	public function insertEntry( array $entry ): int {
		$db = $this->getConnection();
		$db->insert('zotero_entry', ['title' => $entry['title'], 'key' => $entry['key'], 'data' => json_encode($entry, JSON_PRETTY_PRINT)]);
		// TODO iterate over creators and call getCreator and insertCreatorRelation, making them private
		return intval($db->lastInsertId());
	}

	public function buildFulltextIndex(): void {
		$this->getConnection()->executeQuery("INSERT INTO zotero_titles(rowid, title) SELECT id, title FROM zotero_entry");
	}

}
