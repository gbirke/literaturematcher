<?php
declare(strict_types=1);

namespace Birke\LiteratureMatcher;

use Geissler\Converter\Model\Date;
use Geissler\Converter\Model\Entry;
use Geissler\Converter\Model\Person;

/**
 * Create an entry from array
 */
class EntryFactory
{
	private Entry $entry;

	public function build(array $entry ): Entry {
		$this->entry = new Entry();

		foreach( $entry as $key => $value ) {
			$method = 'set'.ucfirst($key);
			$this->{$method}($value);
		}
		if ($this->entry->getCitationLabel() === null ) {
			$this->entry->setCitationLabel( $this->generateCitationLabel() );
		}
		return $this->entry;
	}

	private function setTitle(string $title) {
		$this->entry->setTitle($title);
	}

	private function setPublisher(string $publisher) {
		$this->entry->setPublisher($publisher);
	}

	private function setPlace(string $place) {
		$this->entry->setPublisherPlace($place);
	}

	private function setPages(string $pageRange) {
		$pages = $this->entry->getPages();
		$pages->setRange($pageRange);
	}

	private function setDate(string $date) {
		$this->entry->getIssued()->setDate((new Date())->setYear(intval($date)));
	}

	private function setBookTitle(string $title)
	{
		$this->entry->setCollectionTitle($title);
	}

	private function setItemType(string $itemType) {
		$type = $this->entry->getType();
		switch($itemType) {
			case 'bookSection':
				$type->setChapter();
				break;
			case 'book':
				$type->setBook();
				break;
			case 'journalArticle':
				$type->setArticle();
				break;
			case 'webpage':
				$type->setWebpage();
				break;
			default:
				throw new \RuntimeException("Unkown item type $itemType");
		}
	}

	private function setCreators(array $creators) {
		$creatorTypes = [
			'author' => $this->entry->getAuthor(),
			'editor' => $this->entry->getEditor()
		];

		foreach($creators as $creator) {
			$person = new Person();
			$person->setFamily($creator['lastName']);
			if (!empty($creator['firstName'])) {
				$person->setGiven($creator['firstName']);
			}
			$creatorTypes[$creator['creatorType']]->setPerson($person);
		}
	}

	private function setIssue($value) {
		$this->entry->setNumber($value);
	}

	private function setVolume($value) {
		$this->entry->setVolume($value);
	}

	private function setPublicationTitle(string $title) {
		$this->entry->setJournal($title);
	}

	private function generateCitationLabel(): string
	{
		$parts = [];
		if ($this->entry->getAuthor()->count() > 0) {
			/** @var Person $firstAuthor */
			$firstAuthor = $this->entry->getAuthor()->offsetGet(0);
			$parts[] = $firstAuthor->getFamily();
		}
		$title = preg_replace("/^\s*(der|die|das|the)/i", '', $this->entry->getTitle() ?: '' );
		$firstWord = explode(" ", $title);
		$parts[] = $firstWord[0];
		if ( $this->entry->getIssued()->count() > 0) {
			/** @var Date $issued */
			$issued = $this->entry->getIssued()->offsetGet(0);
			$parts[] = $issued->getYear();
		}
		return implode("_", array_map('strtolower', array_filter( $parts ) ));
	}

	// Dummy functions for internal stuff

	private function setPotentialItemTypes() {
		// Do nothing
	}

	private function setDebug() {
		// Do nothing
	}

}
