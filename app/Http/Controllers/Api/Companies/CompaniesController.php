<?php

namespace App\Http\Controllers\Api\Companies;

use App\Http\Controllers\Controller;
use App\Models\aboutme;
use App\Models\Company;
use App\Models\Country;
use App\Models\Profile;
use App\Models\User;
use App\Utillities\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Retrieve companies associated with the user
        $companies = $user->companies()->with([
            'companyType', 'companySize', 'country', 'city', 'district', 'jobs',
            'skills' => function ($query) use ($user) {
                $query->where('status', 1);
            }
        ])->get();

        // Map the company data
        $companiesData = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'country' => [
                    'id' => $company->country->id ?? null,
                    'name' => $company->country->name ?? null,
                ],
                'city' => [
                    'id' => $company->city->id ?? null,
                    'name' => $company->city->name ?? null,
                ],
                'district' => [
                    'id' => $company->district->id ?? null,
                    'name' => $company->district->name ?? null,
                ],
                'companyType' => [
                    'id' => $company->companyType->id ?? null,
                    'name' => $company->companyType->name ?? null,
                ],
                'companySize' => [
                    'id' => $company->companySize->id ?? null,
                    'name' => $company->companySize->name ?? null,
                ],
                'name' => $company->company_name,
                'phone' => $company->phone,
                'company_email' => $company->company_email,
                'tax_code' => $company->tax_code,
                'date_of_establishment' => \Carbon\Carbon::parse($company->date_of_establishment)->format('Y-m-d'), // Format date
                'working_days' => $company->working_days,
                'overtime_policy' => $company->overtime_policy,
                'website' => $company->website,
                'facebook' => $company->facebook,
                'youtube' => $company->youtube,
                'linked' => $company->linked,
                'logo' => $company->logo ? asset('uploads/images/' . $company->logo) : null, // Full path to logo if exists
                'banner' => $company->banner ? asset('uploads/images/' . $company->banner) : null, // Full path to banner if exists
                'address' => $company->address,
                'latitude' => $company->latitude,
                'longitude' => $company->longitude,
                'description' => $company->description,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved companies.',
            'data' => $companiesData
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'company_size_id' => 'required',
//            'company_type_id' => 'required',
//            'name' => 'required',
//            'Working_days' => 'required',
//            'Overtime_policy' => 'required',
//            'webstie' => 'required',
////            'logo' => 'required',
//            'facebook' => 'required',
//            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $request->only([
            'country_id', 'city_id', 'district_id', 'company_size_id',
            'company_type_id', 'company_name', 'phone', 'tax_code', 'date_of_establishment',
            'working_days', 'overtime_policy', 'website', 'facebook', 'youtube',
            'linked', 'logo', 'banner', 'address', 'latitude', 'longitude', 'description'
        ]);


        // Upload logo
        $file = $request->file('logo');
        $path = public_path('uploads/images');
        $file_name = Common::uploadFile($file, $path);
        $data['logo'] = $file_name;

		// Upload banner
        $bannerFile = $request->file('banner'); // Nhận file banner từ request
        if ($bannerFile) { // Kiểm tra nếu file banner có tồn tại
            $bannerFileName = Common::uploadFile($bannerFile, $path); // Upload banner
            $data['banner'] = $bannerFileName; // Lưu tên file banner vào mảng dữ liệu
        }

        $company = Company::where('users_id', auth()->user()->id)->first();

        if ($company) {
            $company->update($data);
        } else {
            $data['users_id'] = auth()->user()->id;
            $company = Company::create($data);
        }

        // Prepare the company data for the response
        $companyType = optional($company->companyType)->name; // Ensure correct naming convention
        $companySize = optional($company->companySize)->name; // Ensure correct naming convention
        $country = optional($company->country)->name;
        $city = optional($company->city)->name;
        $district = optional($company->district)->name; // Corrected typo 'ditrist' to 'district'

        $companyData = [
            'id' => $company->id,
            'country' => [
                'id' => $company->country->id ?? null,
                'name' => $company->country->name ?? null,
            ],
            'city' => [
                'id' => $company->city->id ?? null,
                'name' => $company->city->name ?? null,
            ],
            'district' => [
                'id' => $company->district->id ?? null,
                'name' => $company->district->name ?? null,
            ],
            'companyType' => [
                'id' => $company->companyType->id ?? null,
                'name' => $company->companyType->name ?? null,
            ],
            'companySize' => [
                'id' => $company->companySize->id ?? null,
                'name' => $company->companySize->name ?? null,
            ],
            'name' => $company->company_name,
            'phone' => $company->phone,
            'company_email' => $company->company_email,
            'tax_code' => $company->tax_code,
            'date_of_establishment' => \Carbon\Carbon::parse($company->date_of_establishment)->format('Y-m-d'), // Format date
            'working_days' => $company->working_days,
            'overtime_policy' => $company->overtime_policy,
            'website' => $company->website,
            'facebook' => $company->facebook,
            'youtube' => $company->youtube,
            'linked' => $company->linked,
            'logo' => $company->logo ? asset('uploads/images/' . $company->logo) : null, // Full path to logo if exists
            'banner' => $company->banner ? asset('uploads/images/' . $company->banner) : null, // Full path to banner if exists
            'address' => $company->address,
            'latitude' => $company->latitude,
            'longitude' => $company->longitude,
            'description' => $company->description,
        ];


        return response()->json([
            'success'   => true,
            'message'   => "success update",
            "data" => $companyData,
            'status_code'    => 200
        ]);
    }
    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {

        $company->load(['companyType', 'companySize', 'country', 'city', 'district']);
        // Use optional to avoid trying to get properties on a null object
        $companyType = optional($company->companyType)->name;
        $companySize = optional($company->companySize)->name;
        $country = optional($company->country)->name;
        $city = optional($company->city)->name;
        $district = optional($company->district)->name;


        // Build the detailed company data
        $companyData = [
            'id' => $company->id,
            'country' => $country,
            'city' => $city,
            'district' => $district,
            'companySize' => $companySize,
            'companyType' => $companyType,
            'name' => $company->company_name,
            'phone' => $company->phone,
            'company_email' => $company->company_email,
            'tax_code' => $company->tax_code,
            'date_of_establishment' => \Carbon\Carbon::parse($company->date_of_establishment)->format('Y-m-d'),

            'working_days' => $company->working_days,
            'overtime_policy' => $company->overtime_policy,
            'website' => $company->website,
            'facebook' => $company->facebook,
            'youtube' => $company->youtube,
            'linked' => $company->linked,
            'logo' => asset('uploads/images/' . $company->logo), // Đường dẫn đầy đủ tới logo
            'banner' => asset('uploads/images/' . $company->banner), // Đường dẫn đầy đủ tới banner
            'address' => $company->address,
            'latitude' => $company->latitude,
            'longitude' => $company->longitude,
            'description' => $company->description,
        ];

        // Return the response with the company details
        return response()->json([
            'success' => true,
            'message' => 'Company details retrieved successfully.',
            'data' => $companyData
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }

    public function logo(Request $request)
    {
        $user_id = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,jpg,png|max:1024',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;

            // Retrieve the old logo filename
            $oldLogo = Company::where('users_id', $user_id)->value('logo');

            // Delete the old logo file
            if(is_file(public_path('uploads/logo/' . $oldLogo))){
                unlink(public_path('uploads/logo/' . $oldLogo));
            }

            $file->move('uploads/logo/', $filename);

            // Update the logo filename in the database
            Company::where('users_id', $user_id)->update([
                'logo' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logo updated successfully.',
                'logo_filename' => $filename
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No logo file provided.',
            ], 400);
        }
    }

    public function banner(Request $request)
    {
        $user_id = auth()->user()->id;

        $validator = Validator::make($request->all(), [
            'banner' => 'required|image|mimes:jpeg,jpg,png|max:1024',
        ], [
            'banner.required' => 'Banner là bắt buộc.',
            'banner.image' => 'Banner phải là hình ảnh.',
            'banner.mimes' => 'Banner phải là định dạng jpeg, jpg hoặc png.',
            'banner.max' => 'Banner không được vượt quá 1MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;

            // Retrieve the old logo filename
            $oldBanner = Company::where('users_id', $user_id)->value('banner');

            // Delete the old logo file
            if (is_file(public_path('uploads/banner/' . $oldBanner))) {
                unlink(public_path('uploads/banner/' . $oldBanner));
            }

            // Move the new file to the uploads directory
            $file->move(public_path('uploads/banner/'), $filename);

            // Update the logo filename in the database
            Company::where('users_id', $user_id)->update([
                'banner' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trang bìa được cập nhật thành công.',
                'logo_filename' => $filename
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không có file logo cung cấp.',
            ], 400);
        }
    }


    public function indexShow(Request $request)
    {
        // Lấy số lượng kết quả mỗi trang từ request, mặc định là 10
        $perPage = $request->input('per_page', 10);

        // Thực hiện truy vấn với phân trang
        $companies = Company::with([
            'companytype',
            'companysize',
            'country',
            'city',
            'jobs',
            'skills' => function ($query) {
                $query->where('status', 1); // Lọc các công việc có trạng thái là 1
            }
        ])->paginate($perPage); // Sử dụng phân trang với số lượng mỗi trang là $perPage

        // Xử lý dữ liệu đầu ra
        $companiesdata = $companies->map(function ($company) {
            $companyType = optional($company->companytype)->name;
            $companySize = optional($company->companysize)->name;
            $country = optional($company->country)->name;
            $city = optional($company->city)->name;
            $job = optional($company->jobs);

            return [
                'id' => $company->id,
                'name' => $company->company_name,
                'companyType' => [
                    'id' => $company->companyType->id ?? null,
                    'name' => $company->companyType->name ?? null,
                ],
                'companySize' => [
                    'id' => $company->companySize->id ?? null,
                    'name' => $company->companySize->name ?? null,
                ],
                'logo' => asset('uploads/images/' . $company->logo),
                'banner' => asset('uploads/images/' . $company->banner),
                'country' => $country,
                'city' => $city,
                'jobs' => $job->count(),
            ];
        });

        // Trả về dữ liệu với thông tin phân trang
        return response()->json([
            'success' => true,
            'message' => 'successfully.',
            'data' => $companiesdata,
            'pagination' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'total' => $companies->total(),
                'per_page' => $companies->perPage(),
                'next_page_url' => $companies->nextPageUrl(),
                'previous_page_url' => $companies->previousPageUrl(),
            ]
        ], 200);
    }



    public function detailShow(Company $company)
    {
        // Tải thông tin chi tiết của công ty bao gồm các quan hệ
        $company->load(['companytype', 'companysize', 'country', 'city', 'district', 'jobs']);
        // Sử dụng optional để tránh lỗi khi đối tượng null
        $companyType = optional($company->companyType)->name;
        $companySize = optional($company->companySize)->name;
        $country = optional($company->country)->name;
        $city = optional($company->city)->name;
        $district = optional($company->district)->name;

        // Đếm số lượng công việc của công ty
        $jobsWithStatusActive = $company->jobs()->where('status', 1)->get()->map(function($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'featured' => $job->featured,
                'is_hot' => ($job->views > 100) ? 1 : 0,
                'company' => [
                    'id' => $job->company->id,
                    'name' => $job->company->company_name,
                    'logo' => $job->company->logo ? asset('uploads/images/' . $job->company->logo) : null,
                ],
                'salary' => [
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to
                ],
                'city' => [
                    'id' => $job->city->id,
                    'name' => $job->city->name,
                ],
                'last_date' => \Carbon\Carbon::parse($job->last_date)->format('d-m-Y'),
            ];
        });        // Tạo dữ liệu chi tiết công ty
        $companyDetails = [
            'id' => $company->id,
            'country' => [
                'id' => $company->country->id ?? null,
                'name' => $company->country->name ?? null,
            ],
            'city' => [
                'id' => $company->city->id ?? null,
                'name' => $company->city->name ?? null,
            ],
            'district' => [
                'id' => $company->district->id ?? null,
                'name' => $company->district->name ?? null,
            ],
            'companyType' => [
                'id' => $company->companyType->id ?? null,
                'name' => $company->companyType->name ?? null,
            ],
            'companySize' => [
                'id' => $company->companySize->id ?? null,
                'name' => $company->companySize->name ?? null,
            ],
            'name' => $company->company_name,
            'phone' => $company->phone,
            'company_email' => $company->company_email,
            'tax_code' => $company->tax_code,
            'date_of_establishment' => \Carbon\Carbon::parse($company->date_of_establishment)->format('Y-m-d'), // Format date
            'working_days' => $company->working_days,
            'overtime_policy' => $company->overtime_policy,
            'website' => $company->website,
            'facebook' => $company->facebook,
            'youtube' => $company->youtube,
            'linked' => $company->linked,
            'logo' => $company->logo ? asset('uploads/images/' . $company->logo) : null, // Full path to logo if exists
            'banner' => $company->banner ? asset('uploads/images/' . $company->banner) : null, // Full path to banner if exists
            'address' => $company->address,
            'latitude' => $company->latitude,
            'longitude' => $company->longitude,
            'description' => $company->description,
            'jobs' => $jobsWithStatusActive,

        ];

        // Trả về thông tin công ty và số lượng công việc
        return response()->json([
            'success' => true,
            'message' => 'Company details retrieved successfully.',
            'data' => $companyDetails
        ], 200);
    }

    public function indexFeaturedCompanies(Request $request)
    {
        $companies = Company::where('is_hot', 1);

        $results = $companies->with(['city'])
        ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => [
                'featured_companies' => $results->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'company_name' => $company->company_name,
                        'logo' => $company->logo ? asset('uploads/images/' . $company->logo) : null,
                        'is_hot' => $company->is_hot,
                        'city' => [
                            'id' => $company->city->id,
                            'name' => $company->city->name,
                        ],
                        'created_at' => \Carbon\Carbon::parse($company->created_at)->format('d-m-Y'),
                    ];
                }),
            ],
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'total' => $results->total(),
        ]);
    }

}

