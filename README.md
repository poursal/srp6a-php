# Thinbus SRP PHP

Copyright (c) Simon Massey, 2015-2017

[Thinbus SRP PHP](https://bitbucket.org/simon_massey/thinbus-php) is an implementation of the SRP-6a Secure Remote Password protocol. It also includes and ships with [Thinbus](https://bitbucket.org/simon_massey/thinbus-srp-js) JavaScript SRP files. As this PHP package has PHP client and server code you can generate a verifier for a temporary password in PHP and have users login to a PHP server using a browser with the JavaScript. 

This PHP library project is published at [Packagist](https://packagist.org/packages/simon_massey/thinbus-php-srp). There is also a seperate repo that has a demo of how to use this library project at [thinbus-php-srp-demo](https://github.com/simbo1905/thinbus-php-srp-demo). 

**Note** Please read the [Thinbus documentation page](https://bitbucket.org/simon_massey/thinbus-srp-js) before attempting to use this library code as that is the documenation for the JavaScript that runs at the browser which is shipped with this PHP library. Also please try running the demo project at [thinbus-php-srp-demo](https://github.com/simbo1905/thinbus-php-srp-demo) and use browser developer tools to watch the SRP6a protocol run over AJAX. 

## Install Dependencies And Run Unit Tests

```sh
composer update
vendor/bin/phpunit ThinbusTest.php
```

There is a demo application that uses this library at [https://packagist.org/packages/simon_massey/thinbus-php-srp-demo](https://packagist.org/packages/simon_massey/thinbus-php-srp-demo). That shows that after running `composer update` the thinbus php code is stored under the `vendor` folder which is where the application loads it from. 



## Using In Your Application

The core PHP library files are in the `thinbus` folder:

* `thinbus/thinbus-srp-config.php` SRP configuration global variables. Must be included before the thinbus library code. Must match the values configured in the JavaScript. 
* `thinbus/thinbus-srp.php` PHP port of the Thinbus Java SRP server code based on code by [Ruslan Zavacky](https://github.com/RuslanZavacky/srp-6a-demo).
* `thinbus/thinbus-srp-common.php` common functions used by the client and server. 
* `thinbus/BigInteger.php` pear.php.net [BigInteger math package](http://pear.php.net/package/BigInteger).
* `thinbus/srand.php` strong random numbers from [George Argyros](https://github.com/GeorgeArgyros/Secure-random-bytes-in-PHP) avoiding known buggy versions of random libraries.
* `thinbus/thinbus-srp-client.php` PHP client code contributed by Keith Wagner.     

The core Thinbus JavaScript library files are in the `resources/thinbus` folder: 

* `thinbus/rfc5054-safe-prime-config.js` A sample configuration. See the main thinbus documentation for how to create your own safe prime. 
* `thinbus/thinbus-srp6a-sha256-versioned.js` The thinbus JS library which is tested in the [main project](https://bitbucket.org/simon_massey/thinbus-srp-js). See the header in that file which states the version. 

The file `thinbus\thinbus-srp-config.php` contains the SRP constants which looks something like: 

```
$SRP6CryptoParams = [
    "N_base10" => "19502997308..."
    "g_base10" => "2",
    "k_base16" => "1a3d1769e1d..."
    "H" => "sha256"
];
```

The numeric constants must match the values configured in the JavaScript file `resource\thinbus\rfc5054-safe-prime-config.js`; see the [Thinbus documentation](https://bitbucket.org/simon_massey/thinbus-srp-js). 
Consider creating your own large safe prime values using openssl using the Thinbus instructions. 

You need to understand that:

* Every users has a password verifier and a unique salt that you store in your database. This implementation uses the [RFC2945](https://www.ietf.org/rfc/rfc2945.txt) approach of hashing the username into the password verifier. This means that if your application lets a user change their username then they will be locked out unless you generate and store a fresh password verifier.  
* At every login attempt the browser first makes an AJAX call to get a one-time random challenge and the user salt from the server. The browser then uses that to compute a one-time proof-of-password and then immediately posts the proof-of-password to the server. The server checks the proof-of-password for the one-time challenge using the information stored in the user database. This means the server has to hold the thinbus object that generated the challenge for a short period (either your favourite cache or in your main database). 

The following diagram shows what you need to know: 

![Thinbus SRP Login Diagram](http://simonmassey.bitbucket.io/thinbus/login.png "Thinbus SRP Login Diagram")

It is expected that you create your own code for loading and saving data to a real database. Do not use my demo application's SQLLite or RedBean code. Only use the PHP files at `thinbus\*.php` folder of this repo. It is expected that you use your own code for handling authorisation of which pages users can or cannot access. Trying to modifying the demo files to support your application may be harder than just modifying your current application to simply use the core Thinbus library at `thinbus\*.php`. 

Please read the recommendations in the [main thinbus documentation](https://bitbucket.org/simon_massey/thinbus-srp-js) and take additional steps such as using HTTPS and encrypting the password verifier in the database which are not shown in this demo. 

**Note:** It is *strongly* *recommended* that you install the PHP [Open SSL extention](http://php.net/manual/en/book.openssl.php) which the random number generator in `srand.php` will try to use. If it cannot find that extension then it's second choice is the PHP [Mcrypt Extension](http://php.net/manual/en/book.mcrypt.php). If it cannot find that or if it is running on Windows it uses it's own random number generating approach by [George Argyros](https://github.com/GeorgeArgyros/Secure-random-bytes-in-PHP). 

## Troubleshooting

If you are having problems first check that the PHP unit code runs locally on your worksation using the exact same version of PHP as you run on your server: 

```sh
composer update
vendor/bin/phpunit --verbose ThinbusTest.php
```

If all test pass should output a final line such as `OK (xx tests, yyy assertions)`. If not raise an issue with the verbose output of the phpunit command and the output of `phpinfo();` on your system. 

## License

```
   Copyright 2015-2017 Simon Massey

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
```
End.