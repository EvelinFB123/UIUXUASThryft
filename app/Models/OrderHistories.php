<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderHistories extends Model
{
    protected $fillable = [
        'user_id',
    'product_id',
    'product_name',
    'quantity',
    'price',
    'shipping_method',
    'payment_method',
    'payment_date',
    'buyer_name',
    'card_number',
    'virtual_account',
    ];
    protected $casts = [
        'items' => 'array',
        'shipping_address' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
