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
            // Chuyển đổi cấp độ thành các chuỗi tương ứng
            $levelString = '';
            switch ($skill->level) {
                case 1:
                    $levelString = 'Beginner';
                    break;
                case 2:
                    $levelString = 'Intermediate';
                    break;
                case 3:
                    $levelString = 'Excellent';
                    break;
                default:
                    $levelString = 'unknown';
            }

            return [
                'id' => $skill->id,
                'level' => $levelString, // Thay vì là số, chúng ta sử dụng chuỗi tương ứng

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
    public function store(Request $request, Validator $validator)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have a profile',
            ], 400);
        }

        $profiles_id = $profile->id;

        $data = $request->json()->all(); // Sử dụng phương thức json() để trích xuất dữ liệu JSON từ request

        $validationRules = [
            '*.name' => 'required|string',
            '*.level' => 'required|numeric',
        ];

        $validation = $validator::make($data, $validationRules);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validation->errors(),
            ], 400);
        }

        // Xóa tất cả kỹ năng hiện có của người dùng
        $profile->skills()->delete();

        // Chuẩn bị dữ liệu cho việc thêm mới kỹ năng
        $skillsData = [];
        foreach ($data as $skill) { // Lặp qua mảng dữ liệu JSON được truyền vào
            $skillsData[] = [
                'name' => $skill['name'],
                'level' => $skill['level'],
                'profiles_id' => $profiles_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Thêm mới các kỹ năng cho người dùng
        Skill::insert($skillsData);

        // Lấy danh sách kỹ năng đã được thêm mới
        $newSkills = Skill::where('profiles_id', $profiles_id)->get();

        return response()->json([
            'success' => true,
            'message' => "Skills updated successfully",
            'skills' => $newSkills, // Trả về danh sách kỹ năng mới
            'status_code' => 200
        ]);
    }




    public function show(Skill $skill)
    {
//        $profileId = auth()->user()->profile->first()->id;
//
//        if ($skill->profiles_id == $profileId) {
//            return response()->json([
//                'success' => true,
//                'message' => 'success',
//                'data' => $skill
//            ]);
//        }
//
//        return response()->json([
//            'success' => false,
//            'message' => 'fail'
//        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $user =  auth()->user();
        $profile = $user->profile->first();
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        $data = $request->all();

        $skill->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
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
                'message' => 'Unauthorized access to delete the award',
            ], 403);
        }
        $skill->delete();
        return response()->json([
            'success' => true,
            'message' => 'Skill deleted successfully',
        ]);
    }
}
