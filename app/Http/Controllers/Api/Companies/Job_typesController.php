<?php

namespace App\Http\Controllers\Api\Companies;

use App\Models\Jobtype;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Job_typesController extends Controller
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
        $company= $user->company;
        dd($company);
    }

    /**
     * Display the specified resource.
     */
    public function show(Jobtype $jobtype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jobtype $jobtype)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jobtype $jobtype)
    {
        //
    }
}
