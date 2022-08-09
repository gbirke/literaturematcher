<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

class LiteratureParser
{
	public static function parseEntry(string $line): array
	{
		[$authorAndYearSection, $titleAndOtherStuff] = explode(":", $line, 2);
		$authorsAndYears = self::parseAuthorsAndYears($authorAndYearSection);
		$titleAndOtherStuff = self::parseTitleAndOtherStuff( $titleAndOtherStuff );
		return [
			...$titleAndOtherStuff,
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

	public static function parseTitleAndOtherStuff(string $titleAndOtherStuff): array
	{
		$result = [];
		// Examples
		// London: the Women’s Press
		// München: Springer
		// language=regexp
		$placeAndPublisherRegex = '!([\\p{L}/ ]+):\s+([\\p{L}\'’ ]+)$!u';

		// Source: https://stackoverflow.com/a/3809435
		$urlRegexSnippet = 'https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/=]*)';

		if (preg_match('/\s*[Ii]n:\s*(.*$)/', $titleAndOtherStuff, $matches, PREG_OFFSET_CAPTURE ) ) {
			$result['title'] = substr($titleAndOtherStuff, 0, $matches[0][1] );
			$otherStuff = substr($titleAndOtherStuff, $matches[1][1]);

			// Clean up notes
			$otherStuff = preg_replace("/;?\s*$urlRegexSnippet,?\s*abgerufen\s*am[0-9 .]+/", '', $otherStuff);

			// Recognize and remove pages at the end
			if (preg_match('/,?\s*S[.\s]+(\d+(\s*-\s*\d+)?)\s*$/', $otherStuff, $matches, PREG_OFFSET_CAPTURE ) ) {
				$result['pages'] = $matches[1][1];
				$otherStuff = substr($otherStuff, 0, $matches[0][1]);
			}
			$result['debugOtherStuff']=$otherStuff;

			if (preg_match($placeAndPublisherRegex, $otherStuff, $matches, PREG_OFFSET_CAPTURE) ) {
				$result['place'] = $matches[1][0];
				$result['publisher'] = $matches[2][0];
				$result['itemType'] = "bookSection";
			} else {
				$result['itemType'] = "journalArticle";
			}

			// TODO parse other parts (publisher, place, type of entry)

		} else {
			// For now, assume a book and title of books ends with dot.
			$parts = explode('. ', $titleAndOtherStuff, 2);
			$result['title'] = $parts[0];
			// TODO check for URL, it might be an internet page
			$result['itemType'] = 'book';

			$result['debug']=$parts[1] ?? '';

			if (!empty($parts[1]) &&preg_match($placeAndPublisherRegex, $parts[1], $matches, PREG_OFFSET_CAPTURE) ) {
				$result['place'] = $matches[1][0];
				$result['publisher'] = $matches[2][0];
			}

		}
		return $result;
	}


}
