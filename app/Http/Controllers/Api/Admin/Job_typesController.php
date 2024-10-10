<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\aboutme;
use App\Models\job_type;
use App\Http\Controllers\Controller;
use App\Models\Jobtype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Job_typesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $job_type = Jobtype::all();
        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $job_type
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
    public function show(Jobtype $job_type)
    {
        dd($job_type->id);
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'name' => $job_type->name,
            ],
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, job_type $job_type)
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

        $job_type->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Job type updated successfully',
            'data' => $job_type,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(job_type $job_type)
    {
        $job_type->delete();
    }
}
