<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExperienceLevel; // Đảm bảo rằng bạn đã import model này
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperienceLevelsController extends Controller
{
    // Hiển thị danh sách các cấp độ kinh nghiệm
    public function index()
    {
        $experienceLevels = ExperienceLevel::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách cấp độ kinh nghiệm thành công",
            'data' => $experienceLevels,
            'status_code' => 200
        ]);
    }

    // Tạo cấp độ kinh nghiệm mới
    public function store(Request $request)
    {
        $data = $request->only('name'); // Lấy dữ liệu từ request

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên cấp độ kinh nghiệm phải có ít nhất :min ký tự.',
            'name.max' => 'Tên cấp độ kinh nghiệm không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $experienceLevel = ExperienceLevel::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo cấp độ kinh nghiệm thành công!",
            'data' => $experienceLevel,
            'status_code' => 200
        ]);
    }

    // Hiển thị chi tiết một cấp độ kinh nghiệm
    public function show(ExperienceLevel $experienceLevel)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin cấp độ kinh nghiệm thành công',
            'data' => $experienceLevel,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin cấp độ kinh nghiệm
    public function update(Request $request, ExperienceLevel $experienceLevel)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên cấp độ kinh nghiệm phải có ít nhất :min ký tự.',
            'name.max' => 'Tên cấp độ kinh nghiệm không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $experienceLevel->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật cấp độ kinh nghiệm thành công',
            'data' => $experienceLevel,
            'status_code' => 200
        ]);
    }

    // Xóa cấp độ kinh nghiệm
    public function destroy(ExperienceLevel $experienceLevel)
    {
        $experienceLevel->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa cấp độ kinh nghiệm thành công',
            'status_code' => 200
        ]);
    }
}
