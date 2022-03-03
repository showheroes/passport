<?php

namespace Database\Factories\Teams;

use ShowHeroes\Passport\Models\Teams\Team;
use ShowHeroes\Passport\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'           => $this->faker->unique()->company(),
            'user_id'        => User::factory(),
            'personal_team'  => false,
            'is_operator'    => true,
            'primary_domain' => null,
        ];
    }
}
