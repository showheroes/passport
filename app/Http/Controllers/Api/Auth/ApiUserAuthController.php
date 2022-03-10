<?php

namespace ShowHeroes\Passport\Http\Controllers\Api\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use ShowHeroes\Passport\Http\Controllers\Api\ApiController;
use ShowHeroes\Passport\Http\Gateways\Auth\ApiUserAuthGateway;
use ShowHeroes\Passport\Exceptions\Auth\ApiAuthExceptions;
use EvgenyL\RestAPICore\Http\Responses\FormattedJSONResponse;
use ShowHeroes\Passport\Http\Requests\Api\Auth\SighInInUserRequest;
use ShowHeroes\Passport\Http\Requests\Api\Auth\RegistrationUserRequest;

/**
 * Class ApiUserAuthController
 * @package ShowHeroes\Passport\Http\Controllers\Api\Auth
 */
class ApiUserAuthController extends ApiController
{
    public const VALIDATION_DATA_EXCEPTION = 422;

    public ApiUserAuthGateway $gateway;

    public function __construct(ApiUserAuthGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param RegistrationUserRequest $request
     * @return array|FormattedJSONResponse|Application|ResponseFactory|Response
     */
    public function register(RegistrationUserRequest $request): Response|array|FormattedJSONResponse|Application|ResponseFactory
    {
        try {
            return $this->gateway->registerUser($request);
        } catch (Exception | ApiAuthExceptions $exception) {
            return FormattedJSONResponse::error(
                self::VALIDATION_DATA_EXCEPTION,
                $exception->getMessage()
            );
        }
    }

    /**
     * @param SighInInUserRequest $request
     * @return FormattedJSONResponse|Application|ResponseFactory|Response
     */
    public function signin(SighInInUserRequest $request): Response|FormattedJSONResponse|Application|ResponseFactory
    {
        try {
            return $this->gateway->signinUser($request);
        } catch (Exception | ApiAuthExceptions $exception) {
            return FormattedJSONResponse::error(
                self::VALIDATION_DATA_EXCEPTION,
                $exception->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function signout(Request $request): Application|ResponseFactory|Response
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response(['redirect' => '/']);
    }
}
