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
                'account_type' => Constant::user_level_developer, // Có thể tùy chỉnh theo yêu cầu
                'status' => Constant::user_status_active, // Có thể điều chỉnh trạng thái mặc định
                'password' => Hash::make($request->password),
            ]);

            // Tạo profile cho user
            Profile::create([
                'users_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Gửi email xác nhận cho người dùng
            $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công. Vui lòng xác minh email của bạn.',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            // Log lỗi nếu có vấn đề
            Log::error('Đăng ký thất bại: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thất bại.',
                'status_code' => 500,
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Kiểm tra xem email có tồn tại trong hệ thống không
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'email' => ['Email chưa được đăng ký']
                ],
                'status_code' => 422
            ], 422);
        }

        // Kiểm tra xem người dùng đã xác minh email hay chưa
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'email' => ['Email chưa được xác minh. Vui lòng kiểm tra email của bạn.']
                ],
                'status_code' => 403
            ], 403);
        }

        // Kiểm tra trạng thái tài khoản
        if ($user->status !== Constant::user_status_active) {
            return response()->json([
                'success' => false,
                'error' => [
                    'account' => ['Tài khoản của bạn đã bị chặn hoạt động.']
                ],
                'status_code' => 403
            ], 403);
        }

        // Xác thực người dùng
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
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
            'message' => 'Đăng nhập thành công',
            'data' => [
                'name' => $user->name,
                'access_token' => $token,
                'email_verified' => $user->hasVerifiedEmail(),
                'token_type' => 'bearer',
            ],
            'status_code' => 200,
        ]);
    }

    public function logout(Request $request)
    {
        // Xóa tất cả token của người dùng khi đăng xuất
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công',
            'status_code' => 200
        ], 200);
    }

    public function changePassword(Request $request)
    {
        // Xác thực dữ liệu với thông báo tùy chỉnh
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
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

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

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
                $message->subject('Thông báo đặt lại mật khẩu');
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

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed'], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'token.required' => 'Vui lòng nhập token.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }


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
                'error' => 'Email không tồn tại trong hệ thống.',
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
