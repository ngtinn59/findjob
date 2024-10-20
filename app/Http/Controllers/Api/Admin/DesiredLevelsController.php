<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesiredLevel; // Đảm bảo rằng bạn đã có model DesiredLevel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesiredLevelsController extends Controller
{
    // Hiển thị danh sách các Desired Level
    public function index()
    {
        $levels = DesiredLevel::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách desired levels thành công",
            'data' => $levels,
            'status_code' => 200
        ]);
    }

    // Tạo Desired Level mới
    public function store(Request $request)
    {
        $data = $request->only('name', 'priority'); // Lấy dữ liệu từ request

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
            'priority' => 'nullable|integer',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên level phải có ít nhất :min ký tự.',
            'name.max' => 'Tên level không được vượt quá :max ký tự.',
            'priority.integer' => 'Mức độ ưu tiên phải là số nguyên.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $level = DesiredLevel::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo desired level thành công!",
            'data' => $level,
            'status_code' => 200
        ]);
    }

    // Hiển thị chi tiết một Desired Level
    public function show(DesiredLevel $desiredLevel)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin desired level thành công',
            'data' => $desiredLevel,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin Desired Level
    public function update(Request $request, DesiredLevel $desiredLevel)
    {
        $data = $request->only('name', 'priority');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
            'priority' => 'nullable|integer',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên level phải có ít nhất :min ký tự.',
            'name.max' => 'Tên level không được vượt quá :max ký tự.',
            'priority.integer' => 'Mức độ ưu tiên phải là số nguyên.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $desiredLevel->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật desired level thành công',
            'data' => $desiredLevel,
            'status_code' => 200
        ]);
    }

    // Xóa Desired Level
    public function destroy(DesiredLevel $desiredLevel)
    {
        $desiredLevel->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa desired level thành công',
            'status_code' => 200
        ]);
    }
}
