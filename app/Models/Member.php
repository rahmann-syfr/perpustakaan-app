<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'member_id');
    }

    public function finePayments(): HasMany
    {
        return $this->hasMany(FinePayment::class, 'member_id');
    }

    /**
     * Peminjaman yang masih aktif milik anggota ini.
     */
    public function activeBorrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'member_id')->active();
    }
}
