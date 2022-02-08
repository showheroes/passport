<?php

namespace ShowHeroes\Passport\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use ShowHeroes\Passport\Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
