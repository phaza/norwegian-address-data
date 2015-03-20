<?php

class BaseTestCase extends PHPUnit_Framework_TestCase {

	/**
	 * @var string
	 */
	protected $testZipFile;
	/**
	 * @var SplFileInfo
	 */
	protected $zipFileInfo;
	/**
	 * @var string
	 */
	protected $testLargeSosiFile;
	/**
	 * @var SplFileInfo
	 */
	protected $largeSosiFileInfo;
	/**
	 * @var string
	 */
	protected $testSmallSosiFile;
	/**
	 * @var SplFileInfo
	 */
	protected $smallSosiFileInfo;

	public function setUp()
	{
		$this->testZipFile = __DIR__ . '/data/PARTIAL-Vegdata_Norge_Adresser_UTM33_SOSI.zip';
		$this->zipFileInfo = new SplFileInfo( $this->testZipFile );

		$this->testLargeSosiFile = __DIR__ . '/data/0101Adresser.sos';
		$this->largeSosiFileInfo = new SplFileInfo( $this->testLargeSosiFile );

		$this->testSmallSosiFile = __DIR__ . '/data/0118Adresser.sos';
		$this->smallSosiFileInfo = new SplFileInfo( $this->testSmallSosiFile );
	}
}
