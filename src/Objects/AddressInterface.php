<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 20/03/15
 * Time: 10:03
 */
namespace Phaza\NorwegianAddressData\Objects;

interface AddressInterface {
	public static function fromBuffer(array $parts);
}
