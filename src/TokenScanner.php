<?php
declare( strict_types=1 );

namespace Birke\LiteratureMatcher;
/**
 * @license GNU GPL v2+
 */
class TokenScanner {
	private int $count;
	private int $cursor;
	private Token $endToken;

	public function __construct( private readonly array $tokens ) {
		$this->count = count($this->tokens);
		$this->cursor = 0;
		$this->endToken = new Token("EOS", '');
	}

	public function currentToken(): Token {
		if ( $this->cursor >= $this->count) {
			return $this->endToken;
		}
		return $this->tokens[$this->cursor];
	}

	public function advance() {
		$this->cursor++;
	}
}
