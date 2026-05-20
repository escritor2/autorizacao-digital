<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Roles: admin, aqv, professor, porteiro
    const ROLE_ADMIN = 'admin';
    const ROLE_AQV = 'aqv';
    const ROLE_PROFESSOR = 'professor';
    const ROLE_PORTEIRO = 'porteiro';

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_AQV,
            self::ROLE_PROFESSOR,
            self::ROLE_PORTEIRO,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAQV(): bool
    {
        return $this->role === self::ROLE_AQV;
    }

    public function isProfessor(): bool
    {
        return $this->role === self::ROLE_PROFESSOR;
    }

    public function isPorteiro(): bool
    {
        return $this->role === self::ROLE_PORTEIRO;
    }

    // Relacionamentos
    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    public function authorizationRequests()
    {
        return $this->hasMany(AuthorizationRequest::class, 'guardian_id');
    }

    public function teacherAuthorizations()
    {
        return $this->hasMany(AuthorizationRequest::class, 'teacher_id');
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'porteiro_id');
    }

    public function logs()
    {
        return $this->hasMany(SystemLog::class, 'user_id');
    }
}
