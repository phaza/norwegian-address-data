<?php namespace Phaza\NorwegianAddressData\Parser;

use Closure;

interface SOSIParserInterface {

	/**
	 * @param Closure  $closure
	 */
	public function __construct( Closure $closure );

	/**
	 * @param resource $resource
	 * @return void
	 */
	public function parse( $resource );
}
