<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function blockUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Người dùng không tồn tại',
                'status_code' => 404
            ], 404);
        }

        $user->update(['status' => Constant::user_status_inactive]);

        return response()->json([
            'success' => true,
            'message' => 'Tài khoản đã bị chặn thành công',
            'status_code' => 200
        ], 200);
    }

    public function unblockUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Người dùng không tồn tại',
                'status_code' => 404
            ], 404);
        }

        $user->update(['status' => Constant::user_status_active]);

        return response()->json([
            'success' => true,
            'message' => 'Tài khoản đã được mở lại thành công',
            'status_code' => 200
        ], 200);
    }

    public function index()
    {
        // Ánh xạ giá trị account_type
        $accountTypeLabels = [
            Constant::user_level_developer => 'Người tìm việc',
            Constant::user_level_employer => 'Người tuyển dụng',
            Constant::user_level_host => 'Admin'
        ];

        // Ánh xạ giá trị status
        $accountTypeStatus = [
            Constant::user_status_active => 'Hoạt động',
            Constant::user_status_inactive => 'Không hoạt động'
        ];

        // Lấy danh sách người dùng có account_type là 1 hoặc 2
        $dataUsers = User::whereIn('account_type', [1, 2])->get()->map(function ($user) use ($accountTypeLabels, $accountTypeStatus) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_type' => $accountTypeLabels[$user->account_type] ?? 'Không xác định', // Sử dụng ánh xạ cho account_type
                'status' => $accountTypeStatus[$user->status] ?? 'Không xác định', // Sử dụng ánh xạ cho status
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách tài khoản thành công!',
            'data' => $dataUsers,
            'status_code' => 200

        ], 200);
    }




    // Create a new user
    public function store(Request $request)
    {
        // Validation với thông báo tiếng Việt
        $messages = [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.string' => 'Email phải là chuỗi ký tự.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ];

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message'=>'Lỗi tạo tài khoản',
                'errors' => $validator->errors(),
                'status_code' => 422
            ],
                422);
        }

        DB::beginTransaction();
        try {
            // Tạo user mới với dữ liệu từ request
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'account_type' => Constant::user_level_developer,
                'email_verified_at' => now(),
                'status' => Constant::user_status_active,
                'password' => Hash::make($request->password),
            ]);

            // Tạo profile cho user
            Profile::create([
                'users_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công. Vui lòng xác minh email của bạn.',
                'user' => $user,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Đăng ký thất bại: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thất bại.',
                'status_code' => 500,
            ], 500);
        }
    }

    // View user details
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Người dùng không tồn tại',
                'status_code' => 404
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin tài khoản thành công',
            'data' => $user,
            'status_code' => 200
        ], 200);
    }

    // Update user information
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại',
                'status_code' => 404
            ], 404);
        }

        // Validation với thông báo tiếng Việt
        $messages = [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.string' => 'Email phải là chuỗi ký tự.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ];

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật tài khoản',
                'errors' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Cập nhật thông tin user
            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tài khoản thành công',
                'user' => $user,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Cập nhật tài khoản thất bại: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật tài khoản thất bại.',
                'status_code' => 500,
            ], 500);
        }
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Người dùng không tồn tại',
                'status_code' => 404
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
            'status_code' => 204
        ], 204);
    }
}
