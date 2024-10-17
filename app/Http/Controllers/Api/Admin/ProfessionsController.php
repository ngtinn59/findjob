<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profession; // Đảm bảo rằng bạn đã có model Profession
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfessionsController extends Controller
{
    // Hiển thị danh sách các nghề
    public function index()
    {
        $professions = Profession::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách nghề thành công",
            'data' => $professions,
            'status_code' => 200
        ]);
    }

    // Tạo nghề mới
    public function store(Request $request)
    {
        $data = $request->only('name',); // Lấy dữ liệu từ request

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên nghề phải có ít nhất :min ký tự.',
            'name.max' => 'Tên nghề không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $profession = Profession::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo nghề thành công!",
            'data' => $profession,
            'status_code' => 200
        ]);
    }

    // Hiển thị chi tiết một nghề
    public function show(Profession $profession)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin nghề thành công',
            'data' => $profession,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin nghề
    public function update(Request $request, Profession $profession)
    {
        $data = $request->only('name',);

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên nghề phải có ít nhất :min ký tự.',
            'name.max' => 'Tên nghề không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $profession->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật nghề thành công',
            'data' => $profession,
            'status_code' => 200
        ]);
    }

    // Xóa nghề
    public function destroy(Profession $profession)
    {
        $profession->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa nghề thành công',
            'status_code' => 200
        ]);
    }
}
