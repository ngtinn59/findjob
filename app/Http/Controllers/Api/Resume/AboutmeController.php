<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\aboutme;
use App\Http\Controllers\Controller;
use App\Models\educations;
use App\Models\profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutmeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user =  auth()->user();
        $profile = $user->profile;
        $profile_id = $profile->id;
        $aboutme = aboutme::where("profiles_id", $profile_id)->get();
        $aboutmeData = $aboutme->map(function ($aboutme) {
            return [
                'description' => $aboutme->description,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $aboutmeData,
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
        $profile_id = $profile->id;

        $data = [
            'description' => $request->input('description'),
            'profiles_id' => $profile_id
        ];

        $validator = Validator::make($data, [
            'description' => 'required',
            'profiles_id' => 'required|exists:profiles,id',
        ],[
            'description.required' => 'Vui lòng nhập thông tin.',
            'profiles_id.required' => 'profiles_id is required.',
            'profiles_id.exists' => 'profiles_id is invalid.',
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
        $aboutme = aboutme::updateOrCreate(
            ['profiles_id' => $profile_id], // Match criteria
            ['description' => $request->input('description')] // Data to update or create
        );

        $status_code = $aboutme->wasRecentlyCreated ? 201 : 200; // 201 if created, 200 if updated

        return response()->json([
            'success' => true,
            'message' => "Phương thức thực hiện thành công",
            "data" => $aboutme,
            'status_code' => $status_code
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(aboutme $aboutme)
    {
        $user = User::where("id", auth()->user()->id)->first();
        $profile = $user->profile->first();
        if ($aboutme->profiles_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to the award',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'description' => $aboutme->description,
            ],
            'status_code' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, aboutme $aboutme)
    {

        $data = [
            'description' => $request->input('description'),
        ];
        $validator = Validator::make($data, [
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();

        $aboutme->update($data);

        return response()->json([
            'success' => true,
            'message' => 'About me updated successfully',
            'data' => $aboutme,
            'status_code' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(aboutme $aboutme)
    {
        $aboutme->delete();

        return response()->json([
            'success' => true,
            'message' => 'About me deleted successfully',
            'status_code' => 200
        ]);

    }
}
