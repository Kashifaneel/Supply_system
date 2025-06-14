<?php

namespace App\Policies;

use App\Models\Supply;
use App\Models\User;

class SupplyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Supply $supply): bool
    {
        return $user->role === 'Admin' || $user->id === $supply->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Supply $supply): bool
    {
        return $user->role === 'Admin' || $user->id === $supply->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Supply $supply): bool
    {
        return $user->role === 'Admin' || $user->id === $supply->user_id;
    }
}
