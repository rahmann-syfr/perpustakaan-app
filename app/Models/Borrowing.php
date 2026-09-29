<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $fillable = [
        'user_id', 'admin_id', 'book_id', 'borrow_date', 
        'due_date', 'return_date', 'status', 'fine_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}