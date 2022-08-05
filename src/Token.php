<?php
declare( strict_types=1 );

namespace Birke\LiteratureMatcher;

class Token {
	public function __construct(
		readonly string $name,
		readonly string $value,
	) {
	}
}
