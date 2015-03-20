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

** Description of the address object **
The address object comes in two flavours. One for addresses with a street and number (StreetAddress), and one for those 
which doesn't have a street and number (CadastreAddress).

	common attributes:
	$id;              // Unique id of this point within this municipality
	$municipality_id; // The id of the municipality this address is contained within
	$zip_code_id;     // The zip code id
	$zip_code_name;   // The zip code name
	$display;         // The display version of this address
	getCoords()       // Coordinates for this point, returned as an Utm object with northing and easting.
	
	CadastreAddress:
	$gnr; // land parcel number, unique within a municipality
	$bnr; // property number, unique within a $gnr
	$fnr; // rental property number, unique within a $bnr
	$unr; // sub division number, unique within a $fnr
	
	StreetAddress:
	$street_name;
	$street_id;   // unique within a municipality
	$number;
	$letter;


[1]: http://tjenestetorget.no
[2]: http://helsetjenester.no
