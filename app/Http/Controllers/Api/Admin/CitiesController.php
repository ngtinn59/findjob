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
        $city = City::all();
        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $city
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
            ], 400);
        }

        $data = $validator->validated();
        $city = City::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $city
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
                'name' => $city->name,
            ],
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
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();

    }
}
