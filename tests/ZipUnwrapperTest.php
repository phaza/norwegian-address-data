<?php

use Phaza\NorwegianAddressData\Objects\Address;
use Phaza\NorwegianAddressData\Parser\SOSIParser;
use Phaza\NorwegianAddressData\ZipUnwrapper;

class ZipUnwrapperTest extends BaseTestCase {
	public function testFindsSosiFiles()
	{
		$gotAddress = false;
		$parser = new SOSIParser(
			function ( Address $address ) use (&$gotAddress) {
				$gotAddress = true;
			}
		);

		$unwrapper = new ZipUnwrapper( $this->zipFileInfo, $parser );
		$unwrapper->parse();

		$this->assertEquals( true, $gotAddress );
	}
}
