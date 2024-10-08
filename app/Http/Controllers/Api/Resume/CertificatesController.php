<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\aboutme;
use App\Models\Award;
use App\Models\Certificate;
use App\Http\Controllers\Controller;
use App\Models\profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CertificatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =  auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id;
        $certificate = Certificate::where("profiles_id", $profile_id)->get();
        $certificateData = $certificate->map(function ($certificate) {
            return [
                'id' => $certificate-> id,
                'title' => $certificate->title,
                'provider' => $certificate->provider,
                'issueDate' => $certificate->issueDate,
                'description' => $certificate->description,
                'certificateUrl' => $certificate->certificateUrl
            ];
        });

        // Trả về danh sách giáo dục dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $certificateData,
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
            'profiles_id' => $profile_id,
            'provider' => $request->input('provider'),
            'issueDate' => $request->input('issueDate'),
            'description' => $request->input('description'),
            'certificateUrl' => $request->input('certificateUrl')
        ];

        $validator = Validator::make($data, [
            'title' => 'required',
            'profiles_id' => 'required',
            'provider' => 'required',
            'issueDate' => 'required|date', // Bạn có thể thêm yêu cầu định dạng ngày
            'description' => 'required',
            'certificateUrl' => 'required|url' // Bạn có thể thêm yêu cầu định dạng URL
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'profiles_id.required' => 'ID hồ sơ không được để trống.',
            'provider.required' => 'Nhà cung cấp không được để trống.',
            'issueDate.required' => 'Ngày cấp không được để trống.',
            'issueDate.date' => 'Ngày cấp phải đúng định dạng ngày.',
            'description.required' => 'Mô tả không được để trống.',
            'certificateUrl.required' => 'URL chứng chỉ không được để trống.',
            'certificateUrl.url' => 'URL chứng chỉ phải đúng định dạng URL.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $certificate = Certificate::create($data);
        $data = [
            'title' => $certificate->title,
            'provider' => $certificate->provider,
            'issueDate' => $certificate->issueDate,
            'description' => $certificate->description,
            'certificateUrl' => $certificate->certificateUrl,
            'id' => $certificate->id
        ];

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $data
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate)
    {
        $user = auth()->user(); // Lấy người dùng đã xác thực
        $profile = $user->profile; // Lấy profile của người dùng

        // Kiểm tra xem chứng chỉ có thuộc về profile của người dùng không
        if ($certificate->profiles_id === $profile->id) {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => [
                    'title' => $certificate->title,
                    'provider' => $certificate->provider,
                    'issueDate' => $certificate->issueDate,
                    'description' => $certificate->description,
                    'certificateUrl' => $certificate->certificateUrl,
                ],
            ]);
        }

        // Nếu không có quyền truy cập, trả về lỗi 403
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized to view this certificate',
        ], 403);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $user = auth()->user();
        $profile = $user->profile; // Lấy profile của người dùng đã xác thực
        $profile_id = $profile->id; // Lấy ID của profile

        // Kiểm tra xem chứng nhận có thuộc về người dùng hiện tại hay không
        if ($certificate->profiles_id !== $profile_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this certificate',
            ], 403);
        }

        $data = [
            'title' => $request->input('title'),
            'provider' => $request->input('provider'),
            'issueDate' => $request->input('issueDate'),
            'description' => $request->input('description'),
            'certificateUrl' => $request->input('certificateUrl')
        ];

        $validator = Validator::make($data, [
            'title' => 'required',
            'provider' => 'required',
            'issueDate' => 'required|date', // Yêu cầu định dạng ngày
            'description' => 'required',
            'certificateUrl' => 'required|url' // Yêu cầu định dạng URL
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'provider.required' => 'Nhà cung cấp không được để trống.',
            'issueDate.required' => 'Ngày cấp không được để trống.',
            'issueDate.date' => 'Ngày cấp phải đúng định dạng ngày.',
            'description.required' => 'Mô tả không được để trống.',
            'certificateUrl.required' => 'URL chứng chỉ không được để trống.',
            'certificateUrl.url' => 'URL chứng chỉ phải đúng định dạng URL.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $certificate->update($data);

        return response()->json([
            'success' => true,
            'message' => "Cập nhật chứng chỉ thành công.",
            'data' => [
                'title' => $certificate->title,
                'provider' => $certificate->provider,
                'issueDate' => $certificate->issueDate,
                'description' => $certificate->description,
                'certificateUrl' => $certificate->certificateUrl,
                'id' => $certificate->id
            ]
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Kiểm tra xem chứng nhận thuộc về người dùng hiện tại hay không
        if ($certificate->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this certificate',
            ], 403);
        }

        // Xoá chứng nhận
        $certificate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certificate deleted successfully',
        ]);
    }

}
