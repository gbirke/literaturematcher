<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Statement;

class ZoteroRepository
{
	private ?Connection $connection = null;
	private ?Statement $fulltextQuery = null;

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
}
