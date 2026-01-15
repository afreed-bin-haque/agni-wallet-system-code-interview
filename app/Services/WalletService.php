<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletService
{
    public function getUserBalance()
    {
        $userId = $userId ?? Auth::id();
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0]
        );
    }

    public function addBalance($amount, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0]
        );
        $wallet->balance += $amount;
        $wallet->save();

        return $wallet;
    }
}
