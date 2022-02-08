<?php

namespace ShowHeroes\Passport\Console\Commands\User;

use Illuminate\Console\Command;
use ShowHeroes\Passport\Models\User;


class UserCreateApiTokenCommand extends Command
{

    protected $signature = 'showheroes:user:create-api-token {user_id} {token_name}';

    protected $description = 'Creates token for user. Should be specified arguments: user id, token name.';

    public function handle()
    {
        $userId = (int)$this->argument('user_id');
        $user = User::find($userId);
        if (!$user) {
            $this->error('User not found.');
            exit;
        }

        $tokenName = $this->argument('token_name');

        $user->tokens()->where('name', $tokenName)->delete();

        $token = $user->createToken($tokenName);

        $this->info('Token created: ' . $token->plainTextToken);
    }
}
