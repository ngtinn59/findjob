<?php

namespace App\Http\Controllers\Api\Resume;

use App\Http\Controllers\Controller;
use App\Models\aboutme;
use App\Models\Objective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ObjectivesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy người dùng đã xác thực
        $user = auth()->user();

        // Kiểm tra xem người dùng có tồn tại không
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không hợp lệ.',
                'status_code' => 401,
            ], 401);
        }

        // Lấy hồ sơ của người dùng
        $profile = $user->profile;

        // Kiểm tra xem hồ sơ có tồn tại không
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Hồ sơ không tồn tại.',
                'status_code' => 404,
            ], 404);
        }

        $profile_id = $profile->id;

        // Lấy tất cả các mục tiêu liên quan đến profile_id
        $objectives = Objective::where('profiles_id', $profile_id)->get();

        // Kiểm tra xem có mục tiêu nào không
        if ($objectives->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mục tiêu nào được tìm thấy.',
                'status_code' => 404,
            ], 404);
        }

        // Chuyển đổi dữ liệu để trả về dưới dạng JSON
        $objectiveData = $objectives->map(function ($objective) {
            return [
                'desired_position' => $objective->desired_position,
                'desired_level' => $objective->desired_level,
                'education_level' => $objective->education_level,
                'experience_years' => $objective->experience_years,
                'profession' => $objective->profession,
                'work_address' => $objective->work_address,
                'expected_salary' => $objective->expected_salary,
                'work_location' => $objective->work_location,
                'employment_type' => $objective->employment_type,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Dữ liệu được lấy thành công!',
            'data' => $objectiveData,
            'status_code' => 200
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id; // Lấy profile_id từ người dùng

        $data = [
            'desired_position' => $request->input('desired_position'),
            'desired_level' => $request->input('desired_level'),
            'education_level' => $request->input('education_level'),
            'experience_years' => $request->input('experience_years'),
            'profession' => $request->input('profession'),
            'work_address' => $request->input('work_address'),
            'expected_salary' => $request->input('expected_salary'),
            'work_location' => $request->input('work_location'),
            'employment_type' => $request->input('employment_type'),
            'profiles_id' => $profile_id, // Thêm profile_id vào dữ liệu
        ];


        $validator = Validator::make($data, [
            'desired_position' => 'required',
            'desired_level' => 'required',
            'education_level' => 'required',
            'experience_years' => 'required',
            'profession' => 'required',
            'work_address' => 'required',
            'work_location' => 'required',
            'employment_type' => 'required',
            'expected_salary' => 'required',
            'profiles_id' => 'required|exists:profiles,id',
        ], [
            'desired_position.required' => 'Vui lòng nhập vị trí mong muốn.',
            'desired_level.required' => 'Vui lòng nhập cấp bậc mong muốn.',
            'education_level.required' => 'Vui lòng nhập trình độ học vấn.',
            'experience_years.required' => 'Vui lòng nhập số năm kinh nghiệm.',
            'profession.required' => 'Vui lòng nhập ngành nghề.',
            'work_address.required' => 'Vui lòng nhập địa chỉ làm việc.',
            'work_location.required' => 'Vui lòng nhập nơi làm việc.',
            'employment_type.required' => 'Vui lòng nhập hình thức làm việc.',
            'expected_salary.required' => 'Vui lòng nhập mức lương mong muốn.',
            'profiles_id.required' => 'profiles_id là bắt buộc.',
            'profiles_id.exists' => 'profiles_id không hợp lệ.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $data = $validator->validated();


        // Update existing record or create a new one
        $objective = Objective::updateOrCreate(
            ['profiles_id' => $profile_id], // Điều kiện tìm kiếm
            $data // Dữ liệu để tạo hoặc cập nhật
        );

        $status_code = $objective->wasRecentlyCreated ? 201 : 200; // 201 nếu mới tạo, 200 nếu cập nhật

        return response()->json([
            'success' => true,
            'message' => "Thực hiện thành công",
            "data" => $objective,
            'status_code' => $status_code
        ]);
    }

    /**
     * Display the specified resource.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Objective $objective)
    {
        $objective->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mục tiêu nghề nghiệp đã được xóa',
            'status_code' => 200
        ]);

    }
    public function search(Request $request)
    {
        // Lấy tất cả các điều kiện tìm kiếm từ request
        $desired_position = $request->input('desired_position');
        $desired_level = $request->input('desired_level');
        $profession = $request->input('profession');
        $expected_salary = $request->input('expected_salary');

        // Khởi tạo truy vấn
        $query = Objective::query();

        // Thêm các điều kiện tìm kiếm vào truy vấn nếu có
        if ($desired_position) {
            $query->where('desired_position', 'like', "%$desired_position%");
        }

        if ($desired_level) {
            $query->where('desired_level', 'like', "%$desired_level%");
        }

        if ($profession) {
            $query->where('profession', 'like', "%$profession%");
        }

        if ($expected_salary) {
            $query->where('expected_salary', '<=', $expected_salary);
        }

        // Thực hiện truy vấn và lấy kết quả
        $objectives = $query->get();

        // Kiểm tra xem có ứng viên nào không
        if ($objectives->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ứng viên nào phù hợp.',
                'status_code' => 404,
            ], 404);
        }

        // Chuyển đổi dữ liệu để trả về dưới dạng JSON
        $objectiveData = $objectives->map(function ($objective) {
            $profile = $objective->profile; // Giả sử rằng Objective có quan hệ với Profile
            return [
                'name' => $profile->name,
                'desired_position' => $objective->desired_position,
                'desired_level' => $objective->desired_level,
                'education_level' => $objective->education_level,
                'experience_years' => $objective->experience_years,
                'profession' => $objective->profession,
                'work_address' => $objective->work_address,
                'expected_salary' => $objective->expected_salary,
                'work_location' => $objective->work_location,
                'employment_type' => $objective->employment_type,
                'profile' => [
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Dữ liệu được lấy thành công!',
            'data' => $objectiveData,
            'status_code' => 200,
        ]);
    }


}
