## This package was sponsored by [tjenestetorget.no][1] / [helsetjenester.no][2]

# What
This package contains functionality to parse Norwegian address information from the Norwegian mapping authority. 

# How
**Install the package**  
    
	composer require "phaza/norwegian-address-data"

**Use the parser**

Bare sosi file
```PHP
$sosiparser = new SOSIParser(
	function ( Address $address ) {
		//Do something with $address here
	}
);

$handle = fopen( 'somefile.sos', 'r' );
$sosiparser->parse( $handle );
fclose( $handle );
```

Zip file from the Norwegian mapping authority
```PHP
$sosiparser = new SOSIParser(
	function ( Address $address ) {
		//Do something with $address here
	}
);

$file = new SplFileInfo('Vegdata_Norge_Adresser_UTM33_SOSI.zip');

$unwrapper = new ZipUnwrapper( $file, $sosiparser );
$unwrapper->parse();
```


[1]: http://tjenestetorget.no
[2]: http://helsetjenester.no
