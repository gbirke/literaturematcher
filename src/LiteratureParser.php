<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

class LiteratureParser
{
	public static function parseEntry(string $line): array
	{
		[$authorAndYearSection, $titleAndOtherStuff] = explode(":", $line);
		$authorsAndYears = self::parseAuthorsAndYears($authorAndYearSection);
		return [
			...$authorsAndYears
		];
	}

	public static function parseAuthorsAndYears(string $authorAndYearSection): array
	{
		if (preg_match('/\s*\((\d{4}|\d{4}\/\d{4})\)( \[\d{4}])?/', $authorAndYearSection, $matches)) {
			$authorString = substr($authorAndYearSection, 0, -strlen($matches[0]));
			return ['creators' => self::parseAuthors($authorString), 'date' => $matches[1] . ($matches[2] ?? '')];
		}
		return ['creators' => self::parseAuthors($authorAndYearSection), 'date' => ''];
	}

	public static function parseAuthors(string $authorString): array
	{
		$authorStrings = preg_split('/\s*[&;]\s*/', $authorString);
		$authorStrings = array_reduce(
			$authorStrings,
			function ($newAuthorStrings, $currentAuthorString) {
				if (substr_count($currentAuthorString, ', ') == 1) {
					$newAuthorStrings[] = $currentAuthorString;
				} else {
					$cleanAuthorString = self::convertCommaSeparatedAuthorsToSemicolonSeparated($currentAuthorString);
					$newAuthorStrings = array_merge($newAuthorStrings, explode('; ', $cleanAuthorString));
				}
				return $newAuthorStrings;
			},
			[]
		);

		return array_map([LiteratureParser::class, 'parseOneAuthor'], $authorStrings);
	}

	public static function parseOneAuthor(string $authorString): array
	{

		$nameParts = explode(', ', $authorString, 2);


		return [
			'creatorType' => 'author',
			'firstName' => $nameParts[1] ?? '',
			'lastName' => $nameParts[0]
		];
	}

	private static function convertCommaSeparatedAuthorsToSemicolonSeparated(string $authorString): string
	{
		$splitAuthors = explode(', ', $authorString);

		return implode(
			'; ',
			array_map(
				fn($nameChunk) => implode(', ', $nameChunk),
				array_chunk($splitAuthors, 2)
			)
		);
	}


}
