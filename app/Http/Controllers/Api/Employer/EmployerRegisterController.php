<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployerRegisterController extends Controller
{
    public function employerRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:255',
            'country_id' => 'required',
            'city_id' => 'required',
        ], [
            'name.required' => 'Vui lòng nhập tên công ty.',
            'name.max' => 'Tên công ty không được vượt quá 225 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ Email của công ty.',
            'email.email' => 'Địa chỉ Email phải đúng định dạng.',
            'email.unique' => 'Địa chỉ Email của bạn đăng ký đã được đăng ký.',
            'password.required' => 'Vui lòng điền mật khẩu.',
            'password.min' => 'Vui lòng nhập mật khẩu lớn hơn 8 ký tự.',
            'country_id.required' => 'Vui lòng lựa chọn quốc gia.',
            'city_id.required' => 'Vui lòng lựa chọn thành phố.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validatedData = $validator->validated();

        try {
            $user = User::create([
                'name'         => $validatedData['name'],
                'email'        => $validatedData['email'],
                'account_type' => Constant::user_level_employer,
                'status'       => Constant::user_status_active,
                'password'     => Hash::make($validatedData['password']),
            ]);

            Company::create([
                'users_id'   => $user->id,
                'country_id' => $validatedData['country_id'],
                'city_id'    => $validatedData['city_id'],
                'name'       => $validatedData['name'], // Assuming company name should be the same as user's name.
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $userData, // Optionally, include user information
                'status_code' => 200,
                'message' => 'Registration successful.'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Registration failed.'], 500);
        }
    }}
