<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Certificate;
use App\Models\experiences;
use App\Models\Profile;
use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Models\projects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =  auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id;
        $project = Project::where("profiles_id", $profile_id)->get();
        $projectData = $project->map(function ($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'description' => $project->description,
            ];
        });

        // Trả về danh sách giáo dục dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $projectData,
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
            'title' => $request->input('title'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'description' => $request->input('description'),
            'profiles_id' => $profile_id
        ];
        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'title' => 'required',
            'start_date' => 'required|date', // Thêm kiểm tra định dạng ngày
            'end_date' => 'required|date|after:start_date', // Kiểm tra ngày kết thúc sau ngày bắt đầu
            'description' => 'required',
            'profiles_id' => 'required',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'description.required' => 'Mô tả là bắt buộc.',
            'profiles_id.required' => 'ID hồ sơ là bắt buộc.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $projects = Project::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $projects,
            'status_code' => 200
        ]);

    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $user = auth()->user(); // Lấy người dùng hiện tại
        $profile = $user->profile; // Lấy hồ sơ của người dùng hiện tại

        // Kiểm tra xem dự án có thuộc về hồ sơ của người dùng không
        if ($project->profiles_id === $profile->id) {
            return response()->json([
                'success' => true,
                'message' => 'Dự án được tìm thấy.',
                'data' => $project,
                'status_code' => 200,
            ]);
        }

        // Nếu không thuộc về hồ sơ, trả về lỗi
        return response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền truy cập vào dự án này.',
            'status_code' => 403, // Trả về mã lỗi 403 cho Unauthorized
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Kiểm tra xem dự án có thuộc về hồ sơ của người dùng không
        if ($project->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật dự án này.',
                'status_code' => 403,
            ]);
        }

        // Xác thực dữ liệu
        $data = [
            'title' => $request->input('title'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'description' => $request->input('description'),
            'profiles_id' => $profile->id,
        ];

        $validator = Validator::make($data, [
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'required',
            'profiles_id' => 'required',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'description.required' => 'Mô tả là bắt buộc.',
            'profiles_id.required' => 'ID hồ sơ là bắt buộc.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Cập nhật dự án
        $data = $validator->validated();
        $project->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Dự án đã được cập nhật thành công.',
            'data' => $project,
            'status_code' => 200,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully',
            'status_code' => 200
        ]);
    }
}
