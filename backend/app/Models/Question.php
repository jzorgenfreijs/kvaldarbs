<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'type',
        'question_text',
        'options',
        'correct_answers',
        'points',
        'order_index'
    ];
    
    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
    ];
    
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
