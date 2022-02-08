<?php

namespace ShowHeroes\Passport\Tests\Fixtures\Apis;

use Illuminate\Testing\TestResponse;

/**
 * Trait UsersApi
 * @package ShowHeroes\Passport\Tests\Stubs\Apis
 */
trait UsersApi
{

    private $apiUsersURL = '/api/v1/users';

    /**
     * @return TestResponse
     */
    public function getCurrentUserDataViaAPI()
    {
        return $this->getJson($this->apiUsersURL . '/current');
    }

}
