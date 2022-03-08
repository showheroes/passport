<?php

namespace ShowHeroes\Passport\Http\Transformers\Users;

use League\Fractal\TransformerAbstract;
use ShowHeroes\Passport\Models\User;

/**
 * @OA\Parameter(parameter="ParameterUserIncludes", name="includes", in="query", description="Comma separated list of includes for user", explode=false, required=false,
 *    @OA\Schema(type="array",
 *       @OA\Items(type="string",
 *        enum={
 *          "team",
 *        },
 *      )
 *    )
 * )
 *
 * @OA\Schema(
 *     schema="UserResource",
 *     @OA\Property(property="id", description="ID", format="int64", example=1),
 *     @OA\Property(property="name", description="User name", example="James Bond"),
 *     @OA\Property(property="email", description="User email", example="james@mi6.uk"),
 * )
 */
class UserTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'team',
    ];

    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        $team = $user->passportCurrentTeam();
        $data = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'team_id' => $team->id,
            'team_name' => $team->name,
            'team_slug' => '',
            'avatar' => $user->photo_url
        ];

        return $data;
    }

    /**
     * @param User $user
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTeam(User $user)
    {
        $team = $user->currentTeam;
        if (!$team) {
            return null;
        }

        return $this->item($team, new TeamTransformer());
    }


}
