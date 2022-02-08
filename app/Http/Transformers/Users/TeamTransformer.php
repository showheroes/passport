<?php

namespace ShowHeroes\Passport\Http\Transformers\Users;

use EvgenyL\RestAPICore\Http\Transformers\FormatDates;
use League\Fractal\TransformerAbstract;
use ShowHeroes\Passport\Models\Team;

/**
 * @OA\Schema(
 *   schema="TeamResource",
 *   description="TeamResource resource",
 *   allOf={
 *    @OA\Schema(
 *       @OA\Property(property="id", description="ID", format="int64", example=1),
 *       @OA\Property(property="name", description="Team name", example="James Bond"),
 *    ),
 *    @OA\Schema(ref="#/components/schemas/StandardLaravelTimestamps"),
 *   }
 * )
 */
class TeamTransformer extends TransformerAbstract
{

    use FormatDates;

    /**
     * @param Team $team
     * @return array
     */
    public function transform(Team $team)
    {
        $data = [
            'id'   => $team->id,
            'name' => $team->name,

            'created_at' => $this->isodate($team->created_at),
            'updated_at' => $this->isodate($team->updated_at),
        ];
        return $data;
    }

}
