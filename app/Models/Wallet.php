<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'agreement_id',
        'payer_account_number',
        'balance'
    ];
    protected $primaryKey = 'wallet_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($wallet) {
            if (!$wallet->wallet_id) {
                $wallet->wallet_id = (string) Str::uuid();
            }
        });
    }

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function walletTrx()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id', 'wallet_id');
    }
}
