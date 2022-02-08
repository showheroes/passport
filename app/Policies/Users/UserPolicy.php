<?php

namespace ShowHeroes\Passport\Policies\Users;

use ShowHeroes\Passport\Models\User;

class UserPolicy
{

    /**
     * Determine whether the user can view the model.
     *
     * @param User $currentUser
     * @param User $user
     * @return mixed
     */
    public function show(User $currentUser, User $user)
    {
        return $currentUser->id == $user->id;
    }
}
