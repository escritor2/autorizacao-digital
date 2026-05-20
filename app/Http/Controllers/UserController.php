<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $query = User::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->input('role') !== '') {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('name', 'asc')->paginate(15);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(User::roles())],
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'user' => $user,
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in(User::roles())],
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso.',
            'user' => $user,
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        // Não permitir excluir a si mesmo
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'Você não pode excluir sua própria conta.'], 400);
        }

        // Toggles active state instead of hard deleting to preserve historical log integrity
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'ativado' : 'desativado';
        return response()->json([
            'message' => "Status do usuário alterado para {$status}.",
            'user' => $user,
        ]);
    }

    // Listar professores para preencher selects
    public function getTeachers(Request $request)
    {
        $teachers = User::where('role', User::ROLE_PROFESSOR)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($teachers);
    }

    // Listar guardiões para preencher selects
    public function getGuardians(Request $request)
    {
        $guardians = User::where('role', User::ROLE_AQV)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($guardians);
    }
}
