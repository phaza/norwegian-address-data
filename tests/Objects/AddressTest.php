<?php namespace Objects;

use BaseTestCase;
use Phaza\NorwegianAddressData\Objects\Address;

class AddressTest extends BaseTestCase {
	public function testUnwrapString()
	{
		$this->assertEquals( 'This is a test', TestAddress::callStaticMethod( 'unwrapString', [ '"This is a test"' ] ) );
		$this->assertEquals( 'This is a test', TestAddress::callStaticMethod( 'unwrapString', [ ' "This is a test"' ] ) );
		$this->assertEquals( 'This is a test', TestAddress::callStaticMethod( 'unwrapString', [ ' "This is a test" ' ] ) );
		$this->assertEquals( 'This is a test', TestAddress::callStaticMethod( 'unwrapString', [ '"This is a test" ' ] ) );
	}
}

class TestAddress extends Address {
	public static function fromBuffer( array $parts )
	{
		return new self;
	}

	public static function callStaticMethod( $method, $args )
	{
		return forward_static_call_array( [ TestAddress::class, $method ], $args );
	}
}
