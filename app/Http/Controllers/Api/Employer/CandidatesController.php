<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Objective;
use Illuminate\Http\Request;

class CandidatesController extends Controller
{
    public function saveCandidate(Request $request, $id)
    {
        // Tìm ứng viên theo ID
        $candidate = Objective::findOrFail($id);
        $user = $request->user();

        // Kiểm tra xem hồ sơ ứng viên đã được lưu chưa
        if ($user->savedCandidates()->where('objective_id', $candidate->id)->exists()) {
            return response()->json(['message' => 'Candidate is already saved'], 200);
        } else {
            // Thêm hồ sơ ứng viên vào danh sách yêu thích
            $user->savedCandidates()->syncWithoutDetaching([$candidate->id]);
            return response()->json([
                'success' => true,
                'message' => 'Candidate saved successfully',
                'status_code' => 200
            ], 200);
        }
    }


    /**
     * Unsave Candidate from saved list
     */
    public function unsaveCandidate(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $request->user();
        $user->savedCandidates()->detach($candidate->id);

        return response()->json([
            'success' => true,
            'message' => 'Candidate removed from saved list',
            'status_code' => 200,
        ], 200);
    }
}
