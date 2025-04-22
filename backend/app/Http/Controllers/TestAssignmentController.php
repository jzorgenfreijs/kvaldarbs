<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestAssignmentController extends Controller
{
    public function enroll(Request $request, Test $test)
    {
        if (!$test->is_public) {
            abort(403, 'This test is not publicly available');
        }

        if ($test->enrollment_password) {
            $request->validate(['password' => 'required|string']);
            
            if (!Hash::check($request->password, $test->enrollment_password)) {
                abort(401, 'Incorrect enrollment password');
            }
        }

        $assignment = $test->assignments()->firstOrCreate([
            'student_id' => auth()->id()
        ], [
            'status' => 'assigned'
        ]);

        return response()->json($assignment);
    }
}
