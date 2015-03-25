<?php namespace Phaza\NorwegianAddressData;

use FilesystemIterator;
use Phaza\NorwegianAddressData\Exceptions\FileNotReadableException;
use Phaza\NorwegianAddressData\Exceptions\ZipFileException;
use Phaza\NorwegianAddressData\Parser\SOSIParserInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use ZipArchive;

class ZipUnwrapper {
	/**
	 * @var string
	 */
	protected $tmpDir;
	/**
	 * @var ZipArchive
	 */
	private $archive;
	/**
	 * @var SOSIParserInterface
	 */
	private $sosiparser;

	public function __construct( SplFileInfo $file, SOSIParserInterface $sosiparser )
	{
		if( !$file->isReadable() ) {
			throw new FileNotReadableException( $file . " isn't readable. Does it exits?" );
		}

		$this->archive = new ZipArchive();
		$status        = $this->archive->open( $file->getRealPath() );
		if( $status !== true ) {
			throw new ZipFileException( $this->getError( $status ) );
		}

		$this->sosiparser = $sosiparser;
	}

	public function parse()
	{
		$dir   = $this->getTmpDir();
		$files = $this->getZipFiles();

		if( !$this->archive->extractTo( $dir, array_values( $files ) ) ) {
			throw new ZipFileException( sprintf( 'Could not extract files to "%s"', $dir ) );
		}

		foreach( $files as $basename => $file ) {
			$zipFile = $this->getTmpPath( $file );
			$handle  = fopen( sprintf( 'zip://%s#%s.sos', $zipFile, $basename ), 'r' );

			if( $handle === false ) {
				throw new RuntimeException( sprintf( 'Could not open "%s" for reading', $zipFile ) );
			}

			try {
				$this->sosiparser->parse( $handle );
			}
			finally {
				fclose( $handle );
				unlink( $zipFile );
			}
		}

		$this->recursiveRmDir( $dir );
	}

	protected function recursiveRmDir( $dir )
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach($iterator as $path) {
			$path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
		}
	}

	protected function getTmpPath( $file )
	{
		return sprintf( '%s/%s', $this->tmpDir, $file );
	}

	protected function getTmpDir()
	{
		if( $this->tmpDir ) {
			$this->recursiveRmDir( $this->tmpDir );
		}

		$tmpDir = tempnam( sys_get_temp_dir(), 'ziparchive' );
		if( $tmpDir ) {
			unlink( $tmpDir );
			mkdir( $tmpDir );
			if( is_dir( $tmpDir ) ) {
				$this->tmpDir = $tmpDir;

				return $this->tmpDir;
			}
		}

		throw new RuntimeException( sprintf( "Could not create temp directory '%s'", $tmpDir ) );
	}

	protected function getZipFiles()
	{
		$files = [ ];
		for( $i = 0; $i < $this->archive->numFiles; $i++ ) {
			$filename = $this->archive->getNameIndex( $i );
			if( preg_match( '/\d{4}Adresser.ZIP$/', $filename ) === 1 ) {
				$files[ basename( $filename, '.ZIP' ) ] = $filename;
			}
		}

		return $files;
	}

	protected function getError( $errCode )
	{
		switch( $errCode ) {
			case ZipArchive::ER_EXISTS:
				return "File already exists.";

			case ZipArchive::ER_INCONS:
				return "Zip archive inconsistent.";

			case ZipArchive::ER_MEMORY:
				return "Malloc failure.";

			case ZipArchive::ER_NOENT:
				return "No such file.";

			case ZipArchive::ER_NOZIP:
				return "Not a zip archive.";

			case ZipArchive::ER_OPEN:
				return "Can't open file.";

			case ZipArchive::ER_READ:
				return "Read error.";

			case ZipArchive::ER_SEEK:
				return "Seek error.";

			default:
				return sprintf( 'Unknown (Code %d)', $errCode );
		}
	}
}
