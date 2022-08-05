<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

use Parsec\Lexer;

class OldLiteratureLexer extends Lexer
{

	public function __construct()
	{
		parent::__construct([
			'openBrace'	=> '\(',
			'closeBrace'	=> '\)',
			'openBracket'	=> '\[',
			'closeBracket'	=> '\]',
			'openQuote' => ' "| “|„',
			'loseQuote' => '" |“ |“,',
			'inCollection' => ' [iI]n: ',
			'etAl' => 'et\.\s*al\.?',
			'colon' => ':',
			'comma' => ',\s*',
			'space' => ' ',
			'dot' => '\.',
			'ampersand' => '&',
			// We can get away with this simplified definition
			'url' => 'https?:\/\/[^ ]+',
			'year' => '\d{4}',
			'number' => '\d+',
			'otherPunctuation' => '[-;]',
			'word' => '[a-zA-ZäöüÄÖÜß]+'
		]);
	}
}
