<?php

namespace App\Http\Controllers\Api\Companies;

use App\Models\aboutme;
use App\Models\Jobskill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Job_skillsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->companies->first();
        if ($company) {
            $job = $company->Job->first();
            if ($job) {
                $data = [
                    'job_id' => $request->job_id,
                    'name' => $request->name,
                ];

                $validator = Validator::make($data, [
                    'job_id' => 'required',
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
                $jobskill = Jobskill::create($data);

                return response()->json([
                    'success'   => true,
                    'message'   => "success",
                    "data" => $jobskill
                ]);
            }
        }

        // Handle the case where the company or job does not exist
        return response()->json([
            'success' => false,
            'message' => 'Company or job not found',
        ], 404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Jobskill $jobskill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jobskill $jobskill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jobskill $jobskill)
    {
        //
    }
}
