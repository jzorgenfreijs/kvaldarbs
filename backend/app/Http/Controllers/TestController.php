<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Answer;
use App\Models\Question;
use App\Models\CompletedTest;
use App\Models\TestAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'teacher') {
            $tests = Test::withCount('questions')
                ->where('creator_id', $user->id)
                ->latest()
                ->get()
                ->map(function ($test) {
                    return [
                        'id' => $test->id,
                        'title' => $test->title,
                        'description' => $test->description,
                        'is_public' => $test->is_public,
                        'question_count' => $test->questions_count,
                        'enrollment_status' => 'creator',
                        'created_at' => $test->created_at
                    ];
                });
        } else {
            $tests = TestAssignment::where('student_id', $user->id)
                ->whereDoesntHave('test.completedTests', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['test' => function($query) {
                    $query->withCount('questions');
                }])
                ->latest()
                ->get()
                ->map(function ($assignment) {
                    return [
                        'id' => $assignment->test->id,
                        'title' => $assignment->test->title,
                        'description' => $assignment->test->description,
                        'is_public' => $assignment->test->is_public,
                        'question_count' => $assignment->test->questions_count,
                        'enrollment_status' => 'enrolled',
                        'created_at' => $assignment->test->created_at
                    ];
                });
        }

        return response()->json($tests);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'enrollment_password' => 'nullable|string|min:3',
            'questions' => 'required|array',
            'questions.*.type' => 'required|in:single_choice,multiple_choice,true_false,text',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answers' => 'nullable|array',
            'questions.*.points' => 'integer|min:1',
        ]);

        $test = Test::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
            'enrollment_password' => isset($validated['enrollment_password']) 
                ? bcrypt($validated['enrollment_password']) 
                : null,
            'creator_id' => auth()->id()
        ]);

        foreach ($validated['questions'] as $questionData) {
            $test->questions()->create([
                'type' => $questionData['type'],
                'question_text' => $questionData['question_text'],
                'options' => $questionData['options'] ?? null,
                'correct_answers' => $questionData['correct_answers'] ?? null,
                'points' => $questionData['points'] ?? 1,
                'order_index' => $test->questions()->count() + 1
            ]);
        }

        return response()->json($test->load('questions'), 201);
    }

    public function publicIndex(Request $request)
    {
        $query = Test::withCount('questions')
            ->where('is_public', true)
            ->latest();
        
        if ($request->has('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }
        
        $tests = $query->get()->map(function ($test) {
            return [
                'id' => $test->id,
                'title' => $test->title,
                'description' => $test->description,
                'is_public' => $test->is_public,
                'question_count' => $test->questions_count,
                'created_at' => $test->created_at,
                'has_password' => !empty($test->enrollment_password)
            ];
        });
        
        return response()->json($tests);
    }

    public function getQuestions($test_id)
    {
        $test = Test::select('title', 'description')
                ->findOrFail($test_id);
        
        $questions = Question::where('test_id', $test_id)
            ->orderBy('order_index')
            ->get(['id', 'type', 'question_text', 'options', 'order_index']);
        
        return response()->json([
            'test_title' => $test->title,
            'test_description' => $test->description,
            'questions' => $questions
        ]);
    }
    
    public function submitAnswers(Request $request, $test_id) {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required',
        ]);

        foreach ($validated['answers'] as $answer) {
            $response = is_array($answer['answer']) 
                ? json_encode($answer['answer'])
                : $answer['answer'];

            Answer::create([
                'user_id' => auth()->id(),
                'question_id' => $answer['question_id'],
                'test_id' => $test_id,
                'response' => $response,
            ]);
        }

        CompletedTest::firstOrCreate([
            'user_id' => auth()->id(),
            'test_id' => $test_id
        ]);

        return response()->json(['message' => 'Answers submitted!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
