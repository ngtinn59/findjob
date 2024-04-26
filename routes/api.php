<?php

use App\Http\Controllers\Api\Admin\CitiesController;
use App\Http\Controllers\Api\Admin\CompanysizesController;
use App\Http\Controllers\Api\Admin\CompanytypesController;
use App\Http\Controllers\Api\Admin\CountriesController;
use App\Http\Controllers\Api\Admin\Job_typesController;
use App\Http\Controllers\Api\Admin\JobtypesControllerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Companies\CompaniesController;
use App\Http\Controllers\Api\Companies\CompanyLocationsController;
use App\Http\Controllers\Api\Companies\JobsController;
use App\Http\Controllers\Api\Employer\EmployerRegisterController;
use App\Http\Controllers\Api\Job\JobApplicationController;
use App\Http\Controllers\Api\Resume\AboutmeController;
use App\Http\Controllers\Api\Resume\AwardsController;
use App\Http\Controllers\Api\Resume\CertificatesController;
use App\Http\Controllers\Api\Resume\CvsController;
use App\Http\Controllers\Api\Resume\EducationController;
use App\Http\Controllers\Api\Resume\ExperiencesController;
use App\Http\Controllers\Api\Resume\GetResumeController;
use App\Http\Controllers\Api\Resume\profilesController;
use App\Http\Controllers\Api\Resume\ProjectsController;
use App\Http\Controllers\Api\Resume\SkillsController;
use App\Http\Controllers\Api\User\UserJobController;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Support\Facades\Route;

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
Route::get('/countries', [CountriesController::class, 'index']);
Route::get('/cities', [CitiesController::class, 'index']);
Route::get('/jobtypes', [JobtypesControllerController::class, 'index']);
Route::get('/companyType', [CompanytypesController::class, 'index']);
Route::get('/companySize', [CompanysizesController::class, 'index']);


//User Jobs
Route::get('/', [JobsController::class, 'indexShow']);
Route::get('/jobs/{job}', [JobsController::class, 'showJob']);
Route::get('/search', [JobsController::class, 'search']);
Route::get('/companies1', [CompaniesController::class, 'indexShow']);
Route::get('/companies1/{company}', [CompaniesController::class, 'show']);

//Auth
Route::post('employer/register', [EmployerRegisterController::class, 'employerRegister']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('profile', ProfilesController::class);
    Route::resource('profiles/educations', EducationController::class);
    Route::resource('profiles/skills', SkillsController::class);
    Route::resource('profiles/aboutMe', AboutmeController::class);
    Route::resource('profiles/certificates', CertificatesController::class);
    Route::resource('profiles/awards', AwardsController::class);
    Route::resource('profiles/projects', ProjectsController::class);
    Route::resource('profiles/getResume', GetResumeController::class);
    Route::resource('profiles/experiences', ExperiencesController::class);
    Route::post('/upload-cv', [CvsController::class, 'upload']);
    Route::resource('cvs', CvsController::class);


    // Route cho việc đặt CV mặc định
    Route::get('/default-cv',  [CvsController::class, 'getDefaultCv']);
    Route::put('/cvs/{cv}/set-default', [CvsController::class, 'setDefault'])->name('cvs.set-default');
    //Company
    Route::resource('companies', CompaniesController::class);
    Route::resource('companies/location', CompanyLocationsController::class);
    Route::resource('company/skills', \App\Http\Controllers\Api\Companies\CompaniesSkillsController::class);

    //Apply

    Route::post('/jobs/{id}/apply', [JobsController::class, 'apply']);
    Route::get('/viewAppliedJobs', [JobsController::class, 'applicant']);

    Route::middleware(CheckUserRole::class)->group(function () {
        Route::resource('job', JobsController::class);
        Route::post('/processApplication/{jobId}/{userId}', [JobApplicationController::class, 'processApplication']);
        Route::get('/applications', [JobApplicationController::class, 'index']);
        Route::post('/{id}/toggle', [JobApplicationController::class, 'toggle']);
        Route::get('/getStatistics', [JobApplicationController::class, 'getStatistics']);
    });

    //favorites Job User
    Route::post('/favorites/{id}/save', [JobsController::class, 'saveJob']);
    Route::post('/favorites/{id}/unsave', [JobsController::class, 'unsaveJob']);
    Route::get('/favorites/saved-jobs', [JobsController::class, 'savedJobs']);
    Route::get('/appliedJobs', [JobsController::class, 'appliedJobs']);


    //Goi y cong viec
    Route::get('/suggest-jobs', [JobsController::class, 'suggestJobs']);



    Route::middleware(\App\Http\Middleware\CheckAdminRole::class)->group(function () {
        Route::resource('Admin/jobtypes', JobtypesControllerController::class);
        Route::resource('Admin/country', CountriesController::class);
        Route::resource('Admin/cties', CitiesController::class);

        Route::resource('Admin/companyType', CompanytypesController::class);
        Route::resource('Admin/companySize', CompanysizesController::class);
        Route::resource('Admin/companies', \App\Http\Controllers\Api\Admin\CompaniesController::class);
        Route::get('/Admin/companies1/count', [\App\Http\Controllers\Api\Admin\CompaniesController::class, 'countCompaniesAndJobs']);

        Route::resource('Admin/jobs', \App\Http\Controllers\Api\Admin\JobsController::class);


    });
    Route::Delete('logout', [AuthController::class, 'logout']);
});
