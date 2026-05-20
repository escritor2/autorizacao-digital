<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isProfessor()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $query = SchoolClass::query()->with('teacher');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('series', 'like', "%{$search}%");
        }

        $classes = $query->orderBy('name', 'asc')->paginate(15);
        return response()->json($classes);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'series' => 'nullable|string|max:50',
            'teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $class = SchoolClass::create($validated);

        return response()->json([
            'message' => 'Turma cadastrada com sucesso.',
            'class' => $class->load('teacher'),
        ], 201);
    }

    public function update(Request $request, SchoolClass $class)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'series' => 'nullable|string|max:50',
            'teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $class->update($validated);

        return response()->json([
            'message' => 'Turma atualizada com sucesso.',
            'class' => $class->load('teacher'),
        ]);
    }

    public function destroy(Request $request, SchoolClass $class)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        // Toggles active state to preserve historical log integrity
        $class->update(['is_active' => !$class->is_active]);

        $status = $class->is_active ? 'ativada' : 'desativada';
        return response()->json([
            'message' => "Status da turma alterado para {$status}.",
            'class' => $class,
        ]);
    }

    // Listar todas as turmas ativas sem paginação (para dropdowns de busca ou formulários rápidos)
    public function listAll(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name', 'asc')->get();
        return response()->json($classes);
    }
}
