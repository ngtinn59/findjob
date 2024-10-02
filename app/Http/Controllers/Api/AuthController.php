<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'account_type' => Constant::user_level_developer,
                'status' => Constant::user_status_active,
                'password' => Hash::make($request->password),
            ]);

            Profile::create([
                'users_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Send email verification notification
            $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'message' => 'Đăng ký thành công. Vui lòng xác minh email của bạn.',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            DB::rollback(); // Log the error
            Log::error('Đăng ký thất bại: ' . $e->getMessage());


            return response()->json(['message' => 'Đăng ký thất bại.'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if email exists
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'error' => [
                    'email' => ['Email chưa được đăng ký']
                ],
                'status_code' => 422
            ], 422);
        }

        // Attempt to authenticate user
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'error' => [
                    'Mật Khẩu' => ['Sai mật khẩu']
                ],
                'status_code' => 401
            ], 401);
        }

        // Auth::user() will return the authenticated user instance.
        $user = Auth::user();

        // Create a new plain-text token for the user.
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'name' => $user->name,
            'access_token' => $token,
            'email_verified' => $user->hasVerifiedEmail(),
            'status_code' => 200,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request) {
            $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout Sucess'], 200);
    }
}
