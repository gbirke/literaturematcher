<?php
declare(strict_types=1);

use Birke\LiteratureMatcher\OldLiteratureLexer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Birke\LiteratureMatcher\OldLiteratureLexer
 */
class OldLiteratureLexerTest extends TestCase
{

	/**
	 * @dataProvider provideAuthorAndYear
	 */
	public function testAuthorLexing( string $text, array $expectedTokens ): void {
		$lexer = new OldLiteratureLexer();
		$tokens = $lexer->tokenize( $text );
		$tokens = $this->removePositions( $tokens );
		$this->assertEquals( $expectedTokens, $tokens );
	}

	public function provideAuthorAndYear(): iterable {
		yield 'Basic Author' => [
			'Abberley, P. (1987):',
			[
				['token' => 'word', 'value' => 'Abberley' ],
				['token' => 'comma', 'value' => ',' ],
				['token' => 'space', 'value' => ' ' ],
				['token' => 'word', 'value' => 'P' ],
				['token' => 'dot', 'value' => '.' ],
				['token' => 'space', 'value' => ' ' ],
				['token' => 'openBrace', 'value' => '(' ],
				['token' => 'year', 'value' => '1987' ],
				['token' => 'closeBrace', 'value' => ')' ],
				['token' => 'colon', 'value' => ':' ],
			]
		];

		yield 'Author with original date' => [
			'Adorno, T.W. (2003) [1965]:',
			[
				['token' => 'word', 'value' => 'Adorno' ],
				['token' => 'comma', 'value' => ',' ],
				['token' => 'space', 'value' => ' ' ],
				['token' => 'word', 'value' => 'T' ],
				['token' => 'dot', 'value' => '.' ],
				['token' => 'word', 'value' => 'W' ],
				['token' => 'dot', 'value' => '.' ],
				['token' => 'space', 'value' => ' ' ],
				['token' => 'openBrace', 'value' => '(' ],
				['token' => 'year', 'value' => '2003' ],
				['token' => 'closeBrace', 'value' => ')' ],
				['token' => 'space', 'value' => ' ' ],
				['token' => 'openBracket', 'value' => '[' ],
				['token' => 'year', 'value' => '1965' ],
				['token' => 'closeBracket', 'value' => ']' ],
				['token' => 'colon', 'value' => ':' ],
			]
		];

	}

	private function removePositions(array $tokens)
	{
		return array_map(
			fn($token) => ['token' => $token['token'], 'value' => $token['value']],
			$tokens
		);
	}
}
