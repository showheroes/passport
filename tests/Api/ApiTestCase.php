<?php

namespace ShowHeroes\Passport\Tests\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ShowHeroes\Passport\Tests\Fixtures\Apis\AuthFixtureProvider;
use ShowHeroes\Passport\Tests\Fixtures\Apis\ResponseFormatAssertions;
use ShowHeroes\Passport\Tests\TestCase;

/**
 * Class ApiTestCase
 * @package ShowHeroes\Passport\Tests\Api
 *
 * General API test case class.
 */
abstract class ApiTestCase extends TestCase
{

    use DatabaseTransactions, ResponseFormatAssertions, AuthFixtureProvider;

    protected $connectionsToTransact = ['mysql'];

    public function createApplication()
    {
        return parent::createApplication();
    }

    public function json($method, $uri, array $data = [], array $headers = [])
    {
        $testResponse = parent::json($method, $uri, $data, $headers);
        $this->responseData = json_decode($testResponse->getContent(), true);
        $this->responseCode = $testResponse->getStatusCode();
        return $testResponse;
    }

}
