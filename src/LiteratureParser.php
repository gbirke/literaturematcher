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
		$creators = array_merge( $authorsAndYears['creators'], $titleAndOtherStuff['creators']);
		return [
			...$titleAndOtherStuff,
			...$authorsAndYears,
			'creators' => $creators
		];
	}

	public static function parseAuthorsAndYears(string $authorAndYearSection): array
	{
		if (preg_match('/\s*\((\d{4}|\d{4}\/\d{4})\)( \[\d{4}])?/', $authorAndYearSection, $matches)) {
			$authorString = substr($authorAndYearSection, 0, -strlen($matches[0]));
			return ['creators' => self::parseCreators($authorString), 'date' => $matches[1] . ($matches[2] ?? '')];
		}
		return ['creators' => self::parseCreators($authorAndYearSection), 'date' => ''];
	}

	public static function parseCreators(string $authorString, string $creatorType = 'author'): array
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

		return array_map(fn($authorString) => self::parseOneAuthor($authorString, $creatorType), $authorStrings);
	}

	public static function parseOneAuthor(string $authorString, string $creatorType): array
	{

		$nameParts = explode(', ', $authorString, 2);


		return [
			'creatorType' => $creatorType,
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
		$result = [
			'potentialItemTypes' => [],
			'creators' => []
		];
		// Examples
		// London: the Women’s Press
		// München: Springer
		// language=regexp
		$placeAndPublisherRegex = '!([\\p{L}/ ]+):\s+([\\p{L}\'’. ]+)$!u';

		// Source: https://stackoverflow.com/a/3809435
		$urlRegexSnippet = 'https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/=]*)';
		$urlRequestedRegex = "/;?\s*$urlRegexSnippet,?\s*abgerufen\s*am[0-9 .]+/";

		if (preg_match($urlRequestedRegex, $titleAndOtherStuff)) {
			$result['potentialItemTypes'][] = 'webpage';
		}


		if (preg_match('/\s*[Ii]n:\s*(.*$)/', $titleAndOtherStuff, $matches, PREG_OFFSET_CAPTURE ) ) {
			$result['title'] = ltrim( self::trimTrailingSpacesAndDots(
				substr($titleAndOtherStuff, 0, $matches[0][1] )
			) );
			$otherStuff = substr($titleAndOtherStuff, $matches[1][1]);

			// Clean up notes
			$otherStuff = preg_replace($urlRequestedRegex, '', $otherStuff);

			// Recognize and remove pages at the end
			if (preg_match('/,?\s*S[.\s]+(\d+(\s*-\s*\d+)?)\s*$/', $otherStuff, $matches, PREG_OFFSET_CAPTURE ) ) {
				$result['pages'] = $matches[1][0];
				$otherStuff = substr($otherStuff, 0, $matches[0][1]);
			}
			//$result['debugOtherStuff']=$otherStuff;

			// Detect book sections (vs journal articles) by looking for a place and publisher at the end
			if (preg_match($placeAndPublisherRegex, $otherStuff, $matches, PREG_OFFSET_CAPTURE) ) {
				$result['place'] = trim($matches[1][0]);
				$result['publisher'] = ltrim( self::trimTrailingSpacesAndDots( $matches[2][0] ) );
				$result['itemType'] = "bookSection";
				$result['potentialItemTypes'][] = 'bookSection';

				$otherStuff = substr( $otherStuff, 0, $matches[0][1]);
				if ( preg_match('/\(Hg\.?\)[:.]?|:/', $otherStuff, $matches, PREG_OFFSET_CAPTURE ) ) {
					$editorSection = trim(substr($otherStuff, 0, $matches[0][1]));
					// Remove duplicated year from editor section. We might add it back as a different field if it's different
					$editorSection = preg_replace('/\s*\(\d{4}\)\s*$/', '', $editorSection);
					$result['creators'] = self::parseCreators( $editorSection, 'editor' );
					$bookTitle = self::trimTrailingSpacesAndDots(
						substr( $otherStuff, $matches[0][1] + strlen( $matches[0][0] ) )
					);
					// check for marker that the book title matches the bookSection title
					if (preg_match('/\(ders\.?\)|ders\.:/i', $bookTitle)) {
						$bookTitle = $result['title'];
					}
					$result['bookTitle'] = trim($bookTitle);
				}
			} else
			// journal entry
			{
				if (preg_match('/(\d+)\s*(?:\((\d+)\))?/', $otherStuff, $matches, PREG_OFFSET_CAPTURE)) {
					$publicationTitle = self::trimTrailingSpacesAndDots(
						substr($otherStuff, 0, $matches[0][1])
					);
					$result['publicationTitle'] = trim($publicationTitle);
					$result['volume'] = $matches[1][0];
					if(!empty($matches[2])) {
						$result['issue'] = $matches[2][0];
					}

				}
				$result['itemType'] = "journalArticle";
				$result['potentialItemTypes'][] = 'journalArticle';
			}

		} else {
			// TODO check for URL, it might be an internet page
			$result['itemType'] = 'book';
			$result['potentialItemTypes'][] = 'book';

			if (preg_match($placeAndPublisherRegex, $titleAndOtherStuff, $matches, PREG_OFFSET_CAPTURE) ) {
				$bookTitle = self::trimTrailingSpacesAndDots( substr($titleAndOtherStuff, 0, $matches[0][1]) );
				// TODO remove quotes and trailing dots/spaces
				$result['title'] = $bookTitle;
				$result['place'] = trim( $matches[1][0]);
				$result['publisher'] = ltrim( self::trimTrailingSpacesAndDots( $matches[2][0] ) );
			} else {
				$result['title'] = $titleAndOtherStuff;
			}

		}
		return $result;
	}

	private static function trimTrailingSpacesAndDots( string $title ): string {
		return preg_replace('/[\s.]+$/','', $title );
	}

}
