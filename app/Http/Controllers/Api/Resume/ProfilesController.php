<?php

namespace App\Http\Controllers\Api\Resume;

use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Utillities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use App\Models\Cv;


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
        $user =  auth()->user();

        $profile = $user->profile;
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
            $data = [
                'users_id' => $profile->users_id,
                'id' => $profile->id,
                'name' => $profile->name,
                'title' => $profile->title,
                'phone' => $profile->phone,
                'email' => $profile->email,
                'birthday' => $profile->birthday,
                'gender' => $profile->gender == 1 ? 'Male' : 'Female',
                'location' => $profile->location,
                'website' => $profile->website,
                'image_url' => url('uploads/images/' . $profile->image), // Xây dựng URL của hình ảnh
            ];
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


    public function download_cv() {
        $user = User::where("id", auth()->user()->id)->firstOrFail();

        $cv=  Cv::where('users_id', $user->id)->where('is_default', 1)->firstOrFail();

        $fileName = $cv->file_path;
        $filePath = public_path('cvs/' . $fileName);

        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);

            return Response::make($fileContent, 200, [
                'Content-Disposition' => 'attachment; filename="'. $fileName. '"',
                'Content-Type' => 'application/octet-stream',
            ]);
        } else {
            abort(404, 'File not found');
        }
    }

}


