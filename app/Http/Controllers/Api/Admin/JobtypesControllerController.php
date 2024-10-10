<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Jobtype;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobtypesControllerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobtype = Jobtype::all();
        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $jobtype,
            'status_code' => 200
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $job_type = Jobtype::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $job_type
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(Jobtype $jobtype)
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'name' => $jobtype->name,
            ],
            'status_code' => 200
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jobtype $jobtype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jobtype $jobtype)
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

        $jobtype->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Job type updated successfully',
            'data' => $jobtype,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jobtype $jobtype)
    {
        $jobtype->delete();
    }
}
