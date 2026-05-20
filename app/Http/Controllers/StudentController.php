<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Student::query()->with(['class', 'guardian']);

        if ($user->isAQV()) {
            // Responsável só vê os seus próprios alunos
            $query->where('guardian_id', $user->id);
        } elseif (!$user->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('registration', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id') && $request->input('class_id') !== '') {
            $query->where('class_id', $request->input('class_id'));
        }

        $students = $query->orderBy('name', 'asc')->paginate(15);
        return response()->json($students);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration' => 'required|string|unique:students,registration',
            'class_id' => 'required|exists:school_classes,id',
            'guardian_id' => 'required|exists:users,id',
            'date_of_birth' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Aluno cadastrado com sucesso.',
            'student' => $student->load(['class', 'guardian']),
        ], 201);
    }

    public function update(Request $request, Student $student)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
            'class_id' => 'required|exists:school_classes,id',
            'guardian_id' => 'required|exists:users,id',
            'date_of_birth' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Aluno atualizado com sucesso.',
            'student' => $student->load(['class', 'guardian']),
        ]);
    }

    public function destroy(Request $request, Student $student)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        // Toggles active state to preserve historical log integrity
        $student->update(['is_active' => !$student->is_active]);

        $status = $student->is_active ? 'ativado' : 'desativado';
        return response()->json([
            'message' => "Status do aluno alterado para {$status}.",
            'student' => $student,
        ]);
    }

    // Listar todos os alunos sem paginação (para dropdowns de busca ou relatórios rápidos)
    public function listAll(Request $request)
    {
        $user = $request->user();
        $query = Student::query();

        if ($user->isAQV()) {
            $query->where('guardian_id', $user->id);
        }

        $students = $query->where('is_active', true)->orderBy('name', 'asc')->get();
        return response()->json($students);
    }
}
