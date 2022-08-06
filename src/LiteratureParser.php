<?php
declare( strict_types=1 );

namespace Birke\LiteratureMatcher;

class LiteratureParser {

	public function parse( string $text, AbstractContext $initialContext, mixed $initialData ) {
		$tokens = new TokenScanner( LiteratureLexer::run( $text ) );
		$currentContext = $initialContext;
		do {
			$currentContext = $currentContext->execute( $tokens, $initialData );
		} while( $currentContext !== null );
	}

}
