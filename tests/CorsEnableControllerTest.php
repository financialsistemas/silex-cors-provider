<?php

namespace JDesrosiers\Silex\Provider\Tests;

use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Silex\Application;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class CorsEnableControllerTest extends PHPUnit_Framework_TestCase
{
    protected HttpKernelBrowser $client;

    public function setUp(): void
    {
        $app = new Application();
        $app["debug"] = true;
        $app->register(new CorsServiceProvider());

        $controller = $app->get("/foo", function () {
            return "foo";
        });
        $app["cors-enabled"]($controller);

        $app->get("/bar", function () {
            return "bar";
        });

        $this->client = new HttpKernelBrowser($app, ["HTTP_ORIGIN" => "http://www.foo.com"]);
    }

    public function testEnabledPreflight()
    {
        $this->client->request("OPTIONS", "/foo");
        $response = $this->client->getResponse();

        $this->assertTrue($response->isEmpty());
        $this->assertTrue($response->headers->has("Access-Control-Allow-Origin"));
    }

    public function testNotEnabledPreflight()
    {
        $this->client->request("OPTIONS", "/bar");
        $response = $this->client->getResponse();

        $this->assertFalse($response->headers->has("Access-Control-Allow-Origin"));
    }

    public function testEnabledController()
    {
        $this->client->request("GET", "/foo");
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertTrue($response->headers->has("Access-Control-Allow-Origin"));
    }

    public function testNotEnabledController()
    {
        $this->client->request("GET", "/bar");
        $response = $this->client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertFalse($response->headers->has("Access-Control-Allow-Origin"));
    }
}