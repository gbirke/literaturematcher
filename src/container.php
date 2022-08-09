<?php
declare(strict_types=1);

use Birke\LiteratureMatcher\ManualLiteratureFile;
use Birke\LiteratureMatcher\ZoteroRepository;

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
	// Configuration
	'db.config' => [ 'url' => 'sqlite://./literature.db'],
	'sourceFileName' => 'Literaturverzeichnis_Diss.txt',

	// Factories
	'SourceFile' => Di\create(ManualLiteratureFile::class)->constructor( Di\get('sourceFileName') ),
	ZoteroRepository::class => Di\create()->constructor( Di\get('db.config') ),
]);
return $builder->build();
