<?php namespace Phaza\NorwegianAddressData\Objects;

class StreetAddress extends Address {
	public $street_name;
	public $street_id;
	public $number;
	public $letter;

	public static function fromBuffer( array $parts )
	{
		$c              = new self;
		$c->street_name = self::unwrapString( $parts[0] );
		$c->street_id   = (int)self::findValue( $parts, 'ADRESSEKODE' );
		$c->number      = (int)self::findValue( $parts, 'NUMMER' );
		$letter         = self::findValue( $parts, 'BOKSTAV' );

		if( !empty( $letter ) ) {
			$c->letter = self::unwrapString( $letter );
		}

		return $c;
	}

}
