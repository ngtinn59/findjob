<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel; // Đảm bảo rằng bạn đã có model EducationLevel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EducationLevelsController extends Controller
{
    // Hiển thị danh sách các trình độ học vấn
    public function index()
    {
        $educationLevels = EducationLevel::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách trình độ học vấn thành công",
            'data' => $educationLevels,
            'status_code' => 200
        ]);
    }

    // Tạo trình độ học vấn mới
    public function store(Request $request)
    {
        $data = $request->only('name'); // Lấy dữ liệu từ request

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50|unique:education_levels,name',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên trình độ học vấn phải có ít nhất :min ký tự.',
            'name.max' => 'Tên trình độ học vấn không được vượt quá :max ký tự.',
            'name.unique' => 'Tên trình độ học vấn phải là duy nhất.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $educationLevel = EducationLevel::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo trình độ học vấn thành công!",
            'data' => $educationLevel,
            'status_code' => 200
        ]);
    }

    // Hiển thị chi tiết một trình độ học vấn
    public function show(EducationLevel $educationLevel)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin trình độ học vấn thành công',
            'data' => $educationLevel,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin trình độ học vấn
    public function update(Request $request, EducationLevel $educationLevel)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50|unique:education_levels,name,' . $educationLevel->id,
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên trình độ học vấn phải có ít nhất :min ký tự.',
            'name.max' => 'Tên trình độ học vấn không được vượt quá :max ký tự.',
            'name.unique' => 'Tên trình độ học vấn phải là duy nhất.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $educationLevel->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trình độ học vấn thành công',
            'data' => $educationLevel,
            'status_code' => 200
        ]);
    }

    // Xóa trình độ học vấn
    public function destroy(EducationLevel $educationLevel)
    {
        $educationLevel->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa trình độ học vấn thành công',
            'status_code' => 200
        ]);
    }
}
