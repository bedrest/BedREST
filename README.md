# BedREST

__BedREST__ is a framework which makes creating RESTful APIs a breeze. Keeping you
focused on the resources and models you want to expose, not the code to make it
happen.

## Features

* __Simple request handling__  
  Gives you easy access to all the information contained in a request with no fuss.
* __Content negotiation__  
  Content negotiation is performed automatically and a bunch of content converters
  make it easy to support all the content types your API needs - both for requests
  _and_ responses.
* __Service management with dependency injection__  
  Services are the bones of any API, so BedREST makes it easy to create and use
  them through robust dependency injection, courtesy of the Symfony framework.
* __Authentication__ _(in v1.0.0)_  
  If you need a quick and easy way to implement authentication for your API,
  BedREST has you covered. Adapters for OAuth v1.0a, simple tokens and more are
  planned for v1.0.0, with a simple authentication framework in place to allow you
  to implement your own if needed.
* __Permissions__ _(in v1.0.0)_  
  Going hand in hand with the authentication framework, it will be easy to create
  and enforce permissions with various levels of granularity.
* __Integration with a whole load of frameworks__  
    * Zend Framework 1
    * Slim (to come)
    * Symfony (to come)
    * Doctrine ORM
    * Doctrine ODM (to come)
* __Simple to extend, just pick what you need__  
* __Ultimate control through configuration__  

## Requirements

* PHP v5.3+

## Installation with Composer

Installation couldn't be easier with Composer. All dependencies will be grabbed automatically - just sit back and enjoy.

```bash
composer require bedrest/bedrest
```
