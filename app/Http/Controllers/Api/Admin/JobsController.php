<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Job;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::all();
        $jobsData = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,

                'salary' => $job->salary,
                'company' => $job->company ? $job->company->name : null,
                'status' => $job->status,
                'address' => $job->address,
                'description' => $job->description,
                'created_at' => $job->created_at->diffForHumans(),
                'updated_at' => $job->updated_at->diffForHumans(),
            ];
        });
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $jobsData,
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        $jobData = [
            'id' => $job->id,
            'title' => $job->title,
            'salary' => $job->salary,
            'company' => $job->company ? $job->company->name : null,
            'status' => $job->status,
            'address' => $job->address,
            'description' => $job->description,
            'created_at' => $job->created_at->diffForHumans(),
            'updated_at' => $job->updated_at->diffForHumans(),
        ];
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $jobData,
            'status_code' => 200
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {

        $job->delete();

    }




    public function countJobs()
    {
        // Sử dụng Eloquent query builder
        $totalJobs = Job::count();

        // Hoặc sử dụng Collection
        // $jobs = Job::all();
        // $totalJobs = $jobs->count();

        return response()->json([
            'success' => true,
            'message' => 'Total jobs counted successfully',
            'total_jobs' => $totalJobs,
            'status_code' => 200
        ]);
    }

}
