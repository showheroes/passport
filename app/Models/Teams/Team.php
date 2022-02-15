<?php

namespace ShowHeroes\Passport\Models\Teams;

use Carbon\Carbon;
use ShowHeroes\Passport\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShowHeroes\Passport\Http\Gateways\Teams\TeamGateway;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $personal_team
 * @property bool $is_operator
 * @property string $primary_domain
 *
 * @property integer $owner_id
 * @property integer|null $default_locale
 * @property array $meta_data
 *
 * @property User $owner
 * @property User[] $users
 * @property TeamSettings|null $team_settings Use {@see self::getTeamSettings()} instead.
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @mixin Builder
 */
class Team extends JetstreamTeam
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'personal_team',
        'meta_data',
        'owner_id',
        'meta_data',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    public const VERIFICATION_UNVERIFIED = 0;
    public const VERIFICATION_APPROVED = 10;
    public const VERIFICATION_DENIED = 20;

    public const ORIGINAL_LABEL_DOMAIN = 'showheroes.com';

    protected array $cast = [
        'verification_status' => 'integer',
        'meta_data' => 'array',
    ];

    protected $hidden = [
        'meta_data',
    ];

    protected array $date = ['created_at', 'updated_at', 'trial_ends_at', 'deleted_at'];

    public function isOperator(): bool
    {
        return $this->id === config('auth.operator.team_id');
    }

    /**
     * Returns operator team.
     *
     * @return Team|null
     */
    public static function getOperator(): Team|null
    {
        return self::getTeamById(config('auth.operator.team_id'));
    }

    public function getDefaultLocale(): ?int
    {
        return $this->default_locale;
    }

    /**
     * Get Team settings.
     */
    public function team_settings(): HasOne
    {
        return $this->hasOne(TeamSettings::class, 'team_id', 'id');
    }

    /**
     * @return TeamSettings|null
     */
    public function getTeamSettings(): ?TeamSettings
    {
        $teamSettings = $this->team_settings;
        if (!$teamSettings) {
            $teamSettings = TeamGateway::getDefaultTeamSettings($this);
        }

        return $teamSettings;
    }

    /**
     * Updates Team meta data.
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


    /**
     * Get all of the users that the user belongs to.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'team_user',
            'user_id',
            'team_id'
        )
            ->withPivot(['role'])
            ->orderBy('name');
    }

    /**
     * Returns user, who is responsible for the Team management.
     *
     * @return User|null
     */
    public function getResponsibleUser(): null|User
    {
        $operatorTeamID = config('auth.operator.team_id');
        $responsibleUser = $this->owner;

        // In case of non-operator team, do not notify ghosts.
        if ($this->id !== $operatorTeamID) {
            $users = $this->users;

            foreach ($users as $user) {
                if ($user->isGhost()) {
                    continue;
                }

                if ($user->roleOn($this) === 'owner') {
                    $responsibleUser = $user;
                    break;
                }
            }
        }

        return $responsibleUser;
    }

    /**
     * @param integer $teamId
     * @return Builder|Model|Team
     */
    public static function getTeamById(int $teamId): Model|Builder|Team
    {
        return self::query()
            ->where('id', $teamId)
            ->first();
    }
}
