<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\CompletedTest;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompletedTestController extends Controller
{
    public function getCompletedStudents(Test $test)
    {
        if ($test->creator_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $completedTests = CompletedTest::where('test_id', $test->id)
            ->with('student:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'test' => $test->only('id', 'title', 'description'),
            'completed_tests' => $completedTests
        ]);
    }

    public function getStudentAnswers(Test $test, $userId)
    {
        if ($test->creator_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $questions = Question::where('test_id', $test->id)
            ->orderBy('order_index')
            ->get();

        $answers = Answer::where('test_id', $test->id)
            ->where('user_id', $userId)
            ->with('question')
            ->get()
            ->keyBy('question_id');

        $response = $questions->map(function ($question) use ($answers) {
            $answer = $answers->get($question->id);
            $isTextQuestion = $question->type === 'text';
            
            $studentAnswerText = null;
            $studentAnswerIndexes = null;
            $correctAnswers = null;
            $correctIndexes = null;
            $options = null;

            if (!$isTextQuestion) {
                $options = is_array($question->options) 
                    ? $question->options 
                    : json_decode($question->options, true) ?? [];
                
                $correctIndexes = is_array($question->correct_answers)
                    ? $question->correct_answers
                    : json_decode($question->correct_answers, true) ?? [];

                $studentAnswerIndexes = $answer 
                    ? (is_array($answer->response) 
                        ? $answer->response 
                        : json_decode($answer->response, true) ?? [])
                    : [];

                $correctAnswers = array_map(function($index) use ($options) {
                    return $options[$index] ?? null;
                }, $correctIndexes);

                $studentAnswerText = array_map(function($index) use ($options) {
                    return $options[$index] ?? null;
                }, $studentAnswerIndexes);

                if ($question->type === 'single_choice' || $question->type === 'true_false') {
                    $studentAnswerText = $studentAnswerText[0] ?? null;
                    $correctAnswers = $correctAnswers[0] ?? null;
                }
            } else {
                $studentAnswerText = $answer ? $answer->response : null;
            }

            return [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->type,
                'question_points' => $question->points,
                'options' => $options,
                'student_answer_index' => $studentAnswerIndexes,
                'student_answer_text' => $studentAnswerText,
                'correct_answer_index' => $correctIndexes,
                'correct_answer_text' => $correctAnswers,
                'is_correct' => $isTextQuestion ? null : ($answer ? $this->isAnswerCorrect($question, $answer) : null),
                'needs_manual_grading' => $isTextQuestion
            ];
        });

        $grade = Grade::where('test_id', $test->id)
            ->where('user_id', $userId)
            ->first();

        return response()->json([
            'test' => [
                'id' => $test->id,
                'title' => $test->title
            ],
            'student_id' => $userId,
            'answers' => $response,
            'grade' => $grade ? $grade->score : null,
            'max_score' => $questions->sum('points')
        ]);
    }

    private function isAnswerCorrect(Question $question, Answer $answer)
    {
        if ($question->type === 'text') {
            return null;
        }

        $studentAnswers = is_array($answer->response) 
            ? $answer->response 
            : json_decode($answer->response, true) ?? [];
        
        $correctAnswers = is_array($question->correct_answers)
            ? $question->correct_answers
            : json_decode($question->correct_answers, true) ?? [];

        if ($question->type === 'single_choice' || $question->type === 'true_false') {
            return isset($studentAnswers[0]) && $studentAnswers[0] == $correctAnswers[0];
        }

        sort($studentAnswers);
        sort($correctAnswers);
        return $studentAnswers == $correctAnswers;
    }

    public function saveGrade(Request $request, Test $test, $userId)
    {
        $request->validate([
            'score' => 'required|numeric|min:0'
        ]);

        if ($test->creator_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Grade::updateOrCreate(
            [
                'test_id' => $test->id,
                'user_id' => $userId
            ],
            ['score' => $request->score]
        );

        return response()->json(['message' => 'Grade saved successfully']);
    }

    public function getTestGrades(Test $test)
    {
        if ($test->creator_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $grades = Grade::where('test_id', $test->id)
            ->with('student:id,name')
            ->get(['id', 'test_id', 'user_id', 'score', 'created_at', 'updated_at']);

        return response()->json($grades);
    }
}