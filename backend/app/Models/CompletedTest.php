<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'completion_id');
    }
}