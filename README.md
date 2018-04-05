
# BlcksheepIO HTTP Messaging
A collection of [PSR-7](https://www.php-fig.org/psr/) compliant classes to aid in the development of HTTP messages.

Build status: [![Build Status](https://travis-ci.org/blcksheepio/http-message.svg?branch=master)](https://travis-ci.org/blcksheepio/http-message)

## Installation
The easiest means to install HTTP Messaging is to use [composer](https://getcomposer.org/). Simply run the following from your composer enabled project directory to install the latest stable version:

    composer require blcksheepio/http-message

## Features
Considering that the classes provided are a realisation of PSR-7, all classes implement the appropriate interfaces and consequential methods defined in the standard.

The official PSR-7 [repository](https://github.com/php-fig/http-message) is added as a composer dependency.
    
## Usage
All files within this repository exist with in the PSR-4 compliant namespace, BlcksheepIO\Http\Message. To include a file simply add the appropriate use statement at the start of your PHP file.

    <?php
    namepsace ACME\MyProjectNamespace
    // Including BlcksheepIO\Http\Message\RequestInterface
    use BlcksheepIO\Http\Message\Request;
    
	class Foo {
		public function fooBar() {
			// Instantiate a new instance of the Request
			$request = new Request();
			// Example usage of instance method
			$uri = $request->getUri();
		}
	}

## Tests
All tests are written using [PHPUnit](https://phpunit.de/) and can be found under the ./tests/ directory. A phpunit.xml file has been included too.