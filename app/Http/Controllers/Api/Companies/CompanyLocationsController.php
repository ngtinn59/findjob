<?php

namespace App\Http\Controllers\Api\Companies;

use App\Models\Company;
use App\Models\Location_Comapny;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyLocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $company = Company::where("users_id", auth()->user()->id)->first();
        $companies = $company->id;
        $data = [
            'location_id' => $request->input('location_id'),
            'company_id' => $companies,
        ];


        $validator = Validator::make($data, [
            'location_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $location_Comapny = Location_Comapny::create($data);

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $location_Comapny
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location_Comapny $location_Comapny)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location_Comapny $location_Comapny)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location_Comapny $location_Comapny)
    {
        //
    }
}
