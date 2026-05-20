<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'guardian_id',
        'class_id',
        'teacher_id',
        'movement_type',
        'reason',
        'status',
        'teacher_notes',
        'teacher_approved_at',
        'porteiro_id',
        'porteiro_validated_at',
    ];

    protected $casts = [
        'teacher_approved_at' => 'datetime',
        'porteiro_validated_at' => 'datetime',
    ];

    // Status
    const STATUS_PENDING_TEACHER = 'pending_teacher';
    const STATUS_READY_PORTEIRO = 'ready_porteiro';
    const STATUS_REJECTED_TEACHER = 'rejected_teacher';
    const STATUS_COMPLETED = 'completed';

    // Movement types
    const TYPE_ENTRY = 'entry';
    const TYPE_EXIT = 'exit';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING_TEACHER,
            self::STATUS_READY_PORTEIRO,
            self::STATUS_REJECTED_TEACHER,
            self::STATUS_COMPLETED,
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function porteiro()
    {
        return $this->belongsTo(User::class, 'porteiro_id');
    }

    public function movement()
    {
        return $this->hasOne(Movement::class, 'authorization_request_id');
    }
}
