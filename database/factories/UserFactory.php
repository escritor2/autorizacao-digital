<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $rand = rand(1000, 9999);
        return [
            'name' => 'Test User ' . $rand,
            'email' => 'testuser' . $rand . '@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'role' => 'aqv',
            'phone' => '551199999' . $rand,
            'is_active' => true,
        ];
    }
}
