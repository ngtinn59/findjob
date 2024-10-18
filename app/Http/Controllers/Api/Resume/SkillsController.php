<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Profile;
use App\Models\Project;
use App\Models\projects;
use App\Models\Skill;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SkillsController extends Controller
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


        $skills = Skill::where('profiles_id', $profiles_id)->get();

        $skillData = $skills->map(function ($skill) {

            return [
                'id' => $skill->id,
                'level' => $skill->level,
                'name' => $skill->name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $skillData,
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
            'name' => $request->input('name'),
            'level' => $request->input('level'),
            'profiles_id' => $profile_id
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:5',
            'profiles_id' => 'required|exists:profiles,id',
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được dài quá 255 ký tự.',
            'level.required' => 'Cấp độ là bắt buộc.',
            'level.integer' => 'Cấp độ phải là một số nguyên.',
            'level.min' => 'Cấp độ phải từ 1 trở lên.',
            'level.max' => 'Cấp độ không được lớn hơn 10.',
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
        $skills  = Skill::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $skills,
            'status_code' => 200
        ]);
    }



    public function show(Skill $skill)
    {
        // Check if the award belongs to the authenticated user
        $user =  auth()->user();

        $profile = $user->profile;
        $profiles_id = $profile->id;
        if ($skill->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực không thể hiển thị quá trình học tập',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'id' => $skill->id,
                'name' => $skill->name,
                'level' => $skill->level,
            ],
            'status_code' => 200
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Kiểm tra quyền truy cập
        if ($skill->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to update the skills',
            ], 403);
        }

        $data = $request->all();

        $skill->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kỹ năng chuyên môn cập nhật thành công!',
            'data' => $skill,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($skill->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to delete the skill',
            ], 403);
        }
        $skill->delete();
        return response()->json([
            'success' => true,
            'message' => 'Kỹ năng chuyên môn đã xóa thành công',
        ]);
    }
}
