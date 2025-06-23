<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\OrderHistories;
use App\Models\ChatMessage;
use App\Models\Expense;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Kolom-kolom yang boleh diisi massal lewat update/create
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone',
        'kota',
        'profile_picture_url',
        'address',
        'role',
    ];

    // Kolom yang disembunyikan saat diubah ke array atau json
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Tipe data casting otomatis
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(OrderHistories::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }


}
