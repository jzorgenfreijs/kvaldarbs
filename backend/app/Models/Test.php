<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_public',
        'enrollment_password',
        'creator_id'
    ];
    
    protected $casts = [
        'is_public' => 'boolean',
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    public function assignments()
    {
        return $this->hasMany(TestAssignment::class);
    }
}
