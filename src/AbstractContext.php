<?php
declare( strict_types=1 );

namespace Birke\LiteratureMatcher;
/**
 * @license GNU GPL v2+
 */
abstract class AbstractContext {
	abstract public function execute( TokenScanner $tokens, mixed $data );
	abstract public function getExitData();
}
