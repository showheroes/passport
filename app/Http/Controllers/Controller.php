<?php

namespace ShowHeroes\Passport\Http\Controllers;

use Auth;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ShowHeroes\Passport\Exceptions\Auth\TeamAuthorizationException;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    use AuthorizesRequests {
        authorize as authorizeFromTrait;
    }

    private bool $isTeamForcedAuthorise = false;

    /**
     * Forces team switch all the time when active team is not matching.
     *
     * @param string $ability
     * @param array $arguments
     * @return Response
     * @throws TeamAuthorizationException
     * @throws AuthorizationException
     */
    public function authorize(string $ability, $arguments = []): Response
    {
        $user = Auth::user();
        if ($user) {
            $userCurrentTeamId = $user->current_team_id;
            if ($userCurrentTeamId !== $arguments->team_id && $user->onTeam($arguments->team)) {
                $exception = new TeamAuthorizationException();
                $exception->setRequiredTeamId($arguments->team_id);
                throw $exception;
            }
        }

        return $this->authorizeFromTrait($ability, $arguments);
    }

    /**
     * Forces team switch only if authorisation is not granted.
     *
     * @param string $ability
     * @param array $arguments
     * @return bool|Response
     * @throws TeamAuthorizationException
     * @throws AuthorizationException
     */
    public function softAuthorise(string $ability, $arguments = []): Response|bool
    {
        $result = false;

        try {
            $result = $this->authorizeFromTrait($ability, $arguments);
        } catch (AuthorizationException $exception) {
            $user = Auth::user();
            if ($user) {
                $userCurrentTeamId = $user->current_team_id;
                if ($userCurrentTeamId !== $arguments->team_id && $user->onTeam($arguments->team)) {
                    $exception = new TeamAuthorizationException();
                    $exception->setRequiredTeamId($arguments->team_id);
                    throw $exception;
                }
            }
        }

        if ($result === false && isset($exception)) {
            throw $exception;
        }

        return $result;
    }

    public function enableTeamForcedAuthorise(): Controller
    {
        $this->isTeamForcedAuthorise = true;

        return $this;
    }
}
