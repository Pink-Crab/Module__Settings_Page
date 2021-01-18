# PinkCrab / Settings_Page #

Settings page is extendable class which allows for the registration, display and processing of Settings in WordPress.

![alt text](https://img.shields.io/badge/Current_Version-0.2.0-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)

![alt text](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat " ") 
![alt text](https://img.shields.io/badge/PHPUnit-PASSING-brightgreen.svg?style=flat " ") 
![alt text](https://img.shields.io/badge/PHCBF-WP_Extra-brightgreen.svg?style=flat " ") 


For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/

## Version ##
**Release 0.2.0**

We have made no changes to how this works from V0.1.*, but we have now moved to using composer purely for this package. You can still use it without composer, but the classes and interfaces would need to be added manually.

## Install ##

````bash
composer require pinkcrab/settings_pages
````

## Why? ##
The WordPress WP-Admin has some interesting API's and sadly most of them have not really changed much over the years. As a result the hoops that need jumping through are fustrating at times. 

So we created this module to make it not only quicker and easier to create settings pages, but also to leverage out Form_Fields module to remove the need to write out each input in raw HTML.

## Testing ##

### PHP Unit ###
If you would like to run the tests for this package, please ensure you add your database details into the test/wp-config.php file before running phpunit.
````bash
$ phpunit
````
````bash 
$ composer test
````

### PHP Stan ###
The module comes with a pollyfill for all WP Functions, allowing for the testing of all core files. The current config omits the Dice file as this is not ours. To run the suite call.
````bash 
$ vendor/bin/phpstan analyse src/ -l8 
````
````bash 
$ composer analyse
````


## License ##

### MIT License ###
http://www.opensource.org/licenses/mit-license.html  

## Change Log ##
0.2.0 - Moved to composer, renamed all namespaces to match the composer format.
