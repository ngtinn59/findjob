<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workplace; // Đảm bảo rằng bạn đã có model Workplace
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkplacesController extends Controller
{
    // Hiển thị danh sách các nơi làm việc
    public function index()
    {
        $workplaces = Workplace::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách nơi làm việc thành công",
            'data' => $workplaces,
            'status_code' => 200
        ]);
    }

    // Tạo nơi làm việc mới
    public function store(Request $request)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên nơi làm việc phải có ít nhất :min ký tự.',
            'name.max' => 'Tên nơi làm việc không được vượt quá :max ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $workplace = Workplace::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo nơi làm việc thành công!",
            'data' => $workplace,
            'status_code' => 200
        ]);
    }

    // Hiển thị chi tiết một nơi làm việc
    public function show(Workplace $workplace)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin nơi làm việc thành công',
            'data' => $workplace,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin nơi làm việc
    public function update(Request $request, Workplace $workplace)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên nơi làm việc phải có ít nhất :min ký tự.',
            'name.max' => 'Tên nơi làm việc không được vượt quá :max ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $workplace->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật nơi làm việc thành công',
            'data' => $workplace,
            'status_code' => 200
        ]);
    }

    // Xóa nơi làm việc
    public function destroy(Workplace $workplace)
    {
        $workplace->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa nơi làm việc thành công',
            'status_code' => 200
        ]);
    }
}
