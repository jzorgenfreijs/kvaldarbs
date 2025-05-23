<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'student_id',
        'status'
    ];
    
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
    
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
