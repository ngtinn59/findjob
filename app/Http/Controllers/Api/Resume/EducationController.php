<?php

namespace App\Http\Controllers\Api\Resume;

use App\Http\Controllers\Controller;
use App\Models\educations;
use App\Models\profile;
use App\Models\User;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =  auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id;
        $educations = educations::where("profiles_id", $profile_id)->get();

        $educationsData = $educations->map(function ($education) {
            return [
                'id' => $education->id,
                'degree' => $education->degree,
                'institution' => $education->institution,
                'start_date' => $education->start_date,
                'end_date' => $education->end_date,
                'additionalDetail' => $education->additionalDetail,
            ];
        });

        // Trả về danh sách giáo dục dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Truy vấn quá trình học tập thành công',
            'data' => $educationsData,
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
        $profiles_id = $profile->id;
        $data = [
            'degree' => $request->input('degree'),
            'institution' => $request->input('institution'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'additionalDetail' => $request->input('additionalDetail'),
            'profiles_id' => $profiles_id
        ];

        $validator = Validator::make($data, [
            'degree' => 'required',
            'institution' => 'required',
            'start_date' => 'required|date', // Thêm kiểm tra định dạng ngày
            'end_date' => 'required|date|after:start_date', // Kiểm tra ngày kết thúc sau ngày bắt đầu
            'additionalDetail' => 'required',
            'profiles_id' => 'required',
        ], [
            'degree.required' => 'Trình độ là bắt buộc.',
            'institution.required' => 'Cơ sở giáo dục là bắt buộc.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'additionalDetail.required' => 'Chi tiết bổ sung là bắt buộc.',
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
        $education = educations::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $education,
            'status_code' => 200
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(educations $education)
    {
        $user =  auth()->user();

        $profile = $user->profile;
        $profiles_id = $profile->id;
        if ($education->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực không thể hiển thị quá trình học tập',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'id' => $education->id,
                'degree' => $education->degree,
                'institution' => $education->institution,
                'start_date' => $education->start_date,
                'end_date' => $education->end_date,
                'additionalDetail' => $education->additionalDetail,
            ],
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, educations $education)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Kiểm tra xem người dùng đã tạo hồ sơ chưa
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa tạo hồ sơ.',
                'status_code' => 400
            ], 400); // Hoặc 403 tùy thuộc vào logic của bạn
        }

        if ($education->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực khôgn thể tạo quá trình học tập',
            ], 403);
        }

        // Lấy ID của hồ sơ từ auth
        $profile_id = $profile->id;
        $data = [
            'degree' => $request->input('degree'),
            'institution' => $request->input('institution'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'additionalDetail' => $request->input('additionalDetail'),
            'profiles_id' => $profile_id // Sử dụng ID hồ sơ từ auth
        ];

        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($data, [
            'degree' => 'required',
            'institution' => 'required',
            'start_date' => 'required|date', // Thêm kiểm tra định dạng ngày
            'end_date' => 'required|date|after:start_date', // Kiểm tra ngày kết thúc sau ngày bắt đầu
            'additionalDetail' => 'required',
            'profiles_id' => 'required',
        ], [
            'degree.required' => 'Trình độ là bắt buộc.',
            'institution.required' => 'Cơ sở giáo dục là bắt buộc.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'additionalDetail.required' => 'Chi tiết bổ sung là bắt buộc.',
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

        // Cập nhật thông tin giáo dục
        $education->update($validatedData);

        // Trả về thông báo thành công và dữ liệu đã cập nhật
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin giáo dục thành công',
            'data' => $education,
            'status_code' => 200
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(educations $education)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($education->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực không thể xóa quá trình học tập',
            ], 403);
        }

        if (!$education) {
            return response()->json([
                'success' => false,
                'message' => 'Quá trình học tập không tìm thấy'
            ], 404);
        }

        $education->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quá trình học tập được xóa thành công',
            'status_code' => 200
        ]);
    }
}
