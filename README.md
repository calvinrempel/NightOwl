NightOwl
=======================

Introduction
------------
An administration panel for Hootsuite's Dark Launch Codes.

Required Software
-----------------
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
