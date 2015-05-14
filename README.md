NightOwl
=======================

Introduction
------------
An administration panel and server-side facade for Hootsuite's Dark Launch codes. You can use this admin panel to create and edit Dark Launch codes in various contexts and data centres. In addition, there is a history tab in which you can see all the changes made to the codes.

Required Software
-----------------
- Consul
- PHP
- MongoDB
- MongoDB PHP Driver

Configuration
-------------
**config/autoload/local.php**
```PHP
return array(
    'dbaccess' => 'mongodb://<user>:<pass>@<host>:<port>/<collection>'
);
```

**User Format (in mongo *Auth* collection)**

Users are located in the Auth collection using this format:
```JSON
{
    "user": "{username}",
    "pass": "{plain_text_pass}",
    "key": "",
    "keyTTL": ""
}  
```


* This format is temporary it is assumed that it will be changed at a later date to something more secure.

Consul Agent Setup
----------------
### OS X

Ensure you have cask plugin installed for homebrew:

    $ brew install caskroom/cask/brew-cask

You can then easily install Consul with:

    $ brew cask install consul
    
### Windows & Linux/Unix

To install Consul, find the appropriate package for your system and download it from here: https://www.consul.io/downloads.html

Unzip the package and copy the Consul binary to somewhere on the PATH so that it can be executed.
- On Unix systems, ~/bin and /usr/local/bin are common installation directories, depending on if you want to restrict the install to a single user or expose it to the entire system.
- On Windows systems, you can put it wherever you would like, as long as that location is on the %PATH%.
    
### Run the Consul agent
    
To run the Consul agent, simply enter this into your Terminal/Command Prompt:

    $ consul agent -server -bootstrap-expect 1 -data-dir <data-directory>

Web Server Setup
----------------

### PHP CLI Server

The simplest way to get started if you are using PHP 5.4 or above is to start the internal PHP cli-server in the root directory:

    php -S 0.0.0.0:8080 -t public/ public/index.php

This will start the cli-server on port 8080, and bind it to all network
interfaces.

**Note: ** The built-in CLI server is *for development only*.

### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerName nightowl.localhost
        DocumentRoot /path/to/nightowl/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/nightowl/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>
