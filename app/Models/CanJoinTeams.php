<?php

namespace ShowHeroes\Passport\Models;

use JetBrains\PhpStorm\Pure;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
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
        return count($this->teams) > 0;
    }

    /**
     * Get all of the teams that the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(
            Membership::class,
            'team_users',
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
        return Membership::query()
            ->where('team_id', $team->id)
            ->exists();
    }

    /**
     * Determine if the given team is owned by the user.
     *
     * @param Team $team
     * @return bool
     */
    public function ownsTeam(Team $team): bool
    {
        return $this->id && $team->owner_id && $this->id === $team->owner_id;
    }

    /**
     * Get all of the teams that the user owns.
     */
    public function ownedTeams(): BelongsToMany
    {
        return $this->teams()->where('owner_id', $this->getKey());
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
        return $this->currentTeam();
    }

    /**
     * Get the team that user is currently viewing.
     *
     * @return Model|null
     */
    public function currentTeam(): ?Model
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
            throw new InvalidArgumentException("The user does not belong to the given team.");
        }

        $this->current_team_id = $team->id;

        $this->save();
    }

    /**
     * Refresh the current team for the user.
     *
     * @return Model|Team
     */
    public function refreshCurrentTeam()
    {
        $this->current_team_id = null;

        $this->save();

        return $this->currentTeam();
    }
}
