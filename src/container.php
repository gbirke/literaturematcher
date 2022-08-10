<?php
declare(strict_types=1);

use Birke\LiteratureMatcher\ManualLiteratureFile;
use Birke\LiteratureMatcher\ZoteroRepository;

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
	// Configuration
	'db.config' => [ 'url' => 'sqlite:///'.realpath( sprintf('%s/../literature.db', __DIR__ ) ) ],
	'sourceFileName' => __DIR__ . '/../Literaturverzeichnis_Diss.txt',
	'zotero.user' => Di\env('ZOTERO_USER'),
	'zotero.api_key' => Di\env('ZOTERO_API_KEY'),
	'zotero.url' => Di\string("https://api.zotero.org/users/{zotero.user}/items"),

	// Factories
	ManualLiteratureFile::class => Di\create()->constructor( Di\get('sourceFileName') ),
	ZoteroRepository::class => Di\create()->constructor( Di\get('db.config') ),
	'zotero.client' => \DI\factory(function (\Psr\Container\ContainerInterface $c) {
		return new \GuzzleHttp\Client([
			'base_uri' => $c->get('zotero.url'),
			'headers' => [
				'Accept' => 'application/json',
				'Zotero-API-Key' => $c->get('zotero.api_key')
			]
		]);
	})
]);
return $builder->build();
