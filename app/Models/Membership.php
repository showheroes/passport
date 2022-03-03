<?php

namespace ShowHeroes\Passport\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * Class Membership
 * @package ShowHeroes\Passport\Models
 *
 * @property integer $id
 * @property integer $team_id
 * @property integer $user_id
 * @property string $role
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Membership extends JetstreamMembership
{
    use SoftDeletes;
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $table = 'team_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'team_id',
        'role'
    ];

    protected array $date = ['created_at', 'updated_at', 'deleted_at'];
}
