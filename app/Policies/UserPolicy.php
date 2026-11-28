<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    /**
     * Melihat daftar pengguna.
     */
    public function viewAny(User $actor): bool
    {
        return $actor->role->canManageUsers();
    }

    /**
     * Membuat pengguna baru.
     */
    public function create(User $actor): bool
    {
        return $actor->role->canManageUsers();
    }

    /**
     * Mengubah akun pengguna (hanya Admin).
     */
    public function update(User $actor, User $target): bool
    {
        return $actor->role === Role::Admin;
    }

    /**
     * Menghapus akun pengguna (Admin tidak dapat menghapus akunnya sendiri).
     */
    public function delete(User $actor, User $target): bool
    {
        if ($actor->id === $target->id) {
            return false;
        }

        return $actor->role === Role::Admin;
    }

    /**
     * Role yang boleh ditetapkan oleh actor terhadap akun target.
     *
     * @return array<int, string>
     */
    public function assignableRoles(User $actor, ?User $target = null): array
    {
        if ($actor->role !== Role::Admin) {
            return [];
        }

        return Role::values();
    }
}
