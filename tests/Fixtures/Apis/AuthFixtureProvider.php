<?php

namespace ShowHeroes\Passport\Tests\Fixtures\Apis;

use Illuminate\Contracts\Auth\Authenticatable;
use ShowHeroes\Passport\Models\Team;
use ShowHeroes\Passport\Models\User;

/**
 * Class AuthFixtureProvider
 * @package ShowHeroes\AdHero\Fixtures
 *
 * Handles authorisation of API guard.
 */
trait AuthFixtureProvider
{

    /** @var User[] */
    private $usersCache = [];
    /** @var Team[] */
    private $teamsCache = [];


    /**
     * Authorises User1 from Team1.
     *
     * @return User
     */
    public function authorise()
    {
        $account = $this->getUser1();
        $this->actingAs($account);
        return $account;
    }

    /**
     * Authorises User2 from Team2.
     *
     * @return User
     */
    public function authoriseUser2()
    {
        $account = $this->getUser2();
        $this->actingAs($account);
        return $account;
    }

    /**
     * Get user from team ShowHeroes.
     *
     * @return User
     */
    protected function getUser1()
    {
        return $this->handleUser('user1', [
            'team_internal_id' => 'team1',
            'team' => [
                'name' => 'ShowHeroes',
            ],
            'user' => [
                'name' => 'Mark Marconi',
            ],
        ]);
    }

    /**
     * Get user from team RedBull.
     *
     * @return User
     */
    protected function getUser2()
    {
        return $this->handleUser('user2', [
            'team_internal_id' => 'team2',
            'team' => [
                'name' => 'Red Bull',
            ],
            'user' => [
                'name' => 'Mark Spencer',
            ],
        ]);
    }

    /**
     * @return Team
     */
    protected function getTeam1()
    {
        return $this->handleTeam('team1', ['name' => 'ShowHeroes']);
    }

    /**
     * @return Team
     */
    protected function getTeam2()
    {
        return $this->handleTeam('team2', ['name' => 'Red Bull']);
    }

    /**
     * @param string $key
     * @param array $attributes
     * @return User
     */
    private function handleUser($key, array $attributes = [])
    {
        if (!isset($this->usersCache[$key])) {
            $this->usersCache[$key] = $this->createUser($attributes);
        } else {
            $this->usersCache[$key] = $this->refreshUser($this->usersCache[$key]);
        }
        return $this->usersCache[$key];
    }

    /**
     * @param string $key
     * @param array $attributes
     * @return Team
     */
    private function handleTeam($key, array $attributes = [])
    {
        if (!isset($this->teamsCache[$key])) {
            $this->teamsCache[$key] = $this->createTeam($attributes);
        } else {
            $this->teamsCache[$key] = $this->refreshTeam($this->teamsCache[$key]);
        }
        return $this->teamsCache[$key];
    }

    /**
     * Creates user with dependencies.
     *
     * @param array $attributes
     * @return User
     */
    private function createUser(array $attributes = [])
    {
        /** @var Team $team */
        $team = $this->handleTeam($attributes['team_internal_id'], $attributes['team']);

        /** @var User $user */
        $user = User::factory()->create($attributes['user'] ?? []);
        $this->attachUserToTeam($team, $user, $attributes['role'] ?? false);

        return $user;
    }

    /**
     * Creates team.
     *
     * @param array $attributes
     * @return mixed
     */
    private function createTeam(array $attributes = [])
    {
        /** @var Team $team */
        $team = Team::factory()->create($attributes??[]);
        foreach ($this->usersCache as $key => $user) {
            $this->attachUserToTeam($team, $user);
            $this->usersCache[$key] = $this->refreshUser($user);
        }
        return $team;
    }

    /**
     * @param Team $team
     * @param User $user
     * @param bool $role
     * @return Team
     */
    private function attachUserToTeam(Team $team, User $user, $role = false)
    {
        $team->users()->attach($user, ['role' => $role ?: 'member']);
        $team->owner()->associate($user);
        $team->save();

        return $team;
    }

    /**
     * Reloads user with dependencies from database.
     *
     * @param User $user
     * @return User
     */
    private function refreshUser(User $user)
    {
        /** @var User $user */
        $user = $user->refresh();
        return $user;
    }

    /**
     * Reloads team with dependencies from database.
     *
     * @param Team $team
     * @return Team
     */
    private function refreshTeam(Team $team)
    {
        /** @var Team $team */
        $team = $team->refresh();
        return $team;
    }

}
