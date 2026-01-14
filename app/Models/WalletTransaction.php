<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'phone',
        'payment_id',
        'trx_id',
        'bkash_res',
        'status',

    ];
    protected $primaryKey = 'wallet_trx_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($walletTrx) {
            if (!$walletTrx->wallet_trx_id) {
                $walletTrx->wallet_trx_id = (string) Str::uuid();
            }
        });
    }
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id', 'wallet_id');
    }
}
