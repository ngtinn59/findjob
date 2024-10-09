<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Companysize;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class CompanysizesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companysize = Companysize::all();
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $companysize,
            'status_code' => 200
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
        $Companysize = Companysize::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $Companysize,
            'status_code' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Companysize $companysize)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'id' => $companysize->id,
                'name' => $companysize->name,
            ],
            'status_code' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Companysize $companysize)
    {
        $data = $request->all();


        $companysize->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Companysize updated successfully',
            'data' => $companysize,
            'status_code' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Companysize $companysize)
    {
        $companysize->delete();
    }
}
