<?php

namespace App\Http\Controllers\Api\Companies;

use App\Http\Controllers\Controller;
use App\Models\aboutme;
use App\Models\Company;
use App\Models\Country;
use App\Models\Location;
use App\Models\Location_Comapny;
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
        $companies = $user->companies()->with(['companytype', 'companysize', 'country', 'city', 'jobs', 'skills' => function ($query) use ($user) {
            $query->where('status', 1);
        }])->get();

        // Map the company data as before
        $companiesdata = $companies->map(function ($company) {
            $companyType = optional($company->companytype)->name;
            $companySize = optional($company->companysize)->name;
            $country = optional($company->country)->name;
            $city = optional($company->city)->name;
            $skills = optional($company->skills)->name;

            return [
                'id' => $company->id,
                'country' => $country,
                'city' => $city,
                'companyType' => $companyType,
                'companySize' => $companySize,
                'name' => $company->company_name,
                'phone' => $company->phone, // Thêm số điện thoại
                'company_email' => $company->company_email, // Thêm email công ty
                'working_days' => $company->working_days,
                'overtime_policy' => $company->overtime_policy,
                'website' => $company->website, // Sửa đúng tên trường từ 'webstie' thành 'website'
                'logo' => asset('uploads/images/' . $company->logo),
                'facebook' => $company->facebook,
                'tax_code' => $company->tax_code,
                'date_of_establishment' => $company->date_of_establishment,
                'banner' => $company->banner,
                'address' => $company->address,
                'description' => $company->description,
            ];

        });

        return response()->json([
            'success' => true,
            'message' => 'successfully.',
            'data' => $companiesdata
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
            'company_size_id', 'company_type_id', 'company_name', 'working_days',
            'overtime_policy', 'website', 'logo', 'facebook', 'description',
            'address', 'banner', 'phone', 'company_email', 'tax_code',
            'date_of_establishment',
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

        $companyType = optional($company->companytype)->name;
        $companySize = optional($company->companysize)->name;
        $country = optional($company->country)->name;
        $city = optional($company->city)->name;

        $companyData = [
            'id' => $company->id,
            'country' => $country,
            'city' => $city,
            'companyType' => $companyType,
            'companySize' => $companySize,
            'name' => $company->company_name, // Sử dụng 'company_name' thay vì 'name'
            'working_days' => $company->working_days, // Chỉnh từ 'Working_days' thành 'working_days'
            'overtime_policy' => $company->overtime_policy, // Chỉnh từ 'Overtime_policy' thành 'overtime_policy'
            'website' => $company->website, // Chỉnh từ 'webstie' thành 'website'
            'logo' => asset('uploads/images/' . $company->logo), // Đường dẫn đầy đủ tới logo
            'banner' => asset('uploads/images/' . $company->banner), // Đường dẫn đầy đủ tới banner
            'facebook' => $company->facebook,
            'address' => $company->address,
            'description' => $company->description,
            'phone' => $company->phone, // Thêm số điện thoại nếu có trong schema
            'company_email' => $company->company_email, // Thêm email công ty
            'tax_code' => $company->tax_code, // Thêm mã số thuế
            'date_of_establishment' => $company->date_of_establishment, // Thêm ngày thành lập
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
        // Eager load the relationships to avoid N+1 problem
        $company->load(['companytype', 'companysize', 'country', 'city']);

        // Use optional to avoid trying to get properties on a null object
        $companyType = optional($company->companytype)->name;
        $companySize = optional($company->companysize)->name;
        $country = optional($company->country)->name;
        $city = optional($company->city)->name;

        // Build the detailed company data
        $companyDetails = [
            'id' => $company->id,
            'name' => $company->name,
            'company_type' => $companyType,
            'company_size' => $companySize,
            'country' => $country,
            'city' => $city,
            'Working_days' => $company->Working_days,
            'Overtime_policy' => $company->Overtime_policy,
            'webstie' => $company->webstie,
            'logo' => asset('uploads/images/' . $company->logo),
            'banner' => asset('uploads/images/' . $company->banner), // Assuming the logo is stored in the 'storage' folder
            'facebook' => $company->facebook,
            'description' => $company->description,
            'address' => $company->address
        ];

        // Return the response with the company details
        return response()->json([
            'success' => true,
            'message' => 'Company details retrieved successfully.',
            'data' => $companyDetails
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


    public function indexShow(Request $request){
        $companies = Company::with(['companytype', 'companysize', 'country', 'city', 'jobs', 'skills' => function ($query) {
            $query->where('status', 1); // Lọc các công việc có trạng thái là 1
        }])->get();        $companiesdata = $companies->map(function ($company) {
            $companyType = optional($company->companytype)->name;
            $companySize = optional($company->companysize)->name;
            $country = optional($company->country)->name;
            $city = optional($company->city)->name;
            $job = optional($company->jobs);

            return [
                'id' => $company->id,
                'name' => $company->name,
                'country' => $country,
                'city' => $city,
                'logo' => asset('uploads/images/' . $company->logo), // Assuming the logo is stored in the 'storage' folder
                'jobs' => $job->count(),
            ];
        });
        return response()->json([
            'success' => true,
            'message' => 'successfully.',
            'data' => $companiesdata
        ], 200);
    }
}
