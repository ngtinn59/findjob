<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Award;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\educations;
use App\Models\profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AwardsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =  auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id;
        $awards = Award::where("profiles_id", $profile_id)->get();

        $awardsData = $awards->map(function ($awards) {
            return [
                'id' => $awards->id,
                'title' => $awards->title,
                'provider' => $awards->provider,
                'issueDate' => $awards->issueDate,
                'description' => $awards->description,
            ];
        });

        // Trả về danh sách giáo dục dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $awardsData,
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
            'description' => $request->input('description')
        ];

        $validator = Validator::make($data, [
            'title' => 'required',
            'profiles_id' => 'required',
            'provider' => 'required',
            'issueDate' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'profiles_id.required' => 'ID hồ sơ là bắt buộc.',
            'provider.required' => 'Nhà cung cấp là bắt buộc.',
            'issueDate.required' => 'Ngày phát hành là bắt buộc.',
            'description.required' => 'Mô tả là bắt buộc.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $Award = Award::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $Award,
            'status_code' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Award $award)
    {
        // Check if the award belongs to the authenticated user
        $user = User::where("id", auth()->user()->id)->first();
        $profile = $user->profile->first();
        if ($award->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to the award',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'title' => $award->title,
                'provider' => $award->provider,
                'issueDate' => $award->issueDate,
                'description' => $award->description,
            ],
            'status_code' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Award $award)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Kiểm tra quyền truy cập
        if ($award->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to update the award',
            ], 403);
        }

        // Dữ liệu đầu vào
        $data = [
            'title' => $request->input('title'),
            'profiles_id' => $profile->id,
            'provider' => $request->input('provider'),
            'issueDate' => $request->input('issueDate'),
            'description' => $request->input('description')
        ];

        // Xác thực dữ liệu
        $validator = Validator::make($data, [
            'title' => 'required',
            'profiles_id' => 'required',
            'provider' => 'required',
            'issueDate' => 'required',
            'description' => 'required',
        ], [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'profiles_id.required' => 'ID hồ sơ là bắt buộc.',
            'provider.required' => 'Nhà cung cấp là bắt buộc.',
            'issueDate.required' => 'Ngày phát hành là bắt buộc.',
            'description.required' => 'Mô tả là bắt buộc.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Cập nhật dữ liệu
        $award->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Award updated successfully',
            'data' => $award,
            'status_code' => 200
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Award $award)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if ($award->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to delete the award',
            ], 403);
        }

        $award->delete();

        return response()->json([
            'success' => true,
            'message' => 'Award deleted successfully',
            'status_code' => 200
        ]);
    }

}
