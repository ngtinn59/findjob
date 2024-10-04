<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use App\Utilities\Constant;
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
            // Tạo user mới với dữ liệu từ request
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'account_type' => Constant::USER_LEVEL_DEVELOPER, // Có thể tùy chỉnh theo yêu cầu
                'status' => Constant::USER_STATUS_ACTIVE, // Có thể điều chỉnh trạng thái mặc định
                'password' => Hash::make($request->password),
            ]);

            // Tạo profile cho user
            Profile::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Gửi email xác nhận cho người dùng
            $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'message' => 'Đăng ký thành công. Vui lòng xác minh email của bạn.',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            // Log lỗi nếu có vấn đề
            Log::error('Đăng ký thất bại: ' . $e->getMessage());

            return response()->json(['message' => 'Đăng ký thất bại.'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Kiểm tra xem email có tồn tại trong hệ thống không
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'error' => [
                    'email' => ['Email chưa được đăng ký']
                ],
                'status_code' => 422
            ], 422);
        }

        // Xác thực người dùng
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'error' => [
                    'password' => ['Sai mật khẩu']
                ],
                'status_code' => 401
            ], 401);
        }

        $user = Auth::user();

        // Tạo token để sử dụng trong API
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'name' => $user->name,
            'access_token' => $token,
            'email_verified' => $user->hasVerifiedEmail(),
            'status_code' => 200,
            'token_type' => 'bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // Xóa tất cả token của người dùng khi đăng xuất
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout thành công'], 200);
    }

    public function changePassword(Request $request)
    {
        // Xác thực dữ liệu
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // Lấy thông tin người dùng hiện tại
        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => ['current_password' => ['Mật khẩu hiện tại không chính xác']],
                'status_code' => 422,
            ], 422);
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Mật khẩu đã được thay đổi thành công.',
            'status_code' => 200,
        ]);
    }


    public function forgotPassword(Request $request)
    {
//        $request->validate([
//            'email' => 'required|email',
//        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Email không tồn tại trong hệ thống.',
                'status_code' => 404,
            ], 404);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send reset password email
        try {
            Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Password Notification');
            });
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không thể gửi email. Vui lòng thử lại sau.',
                'status_code' => 500,
            ], 500);
        }

        return response()->json([
            'message' => 'Email đặt lại mật khẩu đã được gửi.',
            'status_code' => 200,
        ]);
    }

    public function resetPassword(Request $request)
    {
//        $request->validate([
//            'email' => 'required|email',
//            'token' => 'required',
//            'password' => 'required|min:8|confirmed',
//        ]);

        $passwordReset = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'error' => 'Token không hợp lệ hoặc đã hết hạn.',
                'status_code' => 400,
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'Email không tồn tạzi trong hệ thống.',
                'status_code' => 404,
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Mật khẩu đã được đặt lại thành công.',
            'status_code' => 200,
        ]);
    }
}
