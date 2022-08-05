<?php
declare(strict_types=1);

use Birke\LiteratureMatcher\LiteratureLexer;
use Birke\LiteratureMatcher\Token;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Birke\LiteratureMatcher\LiteratureLexer
 */
class LiteratureLexerTest extends TestCase
{

	/**
	 * @dataProvider provideAuthorAndYear
	 */
	public function testAuthorLexing( string $text, array $expectedTokens ): void {
		$tokens = LiteratureLexer::run( $text );
		$this->assertEquals( $expectedTokens, $tokens );
	}

	public function provideAuthorAndYear(): iterable {
		yield 'Basic Author' => [
			'Abberley, P. (1987):',
			[
				new Token('word', 'Abberley'),
				new Token('comma', ', '),
				new Token('word', 'P'),
				new Token('dot', '.'),
				new Token('space', ' '),
				new Token('openBrace', '('),
				new Token('year', '1987'),
				new Token('closeBrace', ')'),
				new Token('colon', ':'),
			]
		];

		yield 'Author with original date' => [
			'Adorno, T.W. (2003) [1965]:',
			[
				new Token('word', 'Adorno'),
				new Token('comma', ', '),
				new Token('word', 'T'),
				new Token('dot', '.'),
				new Token('word', 'W'),
				new Token('dot', '.'),
				new Token('space', ' '),
				new Token('openBrace', '('),
				new Token('year', '2003'),
				new Token('closeBrace', ')'),
				new Token('space', ' '),
				new Token('openBracket', '['),
				new Token('year', '1965'),
				new Token('closeBracket', ']'),
				new Token('colon', ':'),
			]
		];

	}
}
