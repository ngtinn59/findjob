<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmploymentTypesController extends Controller
{
    // Hiển thị danh sách các loại hình làm việc
    public function index()
    {
        $employmentTypes = EmploymentType::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách loại hình làm việc thành công",
            'data' => $employmentTypes,
            'status_code' => 200
        ]);
    }

    // Tạo loại hình làm việc mới
    public function store(Request $request)
    {
        $data = $request->only('name'); // Lấy dữ liệu từ request

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên loại hình làm việc phải có ít nhất :min ký tự.',
            'name.max' => 'Tên loại hình làm việc không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $employmentType = EmploymentType::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo loại hình làm việc thành công!",
            'data' => $employmentType,
            'status_code' => 200
        ]);
    }

    // Hiển thị chi tiết một loại hình làm việc
    public function show(EmploymentType $employmentType)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin loại hình làm việc thành công',
            'data' => $employmentType,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin loại hình làm việc
    public function update(Request $request, EmploymentType $employmentType)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên loại hình làm việc phải có ít nhất :min ký tự.',
            'name.max' => 'Tên loại hình làm việc không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $employmentType->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật loại hình làm việc thành công',
            'data' => $employmentType,
            'status_code' => 200
        ]);
    }

    // Xóa loại hình làm việc
    public function destroy(EmploymentType $employmentType)
    {
        $employmentType->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa loại hình làm việc thành công',
            'status_code' => 200
        ]);
    }
}
