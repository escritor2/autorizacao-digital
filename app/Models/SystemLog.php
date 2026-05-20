<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'related_type',
        'related_id',
        'message',
        'context',
        'level',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    // Log levels
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    // Actions
    const ACTION_AUTHORIZATION_REQUEST_CREATED = 'authorization_request_created';
    const ACTION_AUTHORIZATION_APPROVED_TEACHER = 'authorization_approved_teacher';
    const ACTION_AUTHORIZATION_REJECTED_TEACHER = 'authorization_rejected_teacher';
    const ACTION_MOVEMENT_REGISTERED = 'movement_registered';
    const ACTION_EMAIL_SENT = 'email_sent';
    const ACTION_EMAIL_FAILED = 'email_failed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
