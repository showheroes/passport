<?php

namespace Database\Factories\Teams;

use ShowHeroes\Passport\Models\Teams\TeamSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TeamSettingsFactory
 * @package Database\Factories\Teams
 */
class TeamSettingsFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamSettings::class;


    public function definition()
    {
        return [
            'ui_config' => [],
            'adhero_access' => true,
            'apollo_access' => true,
            'ifactory_access' => true,
            'creative_studio_access' => true,
            'redirect_to_platform_enabled' => false,
            'authorised_domain' => 'showheroes.com'
        ];
    }
}
