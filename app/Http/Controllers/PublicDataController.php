<?php

namespace App\Http\Controllers;

use App\Models\DesiredLevel;
use App\Models\EducationLevel;
use App\Models\EmploymentType;
use App\Models\ExperienceLevel;
use App\Models\Language;
use App\Models\Profession;
use Illuminate\Http\Request;

class PublicDataController extends Controller
{
    public function getLanguages()
    {
        try {
            // Lấy tất cả dữ liệu từ bảng languages
            $languages = Language::all();

            // Kiểm tra nếu không có dữ liệu
            if ($languages->isEmpty()) {
                return response()->json([
                    'message' => 'Không có ngôn ngữ nào được tìm thấy',
                    'data' => []
                ], 404); // HTTP status 404: Not Found
            }

            // Map dữ liệu languages
            $languagesData = $languages->map(function ($language) {
                return [
                    'id' => $language->id,
                    'name' => $language->name
                ];
            });

            // Trả về dữ liệu thành công
            return response()->json([
                'message' => 'Lấy danh sách ngôn ngữ thành công',
                'data' => $languagesData
            ], 200); // HTTP status 200: OK

        } catch (\Exception $e) {
            // Bắt lỗi và trả về thông báo lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách ngôn ngữ',
                'error' => $e->getMessage()
            ], 500); // HTTP status 500: Internal Server Error
        }
    }

    public function getProfessions()
    {
        try {
            // Lấy tất cả dữ liệu từ bảng professions
            $professions = Profession::all();

            // Kiểm tra nếu không có dữ liệu
            if ($professions->isEmpty()) {
                return response()->json([
                    'message' => 'Không có ngành nghề nào được tìm thấy',
                    'data' => []
                ], 404); // HTTP status 404: Not Found
            }

            // Map dữ liệu professions
            $professionsData = $professions->map(function ($profession) {
                return [
                    'id' => $profession->id,
                    'name' => $profession->name
                ];
            });

            // Trả về dữ liệu thành công
            return response()->json([
                'message' => 'Lấy danh sách ngành nghề thành công',
                'data' => $professionsData
            ], 200); // HTTP status 200: OK

        } catch (\Exception $e) {
            // Bắt lỗi và trả về thông báo lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách ngành nghề',
                'error' => $e->getMessage()
            ], 500); // HTTP status 500: Internal Server Error
        }
    }


    public function getEmploymentTypes()
    {
        try {
            // Lấy tất cả dữ liệu từ bảng employment_types
            $employmentTypes = EmploymentType::all();

            // Kiểm tra nếu không có dữ liệu
            if ($employmentTypes->isEmpty()) {
                return response()->json([
                    'message' => 'Không có loại hình công việc nào được tìm thấy',
                    'data' => []
                ], 404); // HTTP status 404: Not Found
            }

            // Map dữ liệu employment_types
            $employmentTypesData = $employmentTypes->map(function ($employmentType) {
                return [
                    'id' => $employmentType->id,
                    'name' => $employmentType->name
                ];
            });

            // Trả về dữ liệu thành công
            return response()->json([
                'message' => 'Lấy danh sách loại hình công việc thành công',
                'data' => $employmentTypesData
            ], 200); // HTTP status 200: OK

        } catch (\Exception $e) {
            // Bắt lỗi và trả về thông báo lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách loại hình công việc',
                'error' => $e->getMessage()
            ], 500); // HTTP status 500: Internal Server Error
        }
    }


    public function getEducationLevels()
    {
        try {
            // Lấy tất cả dữ liệu từ bảng education_levels
            $educationLevels = EducationLevel::all();

            // Kiểm tra nếu không có dữ liệu
            if ($educationLevels->isEmpty()) {
                return response()->json([
                    'message' => 'Không có trình độ học vấn nào được tìm thấy',
                    'data' => []
                ], 404); // HTTP status 404: Not Found
            }

            // Map dữ liệu education_levels
            $educationLevelsData = $educationLevels->map(function ($educationLevel) {
                return [
                    'id' => $educationLevel->id,
                    'name' => $educationLevel->name
                ];
            });

            // Trả về dữ liệu thành công
            return response()->json([
                'message' => 'Lấy danh sách trình độ học vấn thành công',
                'data' => $educationLevelsData
            ], 200); // HTTP status 200: OK

        } catch (\Exception $e) {
            // Bắt lỗi và trả về thông báo lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách trình độ học vấn',
                'error' => $e->getMessage()
            ], 500); // HTTP status 500: Internal Server Error
        }
    }


    public function getDesiredLevels()
    {
        try {
            // Lấy tất cả dữ liệu từ bảng desired_levels
            $desiredLevels = DesiredLevel::all();

            // Kiểm tra nếu không có dữ liệu
            if ($desiredLevels->isEmpty()) {
                return response()->json([
                    'message' => 'Không có cấp bậc mong muốn nào được tìm thấy',
                    'data' => []
                ], 404); // HTTP status 404: Not Found
            }

            // Map dữ liệu desired_levels
            $desiredLevelsData = $desiredLevels->map(function ($desiredLevel) {
                return [
                    'id' => $desiredLevel->id,
                    'name' => $desiredLevel->name
                ];
            });

            // Trả về dữ liệu thành công
            return response()->json([
                'message' => 'Lấy danh sách cấp bậc mong muốn thành công',
                'data' => $desiredLevelsData
            ], 200); // HTTP status 200: OK

        } catch (\Exception $e) {
            // Bắt lỗi và trả về thông báo lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách cấp bậc mong muốn',
                'error' => $e->getMessage()
            ], 500); // HTTP status 500: Internal Server Error
        }
    }

    public function getExperienceLevels()
    {
        try {
            // Lấy tất cả dữ liệu từ bảng experience_levels
            $experienceLevels = ExperienceLevel::all();

            // Kiểm tra nếu không có dữ liệu
            if ($experienceLevels->isEmpty()) {
                return response()->json([
                    'message' => 'Không có cấp độ kinh nghiệm nào được tìm thấy',
                    'data' => []
                ], 404); // HTTP status 404: Not Found
            }

            // Map dữ liệu experience_levels
            $experienceLevelsData = $experienceLevels->map(function ($experienceLevel) {
                return [
                    'id' => $experienceLevel->id,
                    'name' => $experienceLevel->name
                ];
            });

            // Trả về dữ liệu thành công
            return response()->json([
                'message' => 'Lấy danh sách cấp độ kinh nghiệm thành công',
                'data' => $experienceLevelsData
            ], 200); // HTTP status 200: OK

        } catch (\Exception $e) {
            // Bắt lỗi và trả về thông báo lỗi
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách cấp độ kinh nghiệm',
                'error' => $e->getMessage()
            ], 500); // HTTP status 500: Internal Server Error
        }
    }

}
