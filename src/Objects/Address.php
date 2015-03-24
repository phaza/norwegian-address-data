<?php namespace Phaza\NorwegianAddressData\Objects;

use Academe\Proj4Php\Mgrs\Utm;
use DateTime;
use DateTimeZone;
use Phaza\NorwegianAddressData\Exceptions\UnknownAddressTypeException;

abstract class Address implements AddressInterface {
	public $id;
	public $municipality_id;
	public $zip_code_id;
	public $zip_code_name;
	public $display;

	/**
	 * @var DateTime
	 */
	public $last_update;

	/**
	 * @var Utm
	 */
	protected $coords;

	/**
	 * Create a new address from $buffer data
	 *
	 * @param $buffer
	 * @return CadastreAddress|StreetAddress
	 */
	public static function makeFromBuffer( $buffer )
	{
		$type          = self::getSOSIValue( $buffer[1] );
		$zipStart      = array_search( "..POSTNUMMEROMRÅDE", $buffer );
		$addressBuffer = array_slice( $buffer, 4, $zipStart - 4 );

		switch( $type ) {
			case 'Matrikkeladresse':
				$obj = CadastreAddress::fromBuffer( $addressBuffer );
				break;
			case 'Vegadresse':
				$obj = StreetAddress::fromBuffer( $addressBuffer );
				break;
			default:
				throw new UnknownAddressTypeException( sprintf( "Type: %s\n\nBuffer: %s", $type, implode( "\n", $buffer ) ) );
		}

		$obj->id              = (int) substr( self::getSOSIValue( $buffer[0] ), 0, -1 );
		$obj->municipality_id = str_pad( self::getSOSIValue( $buffer[3] ), 4, '0', STR_PAD_LEFT );
		$obj->zip_code_id     = str_pad( self::getSOSIValue( $buffer[ $zipStart + 1 ] ), 4, '0', STR_PAD_LEFT );
		$obj->zip_code_name   = self::unwrapString( self::getSOSIValue( $buffer[ $zipStart + 2 ] ) );
		$obj->display         = self::unwrapString( self::findValue( $buffer, 'ADRESSETEKST' ) );
		$obj->last_update     = DateTime::createFromFormat(
			'Ymd',
			self::findValue( $buffer, 'OPPDATERINGSDATO' ),
			new DateTimeZone( 'Europe/Oslo' )
		);

		$obj->last_update->setTime( 0, 0, 0 );

		$preCords = array_search( "..NØ", $buffer );
		list( $north, $east ) = explode( ' ', $buffer[ $preCords + 1 ] );
		$obj->setCoords( $north / 100.0, $east / 100.0 );

		return $obj;
	}

	/**
	 * @param float $north
	 * @param float $east
	 */
	public function setCoords( $north, $east )
	{
		$this->coords = new Utm( $north, $east, 33, 'N' );
	}

	/**
	 * @return Utm
	 */
	public function getCoords()
	{
		return $this->coords;
	}

	protected static function getSOSIValue( $string )
	{
		return trim( substr( $string, strpos( $string, ' ' ) + 1 ) );
	}

	protected static function unwrapString( $string )
	{
		$left = strpos( $string, '"' );

		return trim( substr( $string, strpos( $string, '"' ) + 1, strrpos( $string, '"' ) - 1 - $left ) );
	}

	protected static function findValue( array $haystack, $needle )
	{
		foreach( $haystack as $straw ) {
			if( strpos( $straw, $needle ) !== false ) {
				return self::getSOSIValue( $straw );
			}
		}

		return null;
	}

}
