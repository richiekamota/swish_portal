<?php

namespace Portal\Policies;

use Portal\User;
use Portal\Application;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the application.
     *
     * @param  \Portal\User  $user
     * @param  \Portal\Application  $application
     * @return mixed
     */
    public function view(User $user, Application $application)
    {
        return $user->role != 'tenant' || $user->id == $application->user_id;
    }

    /**
     * Determine whether the user can create applications.
     *
     * @param  \Portal\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the application.
     *
     * @param  \Portal\User  $user
     * @param  \Portal\Application  $application
     * @return mixed
     */
    public function update(User $user, Application $application)
    {
        return $user->id === $application->user_id;
    }

    /**
     * Determine whether the user can delete the application.
     *
     * @param  \Portal\User  $user
     * @param  \Portal\Application  $application
     * @return mixed
     */
    public function delete(User $user, Application $application)
    {
        //
    }
}
