<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\{
    CitiesController,
    CompanysizesController,
    CompanytypesController,
    CountriesController,
    JobtypesControllerController,
    CompaniesController as AdminCompaniesController,
    JobsController as AdminJobsController
};
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Companies\{
    CompaniesController, CompanyLocationsController, JobsController
};
use App\Http\Controllers\Api\Employer\EmployerRegisterController;
use App\Http\Controllers\Api\Job\JobApplicationController;
use App\Http\Controllers\Api\Resume\{
    AboutmeController,
    AwardsController,
    CertificatesController,
    CvsController,
    EducationController,
    ExperiencesController,
    GetResumeController,
    ProfilesController,
    ProjectsController,
    SkillsController
};
use App\Http\Middleware\CheckUserRole;
use App\Http\Middleware\CheckAdminRole;

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

    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

// Route to handle email verification
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully!']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Route to check if email is verified
Route::get('/email/verify', function (Request $request) {
    return response()->json([
        'email_verified' => $request->user()->hasVerifiedEmail(),
    ]);
})->middleware(['auth:sanctum'])->name('verification.notice');


use Illuminate\Support\Facades\Mail;

Route::get('/send-mail', function () {
    Mail::raw('This is a test email.', function ($message) {
        $message->to('recipient@example.com')
            ->subject('Test Mail');
    });

    return 'Mail sent!';
});

// Public Routes
Route::get('/countries', [CountriesController::class, 'index']);
Route::get('/cities', [CitiesController::class, 'index']);
Route::get('/job-types', [JobtypesControllerController::class, 'index']);
Route::get('/company-types', [CompanytypesController::class, 'index']);
Route::get('/company-sizes', [CompanysizesController::class, 'index']);

// User Jobs
Route::prefix('jobs')->group(function () {
    Route::get('/list', [JobsController::class, 'indexShow']);
    Route::get('/{job}', [JobsController::class, 'showJob']);
    Route::get('/search', [JobsController::class, 'search']);
});

// Companies
Route::prefix('companies')->group(function () {
    Route::get('/', [CompaniesController::class, 'indexShow']);
    Route::get('/{company}', [CompaniesController::class, 'show']);
});

// Auth Routes
Route::post('employer/register', [EmployerRegisterController::class, 'employerRegister']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::delete('logout', [AuthController::class, 'logout']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/download-cv', [ProfilesController::class, 'download_cv']);
    Route::post('/upload-cv', [CvsController::class, 'upload']);
    Route::get('/default-cv', [CvsController::class, 'getDefaultCv']);
    Route::put('/cvs/{cv}/set-default', [CvsController::class, 'setDefault'])->name('cvs.set-default');
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/verify-code', [AuthController::class, 'verifyCodeAndUpdatePassword']);
    // Profile Routes
    Route::prefix('profiles')->group(function () {
        Route::resource('/profiles/educations', EducationController::class);
        Route::resource('/profiles/skills', SkillsController::class);
        Route::resource('/profiles/about_me', AboutmeController::class);
        Route::resource('/profiles/certificates', CertificatesController::class);
        Route::resource('/profiles/awards', AwardsController::class);
        Route::resource('/profiles/projects', ProjectsController::class);
        Route::resource('/profiles/get_resume', GetResumeController::class);
        Route::resource('/profiles/experiences', ExperiencesController::class);
    });

    // CV Routes
    Route::resource('cvs', CvsController::class);

    // Company Routes
    Route::prefix('companies')->group(function () {
        Route::resource('/', CompaniesController::class);
        Route::resource('/locations', CompanyLocationsController::class);
        Route::resource('/skills', \App\Http\Controllers\Api\Companies\CompaniesSkillsController::class);
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
        Route::resource('jobs', JobsController::class);
        Route::post('/process_application/{jobId}/{userId}', [JobApplicationController::class, 'processApplication']);
        Route::get('/applications', [JobApplicationController::class, 'index']);
        Route::post('/{id}/toggle', [JobApplicationController::class, 'toggle']);
        Route::get('/statistics', [JobApplicationController::class, 'getStatistics']);
    });

    Route::middleware(CheckAdminRole::class)->prefix('admin')->group(function () {
        Route::resource('/job-types', JobtypesControllerController::class);
        Route::resource('/countries', CountriesController::class);
        Route::resource('/cities', CitiesController::class);
        Route::resource('/company-types', CompanytypesController::class);
        Route::resource('/company-sizes', CompanysizesController::class);
        Route::resource('/companies', AdminCompaniesController::class);
        Route::get('/companies/count', [AdminCompaniesController::class, 'countCompaniesAndJobs']);
        Route::resource('/jobs', AdminJobsController::class);
    });
});
