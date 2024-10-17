<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Objective;
use Illuminate\Http\Request;

class CandidatesController extends Controller
{
    public function saveCandidate(Request $request, $id)
    {
        // Tìm ứng viên theo ID
        $candidate = Objective::findOrFail($id);
        $user = $request->user();

        // Kiểm tra xem hồ sơ ứng viên đã được lưu chưa
        if ($user->savedCandidates()->where('objective_id', $candidate->id)->exists()) {
            return response()->json(['message' => 'Candidate is already saved'], 200);
        } else {
            // Thêm hồ sơ ứng viên vào danh sách yêu thích
            $user->savedCandidates()->syncWithoutDetaching([$candidate->id]);
            return response()->json([
                'success' => true,
                'message' => 'Candidate saved successfully',
                'status_code' => 200
            ], 200);
        }
    }


    /**
     * Unsave Candidate from saved list
     */
    public function unsaveCandidate(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $request->user();
        $user->savedCandidates()->detach($candidate->id);

        return response()->json([
            'success' => true,
            'message' => 'Candidate removed from saved list',
            'status_code' => 200,
        ], 200);
    }

    public function index(Request $request)
    {
        // Lấy người dùng hiện tại
        $user = $request->user();

        // Lấy danh sách hồ sơ ứng viên mà người dùng đã lưu
        $savedCandidates = $user->savedCandidates()->with(['profile', 'profile.experiences', 'profile.objectives'])->get();
        // Tùy chỉnh dữ liệu trả về
        $customData = $savedCandidates->map(function ($candidate) {
            return [
                'id' => $candidate->id,
                'desired_position' => $candidate->desired_position,
                'name' => $candidate->profile->name ?? null, // Tên ứng viên
                'salary' => [
                    'salary_from' => $candidate->salary_from,
                    'salary_to' => $candidate->salary_to,
                ],
                'experience_level' => $candidate->experienceLevel->name,
                'district' => $candidate->district->name,

                'created_at' => $candidate->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // Trả về dữ liệu dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Danh sách hồ sơ ứng viên đã lưu',
            'data' => $customData,
            'status_code' => 200
        ]);
    }

    public function show(Request $request, $id)
    {
        // Lấy người dùng hiện tại
        $user = $request->user();

        // Tìm hồ sơ ứng viên đã được lưu bởi người dùng dựa vào ID
        $candidate = $user->savedCandidates()->with([
            'profile',
            'experienceLevel',
            'district',
            'profile.experiences',
            'profile.educations',
            'profile.certificates',
            'profile.skills',
            'profile.languageskills.language'
        ])->findOrFail($id);

        // Tùy chỉnh dữ liệu trả về
        $candidateData = [
            'id' => $candidate->id,
            'profile' => [
                'name' => $candidate->profile->name ?? null,
                'phone' => $candidate->profile->phone ?? null,
                'email' => $candidate->profile->email ?? null,
                'image_url' => url('uploads/images/' . $candidate->profile->image),
                'gender' => $candidate->profile->gender === 0 ? 'Nam' : 'Nữ',
                'address' => $candidate->profile->address ?? null,
                'country_id' => $candidate->profile->country->name ?? null,
                'city_id' => $candidate->profile->city->name ?? null,
                'district_id' => $candidate->profile->district->name ?? null,
                'objective' => [
                    'desired_position' => $candidate->desired_position,
                    'desired_level' => $candidate->desiredLevel->name ?? null,
                    'profession' => $candidate->profession->name ?? null,
                    'employment_type' => $candidate->employmentType->name ?? null,
                    'experience_level' => $candidate->experienceLevel->name ?? null,
                    'work_address' => $candidate->work_address,
                    'education_level' => $candidate->educationLevel->name ?? null,
                    'salary_from' => $candidate->salary_from,
                    'salary_to' => $candidate->salary_to,
                    'file' => asset('cvs/' . $candidate->file),
                    'status' => 'hoạt động', // Vì đã lọc theo status = 3
                    'country' => $candidate->country->name ?? null,
                    'city' => $candidate->city->name ?? null,
                    'district' => $candidate->district->name ?? null,
                ],
            ],
            'experiences' => $candidate->profile->experiences->map(function ($experience) {
                return [
                    'company' => $experience->company,
                    'position' => $experience->position,
                    'start_date' => $experience->start_date,
                    'end_date' => $experience->end_date ?? 'Present',
                ];
            }),
            'educations' => $candidate->profile->educations->map(function ($education) {
                return [
                    'degree' => $education->degree,
                    'institution' => $education->institution,
                    'start_date' => $education->start_date,
                    'end_date' => $education->end_date ?? 'Ongoing',
                ];
            }),
            'certificates' => $candidate->profile->certificates->map(function ($certificate) {
                return [
                    'title' => $certificate->title,
                    'provider' => $certificate->provider,
                    'issue_date' => $certificate->issueDate,
                    'description' => $certificate->description,
                ];
            }),
            'skills' => $candidate->profile->skills->map(function ($skill) {
                return [
                    'name' => $skill->name,
                    'level' => $skill->level,
                ];
            }),
            'languages_skills' => $candidate->profile->languageskills->map(function ($languageskill) {
                return [
                    'name' => $languageskill->language->name ?? null,
                ];
            }),
            'created_at' => $candidate->created_at->format('Y-m-d H:i:s'),
        ];

        // Trả về dữ liệu dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Chi tiết hồ sơ ứng viên',
            'data' => $candidateData,
            'status_code' => 200
        ]);
    }


}
