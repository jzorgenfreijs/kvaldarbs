<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Test;
use App\Models\CompletedTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Get completed tests with grades for current student
     */
    public function getCompletedTestsWithGrades()
    {
        $user = Auth::user();

        $completedTests = CompletedTest::where('user_id', $user->id)
            ->with('test')
            ->get()
            ->map(function ($completedTest) {
                $test = $completedTest->test;

                $grade = Grade::where('test_id', $completedTest->test_id)
                    ->where('user_id', $completedTest->user_id)
                    ->first();

                return [
                    'id' => $test->id,
                    'title' => $test->title,
                    'description' => $test->description,
                    'completed_at' => $completedTest->created_at,
                    'grade_percentage' => $grade && $test->questions()->sum('points') > 0
                        ? round(($grade->score / $test->questions()->sum('points')) * 100)
                        : null,
                ];
            });

        return response()->json($completedTests);
    }
}
