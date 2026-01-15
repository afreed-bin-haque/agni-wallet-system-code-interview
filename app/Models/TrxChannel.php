<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxChannel extends Model
{
    protected $fillable = [
        'user_id',
        'payer_account_number',
        'phone',
        'amount',
        'agreement_id',
        'payment_id',
        'trx_id',
        'bkash_res',
        'status',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function trxChannel()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
