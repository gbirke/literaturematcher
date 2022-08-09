<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

class ManualLiteratureFile
{
	public function __construct( private readonly string $fileName )
	{
	}

	public function getLines(): array {
		return file($this->fileName );
	}

	public function getLine(int $lineNumber): string {
		return file( $this->fileName)[$lineNumber];
	}
}
