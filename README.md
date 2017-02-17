silex-cors-provider
===================

[![Build Status](https://travis-ci.org/jdesrosiers/silex-cors-provider.png?branch=master)](https://travis-ci.org/jdesrosiers/silex-cors-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jdesrosiers/silex-cors-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jdesrosiers/silex-cors-provider/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jdesrosiers/silex-cors-provider/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jdesrosiers/silex-cors-provider/?branch=master)

The CorsServiceProvider provides [CORS](http://enable-cors.org/) support as middleware for your silex application.  CORS
allows you to make AJAX requests across domains.  CORS uses OPTIONS requests to make preflight requests.  Because silex
doesn't have functionality for serving OPTIONS request by default, this service goes through all of your routes and
generates the necessary OPTIONS routes.

Installation
------------
Install the silex-cors-provider using [composer](http://getcomposer.org/).  This project uses [sematic versioning](http://semver.org/).

```bash
composer require jdesrosiers/silex-cors-provider "~1.0"
```

Parameters
----------
* **cors.allowOrigin**: (string) Space separated set of allowed domains (wildcards allowed e.g. *.example.com).
Defaults to all.
* **cors.allowMethods**: (string) Comma separated set of allowed HTTP methods.  Defaults to all.
* **cors.maxAge**: (int) The number of seconds a CORS pre-flight response can be cached.  Defaults to 0.
* **cors.allowCredentials**: (boolean) Are cookies allowed?  Defaults to false.
* **cors.exposeHeaders**: (string) Space separated set of headers that are safe to expose.  Defaults to all.

Services
--------
* **cors**: A function that can be added as after middleware to the Application, a ControllerCollection, or a Controller.
* **cors-enabled**: Pass this function an Application, ControllerCollection, or a Controller and it will enable CORS
support for any controller that object includes.

Registering
-----------
```php
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider(), [
    "cors.allowOrigin" => "http://petstore.swagger.wordnik.com",
]);
```

Usage
-----
Add CORS functionality to the entire application.
```php
$app->get("/foo/{id}", function ($id) { /* ... */ });
$app->post("/foo/", function () { /* ... */ });

$app["cors-enabled"]($app);
```
Add CORS functionality to a controller collection.
```php
$foo = $app["controllers_factory"];
$foo->get("/{id}", function () { /* ... */ });
$foo->post("/", function () { /* ... */ });
$app->mount("/foo", $app["cors-enabled"]($foo));

$app->get("/bar/{id}", function ($id) { /* ... */ }); // Not CORS enabled
```
Add CORS functionality to a controller.
```php
$controller = $app->get("/foo/{id}", function ($id) { /* ... */ });
$app["cors-enabled"]($controller);
$app->post("/foo/", function () { /* ... */ }); // Not CORS enabled
```
