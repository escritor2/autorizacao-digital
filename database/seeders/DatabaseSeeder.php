<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@safe-sistema.local',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'phone' => '(11) 99999-9999',
        ]);

        // AQV/Responsável
        $aqv = User::create([
            'name' => 'Maria Silva (Responsável)',
            'email' => 'maria@safe-sistema.local',
            'password' => bcrypt('password'),
            'role' => User::ROLE_AQV,
            'phone' => '(11) 98888-8888',
        ]);

        // Professor
        $professor = User::create([
            'name' => 'Prof. Carlos Santos',
            'email' => 'carlos@safe-sistema.local',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PROFESSOR,
            'phone' => '(11) 97777-7777',
        ]);

        // Porteiro
        User::create([
            'name' => 'João Porteiro',
            'email' => 'joao@safe-sistema.local',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PORTEIRO,
            'phone' => '(11) 96666-6666',
        ]);

        // Classe
        $class = SchoolClass::create([
            'name' => '6º Ano A',
            'series' => '6',
            'teacher_id' => $professor->id,
        ]);

        // Aluno
        Student::create([
            'name' => 'João Pedro Silva',
            'registration' => 'MAT001',
            'class_id' => $class->id,
            'guardian_id' => $aqv->id,
            'date_of_birth' => '2012-05-15',
        ]);

        Student::create([
            'name' => 'Ana Clara Santos',
            'registration' => 'MAT002',
            'class_id' => $class->id,
            'guardian_id' => $aqv->id,
            'date_of_birth' => '2012-08-22',
        ]);
    }
}
