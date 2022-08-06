<?php
declare( strict_types=1 );

namespace Birke\LiteratureMatcher;

class LiteratureLexer {

	const TERMINALS = [
		'openBrace'	=> '/^(\()/',
		'closeBrace'	=> '/^(\))/',
		'openBracket'	=> '/^(\[)/',
		'closeBracket'	=> '/^(\])/',
		'openQuote' => '/^( "| “|„)/',
		'loseQuote' => '/^(" |“ |“,)/',
		'inCollection' => '/^( [iI]n: )/',
		'etAl' => '/^(et\.\s*al\.?)/',
		'colon' => '/^(:)/',
		'comma' => '/^(,\s*)/',
		'space' => '/^( )/',
		'dot' => '/^(\.)/',
		'ampersand' => '/^(&)/',
		// We can get away with this simplified definition
		'url' => '!https?://[^ ]+!',
		'year' => '/^(\d{4})/',
		'number' => '/^(\d+)/',
		'otherPunctuation' => '/^([-;!])/',
		'word' => '/^([a-zA-ZäöüÄÖÜß]+)/'
	];

	/**
	 * @param string $source
	 * @return Token[]
	 */
	public static function run(string $source): array {
		$offset = 0;
		$tokens = [];
		while($offset < strlen($source)) {
			$token = static::match($source, $offset);
			$offset += strlen($token->value);
			$tokens[] = $token;
		}
		return $tokens;
	}

	private static function match( string $source, int $offset ): Token {
		$rest = substr( $source, $offset );
		// TODO compile terminals into one regex and check for matches
		foreach( self::TERMINALS as $name => $pattern ) {
			if ( preg_match($pattern, $rest, $matches ) ) {
				return new Token( $name, $matches[1] );
			}
		}
		throw new \RuntimeException(sprintf(
			"Unknown character '%s' at position %d",
			substr($source,$offset,1),
			$offset
		));
	}
}
