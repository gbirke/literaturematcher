<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

use TypeError;

class LiteratureMatcher
{

	public function __construct( private readonly ZoteroRepository $zoteroRepository )
	{
	}

	public function getEntryForLine(string $line, int $lineNumber ): array {
		try {
			$entry = LiteratureParser::parseEntry($line);
		} catch (TypeError) {
			return $this->newErrorEntry( $line, $lineNumber, [], "Parse Error" );
		}
		if ( empty($entry['title'])) {
			return $this->newErrorEntry( $line, $lineNumber, $entry, 'No title found');
		}

		$titleForQuery = preg_replace("/[^\\p{L} ]/u", '', $entry['title']);
		if (!trim($titleForQuery)) {
			return $this->newErrorEntry( $line, $lineNumber, $entry, 'Trimmed title was empty');

		}
		$zoteroEntry = $this->zoteroRepository->getEntryByTitle( $titleForQuery );
		return [
			'lineNumber' => $lineNumber,
			'line' => $line,
			'manualEntry' => $entry,
			'zoteroEntry' => $zoteroEntry
		];
	}

	private function newErrorEntry(string $line, int $lineNumber, array $entry, string $errorMsg)
	{
		return [
			'lineNumber' => $lineNumber,
			'line' => $line,
			'error' => $errorMsg,
			'manualEntry' => $entry,
			'zoteroEntry' => []
		];
	}
}
