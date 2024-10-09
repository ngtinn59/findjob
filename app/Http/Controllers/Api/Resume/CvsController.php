<?php

namespace App\Http\Controllers\Api\Resume;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Utillities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvsController extends Controller
{
    public function store(Request $request)
    {
//        $request->validate([
//            'cv' => 'required|file|mimes:pdf', // Chỉ chấp nhận file PDF
//        ]);

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $file_name = Common::uploadFile($file, public_path('cvs')); // Sử dụng lớp Common để upload file

            $cv = new CV();
            $cv->users_id  = auth()->id();
            $cv->file_path = $file_name; // Lưu tên file, không phải đường dẫn đầy đủ
            $cv->save();

            $data = [
                'id' => $cv->id,
                'file_path' => asset('cvs/' . $file_name), // Tạo đường dẫn truy cập từ thư mục public
                'is_default' => $cv->is_default
            ];
            return response()->json([
                'success' => true,
                'message' => 'CV uploaded.',
                'data' => $data,
                'status_code' => 200
            ], 200);
        }

        return response()->json(['error' => 'Không tìm thấy tệp CV.'], 400);
    }



    public function setDefault(CV $cv)
    {
        if ($cv->users_id !== auth()->id()) {
            abort(403);
        }

        // Đặt tất cả CV khác của người dùng này thành không phải mặc định
        CV::where('users_id', auth()->id())->update(['is_default' => false]);

        // Đặt CV này là mặc định
        $cv->is_default = true;
        $cv->save();

        $data = [
            'id' => $cv->id,
            'file_path' => asset($cv->file_path),
            'is_default' => $cv->is_default
        ];

        return response()->json([
            'success' => true,
            'message' => 'Success.',
            'data' => $data,
            'status_code' => 200
        ], 200);
    }


    public function getDefaultCv(Request $request)
    {
        $user = auth()->user();
        $defaultCv = $user->cvs()->where('is_default', true)->first();

        $data = [
            'id' => $defaultCv->id,
            'cv' => asset($defaultCv->file_path),
            'is_default' => $defaultCv->is_default
        ];
        if ($defaultCv) {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data,
                'status_code' => 200
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'faild.',
                'status_code' => '404.'

            ], 404);
        }

    }

    // Lấy danh sách tất cả CV của người dùng hiện tại
    public function index()
    {
        $cvs = auth()->user()->cvs;
        $data = [];

        foreach ($cvs as $cv) {
            $data[] = [
                'id' => $cv->id,
                'file_path' => asset('cvs/' . $cv->file_path), // Sử dụng asset để tạo đường dẫn truy cập
                'is_default' => $cv->is_default,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data,
            'status_code' => 200,
        ]);
    }


// Lấy thông tin chi tiết của một CV
    public function show(CV $cv)
    {
        if ($cv->users_id !== auth()->id()) {
            abort(403);
        }

        $data = [
            'id' => $cv->id,
            'file_path' => asset('cvs/' . $cv->file_path), // Sử dụng asset để tạo đường dẫn truy cập
            'is_default' => $cv->is_default,
        ];
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data,
            'status_code' => 200,
        ]);
    }

    // Cập nhật thông tin của CV
    public function update(Request $request, CV $cv)
    {
        if ($cv->users_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            // Các quy tắc xác thực cho các trường cần cập nhật
        ]);

        $cv->update($request->all());

        $data = [
            'id' => $cv->id,
            'file_path' => asset('cvs/' . $cv->file_path), // Sử dụng asset để tạo đường dẫn truy cập
            'is_default' => $cv->is_default,
        ];
        return response()->json([
            'success' => true,
            'message' => 'CV đã được cập nhật.',
            'data' => $data,
            'status_code' => 200
        ], 200);
    }

    // Xóa CV
    public function destroy(CV $cv)
    {
        if ($cv->users_id !== auth()->id()) {
            abort(403);
        }

        $cv->delete();

        return response()->json(['message' => 'CV đã được xóa.'], 200);
    }

}
