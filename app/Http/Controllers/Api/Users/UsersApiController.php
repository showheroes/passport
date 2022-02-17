<?php

namespace ShowHeroes\Passport\Http\Controllers\Api\Users;

use ShowHeroes\Passport\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use ShowHeroes\Passport\Http\Requests\Api\ApiRequest;
use ShowHeroes\Passport\Http\Controllers\Api\ApiController;
use EvgenyL\RestAPICore\Http\Responses\FormattedJSONResponse;
use ShowHeroes\Passport\Http\Transformers\Users\UserTransformer;

/**
 * Class UsersApiController
 * @package ShowHeroes\Passport\Http\Controllers\Api\Users
 */
class UsersApiController extends ApiController
{

    public function __construct()
    {
        $this->setModelTransformer(new UserTransformer());
    }

    /**
     * @OA\Get(
     *      path="/users/{id}",
     *      tags={"Users"},
     *      security={
     *          {"bearerAuth": {}},
     *      },
     *      summary="Get user by id",
     *      description="Returns user data by user id",
     *
     *      @OA\Parameter(name="id", in="path", description="Id of user", required=true,  @OA\Schema(type="integer")),
     *      @OA\Parameter(ref="#/components/parameters/ParameterUserIncludes"),
     *
     *      @OA\Response(response=200, description="Successful operation",
     *          @OA\MediaType(mediaType="application/json",
     *              @OA\Schema(allOf={
     *                @OA\Schema(ref="#/components/schemas/StandardDataResponse"),
     *                @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/UserResource")),
     *              })
     *          )
     *      ),
     *
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not Found"),
     * )
     */
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param ApiRequest $request
     * @return FormattedJsonResponse
     * @throws AuthorizationException
     */
    public function show(int $id, ApiRequest $request): FormattedJSONResponse
    {
        /** @var User $model */
        $model = User::withTrashed()->findOrFail($id);
        $this->authorize('show', $model);
        $data = $this->convertModelJsonData($request, $model);
        return FormattedJsonResponse::show($data);
    }

    /**
     * @OA\Get(
     *      path="/users/current",
     *      tags={"Users"},
     *      security={
     *          {"bearerAuth": {}},
     *      },
     *      summary="Get current user",
     *      description="Returns current user data",
     *
     *      @OA\Parameter(ref="#/components/parameters/ParameterUserIncludes"),
     *
     *      @OA\Response(response=200, description="Successful operation",
     *          @OA\MediaType(mediaType="application/json",
     *              @OA\Schema(allOf={
     *                @OA\Schema(ref="#/components/schemas/StandardDataResponse"),
     *                @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/UserResource")),
     *              })
     *          )
     *      ),
     *
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Not Found"),
     * )
     */
    /**
     * Display the specified resource.
     *
     * @param ApiRequest $request
     * @return FormattedJsonResponse
     */
    public function show_current(ApiRequest $request): FormattedJSONResponse
    {
        /** @var User $model */
        $model = $request->user();
        $data = $this->convertModelJsonData($request, $model);
        return FormattedJsonResponse::show($data);
    }
}
