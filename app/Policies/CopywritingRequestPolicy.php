<?php

namespace App\Policies;

use App\Models\CopywritingRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CopywritingRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CopywritingRequest $copywritingRequest): bool
    {
        return $user->id === $copywritingRequest->user_id || $user->id === $copywritingRequest->assigned_to;
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
    public function update(User $user, CopywritingRequest $copywritingRequest): bool
    {
        return $user->id === $copywritingRequest->assigned_to;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CopywritingRequest $copywritingRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CopywritingRequest $copywritingRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CopywritingRequest $copywritingRequest): bool
    {
        return false;
    }
}
