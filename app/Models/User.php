<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'institution',
        'birth_date',
        'gender',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'birth_date' => 'date',
        ];
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'user_id');
    }

    public function finePayments(): HasMany
    {
        return $this->hasMany(FinePayment::class);
    }

    /**
     * Peminjaman yang masih aktif milik pengguna ini.
     */
    public function activeBorrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'user_id')->active();
    }

    public function isStaff(): bool
    {
        return $this->role->isStaff();
    }
}
