<?php

namespace ShowHeroes\Passport\Models;

use Carbon\Carbon;
use JetBrains\PhpStorm\Pure;
use Laravel\Jetstream\HasTeams;
use ShowHeroes\Constants\Locale;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use ShowHeroes\Passport\Models\Teams\Team;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShowHeroes\Passport\Models\GoogleEntities\GoogleAuth;

/**
 * @property integer $id
 * @property string $name
 * @property string $timezone
 * @property string $email
 * @property string $photo_url
 * @property integer $current_team_id
 * @property boolean $email_verified
 * @property string $profile_photo_url
 * @property integer $level             Access level.
 * @property boolean $is_blocked        Is user was blocked.
 * @property array $meta_data
 * @property integer|null $default_locale @todo:relation to new entity for locales
 *
 * @property string $password
 *
 * @property Carbon|null $last_read_announcements_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 *
 * @property Team[] $teams
 * @property GoogleAuth[]|Collection $google_auth
 *
 * @method Team $currentTeam Current user team. See parent method.
 *
 * @mixin Builder
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use CanJoinTeams;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use HasTeams;
    use HasApiTokens;
    use Notifiable;

    public const LEVEL_USER = 0;
    public const LEVEL_ADMIN = 50;

    public const NOT_BLOCKED = 0;
    public const BLOCKED = 1;

    public const SH_DOMAIN = 'showheroes.com';
    public const PLATFORM_AUTH_URL = 'https://platform.showheroes.com/app/accounts/password/reset/?email=';

    /** @var Team|null */
    private ?Team $forced_current_team;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'default_locale',
        'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'meta_data',
        'level'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified' => 'boolean',
        'trial_ends_at' => 'date',
        'last_read_announcements_at' => 'date',
        'uses_two_factor_auth' => 'boolean',
        'level' => 'integer',
        'is_blocked' => 'integer',
        'meta_data' => 'array',
    ];

    public function google_auth(): HasMany
    {
        return $this->hasMany(GoogleAuth::class, 'user_id', 'id');
    }

    #[Pure] public function hasGoogleAuth(): bool
    {
        return !$this->google_auth->isEmpty();
    }

    /**
     * @param integer $id
     * @return null|GoogleAuth
     */
    public function getGoogleAuthByLocalID(int $id): ?GoogleAuth
    {
        foreach ($this->google_auth as $googleAuth) {
            if ($googleAuth->id === $id) {
                return $googleAuth;
            }
        }

        return null;
    }

    public function isOperator(): bool
    {
        $team = $this->getContextTeam();

        return $team && ($team->id === config('auth.operator.team_id'));
    }

    /**
     * Checks if user has email from showheroes.com domain.
     *
     * @return bool
     */
    #[Pure] public function isFromShowHeroesDomain(): bool
    {
        return strtolower(substr($this->email, -14)) === self::SH_DOMAIN;
    }

    public function forceCurrentTeam(Team $team): self
    {
        $this->forced_current_team = $team;

        return $this;
    }

    /**
     * Returns user team in current context.
     * Could be current user team of forced team.
     *
     * @return Model|BelongsTo|Team|null
     */
    public function getContextTeam(): BelongsTo|Model|Team|null
    {
        return $this->forced_current_team ?? $this->currentTeam();
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked === self::BLOCKED;
    }

    #[Pure] public function hasMultipleTeams(): bool
    {
        return count($this->teams) > 1;
    }

    public function ownsTeam(Team $team): bool
    {
        return $this->id && $team->owner_id && ($this->roleOn($team) === 'owner');
    }

    public function ownedTeams(): BelongsToMany
    {
        return $this->passportTeams()->where('role', 'owner');
    }

    #[Pure] public function isGhost(): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->level === self::LEVEL_ADMIN;
    }

    /**
     * Returns user default locale.
     *
     * @return int|null
     */
    public function getDefaultLocale(): ?int
    {
        $locale = $this->default_locale;
        if ($locale === null) {
            $team = $this->passportCurrentTeam();

            if ($team) {
                $locale = $team->getDefaultLocale();
            }
        }

        return $locale;
    }

    /**
     * Returns user locale to used with Laravel translations.
     *
     * @return int|string|null
     */
    public function getLaravelLocaleSlug(): int|string|null
    {
        $locale = $this->getDefaultLocale();

        if ($locale !== null) {
            $locale = Locale::getSlug($locale);
        }

        return $locale;
    }

    /**
     * @return array
     */
    #[ArrayShape(
        [
            Locale::EN => "string",
            Locale::RU => "string"
        ]
    )] public static function getSupportedLocales(): array
    {
        return [
            Locale::EN => 'English',
            Locale::RU => 'Русский',
        ];
    }

    /**
     * Updates User meta data.
     *
     * @param array $data
     * @return $this
     */
    public function updateMetaData(array $data): self
    {
        $metaData = $this->meta_data ?? [];
        $metaData = array_merge($metaData, $data);
        $this->meta_data = $metaData;

        return $this;
    }

    public function getPlatformResetPasswordUrl(): string
    {
        return self::PLATFORM_AUTH_URL . $this->email;
    }
}
