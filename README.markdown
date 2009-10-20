# EasyOFSR

A PHP class to get links from online file storage websites. An easy online file storage retriever.

It takes the HTML link for a file on and online file storage site, and gets the premium link by logging in with your user credentials.

Please note this is currently version 0.1 ALPHA and is not functional.

## Requires

This class requires [Sean Hubers](http://github.com/shuber/) [curl wrapper](http://github.com/shuber/curl) and the [PHP Simple HMTL DOM Parser](http://simplehtmldom.sourceforge.net/index.htm).

## Usage

### Initialization

Initialize like this:

        require 'class.easyofsr.php';
        $ofs = new OFSR;

### Login like this

You need to log in with your user credentials to get a premium link from some sites.

        $ofs->dsn('username:password@hotfile');
        $ofs->dsn('username:password@rapidshare');

### Get a premium link

Here is how you would get a singular link:

        $url = 'http://hotfile.com/dl/11523121/a883a04/District_9.2009.R5.Xvid-Noir.part1.rar.html';
        $file = $ofs->get($url);

        // Check if the file is still live and do stuff
        if($file->live()) {
                echo $file->link() .' : '. $file->size();
        } else {
                echo $file->error();
        }

        // Single line determination
        // prints $file->link() if $file->live() is equal to true
        // prints $file->error() if $file->live() is equal to false
        echo $file->result();

Here is how you would get a bunch of links:

        $array = array(
                'http://hotfile.com/dl/11523121/a883a04/District_9.2009.R5.Xvid-Noir.part1.rar.html',
                'http://hotfile.com/dl/11523121/a883a04/District_9.2009.R5.Xvid-Noir.part2.rar.html'
        );

        // Returns an array
        $files = $ofs->get($array);

        // Run through each object
        foreach($files as $file) {
                echo $file->result() . '<br/>';
        }

What you do with the files is up to you ;)

