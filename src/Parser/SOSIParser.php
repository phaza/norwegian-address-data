<?php namespace Phaza\NorwegianAddressData\Parser;

use Closure;
use Phaza\NorwegianAddressData\Objects\Address;

class SOSIParser implements SOSIParserInterface {
	/**
	 * @var Closure
	 */
	private $callback;
	/**
	 * @var resource
	 */
	private $handle;

	public function __construct( Closure $callback )
	{
		$this->callback = $callback;
	}

	public function parse( $handle )
	{
		$this->handle = $handle;
		stream_filter_append( $this->handle, 'convert.iconv.ISO-8859-15/UTF-8', STREAM_FILTER_READ );

		$callback = $this->callback;

		while( ( $line = $this->seekTo( '.PUNKT' ) ) !== false ) {
			$buffer = [$line];
			$this->seekTo( '..NØ', $buffer );
			$buffer[] = fgets( $this->handle ); // coordinates

			$adr = Address::makeFromBuffer( $buffer );

			$callback( $adr );
		}

	}

	protected function seekTo( $string, array &$buffer = null )
	{
		do {
			$line = fgets( $this->handle );

			if($buffer) {
				$buffer[] = trim($line);
			}

		} while( $line !== false && strpos( $line, $string ) === false );

		return $line ? trim($line) : false;
	}
}
