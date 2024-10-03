<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

    public function changePassword(Request $request)
    {
        // Lấy thông tin người dùng đã đăng nhập
        $user = Auth::user();

        // Kiểm tra nếu $user là null hoặc không phải đối tượng User
        if (!$user) {
            return response()->json([
                'error' => 'Người dùng không tồn tại hoặc chưa đăng nhập.',
                'status_code' => 401,
            ], 401);
        }

        // Kiểm tra mật khẩu hiện tại
        $request->validate([
            'current_password' => 'required',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => ['current_password' => ['Mật khẩu hiện tại không chính xác']],
                'status_code' => 422
            ], 422);
        }

        // Tạo mã xác minh ngẫu nhiên
        $verificationCode = Str::random(6);

        // Cập nhật mã xác minh trong cơ sở dữ liệu cho người dùng
        $user->update([
            'verification_code' => $verificationCode,
        ]);

        // Gửi mã xác minh đến email người dùng và xử lý lỗi khi gửi email
        try {
            Mail::to($user->email)->send(new \App\Mail\SendVerificationCode($verificationCode));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không thể gửi email. Vui lòng thử lại sau.',
                'status_code' => 500,
            ], 500);
        }

        return response()->json([
            'message' => 'Mã xác minh đã được gửi tới email của bạn.',
            'status_code' => 200,
        ]);
    }


    public function verifyCodeAndUpdatePassword(Request $request)
    {
        $request->validate([
            'verification_code' => 'required',
            'new_password'      => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verify the code
        if ($request->verification_code !== $user->verification_code) {
            return response()->json([
                'success' => false,
                'error' => ['verification_code' => ['Invalid verification code']],
                'status_code' => 422
            ], 422);
        }

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->new_password),
            'verification_code' => null, // Clear the verification code
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
            'status_code' => 200,
        ]);
    }
}
