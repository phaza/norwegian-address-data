<?php namespace Phaza\NorwegianAddressData\Objects;

class CadastreAddress extends Address {
	public $gnr;
	public $bnr;
	public $fnr;
	public $unr;

	public static function fromBuffer( array $parts )
	{
		$c      = new self;
		$c->gnr = (int) self::getSOSIValue( $parts[0] );
		$c->bnr = (int) self::getSOSIValue( $parts[1] );

		$c->unr = self::findValue( $parts, 'UNR' );
		if( !empty( $c->unr ) ) {
			$c->unr = (int) $c->unr;
		}

		$c->fnr = self::findValue( $parts, 'FNR' );
		if( !empty( $c->fnr ) ) {
			$c->fnr = (int) $c->fnr;
		}

		return $c;
	}
}
