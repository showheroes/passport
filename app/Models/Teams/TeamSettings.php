<?php

namespace ShowHeroes\Passport\Models\Teams;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class TeamSettings
 * @package ShowHeroes\Passport\Models\Teams
 *
 * @property integer $team_id
 *
 * @property $ui_config
 *
 * @property boolean $ifactory_access
 * @property boolean $adhero_access
 * @property boolean $apollo_access
 * @property boolean $creative_studio_access
 * @property boolean $redirect_to_platform_enabled
 *
 * @property array|null $permissions
 *
 * @property string|null $authorised_domain          Emails from this domain will be automatically joined to related teams.
 *
 * @property Team $team
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TeamSettings extends Model
{
    use HasFactory;

    public const PRODUCT_IFACTORY = 'ifactory';
    public const PRODUCT_ADHERO = 'adhero';
    public const PRODUCT_APOLLO = 'apollo';
    public const PRODUCT_CS = 'creative_studio';

    protected $table = 'team_settings';
    protected $primaryKey = 'team_id';
    public $incrementing = false;

    protected $fillable = [
        'team_id',
        // Regular settings.
        'ui_config',
        // Admin specific settings.
        'adhero_access', 'apollo_access', 'ifactory_access', 'creative_studio_access',
        // Others
        'redirect_to_platform_enabled', 'authorised_domain '
    ];

    protected $casts = [
        'permissions' => 'array',

        'adhero_access' => 'boolean',
        'apollo_access' => 'boolean',
        'ifactory_access' => 'boolean',
        'creative_studio_access' => 'boolean',
        'ui_config' => 'array',
        'redirect_to_platform_enabled' => 'boolean'
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }

    /**
     * @return Application|UrlGenerator|string
     */
    public function getBannerURL(): string|UrlGenerator|Application
    {
        return url('/img/banner_img.jpg');
    }

    /**
     * while mocking
     * @return array
     */
    public function getUIConfig(): array
    {
        return $this->ui_config ?? [];
    }

    #[ArrayShape(
        ['team_id' => "int",
            'adhero_access' => "bool",
            'apollo_access' => "bool",
            'ifactory_access' => "bool",
            'redirect_to_platform_enabled' => "bool"
        ]
    )] public function toGEQ(): array
    {
        return [
            'team_id' => $this->team_id,
            'adhero_access' => $this->adhero_access,
            'apollo_access' => $this->apollo_access,
            'ifactory_access' => $this->ifactory_access,
            'redirect_to_platform_enabled' => $this->redirect_to_platform_enabled,
        ];
    }
}
