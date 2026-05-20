<?php

namespace App\Http\Controllers;

use App\Models\AuthorizationRequest;
use App\Models\Movement;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function register(Request $request, AuthorizationRequest $authorization)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        if (!$user->isPorteiro()) {
            return response()->json([
                'message' => 'Apenas porteiros podem registrar movimentações',
            ], 403);
        }

        if ($authorization->status !== AuthorizationRequest::STATUS_READY_PORTEIRO) {
            return response()->json([
                'message' => 'Esta solicitação não está pronta para validação',
            ], 400);
        }

        // Registrar movimento
        $movement = Movement::create([
            'student_id' => $authorization->student_id,
            'authorization_request_id' => $authorization->id,
            'type' => $authorization->movement_type,
            'porteiro_id' => $user->id,
            'notes' => $validated['notes'] ?? null,
            'registered_at' => now(),
        ]);

        // Atualizar status da autorização
        $authorization->update([
            'status' => AuthorizationRequest::STATUS_COMPLETED,
            'porteiro_id' => $user->id,
            'porteiro_validated_at' => now(),
        ]);

        // Log
        SystemLog::create([
            'user_id' => $user->id,
            'action' => SystemLog::ACTION_MOVEMENT_REGISTERED,
            'related_type' => 'student',
            'related_id' => $authorization->student_id,
            'message' => "Movimentação de {$authorization->movement_type} registrada",
            'context' => [
                'authorization_id' => $authorization->id,
                'movement_id' => $movement->id,
                'type' => $authorization->movement_type,
            ],
            'level' => SystemLog::LEVEL_INFO,
        ]);

        // Notificar responsável
        $this->notifyGuardian($authorization);

        return response()->json([
            'message' => 'Movimentação registrada com sucesso',
            'movement' => $movement->load('student', 'authorizationRequest', 'porteiro'),
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Movement::query();

        if ($user->isPorteiro()) {
            $query->where('porteiro_id', $user->id);
        }

        $movements = $query->with('student', 'authorizationRequest', 'porteiro')
            ->orderBy('registered_at', 'desc')
            ->paginate(15);

        return response()->json($movements);
    }

    private function notifyGuardian(AuthorizationRequest $authorization)
    {
        $guardian = $authorization->guardian;
        $student = $authorization->student;

        // Log de envio de e-mail
        SystemLog::create([
            'user_id' => null,
            'action' => SystemLog::ACTION_EMAIL_SENT,
            'related_type' => 'authorization_request',
            'related_id' => $authorization->id,
            'message' => "E-mail de confirmação enviado para {$guardian->email}",
            'context' => [
                'to' => $guardian->email,
                'subject' => "Movimentação Registrada - {$student->name}",
                'type' => $authorization->movement_type,
            ],
            'level' => SystemLog::LEVEL_INFO,
        ]);
    }
}
