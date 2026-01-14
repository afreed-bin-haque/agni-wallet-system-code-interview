<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class userDashController extends Controller
{
    protected $walletService;
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;
    private $fallback_url;

    public function __construct(WalletService $walletService)
    {
        $bkash_app_key = config('payment.bkashConfig.bkash_app_key');
        $bkash_app_secret = config('payment.bkashConfig.bkash_app_secret');
        $bkash_username = config('payment.bkashConfig.bkash_username');
        $bkash_password = config('payment.bkashConfig.bkash_password');
        $bkash_base_url = config('payment.bkashConfig.bkash_base_url');
        $frontend_dash = config('system.appConfig.redirect_frontend_url') . '/dashboard';

        $this->app_key = $bkash_app_key;
        $this->app_secret = $bkash_app_secret;
        $this->username = $bkash_username;
        $this->password = $bkash_password;
        $this->base_url = $bkash_base_url;
        $this->fallback_url = $frontend_dash;
        $this->walletService = $walletService;
    }
    public function dashboard()
    {
        $user = Auth::user();
        return Inertia::render("Dashboard", [
            'user' => $user ? $user->only(['id', 'name', 'email']) : null
        ]);
    }

    public function userTrxHistory()
    {
        return Inertia::render("TrxHistory");
    }
    public function addBalance()
    {
        $balance = $this->walletService->getUserBalance();

        return Inertia::render('AddBalance', [
            'wallet' => $balance
        ]);
    }

    public function requstAddBalance(Request $request)
    {
        session()->forget('paymet_id');
        session()->forget('trxId');
        session()->forget('phone');
        session()->forget('authorization');
        $user = Auth::user();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );


        $wallet->save();

        WalletTransaction::create([
            'wallet_id' => $wallet->wallet_id,
            'amount' => $request->amount,
            'phone' => $request->phone,
        ]);
        return redirect()->away('https://merchantdemo.sandbox.bka.sh/tokenized-checkout/version/v2');
    }
}
