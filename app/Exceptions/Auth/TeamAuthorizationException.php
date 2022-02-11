<?php

namespace ShowHeroes\Passport\Exceptions\Auth;

use Illuminate\Auth\Access\AuthorizationException;

/**
 * Class TeamAuthorizationException
 * @package ShowHeroes\Passport\Exceptions\Auth
 */
class TeamAuthorizationException extends AuthorizationException
{
    /** @var integer */
    private int $requiredTeamId;

    /**
     * @return integer
     */
    public function getRequiredTeamId(): int
    {
        return $this->requiredTeamId;
    }

    /**
     * @param integer $requiredTeamId
     * @return $this
     */
    public function setRequiredTeamId(int $requiredTeamId): self
    {
        $this->requiredTeamId = $requiredTeamId;
        return $this;
    }
}
