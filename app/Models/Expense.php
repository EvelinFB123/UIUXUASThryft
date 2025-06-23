<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'amount',
        'category',
        'description',
        'payment_method'
    ];

    // Define relationship to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}