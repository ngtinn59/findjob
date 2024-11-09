<?php

namespace App\Http\Controllers\Api\Resume;

use App\Http\Controllers\Controller;
use App\Models\LanguageSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageSkillsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $profile = $user->profile;
        $profiles_id = $profile->id;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have a profile',
            ], 400);
        }

        $languageSkills = LanguageSkill::where('profiles_id', $profiles_id)->get();

        $languageSkillData = $languageSkills->map(function ($languageSkill) {
            return [
                'id' => $languageSkill->id,
                'language_name' => $languageSkill->language->name, // Lấy tên ngôn ngữ từ mối quan hệ
                'level' => $languageSkill->level, // Hiển thị mức độ thông thạo
                'profile_id' => $languageSkill->profiles_id, // Hiển thị ID hồ sơ nếu cần
            ];
        });


        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $languageSkillData,
            'status_code' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user =  auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id;
        $data = [
            'language_id' => $request->input('language_id'),
            'level' => $request->input('level'),
            'profiles_id' => $profile_id,
        ];

        $validator = Validator::make($data, [
            'language_id' => 'required|exists:languages,id',
            'level' => 'required',
            'profiles_id' => 'required|exists:profiles,id',
        ], [
            'language_id.required' => 'Ngôn ngữ là bắt buộc.',
            'level.required' => 'Mức độ thông thạo là bắt buộc.',
            'profiles_id.required' => 'ID hồ sơ là bắt buộc.',
            'profiles_id.exists' => 'ID hồ sơ không tồn tại.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $languageSkill = LanguageSkill::create($data);

        // Custom response với tên ngôn ngữ
        return response()->json([
            'success'   => true,
            'message'   => "Language skill successfully added!",
            'data' => [
                'id' => $languageSkill->id,
                'language_name' => $languageSkill->language->name,
                'proficiency_level' => $languageSkill->level
            ],
            'status_code' => 200
        ]);
    }


    /**
     * Show the specified resource.
     */
    public function show(LanguageSkill $languageSkill)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($languageSkill->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to the language skill',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'id' => $languageSkill->id,
                'language_name' => $languageSkill->language->name, // Lấy tên ngôn ngữ từ mối quan hệ
                'level' => $languageSkill->level, // Hiển thị mức độ thông thạo
                'profile_id' => $languageSkill->profiles_id, // Hiển thị ID hồ sơ nếu cần
            ],
            'status_code' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LanguageSkill $languageSkill)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($languageSkill->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to update the language skill',
            ], 403);
        }

        // Chỉ lấy các trường cần thiết từ request
        $data = $request->only(['language_id', 'level']);

        // Kiểm tra xem ngôn ngữ có hợp lệ không (nếu cần)
        $validator = Validator::make($data, [
            'language_id' => 'required|exists:languages,id',
            'level' => 'required', // Có thể điều chỉnh tùy theo định dạng mà bạn muốn
        ], [
            'language_id.required' => 'Ngôn ngữ là bắt buộc.',
            'language_id.exists' => 'Ngôn ngữ không tồn tại.',
            'level.required' => 'Mức độ thông thạo là bắt buộc.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $languageSkill->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Language skill updated successfully!',
            'data' => [
                'id' => $languageSkill->id,
                'language_name' => $languageSkill->language->name, // Lấy tên ngôn ngữ từ mối quan hệ
                'level' => $languageSkill->level, // Mức độ thông thạo
                'profile_id' => $languageSkill->profiles_id,
                'updated_at' => $languageSkill->updated_at, // Hiển thị thời gian cập nhật
            ],
            'status_code' => 200
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LanguageSkill $languageSkill)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($languageSkill->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to delete the language skill',
            ], 403);
        }

        $languageSkill->delete();
        return response()->json([
            'success' => true,
            'message' => 'Language skill deleted successfully',
        ]);
    }
}
