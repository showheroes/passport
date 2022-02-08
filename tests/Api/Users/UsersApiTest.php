<?php

namespace ShowHeroes\Passport\Tests\Api\Users;

use ShowHeroes\Passport\Tests\Api\ApiTestCase;
use ShowHeroes\Passport\Tests\Fixtures\Apis\UsersApi;

/**
 * Class UsersApiTest
 * @package ShowHeroes\Passport\Tests\Api\Users
 *
 * @group users
 */
class UsersApiTest extends ApiTestCase
{

    use UsersApi;

    public function test_get_current_user_requires_auth()
    {
        $this->getCurrentUserDataViaAPI();
        $this->assertApiUnauthenticatedError();
    }

    public function test_get_current_user()
    {
        $this->authorise();
        $response = $this->getCurrentUserDataViaAPI();
        $this->assertResponseOk();
        $responseData = $response->json()['data'];
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertArrayHasKey('email', $responseData);
    }

}
