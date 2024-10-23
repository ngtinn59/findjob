<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminCompanySizesController extends Controller
{
    // Hiển thị danh sách các quy mô công ty
    public function index()
    {
        $companySizes = CompanySize::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách quy mô công ty thành công",
            'data' => $companySizes,
            'status_code' => 200
        ]);
    }

    // Tạo quy mô công ty mới
    public function store(Request $request)
    {
        $data = $request->only('name'); // Lấy dữ liệu từ request

        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên quy mô công ty phải có ít nhất :min ký tự.',
            'name.max' => 'Tên quy mô công ty không được vượt quá :max ký tự.',
        ]);

        // Trả về lỗi nếu xác thực thất bại
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        // Tạo quy mô công ty mới sau khi xác thực thành công
        $companySize = CompanySize::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo quy mô công ty thành công!",
            'data' => $companySize,
            'status_code' => 201
        ]);
    }

    // Hiển thị chi tiết một quy mô công ty
    public function show(CompanySize $companySize)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin quy mô công ty thành công',
            'data' => $companySize,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin quy mô công ty
    public function update(Request $request, CompanySize $companySize)
    {
        $data = $request->only('name');

        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên quy mô công ty phải có ít nhất :min ký tự.',
            'name.max' => 'Tên quy mô công ty không được vượt quá :max ký tự.',
        ]);

        // Trả về lỗi nếu xác thực thất bại
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        // Cập nhật quy mô công ty sau khi xác thực thành công
        $companySize->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật quy mô công ty thành công',
            'data' => $companySize,
            'status_code' => 200
        ]);
    }

    // Xóa quy mô công ty
    public function destroy(CompanySize $companySize)
    {
        $companySize->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa quy mô công ty thành công',
            'status_code' => 200
        ]);
    }
}
