<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\User;
use App\Models\TestAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TestAssignmentController extends Controller
{
    public function enroll(Request $request, Test $test)
    {
        if ($test->enrollment_password) {
            $request->validate(['password' => 'required|string']);
            
            if (!Hash::check($request->password, $test->enrollment_password)) {
                abort(401, 'Incorrect enrollment password');
            }
        }

        $assignment = $test->assignments()->firstOrCreate([
            'student_id' => auth()->id()
        ]);

        return response()->json($assignment);
    }

    public function assign(Request $request, Test $test)
    {
        if ($test->creator_id !== auth()->id()) {
            abort(403, 'You can only enroll students in your own tests');
        }

        $validator = Validator::make($request->all(), [
            'emails' => 'required|array',
            'emails.*' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $enrolled = [];
        $alreadyEnrolled = [];

        foreach ($request->emails as $email) {
            $user = User::where('email', $email)->first();

            if ($user->id === auth()->id()) {
                continue;
            }

            $assignment = TestAssignment::firstOrCreate(
                [
                    'test_id' => $test->id,
                    'student_id' => $user->id
                ]
            );

            if ($assignment->wasRecentlyCreated) {
                $enrolled[] = $email;
            } else {
                $alreadyEnrolled[] = $email;
            }
        }

        return response()->json([
            'message' => 'Enrollment completed',
            'enrolled' => $enrolled,
            'already_enrolled' => $alreadyEnrolled
        ]);
    }
}