<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Company;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();
        $companiesData = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'country' => $company->country->name,
                'city' => $company->city->name,
                'district_id' => $company->city->name,


            ];
        });
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $companiesData,
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
    public function show(Company $company)
    {
        $companyData = [
            'id' => $company->id,
            'name' => $company->name,
            'description' => $company->description,
            'logo' => asset('uploads/images/' . $company->logo), // Assuming the logo is stored in the 'storage' folder
            'webstie' => $company->webstie,
            'facebook' => $company->facebook,
            'address' => $company->address,
            'country' => $company->country->name,
            'city' => $company->city->name,
            'created_at' => $company->created_at->diffForHumans(),
            'updated_at' => $company->updated_at->diffForHumans(),
        ];
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $companyData,
            'status_code' => 200
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully.',
            'status_code' => 200
        ]);

    }

    public function countCompaniesAndJobs()
    {
        // Số lượng công ty
        $totalCompanies = Company::count();

        // Số lượng công việc
        $totalJobs = Job::count();

        // Số lượng người dùng có loại 1
        $totalUsersType1 = User::where('account_type', 1)->count();
        $totalSalary = Job::sum('salary');

        return response()->json([
            'success' => true,
            'message' => 'Counted successfully',
            'data' => [
                'total_companies' => $totalCompanies,
                'total_jobs' => $totalJobs,
                'user' => $totalUsersType1,
                'total_salary' => $totalSalary
            ],
            'status_code' => 200
        ]);
    }
}
