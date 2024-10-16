<?php

use App\Http\Controllers\Api\Employer\CandidatesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\{AdminController,
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
    JobtypesControllerController,
    CompaniesController as AdminCompaniesController,
    LanguagesController,
    ProfessionsController};
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
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email đã được xác minh thành công!']);
})->middleware(['signed'])->name('verification.verify');


// Route to check if email is verified
Route::get('/email/verify', function (Request $request) {
    return response()->json([
        'email_verified' => $request->user()->hasVerifiedEmail(),
    ]);
})->middleware(['auth:sanctum'])->name('verification.notice');


// Public Routes
Route::get('/countries', [CountriesController::class, 'index']);
Route::get('/cities', [CitiesController::class, 'index']);
Route::get('/job-types', [JobtypesControllerController::class, 'index']);
Route::get('/company-types', [CompanytypesController::class, 'index']);
Route::get('/company-sizes', [CompanysizesController::class, 'index']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::resource('/districts', DistrictsController::class);
Route::get('countries/{country}/cities', [CitiesController::class, 'getCitiesByCountry']);
Route::get('cities/{city}/districts', [DistrictsController::class, 'getDistrictsByCity']);

// User Jobs

Route::get('/list-jobs', [JobsController::class, 'indexShow']);
Route::get('/list-jobs/{job}', [JobsController::class, 'showJob']);
Route::get('/search', [JobsController::class, 'search']);
Route::get('/companies1', [CompaniesController::class, 'indexShow']);
Route::get('/companies1/{company}', [CompaniesController::class, 'show']);

// Companie

// Auth Routes
Route::post('employer/register', [EmployerRegisterController::class, 'employerRegister']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::delete('logout', [AuthController::class, 'logout']);

// Chat Real time



//User Jobs
Route::get('/list-jobs', [JobsController::class, 'indexShow']);
Route::get('/jobs/{job}', [JobsController::class, 'showJob']);
Route::get('/search', [JobsController::class, 'search']);
Route::get('/list-companies', [CompaniesController::class, 'indexShow']);
Route::get('/list-companies/{company}', [CompaniesController::class, 'show']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/download-cv', [ProfilesController::class, 'download_cv']);
    Route::post('/upload-cv', [CvsController::class, 'upload']);
    Route::get('/default-cv', [CvsController::class, 'getDefaultCv']);
    Route::put('/cvs/{cv}/set-default', [CvsController::class, 'setDefault'])->name('cvs.set-default');

    Route::post('/change-password', [AuthController::class, 'changePassword']);


    Route::post('messages', [MessageController::class, 'sendMessage']);
    Route::get('messages/{userId}', [MessageController::class, 'getMessages']);

    Route::resource('company/skills', CompaniesSkillsController::class);

    // Profile Routes
    Route::resource('profile', ProfilesController::class);

    Route::prefix('profiles')->group(function () {
        Route::resource('/educations', EducationController::class);
        Route::resource('/skills', SkillsController::class);
        Route::resource('/about-me', AboutmeController::class);
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

    // Company Routes
    Route::prefix('companies')->group(function () {
        Route::resource('/', CompaniesController::class);
        Route::resource('/locations', CompanyLocationsController::class);
        Route::resource('/skills', CompaniesSkillsController::class);
    });

    // Job Application and Favorites
    Route::prefix('jobs')->group(function () {
        Route::post('/{id}/apply', [JobsController::class, 'apply']);
        Route::get('/applied', [JobsController::class, 'applicant']);
        Route::post('/favorites/{id}/save', [JobsController::class, 'saveJob']);
        Route::post('/favorites/{id}/unsave', [JobsController::class, 'unsaveJob']);
        Route::get('/favorites/saved', [JobsController::class, 'savedJobs']);
        Route::get('/suggest', [JobsController::class, 'suggestJobs']);
    });

    Route::middleware(CheckUserRole::class)->group(function () {
        Route::resource('employer/jobs', JobsController::class);
        Route::get('/jobs/suggest-jobs', [JobsController::class, 'searchForEmployer']);

        Route::post('/process_application/{jobId}/{userId}', [JobApplicationController::class, 'processApplication']);
        Route::get('/applications', [JobApplicationController::class, 'index']);
        Route::post('/{id}/toggle', [JobApplicationController::class, 'toggle']);
        Route::get('/statistics', [JobApplicationController::class, 'getStatistics']);

        Route::get('resume/objectives/search', [ObjectivesController::class, 'search']);
        Route::get('resume/objectives/search-keyword', [ObjectivesController::class, 'searchByKeyword']);

        Route::post('employer/candidates/save/{id}', [CandidatesController::class, 'saveCandidate']);
        Route::delete('employer/candidates/un-save/{id}', [CandidatesController::class, 'unsaveCandidate']);
        Route::get('employer/candidates/saved', [CandidatesController::class, 'index']);
        Route::get('employer/saved-candidates/{id}', [CandidatesController::class, 'show']);


    });

    Route::middleware(CheckAdminRole::class)->prefix('admin')->group(function () {
        Route::resource('/job-types', JobtypesControllerController::class);

        Route::resource('/countries', CountriesController::class);
        Route::resource('/cities', CitiesController::class);
        Route::resource('/districts', DistrictsController::class);
        Route::get('countries/{country}/cities', [CitiesController::class, 'getCitiesByCountry']);
        Route::get('cities/{city}/districts', [DistrictsController::class, 'getDistrictsByCity']);


        Route::resource('/company-types', CompanytypesController::class);
        Route::resource('/company-sizes', CompanysizesController::class);
        Route::resource('/companies', AdminCompaniesController::class);
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

    });
});
