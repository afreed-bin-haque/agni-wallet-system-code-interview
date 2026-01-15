<?php

namespace App\Http\Controllers;

use App\Models\TrxChannel;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $balance = $this->walletService->getUserBalance();

        return Inertia::render("Dashboard", [
            'user' => $user ? $user->only(['id', 'name', 'email']) : null,
            'wallet' => $balance
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
    public function dbitBalance()
    {
        $balance = $this->walletService->getUserBalance();

        return Inertia::render('DabitBalance', [
            'wallet' => $balance
        ]);
    }

    public function transacctionTable(Request $request)
    {
        try {
            $userId = Auth::id();
            $search = $request->get('search');

            $query = TrxChannel::query();
            $query->where('user_id', $userId);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payer_account_number', 'like', "%{$search}%")
                        ->orWhere('payment_id', 'like', "%{$search}%")
                        ->orWhere('trx_id', 'like', "%{$search}%");
                });
            }

            $results = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

            $results->getCollection()->transform(function ($item) {
                $item->bkash_res = $item->bkash_res ? json_decode($item->bkash_res, true) : null;
                return $item;
            });
            return response()->json([
                "status" => true,
                "msg" => "Data served",
                "result" => $results
            ], 200);
        } catch (Exception $e) {
            $errorDetails = "[Transaction table] error " . $e->getMessage() . " at line: " . $e->getLine();
            Log::error($errorDetails);
            abort(400, 'Request could not process.Please check log');
        }
    }

    public function successTransactionTable(Request $request)
    {
        try {
            $userId = Auth::id();
            $search = $request->get('search');

            $wallet = Wallet::where('user_id', $userId)->first();

            if (!$wallet) {
                return response()->json([
                    "status" => false,
                    "msg" => "Wallet not found",
                    "result" => []
                ], 404);
            }

            $query = $wallet->walletTrx()->where('status', 'success');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('trx_id', $search)
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('payment_id', 'like', "%{$search}%");
                });
            }

            $transactions = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

            $transactions->getCollection()->transform(function ($item) {
                $item->bkash_res = $item->bkash_res ? json_decode($item->bkash_res, true) : null;
                $item->created_at = Carbon::parse($item->created_at)->tz('Asia/Dhaka')->toDateTimeString();
                return $item;
            });

            return response()->json([
                "status" => true,
                "msg" => "Data served",
                "result" => $transactions
            ], 200);
        } catch (\Exception $e) {
            Log::error("[Successfull transaction table] error " . $e->getMessage() . " at line: " . $e->getLine());
            abort(400, 'Request could not process. Please check log');
        }
    }

    public function requstDebitBalance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required',
                'amount' => 'required|int',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $validator->errors()->first()
                ], 200);
            }
            $amount = (int) $request->amount;
            $userId = Auth::id();
            $wallet = $this->walletService->addBalance(-$amount, $userId);


            WalletTransaction::create([
                'wallet_id' => $wallet->wallet_id,
                'type' => 'debit',
                'amount' => (int) $request->amount,
                'phone' => $request->phone,

                'status' => 'success',
            ]);
            return response()->json([
                "status" => true,
                "msg" => "User registered"
            ], 200);
        } catch (Exception $e) {
            $errorDetails = "[Debit balance] error " . $e->getMessage() . " at line: " . $e->getLine();
            Log::error($errorDetails);
            abort(400, 'Request could not process.Please check log');
        }
    }


    public function requstAddBalance(Request $request)
    {
        session()->forget('paymet_id');
        session()->forget('trxId');
        session()->forget('phone');
        session()->forget('authorization');
        $id_key = "bkash_id_key";
        $refresh_key = "bkash_refresh_key";



        $getPhone = $request->phone;
        $payerPhone = (strlen($getPhone) === 14 || strlen($getPhone) === 13)
            ? substr($getPhone, 3)
            : $getPhone;

        $userId =  Auth::id();
        $trxId  = Str::uuid();
        $assignAmount = $request->amount;
        if (Redis::exists($id_key) && Redis::exists($refresh_key)) {
            $id_token = json_decode(Redis::get($id_key), true);
            $refresh_token = json_decode(Redis::get($refresh_key), true);
            Log::info('Successfully hit Redis and got id and refresh key');
        } elseif (Redis::exists($refresh_key)) {
            $get_refresh_token = json_decode(Redis::get($refresh_key), true);
            $json_decoded_get_token = $this->GetIdByRefreshToken($get_refresh_token);
            $id_token = $json_decoded_get_token['id_token'];
            $refresh_token = $get_refresh_token;
            Redis::setex($id_key, 3600, json_encode($id_token));
            Log::info('[Bkash - Redis lock] Successfully hit Redis and got refresh key then sent it to GetIdByRefreshToken');
        } else {
            $json_decoded_get_token = $this->GetIdAndRefreshToken();
            $id_token = $json_decoded_get_token['id_token'];
            $refresh_token = $json_decoded_get_token['refresh_token'];
            Redis::setex($id_key, 3600, json_encode($id_token));
            Redis::setex($refresh_key, 2332800, json_encode($refresh_token));
            Log::info('Successfully hit GetIdAndRefreshToken');
        }
        $call_back_url = route('reroute.mfsp_bkash');
        $post_user_data = collect()->push([
            'callbackURL' => $call_back_url,
            'payerReference' => $payerPhone,
            'mode' => "0011",
            'amount' => $assignAmount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $trxId
        ])->flatMap(function ($values) {
            return  $values;
        })->all();
        $get_checout_url = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'X-APP-Key' => $this->app_key,
            'Authorization' => $id_token,
        ])->post($this->base_url . 'checkout/create', $post_user_data);

        $jsonDecodedGetChecoutUrl = json_decode($get_checout_url->getBody(), true);
        $bkashPaymentId = $jsonDecodedGetChecoutUrl["paymentID"] ?? null;
        TrxChannel::create([
            'user_id' => $userId,
            'payer_account_number' => $getPhone,
            'phone' => $getPhone,
            'amount' => $assignAmount,
            'payment_id' => $bkashPaymentId,
            'bkash_res' => json_encode($jsonDecodedGetChecoutUrl),
        ]);
        Session::put([
            'paymet_id' => $jsonDecodedGetChecoutUrl['paymentID'],
            'trxId' => $trxId,
            'phone' => $getPhone,
            'authorization' => $id_token,
        ]);
        return Redirect::to($jsonDecodedGetChecoutUrl['bkashURL']);
    }

    public function ExecutePayment(Request $request)
    {
        try {
            $userId = Auth::id();
            $paymentID = $request->paymentID;
            $status = $request->status;
            if ($status === 'success') {
                $status = 'Processing';
            } elseif ($status === 'failure') {
                $status = 'Failed';
            } elseif ($status === 'cancel') {
                $status = 'Canceled';
            } else {
                $status = 'Pending';
            }

            $post_payment_id = collect()->push([
                'paymentID' => $paymentID,
            ])->flatMap(function ($values) {
                return  $values;
            })->all();
            if ($paymentID === Session::get('paymet_id')) {
                $get_check_out_exe = Http::withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'X-APP-Key' => $this->app_key,
                    'Authorization' => Session::get('authorization'),
                ])->post($this->base_url . 'checkout/execute/', $post_payment_id);

                $jsonDecodedGetCheckoutExe = json_decode($get_check_out_exe->getBody(), true);
                if ($jsonDecodedGetCheckoutExe['statusMessage'] === 'Successful') {
                    if ($jsonDecodedGetCheckoutExe['transactionStatus'] === 'Completed') {
                        $getTrxChannelData = TrxChannel::where('payment_id', $paymentID)->first();
                        if ($getTrxChannelData) {
                            $getTrxChannelData->trx_id = $jsonDecodedGetCheckoutExe['trxID'];
                            $getTrxChannelData->status = "success";
                            $getTrxChannelData->save();

                            $wallet = $this->walletService->addBalance($jsonDecodedGetCheckoutExe['amount'], $userId);

                            WalletTransaction::create([
                                'wallet_id' => $wallet->wallet_id,
                                'amount' => $jsonDecodedGetCheckoutExe['amount'],
                                'phone' => $jsonDecodedGetCheckoutExe['payerAccount'],
                                'payment_id' => $paymentID,
                                'trx_id' => $jsonDecodedGetCheckoutExe['trxID'],
                                'bkash_res' => json_encode($jsonDecodedGetCheckoutExe),
                                'status' => 'success',
                            ]);
                        }


                        session()->forget('paymet_id');
                        session()->forget('trxId');
                        session()->forget('phone');
                        session()->forget('authorization');
                        $success_reason = 'Bkash payment successful';
                        return Redirect::to(config('system.appConfig.redirect_frontend_url') . '/dashboard?success_reason=' . $success_reason);
                    } else {
                        $getTrxChannelData = TrxChannel::where('payment_id', $paymentID)->first();
                        if ($getTrxChannelData) {
                            $getTrxChannelData->trx_id = $jsonDecodedGetCheckoutExe['trxID'];
                            $getTrxChannelData->status = "failed";
                            $getTrxChannelData->save();
                        }

                        session()->forget('paymet_id');
                        session()->forget('trxId');
                        session()->forget('phone');
                        session()->forget('authorization');
                        $failed_reason = 'Bkash payment unsuccessfull';
                        return Redirect::to(config('system.appConfig.redirect_frontend_url') . '/dashboard?failed_reason=' . $failed_reason);
                    }
                } elseif (($jsonDecodedGetCheckoutExe['statusCode'] === "2023") || ($jsonDecodedGetCheckoutExe['statusCode'] === "2029")) {
                    $getTrxChannelData = TrxChannel::where('payment_id', $paymentID)->first();
                    if ($getTrxChannelData) {

                        $getTrxChannelData->status = "failed";
                        $getTrxChannelData->save();
                    }
                    session()->forget('paymet_id');
                    session()->forget('trxId');
                    session()->forget('phone');
                    session()->forget('authorization');
                    $failed_reason = 'Bkash payment unsuccessfull';
                    return Redirect::to(config('system.appConfig.redirect_frontend_url') . '/dashboard?failed_reason=' . $failed_reason);
                } else {
                    $getTrxChannelData = TrxChannel::where('payment_id', $paymentID)->first();
                    if ($getTrxChannelData) {

                        $getTrxChannelData->status = "failed";
                        $getTrxChannelData->save();
                    }
                    session()->forget('paymet_id');
                    session()->forget('trxId');
                    session()->forget('phone');
                    session()->forget('authorization');
                    $failed_reason = 'Bkash payment unsuccessfull';
                    return Redirect::to(config('system.appConfig.redirect_frontend_url') . '/dashboard?failed_reason=' . $failed_reason);
                }
            } else {
                $getTrxChannelData = TrxChannel::where('payment_id', $paymentID)->first();
                if ($getTrxChannelData) {

                    $getTrxChannelData->status = "failed";
                    $getTrxChannelData->save();
                }
                $failed_reason = 'Bkash payment failed';
                return Redirect::to(config('system.appConfig.redirect_frontend_url') . '/dashboard?failed_reason=' . $failed_reason);
            }
        } catch (Exception $e) {
            $errorDetails = "[Bkash] error " . $e->getMessage() . " at line: " . $e->getLine();
            Log::error($errorDetails);
            abort(400, 'Request could not process.Please check log');
        }
    }

    public function getRefund($bkash_trx, $payment_id)
    {
        $userId = Auth::id();
        $id_key = "bkash_id_key";
        $refresh_key = "bkash_refresh_key";
        if (Redis::exists($id_key) && Redis::exists($refresh_key)) {
            $id_token = json_decode(Redis::get($id_key), true);
            $refresh_token = json_decode(Redis::get($refresh_key), true);
            Log::info('Successfully hit Redis and got id and refresh key');
        } elseif (Redis::exists($refresh_key)) {
            $get_refresh_token = json_decode(Redis::get($refresh_key), true);
            $json_decoded_get_token = $this->GetIdByRefreshToken($get_refresh_token);
            $id_token = $json_decoded_get_token['id_token'];
            $refresh_token = $get_refresh_token;
            Redis::setex($id_key, 3600, json_encode($id_token));
            Log::info('Successfully hit Redis and got refresh key then sent it to GetIdByRefreshToken');
        } else {
            $json_decoded_get_token = $this->GetIdAndRefreshToken();
            $id_token = $json_decoded_get_token['id_token'];
            $refresh_token = $json_decoded_get_token['refresh_token'];
            Redis::setex($id_key, 3600, json_encode($id_token));
            Redis::setex($refresh_key, 2332800, json_encode($refresh_token));
            Log::info('Successfully hit GetIdAndRefreshToken');
        }
        $trx = TrxChannel::where('payment_id', $payment_id)
            ->where('trx_id', $bkash_trx)
            ->first();

        $amount = $trx?->amount;

        $post_payment_id = collect()->push([
            'paymentID' => $payment_id,
            'trxID' => $bkash_trx,
            'amount' => (int)$amount,
            'reason' => 'test payer',
            'sku' => 'test'
        ])->flatMap(function ($values) {
            return  $values;
        })->all();
        $get_payment_id = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'X-APP-Key' => $this->app_key,
            'Authorization' => $id_token,
        ])->post($this->base_url . 'checkout/payment/refund', $post_payment_id);
        TrxChannel::create([
            'user_id' => $userId,
            'amount' => $amount,
            'payment_id' => $payment_id,
            'bkash_res' => json_encode($get_payment_id),
        ]);
        $wallet = $this->walletService->addBalance(-$amount, $userId);
        WalletTransaction::create([
            'wallet_id' => $wallet->wallet_id,
            'type' => 'refund',
            'amount' => (int) $amount,
            'status' => 'success',
        ]);
        return Redirect::to(config('system.appConfig.redirect_frontend_url') . '/dashboard?success=Successfully refunded');
    }

    private function GetIdAndRefreshToken()
    {
        try {
            $post_token = array(
                'app_key' => $this->app_key,
                'app_secret' => $this->app_secret
            );

            $get_token = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'password' => $this->password,
                'username' => $this->username
            ])->post($this->base_url . 'checkout/token/grant', $post_token);
            $res = json_decode($get_token->getBody(), true);
            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function GetIdByRefreshToken($refresh_token)
    {
        try {
            $post_token = array(
                'app_key' => $this->app_key,
                'app_secret' => $this->app_secret,
                'refresh_token' => $refresh_token
            );

            $get_token = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'password' => $this->password,
                'username' => $this->username
            ])->post($this->base_url . 'checkout/token/refresh', $post_token);
            $res = json_decode($get_token->getBody(), true);
            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
