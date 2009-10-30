# EasyOFSR

A PHP class to get links from online file storage websites. An easy online file storage retriever.

It takes the HTML link for a file on and online file storage site, and gets the premium link by logging in with your user credentials.

Please note this is currently version 0.3 beta so don't expect everything to work.

### Issues
* Upgraded the hotfile module to 0.4a as yet untested.  Should handle links properly now.
* The rapidshare module is version 0.2a should work but as yet untested.

## Requires

This class requires [Sean Hubers](http://github.com/shuber/) [curl wrapper](http://github.com/shuber/curl) and the [PHP Simple HMTL DOM Parser](http://simplehtmldom.sourceforge.net/index.htm).  It also requires the [PHP_RSGet](http://github.com/hamstar/PHP_RSGet) class for pulling links from rapidshare.

## Usage

### Initialization

Initialize like this:

        require 'EasyOFSR.php';
        $ofs = new OFSR;

### Login like this

You need to log in with your user credentials to get a premium link from some sites.

        $ofs->dsn('username:password@hotfile.com');
        $ofs->dsn('username:password@rapidshare.com');

### Get a premium link

Here is how you would get a singular link:

	// This could also be an array
        $url = 'http://hotfile.com/dl/11523121/a883a04/District_9.2009.R5.Xvid-Noir.part1.rar.html';

	// Get an array of the files found
        $files = $ofs->getLinks($url);

        // Check if the file is still live and do stuff
	if($files) {
		foreach ( $files as $file ) {
			// Multi-line getting
		        if($file->live()) {
                		echo $file->link() .' : '. $file->size();
		        } else {
                		echo $file->error();
			}

	        	// Single line getting
		        // prints $file->link() if $file->live() is equal to true
        		// prints $file->error() if $file->live() is equal to false
		        echo $file->result();
		}
	}

What you do with the files is up to you ;)

