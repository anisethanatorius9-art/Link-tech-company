<?php

namespace App\Policies;

use App\Models\Tender;
use App\Models\User;

class TenderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Tender $tender): bool
    {
        return $user->isAdmin() || $this->isAssignedOrOwner($user, $tender);
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Tender $tender): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->isAssignedOrOwner($user, $tender)
            && ! in_array($tender->status, ['submitted', 'awarded'], true);
    }

    public function delete(User $user, Tender $tender): bool
    {
        return $user->isAdmin()
            || ($this->isAssignedOrOwner($user, $tender) && $tender->status === 'draft');
    }

    public function approveQuote(User $user, Tender $tender): bool
    {
        return $user->isAdmin();
    }

    public function manageDocuments(User $user, Tender $tender): bool
    {
        return $user->isAdmin() || $this->isAssignedOrOwner($user, $tender);
    }

    private function isAssignedOrOwner(User $user, Tender $tender): bool
    {
        return $tender->assigned_officer_id === $user->id || $tender->created_by_id === $user->id;
    }
}
