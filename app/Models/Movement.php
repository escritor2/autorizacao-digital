<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'authorization_request_id',
        'type',
        'porteiro_id',
        'notes',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function authorizationRequest()
    {
        return $this->belongsTo(AuthorizationRequest::class);
    }

    public function porteiro()
    {
        return $this->belongsTo(User::class, 'porteiro_id');
    }
}
