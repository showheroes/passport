<?php

namespace ShowHeroes\Passport\Models\GoogleEntities;

use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class GoogleAuth
 * @package ShowHeroes\Passport\Models\GoogleEntities
 * * @mixin Builder
 *
 * @property integer $id
 * @property string $auth_id
 * @property integer $user_id
 * @property string $email
 * @property string $token
 * @property array $meta_data
 *
 * @property Carbon $last_login_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $last_api_request_at When user requested his Google
 * data last time. Used to indicate that cache should be updated.
 */
class GoogleAuth extends Model
{
    protected $table = 'google_auth';
    protected $fillable = [
        'auth_id', 'email', 'token', 'meta_data', 'last_login_at'
    ];
    protected $casts = [
        'meta_data' => 'array',
        'auth_id' => 'string',
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'last_login_at', 'last_api_request_at'];

    #[ArrayShape(
        [
            'access_token' => "string",
            'refresh_token' => "mixed|null",
            'expires_in' => "mixed|null",
            'created' => "mixed|null"
        ]
    )] public function getTokenData(): array
    {
        return [
            'access_token' => $this->token,
            'refresh_token' => $this->meta_data['refreshToken'] ?? null,
            'expires_in' => $this->meta_data['expiresIn'] ?? null,
            'created' => $this->meta_data['created'] ?? null,
        ];
    }

    /**
     * Mark this record as currently used by a user.
     */
    public function touchAPI(): void
    {
        $this->last_api_request_at = Carbon::now();
        $this->save();
    }

    public function getName()
    {
        return $this->meta_data['name'] ?? 'Unknown';
    }
}
