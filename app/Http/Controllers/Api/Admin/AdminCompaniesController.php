<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminCompaniesController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách công ty, có thể phân trang
        $companies = Company::paginate(10);

        $companiesdata = $companies->map(function ($company) {
            $companyType = optional($company->companytype)->name;
            $companySize = optional($company->companysize)->name;
            $country = optional($company->country)->name;
            $city = optional($company->city)->name;
            $district = optional($company->district)->name;

            return [
                'id' => $company->id,
                'name' => $company->company_name,
                'phone' => $company->phone, // Thêm số điện thoại
                'company_email' => $company->company_email, // Thêm email công ty
                'logo' => asset('uploads/images/' . $company->logo),
                'city' => $city,
                'is_hot' => $company->is_hot
            ];

        });

        return response()->json([
            'success' => true,
            'message' => 'successfully.',
            'data' => $companiesdata
        ], 200);
    }

    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Công ty không tồn tại.',
            ], 404);
        }

        // Retrieve optional related data
        $companyType = optional($company->companytype)->name;
        $companySize = optional($company->companysize)->name;
        $country = optional($company->country)->name;
        $city = optional($company->city)->name;
        $district = optional($company->district)->name;

        // Structured company data
        $companyDetails = [
            'id' => $company->id,
            'name' => $company->company_name,
            'company_type' => $companyType,
            'company_size' => $companySize,
            'country' => $country,
            'city' => $city,
            'district' => $district,
            'phone' => $company->phone,
            'company_email' => $company->company_email,
            'tax_code' => $company->tax_code,
            'date_of_establishment' => $company->date_of_establishment,
            'working_days' => $company->working_days,
            'overtime_policy' => $company->overtime_policy,
            'website' => $company->website,
            'facebook' => $company->facebook,
            'youtube' => $company->youtube,
            'linked' => $company->linked,
            'address' => $company->address,
            'latitude' => $company->latitude,
            'longitude' => $company->longitude,
            'description' => $company->description,
            'is_hot' => $company->is_hot,
            'logo' => asset('uploads/images/' . $company->logo),
            'banner' => asset('uploads/images/' . $company->banner),

        ];

        return response()->json([
            'success' => true,
            'message' => 'successfully.',
            'data' => $companyDetails
        ], 200);
    }


    public function markAsHot($companyId)
    {
        $company = Company::find($companyId);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy công ty.',
                'status_code' => 404,
            ], 404);
        }

        // Đánh dấu công ty là "nổi bật"
        $company->is_hot = true;
        $company->save();

        // Chuẩn bị dữ liệu trả về
        $companyType = optional($company->companytype)->name;
        $companySize = optional($company->companysize)->name;
        $country = optional($company->country)->name;
        $city = optional($company->city)->name;
        $district = optional($company->district)->name;

        $companyDetails = [
            'id' => $company->id,
            'name' => $company->company_name,
            'company_type' => $companyType,
            'company_size' => $companySize,
            'country' => $country,
            'city' => $city,
            'district' => $district,
            'phone' => $company->phone,
            'company_email' => $company->company_email,
            'tax_code' => $company->tax_code,
            'date_of_establishment' => $company->date_of_establishment,
            'working_days' => $company->working_days,
            'overtime_policy' => $company->overtime_policy,
            'website' => $company->website,
            'facebook' => $company->facebook,
            'youtube' => $company->youtube,
            'linked' => $company->linked,
            'address' => $company->address,
            'latitude' => $company->latitude,
            'longitude' => $company->longitude,
            'description' => $company->description,
            'is_hot' => $company->is_hot,
            'logo' => asset('uploads/images/' . $company->logo),
            'banner' => asset('uploads/images/' . $company->banner),

        ];

        return response()->json([
            'success' => true,
            'message' => 'Công ty đã được đánh dấu là nổi bật.',
            'data' => $companyDetails,
            'status_code' => 200,
        ], 200);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully.',
            'status_code' => 200
        ]);

    }


}
