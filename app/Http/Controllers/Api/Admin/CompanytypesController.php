<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Companytype;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanytypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companytype = Companytype::all();
        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $companytype
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
        ];

        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $companytype = Companytype::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $companytype
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Companytype $companytype)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $companytype,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Companytype $companytype)
    {
        $data = $request->all();


        $companytype->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Companytype updated successfully',
            'data' => $companytype,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Companytype $companytype)
    {
        $companytype->delete();
    }
}
