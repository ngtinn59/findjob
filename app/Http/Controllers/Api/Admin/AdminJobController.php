<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminJobController extends Controller
{
    public function confirmJob($jobId)
    {
        $job = Job::find($jobId);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Công việc không tồn tại.',
            ], 404);
        }

        // Chỉ cho phép admin xác nhận


        $job->status = Constant::user_status_active; // Bật công việc
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Công việc đã được xác nhận và kích hoạt.',
            'data' => $job,
            'status_code' => 200
        ]);
    }

    public function unconfirmJob($jobId)
    {
        $job = Job::find($jobId);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Công việc không tồn tại.',
            ], 404);
        }

        // Chỉ cho phép admin hủy xác nhận


        $job->status = Constant::user_status_inactive; // Tắt công việc
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Công việc đã được hủy xác nhận và vô hiệu hóa.',
            'data' => $job,
            'status_code' => 200
        ]);
    }


    public function index()
    {
        // Lấy danh sách tất cả công việc từ tất cả các công ty
        $jobs = \App\Models\Job::paginate(10); // Phân trang, mỗi trang 10 công việc

        // Map dữ liệu công việc
        $jobsData = $jobs->map(function ($job) {
            return [
                'id' => $job->id, // Corrected from 'ids' to 'id'
                'title' => $job->title,
                'profession' => $job->profession->name,
                'desired_level' => $job->desiredLevel->name,
                'workplace' => $job->workPlace->name,
                'employment_type' => $job->employmentType->name,
                'quantity' => $job->quantity,
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'education_level' => $job->educationLevel->name,
                'last_date' => $job->last_date,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'country' => $job->country->name,
                'city' => $job->city->name,
                'district' => $job->district->name,
                'work_address' => $job->work_address,
                'latitude' => $job->latitude,
                'longitude' => $job->longitude,
                'contact_name' => $job->contact_name,
                'phone' => $job->phone,
                'email' => $job->email,
                'featured' => $job->featured,
                'status' => $job->status,
                'company' => [ // Add company information
                    'company_id' => $job->company->id,
                    'company_name' => $job->company->company_name,
                    'logo' => asset('uploads/images/' . $job->company->logo),
                ],
            ];
        });

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $jobsData,
            'links' => [
                'first' => $jobs->url(1),
                'last' => $jobs->url($jobs->lastPage()),
                'prev' => $jobs->previousPageUrl(),
                'next' => $jobs->nextPageUrl(),
            ],
            'status_code' => 200
        ]);
    }



    public function show(Job $job)
    {
        $jobData = [
            'id' => $job->id,
            'title' => $job->title,
            'profession' => $job->profession->name,
            'desired_level' => $job->desiredLevel->name,
            'workplace' => $job->workPlace->name,
            'employment_type' => $job->employmentType->name,
            'quantity' => $job->quantity,
            'salary_from' => $job->salary_from,
            'salary_to' => $job->salary_to,
            'education_level' => $job->educationLevel->name,
            'last_date' => $job->last_date,
            'description' => $job->description,
            'skill_experience' => $job->skill_experience,
            'benefits' => $job->benefits,
            'country' => $job->country->name,
            'city' => $job->city->name,
            'district' => $job->district->name,
            'work_address' => $job->work_address,
            'latitude' => $job->latitude,
            'longitude' => $job->longitude,
            'contact_name' => $job->contact_name,
            'phone' => $job->phone,
            'email' => $job->email,
            'featured' => $job->featured,
            'status' => $job->status,
            'company' => [ // Add company information
                'company_id' => $job->company->id,
                'company_name' => $job->company->company_name,
                'logo' => asset('uploads/images/' . $job->company->logo),
            ],
        ];

        // Return data as JSON response
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $jobData,
            'status_code' => 200
        ]);
    }




    public function destroy(Job $job)
    {
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully',
            'status_code' => 200
        ]);
    }



}
