<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\error;

class EmployerRegisterController extends Controller
{
    public function employerRegister(Request $request)
    {


        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'country_id' => $request->input('country_id'),
            'city_id' => $request->input('city_id'),
            'district_id' => $request->input('district_id'),
            'company_name' => $request->input('company_name'),
            'company_email' => $request->input('company_email'),
            'phone' => $request->input('phone'),
            'tax_code' => $request->input('tax_code'),
            'date_of_establishment' => $request->input('date_of_establishment'),
            'company_size_id' => $request->input('company_size_id'),
            'company_type_id' => $request->input('company_type_id'),
            'website' => $request->input('website'),
            'address' => $request->input('address')
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id', // Sửa lại exists cho district_id
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:15|regex:/^([0-9\s\-\+\(\)]*)$/',
            'tax_code' => 'nullable|string|max:50',
            'date_of_establishment' => 'nullable|date',
            'company_size_id' => 'required|exists:company_sizes,id',
            'company_type_id' => 'required|exists:company_types,id',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string|max:255',
        ], [
            'name.required' => 'Nhập tên người dùng.',
            'name.max' => 'Tên người dùng không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ Email của của bạn.',
            'email.email' => 'Địa chỉ Email phải đúng định dạng.',
            'email.unique' => 'Địa chỉ Email này đã được đăng ký.',
            'password.required' => 'Vui lòng điền mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'country_id.required' => 'Vui lòng lựa chọn quốc gia.',
            'country_id.exists' => 'Quốc gia không hợp lệ.',
            'city_id.required' => 'Vui lòng lựa chọn thành phố.',
            'city_id.exists' => 'Thành phố không hợp lệ.',
            'district_id.required' => 'Vui lòng lựa chọn quận/huyện.',
            'district_id.exists' => 'Quận/huyện không hợp lệ.', // Thông báo cho district_id
            'company_name.required' => 'Vui lòng nhập tên công ty.',
            'company_name.max' => 'Tên công ty không được vượt quá 255 ký tự.',
            'company_email.required' => 'Vui lòng nhập Email của công ty.',
            'company_email.email' => 'Địa chỉ Email của công ty phải đúng định dạng.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'tax_code.max' => 'Mã số thuế không được vượt quá 50 ký tự.',
            'date_of_establishment.date' => 'Ngày thành lập không đúng định dạng.',
            'company_size_id.required' => 'Vui lòng chọn kích thước công ty.',
            'company_size_id.exists' => 'Kích thước công ty không hợp lệ.',
            'company_type_id.required' => 'Vui lòng chọn loại công ty.',
            'company_type_id.exists' => 'Loại công ty không hợp lệ.',
            'website.url' => 'Địa chỉ website phải đúng định dạng URL.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validatedData = $validator->validated();


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validatedData = $validator->validated();

        try {
            // Sử dụng giao dịch để đảm bảo tất cả các thao tác xảy ra đồng thời hoặc không có thao tác nào được thực hiện nếu có lỗi
            DB::transaction(function () use ($validatedData) {
                $user = User::create([
                    'name'         => $validatedData['name'],
                    'email'        => $validatedData['email'],
                    'account_type' => Constant::user_level_employer,
                    'status'       => Constant::user_status_active,
                    'password'     => Hash::make($validatedData['password']),
                ]);

                $companyData = [
                    'users_id'   => $user->id,
                    'country_id' => $validatedData['country_id'],
                    'city_id'    => $validatedData['city_id'],
                    'district_id'    => $validatedData['district_id'],

                    'company_name' => $validatedData['company_name'],
                    'company_email' => $validatedData['company_email'],
                    'phone'       => $validatedData['phone'],
                    'website'     => $validatedData['website'],
                    'tax_code'     => $validatedData['tax_code'],
                    'date_of_establishment'     => $validatedData['date_of_establishment'],
                    'company_size_id'     => $validatedData['company_size_id'],
                    'company_type_id'     => $validatedData['company_type_id'],
                    'address'     => $validatedData['address'],
                    'approved'    => true,
                ];

                Company::create($companyData);

                $user->sendEmailVerificationNotification();

            });

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công. Vui lòng xác nhận Email của bạn.',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Registration failed.',
                'errors' => $e->getMessage(), // Nhảy ra lỗi chi tiết
            ], 500);
        }
    }
}
