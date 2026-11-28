<?php

namespace App\Enums;

/**
 * Hak akses pengguna sistem perpustakaan (Admin dan Petugas).
 */
enum Role: string
{
    case Admin = 'admin';
    case Petugas = 'petugas';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Petugas => 'Petugas',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'bg-light-danger text-danger',
            self::Petugas => 'bg-light-info text-info',
        };
    }

    /**
     * Role yang boleh mengelola akun pengguna dan pengaturan sistem.
     */
    public function canManageUsers(): bool
    {
        return $this === self::Admin;
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
