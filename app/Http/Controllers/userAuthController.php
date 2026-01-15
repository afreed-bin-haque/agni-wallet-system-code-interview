<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class userAuthController extends Controller
{
    protected $walletService;
    public function __construct(WalletService $walletService)
    {

        $this->walletService = $walletService;
    }
    public function registerUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $validator->errors()->first()
                ], 200);
            }
            $hashedPass = Hash::make($request->password);

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => $hashedPass,
            ]);
            $userId = $user->id;
            $amount = 0;
            $wallet = $this->walletService->addBalance($amount, $userId);
            return response()->json([
                "status" => true,
                "msg" => "User registered"
            ], 200);
        } catch (Exception $e) {
            $errorDetails = "[userAuthController] error " . $e->getMessage() . " at line: " . $e->getLine();
            Log::error($errorDetails);
            abort(400, 'Request could not process.Please check log');
        }
    }
    public function loginUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $validator->errors()->first()
                ], 200);
            }

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return response()->json([
                    'status' => true,
                    'msg' => 'Login successful'
                ], 200);
            }
            return response()->json([
                "status" => false,
                "msg" => "Invalid creadentials"
            ], 200);
        } catch (Exception $e) {
            $errorDetails = "[userAuthController] error " . $e->getMessage() . " at line: " . $e->getLine();
            Log::error($errorDetails);
            abort(400, 'Request could not process.Please check log');
        }
    }

    public function userLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            "status" => true,
            "msg" => "Successfully loggedout",
            "csrf_token" => csrf_token(),
            "redirect" => route('login')
        ]);
    }
}
