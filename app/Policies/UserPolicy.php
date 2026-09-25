<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $model->hasRole('receptionist') || $model->hasRole('client');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            if ($model->hasRole('receptionist')) {
                return $model->created_by_id === $user->id;
            }

            if ($model->hasRole('client')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            if ($model->hasRole('receptionist')) {
                return $model->created_by_id === $user->id;
            }

            if ($model->hasRole('client')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can ban the model.
     */
    public function ban(User $user, User $model): bool
    {
        if ($user->id === $model->id || $model->hasRole('admin')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            if ($model->hasRole('receptionist')) {
                return $model->created_by_id === $user->id;
            }

            if ($model->hasRole('client')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can unban the model.
     */
    public function unban(User $user, User $model): bool
    {
        return $this->ban($user, $model);
    }
}
