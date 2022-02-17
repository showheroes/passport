<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use ShowHeroes\Passport\Models\User;
use Illuminate\Support\Facades\Hash;
use ShowHeroes\Passport\Models\Teams\Team;
use ShowHeroes\Passport\Models\Teams\TeamSettings;

/**
 * Class ShowHeroesUserTeamSeeder
 * @package Database\Seeders
 */
class ShowHeroesUserTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultUser = config('services.default_user');

        $users = User::factory(1)->create(
            [
                'name' => $defaultUser['name'],
                'email' => $defaultUser['email'],
                'password' => Hash::make($defaultUser['password']),
                'email_verified_at' => null,
            ]
        );

        foreach ($users as $user) {
            $userId = $user->id;
        }

        $teams = Team::factory(1)->create(
            [
                'name' => $defaultUser['team_name'],
                'personal_team' => true,
                'meta_data' => '',
                'owner_id' => $userId,
                'user_id' => $userId,
            ]
        );

        foreach ($teams as $team) {
            $teamId = $team->id;
        }

        TeamSettings::factory(1)->create(
            [
                'team_id' => $teamId,
                'permissions' => ''
            ]
        );
    }
}
