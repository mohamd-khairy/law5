<?php

namespace App\Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * test homepage
     *
     * @return void
     */
    public function testHomepage()
    {
        $this->get('/');

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals($this->app->version(), $this->response->getContent());
    }

    /**
     * test NotFound Page
     */
    public function testNotFoundRoute(){
        $this->get('/nonexistentPage');

        $this->assertEquals(404, $this->response->getStatusCode());
    }
}
