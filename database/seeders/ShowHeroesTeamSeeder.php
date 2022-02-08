<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ShowHeroesTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('teams')->insert([
            'name'           => 'ShowHeroes',
            'user_id'        => 0,
            'personal_team'  => 0,
            'is_operator'    => true,
            'primary_domain' => 'showheroes.com',
        ]);
    }
}
