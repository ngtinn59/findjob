<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Objective;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\CandidateNotification;
use Illuminate\Support\Facades\Mail;

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

        $candidate = Objective::findOrFail($id);
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
            'profile.languageskills.language',
            'desiredLevel',
            'profession',
            'employmentType',
            'educationLevel',
            'country',
            'city'
        ])->findOrFail($id);

        // Tùy chỉnh dữ liệu trả về
        $candidateData = [
            'id' => $candidate->id,
            'profile' => [
                'id' => $candidate->profile->id,
                'name' => $candidate->profile->name,
                'title' => $candidate->profile->title,
                'phone' => $candidate->profile->phone,
                'email' => $candidate->profile->email,
                'age' => $candidate->profile->birthday ? Carbon::parse($candidate->profile->birthday)->age : null,
                'image_url' => url('uploads/images/' . $candidate->profile->image),
                'gender' => $candidate->profile->gender,
                'location' => $candidate->profile->location,
                'website' => $candidate->profile->website,
            ],
            'objective' => [
                'desired_position' => $candidate->desired_position,
                'desiredLevel' => [
                    'id' => $candidate->desiredLevel->id ?? null,
                    'name' => $candidate->desiredLevel->name ?? null,
                ],
                'profession' => [
                    'id' => $candidate->profession->id ?? null,
                    'name' => $candidate->profession->name ?? null
                ],
                'employmentType' => [
                    'id' => $candidate->employmentType->id ?? null,
                    'name' => $candidate->employmentType->name ?? null
                ],
                'experienceLevel' => [
                    'id' => $candidate->experienceLevel->id ?? null,
                    'name' => $candidate->experienceLevel->name ?? null
                ],
                'work_address' => $candidate->work_address,
                'educationLevel' => [
                    'id' => $candidate->educationLevel->id ?? null,
                    'name' => $candidate->educationLevel->name ?? null,
                ],
                'salary_from' => $candidate->salary_from,
                'salary_to' => $candidate->salary_to,
                'file' => asset('cvs/' . $candidate->file),
                'status' => $candidate->status,
                'country' => [
                    'id' => $candidate->country->id ?? null,
                    'name' => $candidate->country->name ?? null,
                ],
                'city' => [
                    'id' => $candidate->city->id ?? null,
                    'name' => $candidate->city->name ?? null
                ],
                'district' => [
                    'id' => $candidate->district->id ?? null,
                    'name' => $candidate->district->name ?? null,
                ],
            ],
            'aboutme' => $candidate->profile->abouts->map(function ($aboutme) {
                return [
                    'id' => $aboutme->id,
                    'description' => $aboutme->description,
                ];
            }),
            'educations' => $candidate->profile->educations->map(function ($education) {
                return [
                    'id' => $education->id,
                    'degree' => $education->degree,
                    'institution' => $education->institution,
                    'start_date' => $education->start_date,
                    'end_date' => $education->end_date,
                    'additionalDetail' => $education->additionalDetail,
                ];
            }),
            'skills' => $candidate->profile->skills->map(function ($skill) {
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
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'level' => $levelString,
                ];
            }),
            'PersonalProject' => $candidate->profile->projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'description' => $project->description,
                ];
            }),
            'Certificate' => $candidate->profile->certificates->map(function ($certificate) {
                return [
                    'id' => $certificate->id,
                    'title' => $certificate->title,
                    'provider' => $certificate->provider,
                    'issueDate' => $certificate->issueDate,
                    'description' => $certificate->description,
                    'certificateUrl' => $certificate->certificateUrl,
                ];
            }),
            'WorkExperience' => $candidate->profile->experiences->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'position' => $experience->position,
                    'company' => $experience->company,
                    'start_date' => $experience->start_date,
                    'end_date' => $experience->end_date,
                    'responsibilities' => $experience->responsibilities,
                ];
            }),
            'Award' => $candidate->profile->awards->map(function ($award) {
                return [
                    'id' => $award->id,
                    'title' => $award->title,
                    'provider' => $award->provider,
                    'issueDate' => $award->issueDate,
                    'description' => $award->description,
                ];
            }),
        ];

        // Trả về dữ liệu dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'Chi tiết hồ sơ ứng viên',
            'data' => $candidateData,
            'status_code' => 200
        ]);
    }


    public function apply(Request $request, $id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Công việc không tồn tại.'], 404);
        }

        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($job->users()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Bạn đã ứng tuyển công việc này rồi.',
                'status_code' => 409
            ], 409);
        }

        // Kiểm tra xem người dùng đã chọn CV có sẵn hay upload CV mới
        if ($request->has('selected_cv_id')) {
            // Người dùng chọn một CV có sẵn
            $selectedCv = Cv::find($request->selected_cv_id);
            if (!$selectedCv || $selectedCv->users_id != $user->id) {
                return response()->json(['message' => 'CV không hợp lệ.'], 400);
            }
            $cvFileName = $selectedCv->file_path;
        } elseif ($request->hasFile('cv')) {
            // Người dùng tải lên CV mới
            $cv = $request->file('cv');
            $cvFileName = time() . '_' . $cv->getClientOriginalName();
            $cv->storeAs('public/cv', $cvFileName);
        } else {
            // Không có CV nào được chọn hoặc tải lên
            return response()->json(['message' => 'Vui lòng chọn hoặc tải lên CV.'], 400);
        }

        // Tiếp tục quá trình ứng tuyển
        Mail::to($user->email)->send(new JobApplied($job, $user, $cvFileName));
        $job->users()->attach($user->id, ['status' => 'pending', 'cv' => $cvFileName]);

        return response()->json([
            'success' => true,
            'message' => 'Ứng tuyển công việc thành công.',
            'status_code' => 200,
        ], 200);
    }

    public function getUserCvs(Request $request)
    {
        $user = $request->user();
        $cvs = $user->cvs; // Giả sử người dùng có mối quan hệ hasMany với CVs

        return response()->json([
            'success' => true,
            'cvs' => $cvs
        ]);
    }

    public function sendEmailToCandidate(Request $request, $userId)
    {

        // Lấy thông tin ứng viên từ User ID
        // Lấy người dùng hiện tại
        $user = $request->user();
        $company_name = $user->companies->company_name;
        // Tìm hồ sơ ứng viên đã được lưu bởi người dùng dựa vào ID
        $candidate = $user->savedCandidates()->with([
            'profile',
        ])->findOrFail($userId);
        $email = $candidate->profile->email;
        if (!$email) {
            return response()->json(['message' => 'Ứng viên không tồn tại.'], 404);
        }

        // Lấy email từ ứng viên


        // Kiểm tra xem email có hợp lệ không
        if (empty($email)) {
            return response()->json(['message' => 'Email của ứng viên không tồn tại.'], 400);
        }

        // Lấy nội dung email từ request
        $subject = $request->input('subject');
        $messageContent = $request->input('message');

        if (!$subject || !$messageContent) {
            return response()->json(['message' => 'Vui lòng cung cấp tiêu đề và nội dung email.'], 400);
        }

        // Gửi email
        Mail::to($email)->send(new CandidateNotification($subject, $messageContent,$company_name));

        return response()->json([
            'success' => true,
            'message' => 'Email đã được gửi đến ứng viên thành công.',
        ], 200);
    }




}
