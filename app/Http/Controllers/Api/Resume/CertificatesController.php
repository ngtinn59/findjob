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
            'profiles_id' =>$profile_id,
            'provider' => $request->input('provider'),
            'issueDate' => $request->input('issueDate'),
            'description' => $request->input('description'),
            'certificateUrl' => $request->input('certificateUrl')

        ];


        $validator = Validator::make($data, [
            'title' => 'required',
            'profiles_id' => 'required',
            'provider' => 'required',
            'issueDate' => 'required',
            'description' => 'required',
            'certificateUrl' => 'required',
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
//        $user = User::where("id", auth()->user()->id)->first();
//        $profile = $user->profile->first();
//        if ($certificate->profiles_id == $profile->id) {
//            return response()->json([
//                'success' => true,
//                'message' => 'success',
//                'data' => [
//                    'title' => $certificate->title,
//                    'provider' => $certificate->provider,
//                    'issueDate' => $certificate->issueDate,
//                    'description' => $certificate->description,
//                    'certificateUrl' => $certificate->certificateUrl,
//
//                ],
//            ]);
//        }else{
//
//        }


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
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
        $data = $request->all();


        $certificate->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Award me updated successfully',
            'data' => $certificate,
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
