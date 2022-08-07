<?php
declare(strict_types=1);


use Birke\LiteratureMatcher\LiteratureParser;
use PHPUnit\Framework\TestCase;

class LiteratureParserTest extends TestCase
{
	public function testSplittingOfAuthorAndYear(): void
	{
		$result = LiteratureParser::parseAuthorsAndYears('Abberley, P. (1992)');

		$this->assertSame('1992', $result['date']);
		$this->assertSame([
			[
				'creatorType' => 'author',
				'firstName' => 'P.',
				'lastName' => 'Abberley'
			]], $result['creators']);
	}

	public function testSplittingOfYearWithPublicationDate(): void
	{
		$result = LiteratureParser::parseAuthorsAndYears('Glaser, B.G., Strauss, A.L. (2003) [1965]');

		$this->assertSame('2003 [1965]', $result['date']);
	}

	public function testSplittingMultipleAuthorsSeparatedByComma(): void
	{
		$result = LiteratureParser::parseAuthorsAndYears('Glaser, B.G., Strauss, A.L. (2003) [1965]');

		$this->assertSame(
			[
				[
					'creatorType' => 'author',
					'firstName' => 'B.G.',
					'lastName' => 'Glaser'
				],
				[
					'creatorType' => 'author',
					'firstName' => 'A.L.',
					'lastName' => 'Strauss'
				],
			],
			$result['creators']
		);
	}

	public function testSplittingMultipleAuthorsSeparatedBySemicolonAndAmpersand(): void
	{
		$result = LiteratureParser::parseAuthorsAndYears('Buschbacher, R. M.; Atkins, J.;  Lay, B. & Braddom, R. L. (2000)');

		$this->assertSame(
			[
				[
					'creatorType' => 'author',
					'firstName' => 'R. M.',
					'lastName' => 'Buschbacher'
				],
				[
					'creatorType' => 'author',
					'firstName' => 'J.',
					'lastName' => 'Atkins'
				],
				[
					'creatorType' => 'author',
					'firstName' => 'B.',
					'lastName' => 'Lay'
				],
				[
					'creatorType' => 'author',
					'firstName' => 'R. L.',
					'lastName' => 'Braddom'
				],
			],
			$result['creators']
		);
	}

	public function testSplittingOfYearWithMultiplePublicationDates(): void
	{
		$result = LiteratureParser::parseAuthorsAndYears('Leontjew, A. (1959/1971)');

		$this->assertSame('1959/1971', $result['date']);
	}
}
