<?php

namespace App\Http\Controllers\Api\Resume;

use App\Http\Resources\UserResourceCollection;
use App\Models\aboutme;
use App\Models\Certificates;
use App\Http\Controllers\Controller;
use App\Models\profiles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CertificatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where("id", auth()->user()->id)->firstOrFail();
        $profiles = profiles::where("users_id", $user->id)->firstOrFail();
        $Certificates = Certificates::where("profiles_id", $profiles->id)->get();
        $CertificatesData = $Certificates->map(function ($Certificates) {
            return [
                'title' => $Certificates->title,
                'name' => $Certificates->name,
                'url' => $Certificates->url,
                'date' => $Certificates->date,
                'link' => $Certificates->link
            ];
        });

        // Trả về danh sách giáo dục dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $CertificatesData
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = [
            'title' => $request->input('title'),
            'profiles_id' => $request->input('profiles_id'),
            'name' => $request->input('name'),
            'date' => $request->input('date'),
            'link' => $request->input('link')
        ];

        $validator = Validator::make($data, [
            'title' => 'required',
            'profiles_id' => 'required',
            'name' => 'required',
            'date' => 'required',
            'link' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();
        $Certificates = Certificates::create($data);

        return response()->json([
            'success'   => true,
            'message'   => "success",
            "data" => $Certificates
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificates $certificates)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificates $certificates)
    {
        $data = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'profiles_id' => $request->input('profiles_id'),
            'name' => $request->input('name'),
            'date' => $request->input('date'),
            'link' => $request->input('link')
        ];
        dd($data);
        $validator = Validator::make($data, [

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();

        $certificates->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Certificates updated successfully',
            'data' => $certificates,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificates $certificates)
    {
        //
    }
}
