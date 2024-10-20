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
                'country' => $country,
                'city' => $city,
                'district' => $district,

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
                'is_hot' => $company->is_hot
            ];

        });

        return response()->json([
            'success' => true,
            'message' => 'successfully.',
            'data' => $companiesdata
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

        $companyData = [
            'id' => $company->id,
            'country' => $country,
            'city' => $city,
            'district' => $district,
            'companyType' => $companyType,
            'companySize' => $companySize,
            'name' => $company->company_name,
            'phone' => $company->phone,
            'company_email' => $company->company_email,
            'working_days' => $company->working_days,
            'overtime_policy' => $company->overtime_policy,
            'website' => $company->website,
            'logo' => asset('uploads/images/' . $company->logo),
            'facebook' => $company->facebook,
            'tax_code' => $company->tax_code,
            'date_of_establishment' => $company->date_of_establishment,
            'banner' => $company->banner,
            'address' => $company->address,
            'description' => $company->description,
            'is_hot' => $company->is_hot
        ];

        return response()->json([
            'success' => true,
            'message' => 'Công ty đã được đánh dấu là nổi bật.',
            'data' => $companyData,
            'status_code' => 200,
        ], 200);
    }


}
