<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\City;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Lấy tất cả dữ liệu từ bảng City và load thông tin Country
            $cities = City::with('country')->get();  // Giả sử bảng City có quan hệ với Country thông qua phương thức 'country'

            // Kiểm tra nếu không có dữ liệu
            if ($cities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có thành phố nào được tìm thấy',
                    'data' => [],
                    'status_code' => 404
                ]);
            }

            // Tùy chỉnh lại dữ liệu trả về, bao gồm id, name của city và country name
            $citiesData = $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'country' => [
                        'id' =>$city->country->id,
                        'name' =>$city->country->name,
                    ],
                    'created_at' => $city->created_at,   // Lấy ngày tạo
                    'updated_at' => $city->updated_at,   // Lấy ngày cập nhật
                ];
            });

            // Trả về dữ liệu thành công
            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách thành phố và quốc gia thành công',
                'data' => $citiesData,
                'status_code' => 200
            ]);

        } catch (\Exception $e) {
            // Bắt lỗi và trả về thông báo lỗi
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách thành phố và quốc gia',
                'error' => $e->getMessage(),
                'status_code' => 500
            ]);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user()->id;

        $data = [
            'users_id' => $user,
            'country_id' => $request->input('country_id'),
            'name' => $request->input('name'),
        ];
        $validator = Validator::make($data, [
            'users_id' => 'required',
            'name' => 'required',
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $data = $validator->validated();
        $city = City::create($data);

        return response()->json([
            'success' => true,
            'message' => "success",
            "data" => $city,
            'status_code' => 200
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'id' => $city->id,
                'name' => $city->name,
            ],
            'status_code' => 200
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {

        $data = $request->all();


        $city->update($data);

        return response()->json([
            'success' => true,
            'message' => 'City updated successfully',
            'data' => $city,
            'status_code' => 200
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();

    }

    public function getCitiesByCountry($countryId)
    {
        $cities = City::where('country_id', $countryId)->get();
        $citiesData = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
            ];
        });
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $citiesData,
            'status_code' => 200
        ],200);
    }

}
