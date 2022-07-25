<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function register(User $user)
    {
        return $user->role_id == User::LEVEL_ADMIN;
    }
     /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        return $user->role_id == User::LEVEL_ADMIN;
    }
     /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function listAdmin(User $user)
    {
        return $user->role_id == User::LEVEL_ADMIN;
    }
        /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function listMember(User $user)
    {
        return $user->role_id == User::LEVEL_ADMIN || $user->role_id == User::LEVEL_MEMBER;
    }

}