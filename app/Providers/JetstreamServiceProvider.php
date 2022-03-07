<?php

namespace ShowHeroes\Passport\Providers;

use ShowHeroes\Passport\Actions\Jetstream\AddTeamMember;
use ShowHeroes\Passport\Actions\Jetstream\CreateTeam;
use ShowHeroes\Passport\Actions\Jetstream\DeleteTeam;
use ShowHeroes\Passport\Actions\Jetstream\DeleteUser;
use ShowHeroes\Passport\Actions\Jetstream\InviteTeamMember;
use ShowHeroes\Passport\Actions\Jetstream\RemoveTeamMember;
use ShowHeroes\Passport\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        Jetstream::useUserModel('ShowHeroes\Passport\Models\User');
        Jetstream::useTeamModel('ShowHeroes\Passport\Models\Teams\Team');
        Jetstream::useMembershipModel('ShowHeroes\Passport\Models\Membership');
      //  Jetstream::useTeamInvitationModel('ShowHeroes\Passport\Models\Teams\TeamInvitation');
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');
    }
}
