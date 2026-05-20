<?php

namespace App\Http\Controllers;

use App\Models\AuthorizationRequest;
use App\Models\Student;
use App\Models\SystemLog;
use App\Events\AuthorizationCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthorizationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'movement_type' => 'required|in:entry,exit',
            'reason' => 'required|string|min:5',
        ]);

        $user = $request->user();
        $student = Student::findOrFail($validated['student_id']);

        // Verificar se o responsável é o guardião do aluno
        if ($user->id !== $student->guardian_id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Você não tem permissão para criar solicitações para este aluno',
            ], 403);
        }

        $authorization = AuthorizationRequest::create([
            'student_id' => $validated['student_id'],
            'guardian_id' => $user->id,
            'class_id' => $student->class_id,
            'teacher_id' => $student->class->teacher_id,
            'movement_type' => $validated['movement_type'],
            'reason' => $validated['reason'],
            'status' => AuthorizationRequest::STATUS_PENDING_TEACHER,
        ]);

        // Log
        SystemLog::create([
            'user_id' => $user->id,
            'action' => SystemLog::ACTION_AUTHORIZATION_REQUEST_CREATED,
            'related_type' => 'student',
            'related_id' => $student->id,
            'message' => "Solicitação de {$validated['movement_type']} criada",
            'context' => [
                'student_id' => $student->id,
                'reason' => $validated['reason'],
            ],
            'level' => SystemLog::LEVEL_INFO,
        ]);

        // Enviar notificação ao professor
        $this->notifyTeacher($authorization);

        return response()->json([
            'message' => 'Solicitação criada com sucesso',
            'authorization' => $authorization->load('student', 'guardian', 'teacher'),
        ], 201);
    }

    public function approveByTeacher(Request $request, AuthorizationRequest $authorization)
    {
        $user = $request->user();

        if (!$user->isProfessor()) {
            return response()->json([
                'message' => 'Apenas professores podem aprovar solicitações',
            ], 403);
        }

        if ($authorization->teacher_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para aprovar esta solicitação',
            ], 403);
        }

        if ($authorization->status !== AuthorizationRequest::STATUS_PENDING_TEACHER) {
            return response()->json([
                'message' => 'Esta solicitação já foi processada',
            ], 400);
        }

        $authorization->update([
            'status' => AuthorizationRequest::STATUS_READY_PORTEIRO,
            'teacher_approved_at' => now(),
        ]);

        // Log
        SystemLog::create([
            'user_id' => $user->id,
            'action' => SystemLog::ACTION_AUTHORIZATION_APPROVED_TEACHER,
            'related_type' => 'student',
            'related_id' => $authorization->student_id,
            'message' => 'Solicitação aprovada pelo professor',
            'context' => [
                'authorization_id' => $authorization->id,
            ],
            'level' => SystemLog::LEVEL_INFO,
        ]);

        // Notificar responsável
        $this->notifyGuardian($authorization, 'approved');

        return response()->json([
            'message' => 'Solicitação aprovada com sucesso',
            'authorization' => $authorization->load('student', 'guardian', 'teacher'),
        ]);
    }

    public function rejectByTeacher(Request $request, AuthorizationRequest $authorization)
    {
        $validated = $request->validate([
            'notes' => 'required|string|min:5',
        ]);

        $user = $request->user();

        if (!$user->isProfessor()) {
            return response()->json([
                'message' => 'Apenas professores podem rejeitar solicitações',
            ], 403);
        }

        if ($authorization->teacher_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para rejeitar esta solicitação',
            ], 403);
        }

        if ($authorization->status !== AuthorizationRequest::STATUS_PENDING_TEACHER) {
            return response()->json([
                'message' => 'Esta solicitação já foi processada',
            ], 400);
        }

        $authorization->update([
            'status' => AuthorizationRequest::STATUS_REJECTED_TEACHER,
            'teacher_notes' => $validated['notes'],
            'teacher_approved_at' => now(),
        ]);

        // Log
        SystemLog::create([
            'user_id' => $user->id,
            'action' => SystemLog::ACTION_AUTHORIZATION_REJECTED_TEACHER,
            'related_type' => 'student',
            'related_id' => $authorization->student_id,
            'message' => 'Solicitação rejeitada pelo professor',
            'context' => [
                'authorization_id' => $authorization->id,
                'notes' => $validated['notes'],
            ],
            'level' => SystemLog::LEVEL_INFO,
        ]);

        // Notificar responsável
        $this->notifyGuardian($authorization, 'rejected');

        return response()->json([
            'message' => 'Solicitação rejeitada com sucesso',
            'authorization' => $authorization->load('student', 'guardian', 'teacher'),
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = AuthorizationRequest::query();

        if ($user->isAQV()) {
            $query->where('guardian_id', $user->id);
        } elseif ($user->isProfessor()) {
            $query->where('teacher_id', $user->id);
        } elseif ($user->isPorteiro()) {
            $query->where('status', AuthorizationRequest::STATUS_READY_PORTEIRO);
        }

        $authorizations = $query->with('student', 'guardian', 'teacher', 'porteiro')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($authorizations);
    }

    private function notifyTeacher(AuthorizationRequest $authorization)
    {
        $teacher = $authorization->teacher;
        $student = $authorization->student;

        // Log de envio de e-mail
        SystemLog::create([
            'user_id' => null,
            'action' => SystemLog::ACTION_EMAIL_SENT,
            'related_type' => 'authorization_request',
            'related_id' => $authorization->id,
            'message' => "E-mail enviado para {$teacher->email}",
            'context' => [
                'to' => $teacher->email,
                'subject' => "Nova solicitação de {$authorization->movement_type} - {$student->name}",
            ],
            'level' => SystemLog::LEVEL_INFO,
        ]);
    }

    private function notifyGuardian(AuthorizationRequest $authorization, string $status)
    {
        $guardian = $authorization->guardian;
        $student = $authorization->student;

        $subject = $status === 'approved' 
            ? "Solicitação Aprovada - {$student->name}"
            : "Solicitação Rejeitada - {$student->name}";

        // Log de envio de e-mail
        SystemLog::create([
            'user_id' => null,
            'action' => SystemLog::ACTION_EMAIL_SENT,
            'related_type' => 'authorization_request',
            'related_id' => $authorization->id,
            'message' => "E-mail enviado para {$guardian->email}",
            'context' => [
                'to' => $guardian->email,
                'subject' => $subject,
                'status' => $status,
            ],
            'level' => SystemLog::LEVEL_INFO,
        ]);
    }
}
