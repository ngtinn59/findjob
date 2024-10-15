<?php

namespace App\Http\Controllers\Api\Resume;

use App\Http\Controllers\Controller;
use App\Models\aboutme;
use App\Models\Cv;
use App\Models\Objective;
use App\Utillities\Common;
use Carbon\Carbon;
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
                    'id' => $objective->id, // ID của bản ghi vừa tạo
                    'desired_position' => $objective->desired_position,
                    'desired_level_id' => $objective->desiredLevel->name ?? null,
                    'profession_id' => $objective->profession->name ?? null,
                    'employment_type_id' => $objective->employmentType->name ?? null,
                    'experience_level_id' => $objective->experienceLevel->name ?? null,
                    'work_address' => $objective->work_address,
                    'education_level_id' => $objective->educationLevel->name ?? null,
                    'salary_from' => $objective->salary_from,
                    'salary_to' => $objective->salary_to,
                    'file' =>  asset('cvs/' . $objective->file),
                    'status' => ($objective->status == 3) ? 'hoạt động' : (($objective->status == 4) ? 'không hoạt động' : 'không xác định'),
                    'country' => $objective->country ? $objective->country->name : null, // Tên quốc gia
                    'city' => $objective->city ? $objective->city->name : null, // Tên thành phố
                    'district' => $objective->district ? $objective->district->name : null, // Tên quận/huyện
                    'profiles_id' => $objective->profiles_id,
                    'created_at' => $objective->created_at, // Thêm thời gian tạo nếu cần
                    'updated_at' => $objective->updated_at, // Thêm thời gian cập nhật nếu cần
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

        // Kiểm tra và validate các trường nhập liệu
        $validator = Validator::make($request->all(), [
            'desired_position' => 'required|string|max:255',
            'desired_level_id' => 'required|integer',
            'profession_id' => 'required|integer',
            'employment_type_id' => 'required|integer',
            'experience_level_id' => 'required|integer',
            'work_address' => 'required|string|max:255',
            'education_level_id' => 'required|integer',
            'salary_from' => 'required|integer|min:0',
            'salary_to' => 'required|integer|min:0|gte:salary_from',
            'status' => 'required',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'district_id' => 'required|integer',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Kiểm tra file nếu có
        ], [
            'desired_position.required' => 'Vui lòng nhập vị trí mong muốn.',
            'desired_level_id.required' => 'Vui lòng chọn cấp bậc mong muốn.',
            'profession_id.required' => 'Vui lòng chọn nghề nghiệp.',
            'employment_type_id.required' => 'Vui lòng chọn hình thức làm việc.',
            'experience_level_id.required' => 'Vui lòng nhập số năm kinh nghiệm.',
            'work_address.required' => 'Vui lòng nhập địa chỉ làm việc.',
            'education_level_id.required' => 'Vui lòng chọn trình độ học vấn.',
            'salary_from.required' => 'Vui lòng nhập mức lương bắt đầu.',
            'salary_to.required' => 'Vui lòng nhập mức lương kết thúc.',
            'salary_to.gte' => 'Mức lương kết thúc phải lớn hơn hoặc bằng mức lương bắt đầu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'country_id.required' => 'Vui lòng chọn quốc gia.',
            'city_id.required' => 'Vui lòng chọn thành phố.',
            'district_id.required' => 'Vui lòng chọn quận/huyện.',
            'file.mimes' => 'Tệp tin phải có định dạng pdf, doc, hoặc docx.',
            'file.max' => 'Tệp tin không được vượt quá 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $validatedData = $validator->validated();

        // Kiểm tra tệp tin và xử lý upload file nếu có
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $file_name = Common::uploadFile($file, public_path('cvs')); // Sử dụng lớp Common để upload file
            $validatedData['file'] = $file_name; // Lưu tên file vào dữ liệu
        }

        // Thêm profile_id vào dữ liệu đã được validate
        $validatedData['profiles_id'] = $profile_id;

        // Tạo mới bản ghi
        $objective = Objective::create($validatedData);

        $responseData = [
            'id' => $objective->id, // ID của bản ghi vừa tạo
            'desired_position' => $objective->desired_position,
            'desired_level_id' => $objective->desiredLevel->name ?? null,
            'profession_id' => $objective->profession->name ?? null,
            'employment_type_id' => $objective->employmentType->name ?? null,
            'experience_level_id' => $objective->experienceLevel->name ?? null,
            'work_address' => $objective->work_address,
            'education_level_id' => $objective->educationLevel->name ?? null,
            'salary_from' => $objective->salary_from,
            'salary_to' => $objective->salary_to,
            'file' =>  asset('cvs/' . $objective->file),
            'status' => ($objective->status == 3) ? 'hoạt động' : (($objective->status == 4) ? 'không hoạt động' : 'không xác định'),
            'country' => $objective->country ? $objective->country->name : null, // Tên quốc gia
            'city' => $objective->city ? $objective->city->name : null, // Tên thành phố
            'district' => $objective->district ? $objective->district->name : null, // Tên quận/huyện
            'profiles_id' => $objective->profiles_id,
            'created_at' => $objective->created_at, // Thêm thời gian tạo nếu cần
            'updated_at' => $objective->updated_at, // Thêm thời gian cập nhật nếu cần
        ];


        return response()->json([
            'success' => true,
            'message' => "Thực hiện thành công",
            'data' => $responseData, // Trả về dữ liệu đã được tùy chỉnh
            'status_code' => 200,
        ]);
    }


    public function update(Request $request, $id)
    {
        // Xác thực người dùng
        $user = auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id; // Lấy profile_id từ người dùng

        // Tìm đối tượng theo ID
        $objective = Objective::findOrFail($id);

        // Kiểm tra và validate các trường nhập liệu
        $validator = Validator::make($request->all(), [
            'desired_position' => '|string|max:255',
            'desired_level_id' => '|integer',
            'profession_id' => '|integer',
            'employment_type_id' => '|integer',
            'experience_level_id' => '|integer',
            'work_address' => '|string|max:255',
            'education_level_id' => '|integer',
            'salary_from' => '|integer|min:0',
            'salary_to' => '|integer|min:0|gte:salary_from',
            'status' => '',
            'country_id' => '|integer',
            'city_id' => '|integer',
            'district_id' => '|integer',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Kiểm tra file nếu có
        ], [
            'salary_to.gte' => 'Mức lương kết thúc phải lớn hơn hoặc bằng mức lương bắt đầu.',
            'file.mimes' => 'Tệp tin phải có định dạng pdf, doc, hoặc docx.',
            'file.max' => 'Tệp tin không được vượt quá 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $validatedData = $validator->validated();

        // Kiểm tra tệp tin và xử lý upload file nếu có
        if ($request->hasFile('file')) {
            // Xóa file cũ nếu cần thiết (nếu có tệp tin trước đó)
            if ($objective->file) {
                $oldFilePath = public_path('cvs/' . $objective->file);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Xóa file cũ
                }
            }

            $file = $request->file('file');
            $file_name = Common::uploadFile($file, public_path('cvs')); // Sử dụng lớp Common để upload file
            $validatedData['file'] = $file_name;
            // Lưu tên file vào dữ liệu
        }

        // Cập nhật profile_id vào dữ liệu đã được validate
        $validatedData['profiles_id'] = $profile_id;

        // Cập nhật thông tin đối tượng
        $objective->update($validatedData);

        $responseData = [
            'id' => $objective->id, // ID của bản ghi vừa cập nhật
            'desired_position' => $objective->desired_position,
            'desired_level_id' => $objective->desiredLevel->name ?? null,
            'profession_id' => $objective->profession->name ?? null,
            'employment_type_id' => $objective->employmentType->name ?? null,
            'experience_level_id' => $objective->experienceLevel->name ?? null,
            'work_address' => $objective->work_address,
            'education_level_id' => $objective->educationLevel->name ?? null,
            'salary_from' => $objective->salary_from,
            'salary_to' => $objective->salary_to,
            'file' => asset('cvs/' . $objective->file),
            'status' => ($objective->status == 3) ? 'hoạt động' : (($objective->status == 4) ? 'không hoạt động' : 'không xác định'),
            'country' => $objective->country ? $objective->country->name : null, // Tên quốc gia
            'city' => $objective->city ? $objective->city->name : null, // Tên thành phố
            'district' => $objective->district ? $objective->district->name : null, // Tên quận/huyện
            'profiles_id' => $objective->profiles_id,
            'created_at' => $objective->created_at,
            'updated_at' => $objective->updated_at,
        ];

        return response()->json([
            'success' => true,
            'message' => "Cập nhật thành công",
            'data' => $responseData, // Trả về dữ liệu đã được tùy chỉnh
            'status_code' => 200,
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
        // Lấy tất cả các điều kiện tìm kiếm từ request
        $desired_position = $request->input('desired_position');
        $desired_level = $request->input('desired_level');
        $profession = $request->input('profession');
        $employment_type = $request->input('employment_type');
        $experience_level = $request->input('experience_level');
        $work_address = $request->input('work_address');
        $education_level = $request->input('education_level');
        $salary_from = $request->input('salary_from');
        $salary_to = $request->input('salary_to');
        $country = $request->input('country');
        $city = $request->input('city');
        $district = $request->input('district');
        // Khởi tạo truy vấn
        $query = Objective::query()->where('status', 3); // Lọc theo status = 3

        // Thêm các điều kiện tìm kiếm vào truy vấn nếu có
        if ($desired_position) {
            $query->where('desired_position', 'like', "%$desired_position%");
        }

        if ($desired_level) {
            $query->whereHas('desiredLevel', function ($q) use ($desired_level) {
                $q->where('name', 'like', "%$desired_level%");
            });
        }

        if ($profession) {
            $query->whereHas('profession', function ($q) use ($profession) {
                $q->where('name', 'like', "%$profession%");
            });
        }

        if ($employment_type) {
            $query->whereHas('employmentType', function ($q) use ($employment_type) {
                $q->where('name', 'like', "%$employment_type%");
            });
        }

        if ($experience_level) {
            $query->whereHas('experienceLevel', function ($q) use ($experience_level) {
                $q->where('name', 'like', "%$experience_level%");
            });
        }

        if ($work_address) {
            $query->where('work_address', 'like', "%$work_address%");
        }

        if ($education_level) {
            $query->whereHas('educationLevel', function ($q) use ($education_level) {
                $q->where('name', 'like', "%$education_level%");
            });
        }

        if ($salary_from) {
            $query->where('salary_from', '>=', $salary_from);
        }

        if ($salary_to) {
            $query->where('salary_to', '<=', $salary_to);
        }

        if ($country) {
            $query->whereHas('country', function ($q) use ($country) {
                $q->where('name', 'like', "%$country%");
            });
        }

        if ($city) {
            $query->whereHas('city', function ($q) use ($city) {
                $q->where('name', 'like', "%$city%");
            });
        }

        if ($district) {
            $query->whereHas('district', function ($q) use ($district) {
                $q->where('name', 'like', "%$district%");
            });
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
            return [
                'id' => $objective->id,
                'desired_position' => $objective->desired_position,
                'desired_level_id' => $objective->desiredLevel->name ?? null,
                'profession_id' => $objective->profession->name ?? null,
                'employment_type_id' => $objective->employmentType->name ?? null,
                'experience_level_id' => $objective->experienceLevel->name ?? null,
                'work_address' => $objective->work_address,
                'education_level_id' => $objective->educationLevel->name ?? null,
                'salary_from' => $objective->salary_from,
                'salary_to' => $objective->salary_to,
                'file' => asset('cvs/' . $objective->file),
                'status' => 'hoạt động', // Vì đã lọc theo status = 3
                'country' => $objective->country ? $objective->country->name : null,
                'city' => $objective->city ? $objective->city->name : null,
                'district' => $objective->district ? $objective->district->name : null,
                'profiles_id' => $objective->profiles_id,
                'created_at' => $objective->created_at,
                'updated_at' => $objective->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Dữ liệu được lấy thành công!',
            'data' => $objectiveData,
            'status_code' => 200,
        ]);
    }

    public function uploadFile(Request $request, $id)
    {
        // Xác thực người dùng
        $user = auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id; // Lấy profile_id từ người dùng

        // Tìm đối tượng theo ID
        $objective = Objective::findOrFail($id);

        // Kiểm tra và validate các trường nhập liệu
        $validator = Validator::make($request->all(), [
            'desired_position' => '|string|max:255',
            'desired_level_id' => '|integer',
            'profession_id' => '|integer',
            'employment_type_id' => '|integer',
            'experience_years' => '|integer|min:0',
            'work_address' => '|string|max:255',
            'education_level_id' => '|integer',
            'salary_from' => '|integer|min:0',
            'salary_to' => '|integer|min:0|gte:salary_from',
            'status' => '',
            'country_id' => '|integer',
            'city_id' => '|integer',
            'district_id' => '|integer',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Kiểm tra file nếu có
        ], [
            'salary_to.gte' => 'Mức lương kết thúc phải lớn hơn hoặc bằng mức lương bắt đầu.',
            'file.mimes' => 'Tệp tin phải có định dạng pdf, doc, hoặc docx.',
            'file.max' => 'Tệp tin không được vượt quá 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $validatedData = $validator->validated();

        // Kiểm tra tệp tin và xử lý upload file nếu có
        if ($request->hasFile('file')) {
            // Xóa file cũ nếu cần thiết (nếu có tệp tin trước đó)
            if ($objective->file) {
                $oldFilePath = public_path('cvs/' . $objective->file);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); // Xóa file cũ
                }
            }

            $file = $request->file('file');
            $file_name = Common::uploadFile($file, public_path('cvs')); // Sử dụng lớp Common để upload file
            $validatedData['file'] = $file_name;
            // Lưu tên file vào dữ liệu
        }

        // Cập nhật profile_id vào dữ liệu đã được validate
        $validatedData['profiles_id'] = $profile_id;

        // Cập nhật thông tin đối tượng
        $objective->update($validatedData);

        $responseData = [
            'id' => $objective->id, // ID của bản ghi vừa cập nhật
            'desired_position' => $objective->desired_position,
            'desired_level_id' => $objective->desiredLevel->name ?? null,
            'profession_id' => $objective->profession->name ?? null,
            'employment_type_id' => $objective->employmentType->name ?? null,
            'experience_level_id' => $objective->experienceLevel->name ?? null,
            'work_address' => $objective->work_address,
            'education_level_id' => $objective->educationLevel->name ?? null,
            'salary_from' => $objective->salary_from,
            'salary_to' => $objective->salary_to,
            'file' => asset('cvs/' . $objective->file),
            'status' => ($objective->status == 3) ? 'hoạt động' : (($objective->status == 4) ? 'không hoạt động' : 'không xác định'),
            'country' => $objective->country ? $objective->country->name : null, // Tên quốc gia
            'city' => $objective->city ? $objective->city->name : null, // Tên thành phố
            'district' => $objective->district ? $objective->district->name : null, // Tên quận/huyện
            'profiles_id' => $objective->profiles_id,
            'created_at' => $objective->created_at,
            'updated_at' => $objective->updated_at,
        ];

        return response()->json([
            'success' => true,
            'message' => "Cập nhật thành công",
            'data' => $responseData, // Trả về dữ liệu đã được tùy chỉnh
            'status_code' => 200,
        ]);
    }


    public function show (Objective $objective): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($objective->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to the language skill',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'id' => $objective->id, // ID của bản ghi vừa cập nhật
                'desired_position' => $objective->desired_position,
                'desired_level_id' => $objective->desiredLevel->name ?? null,
                'profession_id' => $objective->profession->name ?? null,
                'employment_type_id' => $objective->employmentType->name ?? null,
                'experience_level_id' => $objective->experienceLevel->name ?? null,
                'work_address' => $objective->work_address,
                'education_level_id' => $objective->educationLevel->name ?? null,
                'salary_from' => $objective->salary_from,
                'salary_to' => $objective->salary_to,
                'file' => asset('cvs/' . $objective->file),
                'status' => ($objective->status == 3) ? 'hoạt động' : (($objective->status == 4) ? 'không hoạt động' : 'không xác định'),
                'country' => $objective->country ? $objective->country->name : null, // Tên quốc gia
                'city' => $objective->city ? $objective->city->name : null, // Tên thành phố
                'district' => $objective->district ? $objective->district->name : null, // Tên quận/huyện
                'profiles_id' => $objective->profiles_id,
                'created_at' => $objective->created_at,
                'updated_at' => $objective->updated_at,
            ],
            'status_code' => 200
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        // Xác thực người dùng
        $user = auth()->user();
        $profile = $user->profile;

        // Tìm đối tượng Objective theo ID
        $objective = Objective::findOrFail($id);

        // Xác thực rằng mục tiêu thuộc về hồ sơ của người dùng
        if ($objective->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền cập nhật trạng thái của mục tiêu này.',
                'status_code' => 403,
            ], 403);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:,3,4', // Các giá trị status có thể là  3, 4
        ], [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // Cập nhật trạng thái của mục tiêu
        $objective->status = $request->input('status');
        $objective->save();

        // Trả về phản hồi JSON với dữ liệu đã cập nhật
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật thành công!',
            'data' => [
                'id' => $objective->id,
                'status' => ($objective->status == 3) ? 'hoạt động' : (($objective->status == 4) ? 'không hoạt động' : 'không xác định'),
            ],
            'status_code' => 200,
        ]);
    }

    public function searchByKeyword(Request $request)
    {
        $keyword = $request->input('keyword');

        // Khởi tạo truy vấn và lọc theo status = 3
        $query = Objective::query()->where('status', 3);

        // Nếu có keyword, thêm điều kiện tìm kiếm từ khóa vào các trường liên quan
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('desired_position', 'like', "%$keyword%")
                    ->orWhereHas('desiredLevel', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('profession', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('employmentType', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('experienceLevel', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->orWhere('work_address', 'like', "%$keyword%")
                    ->orWhereHas('educationLevel', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('country', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('city', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('district', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })
                    // Tìm kiếm từ khóa trong tên ứng viên từ bảng Profile
                    ->orWhereHas('profile', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
            });
        }

        // Thêm truy vấn để lấy thông tin từ Profile
        $query->with('profile');  // profile là tên quan hệ giữa Objective và Profile

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
            return [
                'id' => $objective->id,
                'age' => $objective->profile->birthday ? Carbon::parse($objective->profile->birthday)->age : null,
                'name' => $objective->profile->name  ?? null,
                'experience_level' => $objective->experienceLevel->name ?? null,
                'salary_from' => $objective->salary_from,
                'salary_to' => $objective->salary_to,
                'file' => asset('cvs/' . $objective->file),
                'country' => $objective->country ? $objective->country->name : null,
                'city' => $objective->city ? $objective->city->name : null,
                'district' => $objective->district ? $objective->district->name : null,
                'profile' => [
                    'name' => $objective->profile->name  ?? null,
                    'phone' => $objective->profile->phone ?? null,
                    'email' => $objective->profile->email ?? null,
                    'image_url' => url('uploads/images/' . $objective->profile->image),
                    'gender' => $objective->profile->gender === 0 ? 'Nam' : 'Nữ',
                    'address' => $objective->profile->address ?? null,
                    'country_id' => $objective->profile->country->name ?? null,
                    'city_id' => $objective->profile->city->name ?? null,
                    'district_id' => $objective->profile->district->name ?? null,
                    'objective' => [
                        'desired_position' => $objective->desired_position,
                        'desired_level' => $objective->desiredLevel->name ?? null,
                        'profession' => $objective->profession->name ?? null,
                        'employment_type' => $objective->employmentType->name ?? null,
                        'experience_level' => $objective->experienceLevel->name ?? null,
                        'work_address' => $objective->work_address,
                        'education_level' => $objective->educationLevel->name ?? null,
                        'salary_from' => $objective->salary_from,
                        'salary_to' => $objective->salary_to,
                        'file' => asset('cvs/' . $objective->file),
                        'status' => 'hoạt động', // Vì đã lọc theo status = 3
                        'country' => $objective->country ? $objective->country->name : null,
                        'city' => $objective->city ? $objective->city->name : null,
                        'district' => $objective->district ? $objective->district->name : null,
                    ],
                    'aboutme' => $objective->profile->abouts->map(function ($aboutme) {
                        return [
                            'id' => $aboutme->id,
                            'description' => $aboutme->description,

                        ];
                    }),
                    'experiences' => $objective->profile->experiences->map(function ($experience) {
                        return [
                            'id' => $experience->id,
                            'position' => $experience->position,
                            'company' => $experience->company,
                            'start_date' => $experience->start_date,
                            'end_date' => $experience->end_date,
                            'responsibilities' => $experience->responsibilities,
                        ];
                    }),
                    'educations' => $objective->profile->educations->map(function ($education) {
                        return [
                            'id' => $education->id,
                            'degree' => $education->degree,
                            'institution' => $education->institution,
                            'start_date' => $education->start_date,
                            'end_date' => $education->end_date,
                            'additionalDetail' => $education->additionalDetail,
                        ];
                    }),
                    'certificates' => $objective->profile->certificates->map(function ($certificate) {
                        return [
                            'id' => $certificate->id,
                            'title' => $certificate->title,
                            'provider' => $certificate->provider,
                            'issueDate' => $certificate->issueDate,
                            'description' => $certificate->description,
                            'certificateUrl' => $certificate->certificateUrl,
                        ];
                    }),
                    'awards' => $objective->profile->awards->map(function ($award) {
                        return [
                            'id' => $award->id,
                            'title' => $award->title,
                            'provider' => $award->provider,
                            'issueDate' => $award->issueDate,
                            'description' => $award->description,
                        ];
                    }),
                    'projects' => $objective->profile->projects->map(function ($project) {
                        return [
                            'id' => $project->id,
                            'title' => $project->title,
                            'start_date' => $project->start_date,
                            'end_date' => $project->end_date,
                            'description' => $project->description,
                        ];
                    }),
                    'skills' => $objective->profile->skills->map(function ($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                            'level' => $skill->level,
                        ];
                    }),

                    'languages_skills' => $objective->profile->languageskills->map(function ($languageskill) {
                        return [
                            'id' => $languageskill->language->id ?? null,
                            'name' => $languageskill->language->name ?? null,
                        ];
                    }),




            ],
                'created_at' => $objective->created_at,
                'updated_at' => $objective->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $objectiveData,
        ]);
    }
}
