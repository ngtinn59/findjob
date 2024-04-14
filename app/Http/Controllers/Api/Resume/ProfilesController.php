<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Utillities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where("id", auth()->user()->id)->firstOrFail();
        $profile = Profile::where("users_id", $user->id)->get();
        $profilesData = $profile->map(function ($profile) {
            return [
                'title' => $profile->title,
                'name' => $profile->name,
                'phone' => $profile->phone,
                'email' => $profile->email,
                'birthday' => $profile->birthday,
                'image_url' => url('uploads/images/' . $profile->image), // Xây dựng URL của hình ảnh
                'gender' => $profile->gender == 1 ? 'Male' : 'Female',
                'location' => $profile->location,
                'website' => $profile->website,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $profilesData,
            'status_code' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Tìm profile của người dùng hiện tại
        $profile = Profile::where('users_id', auth()->user()->id)->first();

        // Nếu không có profile tồn tại, tạo mới
        if (!$profile) {
            // Thực hiện validation
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'title' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'birthday' => 'required',
                'location' => 'required',
                'website' => 'required',
//                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Nếu validation không thành công, trả về lỗi
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Upload file ảnh và lấy tên file
            $file = $request->file('image');
            $path = public_path('uploads/images');
            $file_name = Common::uploadFile($file, $path);

            // Tạo dữ liệu cho profile mới
            $data = [
                'name' => $request->input('name'),
                'title' => $request->input('title'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'birthday' => $request->input('birthday'),
                'gender' => $request->input('gender'),
                'location' => $request->input('location'),
                'website' => $request->input('website'),
                'image' => $file_name,
                'users_id' => auth()->user()->id,
            ];
        } else {
            // Nếu có profile tồn tại, cập nhật
            // Kiểm tra xem request có chứa file ảnh mới không
            if ($request->hasFile('image')) {
                $validator = Validator::make($request->all(), [
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                // Nếu validation không thành công, trả về lỗi
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors(),
                    ], 400);
                }

                // Upload file ảnh mới và lấy tên file
                $file = $request->file('image');
                $path = public_path('uploads/images');
                $file_name = Common::uploadFile($file, $path);

                // Cập nhật dữ liệu với file ảnh mới
                $profile->image = $file_name;
            }

            // Cập nhật các trường dữ liệu khác
            $profile->name = $request->input('name', $profile->name);
            $profile->title = $request->input('title', $profile->title);
            $profile->phone = $request->input('phone', $profile->phone);
            $profile->email = $request->input('email', $profile->email);
            $profile->birthday = $request->input('birthday', $profile->birthday);
            $profile->gender = $request->input('gender', $profile->gender);
            $profile->location = $request->input('location', $profile->location);
            $profile->website = $request->input('website', $profile->website);
            $profile->users_id = auth()->user()->id;
            $profile->save();

            // Gán dữ liệu đã cập nhật
            $data = $profile->toArray();
        }

        return response()->json([
            'success' => true,
            'message' => "Profile saved successfully",
            'data' => $data,
            'status_code' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $profile,
                'status_code' => 200
            ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profile $profile)
    {
        $data = $request->all();
        $profile->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile me updated successfully',
            'data' => $profile,
            'status_code' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {

        $profile->delete();
        return response()->json([
            'success' => true,
            'message' => 'Profile me deleted successfully',
            'status_code' => 200
        ]);
    }
}
