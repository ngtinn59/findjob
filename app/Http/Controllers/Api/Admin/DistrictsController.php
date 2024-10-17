<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistrictsController extends Controller
{
    public function index()
    {
        $district = District::all();
        return response()->json([
            'message' => "Lấy danh sách các quận huyện thành công",
            "data" => $district,
            'status_code' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user()->id;

        $data = [
            'users_id' => $user,
            'city_id' => $request->input('city_id'),
            'name' => $request->input('name'),
        ];

        $validator = Validator::make($data, [
            'users_id' => 'required|exists:users,id', // Kiểm tra người dùng có tồn tại không
            'name' => 'required|string|min:3|max:50', // Tên phải là chuỗi, từ 3 đến 50 ký tự
            'city_id' => 'required|exists:cities,id', // City_id phải tồn tại trong bảng cities
        ], [
            'users_id.required' => 'Trường người dùng là bắt buộc.',
            'users_id.exists' => 'Người dùng không hợp lệ.', // Thông báo lỗi nếu người dùng không tồn tại
            'name.required' => 'Trường tên là bắt buộc.',
            'name.min' => 'Tên phải có ít nhất :min ký tự.', // Custom error message
            'name.max' => 'Tên không được vượt quá :max ký tự.',
            'city_id.required' => 'Trường thành phố là bắt buộc.',
            'city_id.exists' => 'Thành phố không hợp lệ.', // Thông báo lỗi nếu city_id không tồn tại
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }


        $data = $validator->validated();
        $district = District::create($data);

        return response()->json([
            'success' => true,
            'message' => "Tạo quận huyện thành công!",
            "data" => $district,
            'status_code' => 200
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'id' => $district->id,
                'name' => $district->name,
            ],
            'status_code' => 200
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, District $district)
    {

        $data = $request->all();


        $district->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật quận thành công',
            'data' => $district,
            'status_code' => 200
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district->delete();

    }
    public function getDistrictsByCity($cityId)
    {
        $districts = District::where('city_id', $cityId)->get();
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $districts,
            'status_code' => 200
        ]);
    }


}
