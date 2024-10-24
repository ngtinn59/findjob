<?php

use App\Http\Controllers\Api\Candidates\JobSeekersController;
use App\Http\Controllers\Api\Candidates\NotificationController;
use App\Http\Controllers\Api\Employer\CandidatesController;
use App\Http\Controllers\Api\Employer\EmployerMailController;
use App\Http\Controllers\PublicDataController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\{AdminCompaniesController,
    AdminCompanySizesController,
    AdminCompanyTypesController,
    AdminController,
    AdminJobController,
    AdminStatsController,
    AdminUserController,
    CitiesController,
    CompanysizesController,
    CompanytypesController,
    CountriesController,
    DesiredLevelsController,
    DistrictsController,
    EducationLevelsController,
    EmploymentTypesController,
    ExperienceLevelsController,
    LanguagesController,
    ProfessionsController,
    WorkplacesController};
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Companies\{CompaniesController,
    CompaniesSkillsController,
    CompanyLocationsController,
    JobsController};
use App\Http\Controllers\Api\Employer\EmployerRegisterController;
use App\Http\Controllers\Api\Job\JobApplicationController;
use App\Http\Controllers\Api\Resume\{AboutmeController,
    AwardsController,
    CertificatesController,
    CvsController,
    EducationController,
    ExperiencesController,
    GetResumeController,
    LanguageSkillsController,
    ObjectivesController,
    ProfilesController,
    ProjectsController,
    SkillsController};
use App\Http\Middleware\CheckUserRole;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Controllers\MessageController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Route to send email verification notification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Đã gửi liên kết xác minh!']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// Route to handle email verification
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return response()->json(['message' => 'Invalid email verification link'], 400);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return response()->json(['message' => 'Email đã được xác minh thành công!']);
})->middleware(['signed'])->name('verification.verify');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    // Check if the email hash is correct
    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return response()->json([
            'message' => 'Invalid email verification link'
        ], 400);
    }

    // Check if the email is already verified
    if (!$user->hasVerifiedEmail()) {
        // Mark the email as verified and trigger the Verified event
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return response()->json([
        'message' => 'Email đã được xác minh thành công!'
    ]);
})->middleware(['signed'])->name('verification.verify');






// Route to check if email is verified
Route::get('/email/verify', function (Request $request) {
    return response()->json([
        'email_verified' => $request->user()->hasVerifiedEmail(),
    ]);
})->middleware(['auth:sanctum'])->name('verification.notice');


Route::get('/openapi.json', function () {
    $jsonPath = storage_path('api.json');
    if (file_exists($jsonPath)) {
        return response()->json(json_decode(file_get_contents($jsonPath)), 200, [], JSON_PRETTY_PRINT);
    }
    return response()->json(['error' => 'OpenAPI JSON file not found'], 404);
});

// Public Routes
Route::get('/countries', [CountriesController::class, 'index']);
Route::get('/cities', [CitiesController::class, 'index']);
Route::get('/company-types', [AdminCompanyTypesController::class, 'index']);
Route::get('/company-sizes', [AdminCompanySizesController::class, 'index']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::resource('/districts', DistrictsController::class);
Route::get('countries/{country}/cities', [CitiesController::class, 'getCitiesByCountry']);
Route::get('cities/{city}/districts', [DistrictsController::class, 'getDistrictsByCity']);
Route::get('languages', [PublicDataController::class, 'getLanguages']);
Route::get('professions', [PublicDataController::class, 'getProfessions']);
Route::get('employment-types', [PublicDataController::class, 'getEmploymentTypes']);
Route::get('education-levels', [PublicDataController::class, 'getEducationLevels']);
Route::get('desired-levels', [PublicDataController::class, 'getDesiredLevels']);
Route::get('experience-levels', [PublicDataController::class, 'getExperienceLevels']);


// Auth Routes
Route::post('employer/register', [EmployerRegisterController::class, 'employerRegister']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::delete('logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/download-cv', [ProfilesController::class, 'download_cv']);
    Route::post('/upload-cv', [CvsController::class, 'upload']);
    Route::get('/default-cv', [CvsController::class, 'getDefaultCv']);
    Route::put('/cvs/{cv}/set-default', [CvsController::class, 'setDefault'])->name('cvs.set-default');

    Route::post('/change-password', [AuthController::class, 'changePassword']);


    Route::post('messages', [MessageController::class, 'sendMessage']);
    Route::get('messages/{userId}', [MessageController::class, 'getMessages']);

    Route::get('/list-jobs', [JobsController::class, 'indexShow']);
    Route::get('/list-jobs/urgent', [JobsController::class, 'indexUrgent']);

    Route::get('/list-jobs/{job}', [JobsController::class, 'showJob']);
    Route::get('/jobs/search', [JobsController::class, 'search']);
    Route::get('/list-companies', [CompaniesController::class, 'indexShow']);
    Route::get('/list-companies/featured', [CompaniesController::class, 'indexFeaturedCompanies']);

    Route::get('/list-companies/{company}', [CompaniesController::class, 'detailShow']);
    // Profile Routes
    Route::resource('profile', ProfilesController::class);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);

    Route::prefix('profiles')->group(function () {
        Route::resource('/educations', EducationController::class);
        Route::resource('/skills', SkillsController::class);
        Route::resource('/aboutMe', AboutmeController::class);
        Route::resource('/certificates', CertificatesController::class);
        Route::resource('/awards', AwardsController::class);
        Route::resource('/projects', ProjectsController::class);
        Route::resource('/resume', GetResumeController::class);
        Route::resource('/experiences', ExperiencesController::class);
        Route::resource('/objectives', ObjectivesController::class);
        Route::put('/objectives/{id}/upload', [ObjectivesController::class,'uploadFile']);
        Route::put('/objectives/{id}/status', [ObjectivesController::class, 'updateStatus']);

        Route::resource('/language-skills', LanguageSkillsController::class);

    });

    // CV Routes
    Route::resource('cvs', CvsController::class);

    // Job Application and Favorites
    Route::prefix('jobs')->group(function () {
        Route::post('/{id}/apply', [JobSeekersController::class, 'apply']);
        Route::get('/user/cvs', [JobSeekersController::class, 'getUserCvs']);

        Route::get('/applied', [JobsController::class, 'applicant']);
        Route::post('/favorites/{id}/save', [JobSeekersController::class, 'saveJob']);
        Route::post('/favorites/{id}/un-save', [JobSeekersController::class, 'unsaveJob']);
        Route::get('/favorites/saved', [JobSeekersController::class, 'savedJobs']);
        Route::get('/suggest', [JobsController::class, 'suggestJobs']);
    });

    Route::middleware(CheckUserRole::class)->group(function () {
        Route::resource('employer/jobs', JobsController::class);
        Route::get('employer/companies/notifications', [JobsController::class, 'getNotifications']);
        Route::post('employer/companies/notifications/read', [JobsController::class, 'markAsRead']);

        Route::post('/jobs/{jobId}/applicants/{userId}/send-email', [EmployerMailController::class, 'sendEmailToApplicant']);

        Route::post('/process-application/{jobId}/{userId}', [JobApplicationController::class, 'processApplication']);
        Route::get('/applications', [JobApplicationController::class, 'index']);
        Route::post('/{id}/toggle', [JobApplicationController::class, 'toggle']);
        Route::get('/statistics', [JobApplicationController::class, 'getStatistics']);
        Route::delete('/jobs/{jobId}/applicants/{userId}', [JobApplicationController::class, 'destroy']);

        Route::get('resume/objectives/search', [ObjectivesController::class, 'search']);
        Route::get('resume/objectives/search-keyword', [ObjectivesController::class, 'searchByKeyword']);

        Route::post('employer/candidates/save/{id}', [CandidatesController::class, 'saveCandidate']);
        Route::delete('employer/candidates/un-save/{id}', [CandidatesController::class, 'unsaveCandidate']);
        Route::get('employer/candidates/saved', [CandidatesController::class, 'index']);
        Route::get('employer/saved-candidates/{id}', [CandidatesController::class, 'show']);
        Route::resource('employer/companies', CompaniesController::class);


    });

    Route::middleware(CheckAdminRole::class)->prefix('admin')->group(function () {
        Route::resource('/workplaces', WorkplacesController::class);

        Route::resource('/countries', CountriesController::class);
        Route::resource('/cities', CitiesController::class);
        Route::resource('/districts', DistrictsController::class);
        Route::get('countries/{country}/cities', [CitiesController::class, 'getCitiesByCountry']);
        Route::get('cities/{city}/districts', [DistrictsController::class, 'getDistrictsByCity']);



        Route::get('/companies/count', [AdminCompaniesController::class, 'countCompaniesAndJobs']);

        route::resource('/jobs', AdminJobController::class);
        Route::post('/jobs/{jobId}/confirm', [AdminJobController::class, 'confirmJob']);
        Route::post('/jobs/{jobId}/un-confirm', [AdminJobController::class, 'unconfirmJob']);


        Route::resource('/users', AdminUserController::class);
        Route::post('/block-user/{id}', [AdminUserController::class, 'blockUser']);
        Route::post('/unblock-user/{id}', [AdminUSerController::class, 'unblockUser']);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::get('/users/{id}', [AdminUserController::class, 'show']);
        Route::put('/users/{id}', [AdminUserController::class, 'update']);
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);


        //Thống kê

        Route::get('/statistics', [AdminStatsController::class, 'index']);

        Route::resource('/languages', LanguagesController::class);

        Route::resource('/professions', ProfessionsController::class);

        Route::resource('/employment-types', EmploymentTypesController::class);

        Route::resource('/education-levels', EducationLevelsController::class);

        Route::resource('/desired-levels', DesiredLevelsController::class);

        Route::resource('/experience-levels', ExperienceLevelsController::class);

        Route::resource('/company-types', AdminCompanyTypesController::class);
        Route::resource('/company-sizes', AdminCompanySizesController::class);
        Route::resource('/companies', AdminCompaniesController::class);
        // Lấy danh sách công ty
        Route::get('companies', [AdminCompaniesController::class, 'index']);

        // Đánh dấu công ty là nổi bật
        Route::post('companies/{companyId}/mark-as-hot', [AdminCompaniesController::class, 'markAsHot']);

    });
});
