<?php

namespace App\Http\Controllers\Api\Companies;

use App\Models\Companyskill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompaniesSkillsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comapyId = auth()->user()->companies->id;

        $skillsDataResponse = Companyskill::where('company_id', $comapyId)->get();
        $skillsDataResponseFormat =  $skillsDataResponse->map(function ($skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name,
            ];
        });
        // Trả về phản hồi JSON với thông tin về kỹ năng công ty vừa được tạo
        return response()->json([
            'success' => true,
            'message' => 'Company skills created successfully',
            'data' => $skillsDataResponseFormat,
        ], 201);

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
        $comapyId = auth()->user()->companies->id;

        // Validate request data
        $validationRules = [
            'skills.*.name' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        // Xóa tất cả các kỹ năng của công ty hiện có
        Companyskill::where('company_id', $comapyId)->delete();

        $skillsData = [];

        // Lặp qua mỗi kỹ năng trong mảng kỹ năng
        foreach ($request->skills as $skill) {
            $skillsData[] = [
                'name' => $skill['name'],
                'company_id' => $comapyId
            ];
        }

        // Thêm mới các kỹ năng vào cơ sở dữ liệu
        $companySkills = Companyskill::insert($skillsData);

        $skillsDataResponse = Companyskill::where('company_id', $comapyId)->get();
        $skillsDataResponseFormat =  $skillsDataResponse->map(function ($skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name,
            ];
        });
        // Trả về phản hồi JSON với thông tin về kỹ năng công ty vừa được tạo
        return response()->json([
            'success' => true,
            'message' => 'Company skills created successfully',
            'data' => $skillsDataResponseFormat,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Companyskill $companyskill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Companyskill $companyskill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Companyskill $companyskill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Companyskill $companyskill)
    {
        //
    }
}
