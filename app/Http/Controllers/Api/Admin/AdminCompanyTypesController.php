<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminCompanyTypesController extends Controller
{
    // Hiển thị danh sách các loại công ty
    public function index()
    {
        $companyTypes = CompanyType::all();
        return response()->json([
            'success' => true,
            'message' => "Lấy danh sách loại công ty thành công",
            'data' => $companyTypes,
            'status_code' => 200
        ]);
    }

    // Tạo loại công ty mới
    public function store(Request $request)
    {
        $data = $request->only('name'); // Lấy dữ liệu từ request

        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên loại công ty phải có ít nhất :min ký tự.',
            'name.max' => 'Tên loại công ty không được vượt quá :max ký tự.',
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

        // Tạo loại công ty mới sau khi xác thực thành công
        $companyType = CompanyType::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo loại công ty thành công!",
            'data' => $companyType,
            'status_code' => 201
        ]);
    }

    // Hiển thị chi tiết một loại công ty
    public function show(CompanyType $companyType)
    {
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin loại công ty thành công',
            'data' => $companyType,
            'status_code' => 200
        ]);
    }

    // Cập nhật thông tin loại công ty
    public function update(Request $request, CompanyType $companyType)
    {
        $data = $request->only('name');

        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên loại công ty phải có ít nhất :min ký tự.',
            'name.max' => 'Tên loại công ty không được vượt quá :max ký tự.',
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

        // Cập nhật loại công ty sau khi xác thực thành công
        $companyType->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật loại công ty thành công',
            'data' => $companyType,
            'status_code' => 200
        ]);
    }

    // Xóa loại công ty
    public function destroy(CompanyType $companyType)
    {
        $companyType->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa loại công ty thành công',
            'status_code' => 200
        ]);
    }
}
