<?php

namespace ShowHeroes\Passport\Http\Gateways\Teams;

use ShowHeroes\Passport\Models\Teams\Team;
use ShowHeroes\Passport\Models\Teams\TeamSettings;

/**
 * Class TeamGateway
 * @package ShowHeroes\Passport\Http\Gateways\Teams
 */
class TeamGateway
{
    /**
     * Returns default team settings.
     *
     * @param Team $team
     * @return TeamSettings
     */
    public static function getDefaultTeamSettings(Team $team)
    {
        $teamSettings = new TeamSettings();
        $defaultTeamUI = config('passport.default_team_ui_colors');
        $teamSettings->ui_config = [
            'header_bg_color'           => $defaultTeamUI['header_bg_color'],
            'primary_light_color'       => $defaultTeamUI['primary_light_color'],
            'hamburger_bg_color'        => $defaultTeamUI['hamburger_bg_color'],
            'hamburger_bg_dark_color'   => $defaultTeamUI['hamburger_bg_dark_color'],
            'menu_highlight_color'      => $defaultTeamUI['menu_highlight_color'],
            'menu_highlight_dark_color' => $defaultTeamUI['menu_highlight_dark_color'],
            'header_bg_solid_color'     => false,
        ];

        $teamSettings->redirect_to_platform_enabled = false;
        $teamSettings->team_id = $team->id;
        $teamSettings->permissions = [];

        return $teamSettings;
    }
}
