<?php namespace Parser;

use BaseTestCase;
use Phaza\NorwegianAddressData\Objects\Address;
use Phaza\NorwegianAddressData\Objects\CadastreAddress;
use Phaza\NorwegianAddressData\Objects\StreetAddress;
use Phaza\NorwegianAddressData\Parser\SOSIParser;

class ParserTest extends BaseTestCase {

	public function testCallbackIsInvoked()
	{
		$called     = false;
		$sosiparser = new SOSIParser(
			function ( Address $address ) use ( &$called ) {
				$called = true;
			}
		);

		$handle = fopen( $this->smallSosiFileInfo->getRealPath(), 'r' );
		$sosiparser->parse( $handle );
		fclose( $handle );

		$this->assertEquals( $called, true );
	}

	public function testFindsAllPoints()
	{
		$count      = 0;
		$sosiparser = new SOSIParser(
			function ( Address $address ) use ( &$count ) {
				$count++;
			}
		);

		$handle = fopen( $this->largeSosiFileInfo->getRealPath(), 'r' );
		$sosiparser->parse( $handle );
		fclose( $handle );

		$this->assertEquals( 13520, $count );
	}

	public function testYieldsCorrectCadastreAddress()
	{
		/**
		 * @var CadastreAddress $address
		 */
		$address    = null;
		$sosiparser = new SOSIParser(
			function ( Address $addr ) use ( &$address ) {
				if( empty( $address ) ) {
					$address = $addr;
				}
			}
		);

		$handle = fopen( $this->smallSosiFileInfo->getRealPath(), 'r' );
		$sosiparser->parse( $handle );
		fclose( $handle );

		/*
		* .PUNKT 1:
		* ..OBJTYPE Matrikkeladresse
		* ..MATRIKKELADRESSEIDENT
		* ...KOMM 0118
		* ...GNR 60
		* ...BNR 1
		* ...FNR 69
		* ..POSTNUMMEROMR�DE
		* ...POSTNUMMER 1798
		* ...POSTSTED "AREMARK"
		* ..ADRESSETEKST "60/1/69"
		* ..OPPDATERINGSDATO 20140507
		* ..KOPIDATA
		* ...OMR�DEID 0118
		* ...ORIGINALDATAVERT "Matrikkelen"
		* ...KOPIDATO 20150127
		* ..N�
		* 657191456 30999501
		 */

		$this->assertInstanceOf( CadastreAddress::class, $address );
		$this->assertEquals( '0118', $address->municipality_id );
		$this->assertEquals( 60, $address->gnr );
		$this->assertEquals( 1, $address->bnr );
		$this->assertEquals( 69, $address->fnr );
		$this->assertEquals( '1798', $address->zip_code_id );
		$this->assertEquals( 'AREMARK', $address->zip_code_name );
		$this->assertEquals( '60/1/69', $address->display );
		$this->assertEquals( '2014.05.07', $address->last_update->format( 'Y.m.d' ) );
		$this->assertEquals( 6571914.56, $address->getCoords()->getNorthing(), 0.01 );
		$this->assertEquals( 309995.01, $address->getCoords()->getEasting(), 0.01 );
	}

	public function testYieldsCorrectStreetAddress()
	{
		/**
		 * @var StreetAddress $address
		 */
		$address    = null;
		$sosiparser = new SOSIParser(
			function ( Address $addr ) use ( &$address ) {
				if( empty( $address ) ) {
					$address = $addr;
				}
			}
		);

		$handle = fopen( $this->largeSosiFileInfo->getRealPath(), 'r' );
		$sosiparser->parse( $handle );
		fclose( $handle );

		/*
		* .PUNKT 1:
		* ..OBJTYPE Vegadresse
		* ..VEGADRESSEIDENT
		* ...KOMM 0101
		* ...ADRESSENAVN "S�lvgata"
		* ...ADRESSEKODE 6640
		* ...NUMMER 5
		* ..STED_VERIF JA
		* ..POSTNUMMEROMR�DE
		* ...POSTNUMMER 1767
		* ...POSTSTED "HALDEN"
		* ..ADRESSETEKST "S�lvgata 5"
		* ..OPPDATERINGSDATO 20150106
		* ..KOPIDATA
		* ...OMR�DEID 0101
		* ...ORIGINALDATAVERT "Matrikkelen"
		* ...KOPIDATO 20150127
		* ..N�
		* 655857599 29323821
		 */

		$this->assertInstanceOf( StreetAddress::class, $address );
		$this->assertEquals( '0101', $address->municipality_id );
		$this->assertEquals( 'Sølvgata', $address->street_name );
		$this->assertEquals( 6640, $address->street_id );
		$this->assertEquals( 5, $address->number );
		$this->assertEquals( '1767', $address->zip_code_id );
		$this->assertEquals( 'HALDEN', $address->zip_code_name );
		$this->assertEquals( 'Sølvgata 5', $address->display );
		$this->assertEquals( '2015.01.06', $address->last_update->format( 'Y.m.d' ) );
		$this->assertEquals( 6558575.99, $address->getCoords()->getNorthing(), 0.01 );
		$this->assertEquals( 293238.21, $address->getCoords()->getEasting(), 0.01 );
	}

}
