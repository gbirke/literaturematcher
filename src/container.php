<?php
declare(strict_types=1);

use Birke\LiteratureMatcher\ManualLiteratureFile;
use Birke\LiteratureMatcher\ZoteroRepository;

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
	// Configuration
	'db.config' => [ 'url' => 'sqlite:///'.realpath( sprintf('%s/../literature.db', __DIR__ ) ) ],
	'sourceFileName' => __DIR__ . '/../Literaturverzeichnis_Diss.txt',

	// Factories
	ManualLiteratureFile::class => Di\create()->constructor( Di\get('sourceFileName') ),
	ZoteroRepository::class => Di\create()->constructor( Di\get('db.config') ),
]);
return $builder->build();
