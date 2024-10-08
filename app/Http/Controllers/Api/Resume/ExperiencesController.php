<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Certificate;
use App\Models\Experience;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperiencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =  auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id;
        $experience = Experience::where("profiles_id", $profile_id)->get();
        $experienceData = $experience->map(function ($experience) {
            return [
                'id' => $experience->id,
                'position' => $experience->position,
                'company' => $experience->company,
                'start_date' => $experience->start_date,
                'end_date' => $experience->end_date,
                'responsibilities' => $experience->responsibilities,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $experienceData,
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
            'position' => $request->input('position'),
            'company' => $request->input('company'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'responsibilities' => $request->input('responsibilities'),
            'profiles_id' => $profile_id
        ];

        $validator = Validator::make($data, [
            'position' => 'required',
            'company' => 'required',
            'start_date' => 'required|date', // Kiểm tra định dạng ngày
            'end_date' => 'required|date|after:start_date', // Kiểm tra ngày kết thúc sau ngày bắt đầu
            'responsibilities' => 'required',
            'profiles_id' => 'required',
        ], [
            'position.required' => 'Vị trí là bắt buộc.',
            'company.required' => 'Công ty là bắt buộc.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'responsibilities.required' => 'Trách nhiệm là bắt buộc.',
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
        $experience = Experience::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $experience,
            'status_code' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Experience $experience)
    {
//        $user = User::where("id", auth()->user()->id);
//        $profile = $user->profile;
//        if ($experience->profiles_id !== $profile->id) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Unauthorized access to the award',
//            ], 403);
//        }
//
//        return response()->json([
//            'success' => true,
//            'message' => 'success',
//            'data' => [
//                'position' => $experience->position,
//                'company' => $experience->company,
//                'start_date' => $experience->start_date,
//                'end_date' => $experience->end_date,
//                'responsibilities' => $experience->responsibilities,
//            ],
//            'status_code' => 200
//        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Experience $experience)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Kiểm tra xem người dùng đã tạo hồ sơ chưa
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa tạo hồ sơ.',
            ], 400);
        }

        // Kiểm tra quyền truy cập
        if ($experience->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực không thể tạo kinh nghiệm làm việc',
            ], 403);
        }

        $data = [
            'position' => $request->input('position'),
            'company' => $request->input('company'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'responsibilities' => $request->input('responsibilities'),
            'profiles_id' => $profile->id
        ];

        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($data, [
            'position' => 'required',
            'company' => 'required',
            'start_date' => 'required|date', // Kiểm tra định dạng ngày
            'end_date' => 'required|date|after:start_date', // Kiểm tra ngày kết thúc sau ngày bắt đầu
            'responsibilities' => 'required',
            'profiles_id' => 'required',
        ], [
            'position.required' => 'Vị trí là bắt buộc.',
            'company.required' => 'Công ty là bắt buộc.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'responsibilities.required' => 'Trách nhiệm là bắt buộc.',
            'profiles_id.required' => 'ID hồ sơ là bắt buộc.',
        ]);

        // Nếu dữ liệu không hợp lệ, trả về thông báo lỗi
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Lấy dữ liệu đã validate
        $validatedData = $validator->validated();

        // Cập nhật thông tin kinh nghiệm
        $experience->update($validatedData);

        // Trả về thông báo thành công và dữ liệu đã cập nhật
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật kinh nghiệm thành công',
            'data' => $experience,
            'status_code' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experience $experience)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Kiểm tra xem experience có thuộc về profile của người dùng không
        if ($experience->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực không thể xóa kinh nghiệm',
            ], 403);
        }

        // Kiểm tra nếu không tìm thấy kinh nghiệm
        if (!$experience) {
            return response()->json([
                'success' => false,
                'message' => 'Kinh nghiệm không tìm thấy'
            ], 404);
        }

        // Xóa kinh nghiệm
        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kinh nghiệm được xóa thành công',
            'status_code' => 200
        ]);
    }

}
