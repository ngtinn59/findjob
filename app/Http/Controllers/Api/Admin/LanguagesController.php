<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguagesController extends Controller
{
    public function index()
    {
        $languages = Language::all();
        return response()->json([
            'message' => "Lấy danh sách ngôn ngữ thành công",
            'data' => $languages,
            'status_code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|string|min:3|max:50',
        ], [
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên phải có ít nhất :min ký tự.',
            'name.max' => 'Tên không được vượt quá :max ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $language = Language::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => "Tạo ngôn ngữ thành công!",
            'data' => $language,
            'status_code' => 200
        ]);
    }

    public function show(Language $language)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $language,
            'status_code' => 200
        ]);
    }

    public function update(Request $request, Language $language)
    {
        $data = $request->only('name');
        $language->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ngôn ngữ thành công',
            'data' => $language,
            'status_code' => 200
        ]);
    }

    public function destroy(Language $language)
    {
        $language->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa ngôn ngữ thành công',
            'status_code' => 200
        ]);
    }
}
