<?php
declare( strict_types=1 );

namespace Birke\LiteratureMatcher;
use Parsec\Context;

/**
 * @license GNU GPL v2+
 */
class AuthorContext extends Context {

	protected array $author = ['firstName' => '', 'lastName' => ''];
	protected string $namePart = 'lastName';

	public function tokenWord($value) {
		$this->author[$this->namePart] .= $value;
	}

	public function tokenComma() {
		if ( $this->namePart === 'lastName' ) {
			$this->namePart = 'firstName';
			return;
		}
		$this->exitContext($this->author);
	}

	public function tokenSpace() {

	}
}
