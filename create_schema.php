<?php
declare(strict_types=1);

/** Create database schema for searching entries */

require __DIR__ . '/vendor/autoload.php';

$schema = new \Doctrine\DBAL\Schema\Schema();

$zoteroAuthors = $schema->createTable('zotero_creator');
$zoteroAuthors->addColumn("id", "integer", ["unsigned" => true, 'autoincrement' => 'true']);
$zoteroAuthors->addColumn("firstName", 'string');
$zoteroAuthors->addColumn("lastName", 'string');
$zoteroAuthors->setPrimaryKey(["id"]);
$zoteroAuthors->addIndex(['lastName', 'firstName']);

$zoteroAuthorsToEntries = $schema->createTable('zotero_creators_entries');
$zoteroAuthorsToEntries->addColumn("author_id", "integer", ["unsigned" => true]);
$zoteroAuthorsToEntries->addColumn("entry_id", "integer", ["unsigned" => true]);
$zoteroAuthorsToEntries->addColumn("creatorType", 'string');
$zoteroAuthorsToEntries->addColumn("entry_key", 'string', ['length' => 8]);
$zoteroAuthorsToEntries->setPrimaryKey(['author_id', 'entry_id', 'creatorType']);

$zoteroEntries = $schema->createTable('zotero_entry');
$zoteroEntries->addColumn("id", "integer", ["unsigned" => true, 'autoincrement' => 'true']);
$zoteroEntries->addColumn('title', 'string');
$zoteroEntries->addColumn('key', 'string', ['length' => 8]);
$zoteroEntries->addColumn('data', 'json');
$zoteroEntries->setPrimaryKey(['id']);
$zoteroEntries->addIndex(['title']);
$zoteroEntries->addIndex(['key']);

$db = \Doctrine\DBAL\DriverManager::getConnection(['url' => 'sqlite://./literature.db']);
$db->createSchemaManager()->migrateSchema($schema);
$db->executeQuery("CREATE VIRTUAL TABLE zotero_titles USING FTS5(title, content='zotero_entry', content_rowid='id')");


