<?php

namespace App\Http\Controllers\Api\Resume;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class GetResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where('id', auth()->user()->id)->firstOrFail();
        $profile = profile::where('users_id', $user->id)->get();
        $profilesData = $profile->map(function ($profile) {
            return [
               'title' => $profile->title,
               'name' => $profile->name,
               'phone' => $profile->phone,
               'email' => $profile->email,
               'date_of_birth' => $profile->date_of_birth,
               'gender' => $profile->gender === 1 ? 'Male' : 'Female',
               'address' => $profile->address,
               'portfolio_url' => $profile->portfolio_url,
               'github_url' => $profile->github_url,
               'linkedin_url' => $profile->linkedin_url,
                'aboutme' => $profile->abouts->map(function ($aboutme) {
                    return [
                        'description' => $aboutme->description,
                    ];
                }),
                'educations' => $profile->educations->map(function ($education) {
                    $startDate = $education->start_date;
                    $endDate = $education->end_date;

                    $duration = [
                        'start' => $startDate,
                        'end' => $endDate
                    ];
                    return [
                        'degree' => $education->degree,
                        'institution' => $education->institution,
                        'duration' => $duration,
                        'additionalDetail' => $education->additionalDetail,
                    ];
                }),
                'skills' => $profile->skills->map(function ($skill) {
                    $levelString = '';
                    switch ($skill->level) {
                        case 1:
                            $levelString = 'Beginner';
                            break;
                        case 2:
                            $levelString = 'Intermediate';
                            break;
                        case 3:
                            $levelString = 'Excellent';
                            break;
                        default:
                            $levelString = 'Unknown';
                            break;
                    }

                    return [
                        'name' => $skill->name,
                        'level' => $levelString,
                    ];
                }),
                'PersonalProject' => $profile->projects->map(function ($project) {
                    $startDate = $project->start_date;
                    $endDate = $project->end_date;

                    $duration = [
                        'start' => $startDate,
                        'end' => $endDate
                    ];
                    return [
                        'title' => $project->title,
                        'duration' => $duration,
                        'description' => $project->description,
                    ];
                }),
                'Certificate' => $profile->certificates->map(function ($certificates) {
                    return [
                        'title' => $certificates->title,
                        'provider' => $certificates->provider,
                        'issueDate' => $certificates->issueDate,
                        'description' => $certificates->description,
                        'certificateUrl' => $certificates->certificateUrl,
                    ];
                }),
                'WorkExperience' => $profile->experiences->map(function ($experience) {
                    $startDate = $experience->start_date;
                    $endDate = $experience->end_date;
                    $duration = [
                        'start' => $startDate,
                        'end' => $endDate
                    ];
                    return [
                        'position' => $experience->position,
                        'company' => $experience->company,
                        'duration' => $duration,
                        'responsibilities' => $experience->responsibilities,
                    ];
                }),
                'Award' => $profile->awards->map(function ($awards) {
                    return [
                        'title' => $awards->title,
                        'provider' => $awards->provider,
                        'issueDate' => $awards->issueDate,
                        'description' => $awards->description,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $profilesData,
        ]);
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
    public function show(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
