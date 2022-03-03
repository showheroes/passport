<?php

namespace ShowHeroes\Passport\Models;

use JetBrains\PhpStorm\Pure;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\Jetstream;
use ShowHeroes\Passport\Models\Teams\Team;
use ShowHeroes\Passport\Models\Teams\Membership;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait CanJoinTeams
 * @package ShowHeroes\Passport\Models
 */
trait CanJoinTeams
{
    /**
     * Determine if the user is a member of any teams.
     *
     * @return bool
     */
    #[Pure] public function hasTeams(): bool
    {
        return $this->teams()->count() > 0;
    }

    /**
     * Get all of the teams that the user belongs to.
     */
    public function passportTeams(): BelongsToMany
    {
        return $this->belongsToMany(
            Team::class,
            'team_user',
            'user_id',
            'team_id'
        )
           ->withPivot(['role'])
            ->orderBy('name');
    }

    /**
     * Determine if the user is on the given team.
     *
     * @param Team $team
     * @return bool
     */
    public function onTeam(Team $team): bool
    {
        return $this->teams->contains($team);
    }

    /**
     * @param User $user
     * @return int
     */
    public function onTeamByUserId(User $user): int
    {
        return Membership::query()
            ->where('team_id', $user->id)
            ->count();
    }


    /**
     * Get the user's role on a given team.
     *
     * @param Team $team
     * @return string|null
     */
    public function roleOn(Team $team): ?string
    {
        /** @var Team $isTeam */
        $isTeam = Team::query()
            ->find($team->id);

        if ($team->name === $isTeam->name) {
            return $team->pivot->role;
        }

        return null;
    }

    /**
     * Get the user's role on the team currently being viewed.
     *
     * @return string
     */
    public function roleOnCurrentTeam(): string
    {
        return $this->roleOn($this->currentTeam);
    }

    /**
     * Accessor for the currentTeam method.
     *
     * @return Model|null
     */
    public function getCurrentTeamAttribute(): ?Model
    {
        return $this->passportCurrentTeam();
    }

    /**
     * Get the team that user is currently viewing.
     *
     * @return Model|null
     */
    public function passportCurrentTeam(): ?Model
    {
        if (is_null($this->current_team_id) && $this->hasTeams()) {
            $this->switchToTeam($this->teams->first());

            return $this->currentTeam();
        }

        if (!is_null($this->current_team_id)) {
            $currentTeam = $this->teams->find($this->current_team_id);

            return $currentTeam ?: $this->refreshCurrentTeam();
        }

        return null;
    }

    /**
     * Switch the current team for the user.
     *
     * @param Team $team
     * @return void
     */
    public function switchToTeam(Team $team): void
    {
        if (!$this->onTeam($team)) {
            throw new InvalidArgumentException("The user is not belong to the default team.");
        }

        $this->current_team_id = $team->id;

        $this->save();
    }

    /**
     * Refresh the current team for the user.
     *
     * @return Model|Team|null
     */
    public function refreshCurrentTeam(): Model|Team|null
    {
        $this->current_team_id = null;

        $this->save();

        return $this->currentTeam();
    }
}
