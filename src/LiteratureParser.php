<?php
declare( strict_types=1 );

namespace Birke\LiteratureMatcher;
/**
 * @license GNU GPL v2+
 */
class LiteratureParser {
	public static function parseEntry(string $line): array {
		[$authorAndYearSection, $titleAndOtherStuff] = explode(":", $line);
		$authorsAndYears = self::parseAuthorsAndYears($authorAndYearSection);
		return [
			...$authorsAndYears
		];
	}

	public static function parseAuthorsAndYears( string $authorAndYearSection ): array {
		if ( preg_match('/\s*\((\d{4}|\d{4}\/\d{4})\)( \[\d{4}])?/', $authorAndYearSection, $matches ) ) {
			
			return ['creators' => self::, 'date' => $matches[1] . ($matches[2] ?? '') ];
		}
		return ['creators' => [], 'date' => ''];
	}

	public function parseAuthors(string $authorString): array {
		$commaCount = substr_count($authorString, ',');
		$authors = [];
		if ($commaCount % 2 === 0 ) {
			$parts = explode(',', $authorString);
			for($i=0;$i<$commaCount;$i+=2) {
				$authors[] = [
					'creatorType' => 'author',
					'firstName' => $parts[$i],
					'lastName' => $parts[$i+1]
				];
			}
		}
		return $authors;
	}


}
