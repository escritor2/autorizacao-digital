# SAFE - Manual Técnico e Operacional

## Visão Geral

O **SAFE** é um sistema web de controle de entrada e saída de alunos com fluxo de autorização em múltiplas etapas. Implementa notificações automáticas, logs estruturados e rastreabilidade completa.

## Stack Tecnológico

- **Backend:** Laravel 11 + PHP 8.2
- **Frontend:** Vue 3 + Vite
- **Banco de Dados:** SQLite
- **Autenticação:** Laravel Sanctum + JWT
- **Notificações:** Laravel Mail + Mailpit
- **UI:** Tailwind CSS
- **Testes:** Pest + Vitest

## Fluxo de Autorização

### Etapa 1: AQV/Responsável cria solicitação

```
POST /api/authorizations
{
  "student_id": 1,
  "movement_type": "exit",
  "reason": "Consulta médica"
}
```

**Resposta:**
```json
{
  "id": 1,
  "student_id": 1,
  "guardian_id": 2,
  "status": "pending_teacher",
  "movement_type": "exit",
  "reason": "Consulta médica",
  "created_at": "2026-05-19T10:00:00Z"
}
```

**Log Registrado:**
```
action: authorization_request_created
user_id: 2 (Responsável)
related_id: 1 (Aluno)
```

### Etapa 2: Professor aprova ou rejeita

**Aprovar:**
```
POST /api/authorizations/{id}/approve
```

**Rejeitar:**
```
POST /api/authorizations/{id}/reject
{
  "notes": "Aluno não tem permissão"
}
```

**Log Registrado:**
```
action: authorization_approved_teacher ou authorization_rejected_teacher
user_id: 3 (Professor)
related_id: 1 (Aluno)
```

### Etapa 3: Porteiro registra movimentação

```
POST /api/authorizations/{id}/register-movement
{
  "notes": "Aluno saiu normalmente"
}
```

**Log Registrado:**
```
action: movement_registered
user_id: 4 (Porteiro)
related_id: 1 (Aluno)
```

## Modelos de Dados

### User (Usuário)

```php
{
  "id": 1,
  "name": "Maria Silva",
  "email": "maria@safe-sistema.local",
  "role": "aqv", // admin, aqv, professor, porteiro
  "phone": "(11) 98888-8888",
  "is_active": true,
  "created_at": "2026-05-19T10:00:00Z"
}
```

### Student (Aluno)

```php
{
  "id": 1,
  "name": "João Pedro Silva",
  "registration": "MAT001",
  "class_id": 1,
  "guardian_id": 2,
  "date_of_birth": "2012-05-15",
  "is_active": true
}
```

### AuthorizationRequest (Solicitação)

```php
{
  "id": 1,
  "student_id": 1,
  "guardian_id": 2,
  "class_id": 1,
  "teacher_id": 3,
  "movement_type": "exit",
  "reason": "Consulta médica",
  "status": "pending_teacher",
  "teacher_notes": null,
  "teacher_approved_at": null,
  "porteiro_id": null,
  "porteiro_validated_at": null,
  "created_at": "2026-05-19T10:00:00Z"
}
```

### Movement (Movimentação)

```php
{
  "id": 1,
  "student_id": 1,
  "authorization_request_id": 1,
  "type": "exit",
  "porteiro_id": 4,
  "notes": "Aluno saiu normalmente",
  "registered_at": "2026-05-19T10:15:00Z"
}
```

### SystemLog (Log do Sistema)

```php
{
  "id": 1,
  "user_id": 2,
  "action": "authorization_request_created",
  "related_type": "student",
  "related_id": 1,
  "message": "Solicitação de exit criada",
  "context": {
    "student_id": 1,
    "reason": "Consulta médica"
  },
  "level": "info",
  "created_at": "2026-05-19T10:00:00Z"
}
```

## Endpoints da API

### Autenticação

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/auth/login` | Fazer login |
| GET | `/api/auth/me` | Obter usuário atual |
| POST | `/api/auth/logout` | Fazer logout |

### Autorizações

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/authorizations` | Listar autorizações |
| POST | `/api/authorizations` | Criar autorização |
| POST | `/api/authorizations/{id}/approve` | Aprovar (Professor) |
| POST | `/api/authorizations/{id}/reject` | Rejeitar (Professor) |

### Movimentações

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/movements` | Listar movimentações |
| POST | `/api/authorizations/{id}/register-movement` | Registrar movimentação (Porteiro) |

## Controle de Acesso (RBAC)

### Admin
- Visualizar todas as autorizações
- Visualizar todos os logs
- Gerenciar usuários e classes

### AQV/Responsável
- Criar solicitações para seus alunos
- Visualizar suas solicitações
- Receber notificações

### Professor
- Visualizar solicitações da sua turma
- Aprovar/rejeitar solicitações
- Adicionar notas

### Porteiro
- Visualizar autorizações prontas
- Registrar movimentações
- Visualizar histórico de movimentações

## Notificações

O sistema envia notificações em cada etapa via Mailpit (SMTP local):

1. **Solicitação Criada** → Professor
2. **Aprovada** → Responsável
3. **Rejeitada** → Responsável
4. **Validada** → Responsável

### Configuração Mailpit

```env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@safe-sistema.local
```

### Acessar Mailpit UI

```
http://localhost:8025
```

## Logs Estruturados

Todos os eventos são registrados em `system_logs`:

```php
SystemLog::create([
    'user_id' => $user->id,
    'action' => SystemLog::ACTION_AUTHORIZATION_REQUEST_CREATED,
    'related_type' => 'student',
    'related_id' => $student->id,
    'message' => 'Solicitação de exit criada',
    'context' => [
        'student_id' => $student->id,
        'reason' => 'Consulta médica',
    ],
    'level' => SystemLog::LEVEL_INFO,
]);
```

## Testes

### Testes Unitários (Pest)

```bash
php artisan test
```

### Testes de Feature

```bash
php artisan test --filter=AuthorizationTest
```

## Troubleshooting

### Erro: "SQLSTATE[HY000]: General error: 1 unable to open database file"

```bash
touch database/database.sqlite
chmod 666 database/database.sqlite
php artisan migrate
```

### Erro: "Port 8000 already in use"

```bash
php artisan serve --port=8001
```

### E-mails não chegam em Mailpit

1. Verifique se Mailpit está rodando: `http://localhost:8025`
2. Confirme `MAIL_HOST=localhost` e `MAIL_PORT=1025` no `.env`
3. Verifique logs: `tail -f storage/logs/laravel.log`

## Desenvolvimento

### Criar Migration

```bash
php artisan make:migration create_table_name
```

### Criar Model

```bash
php artisan make:model ModelName -m
```

### Criar Controller

```bash
php artisan make:controller Api/ControllerName
```

### Executar Seeders

```bash
php artisan db:seed
```

## Estrutura de Diretórios

```
safe-laravel/
├── app/
│   ├── Models/              # Modelos Eloquent
│   ├── Http/
│   │   ├── Controllers/     # Controllers
│   │   └── Requests/        # Form Requests
│   ├── Events/              # Eventos
│   ├── Listeners/           # Listeners
│   └── Mail/                # Mailable classes
├── database/
│   ├── migrations/          # Migrações
│   ├── seeders/             # Seeders
│   └── database.sqlite      # Arquivo SQLite
├── resources/
│   ├── js/
│   │   ├── components/      # Componentes Vue
│   │   ├── pages/           # Páginas Vue
│   │   ├── stores/          # Pinia stores
│   │   └── app.js           # App Vue
│   └── css/                 # Estilos
├── routes/
│   ├── api.php              # Rotas API
│   └── web.php              # Rotas web
├── tests/
│   ├── Feature/             # Testes de feature
│   └── Unit/                # Testes unitários
└── storage/
    └── logs/                # Logs da aplicação
```

## Contas de Teste

| Perfil | E-mail | Senha |
|--------|--------|-------|
| Admin | admin@safe-sistema.local | password |
| AQV | maria@safe-sistema.local | password |
| Professor | carlos@safe-sistema.local | password |
| Porteiro | joao@safe-sistema.local | password |

## Suporte

Para dúvidas ou problemas:
- 📧 E-mail: suporte@safe-sistema.local
- 📞 Telefone: (XX) XXXX-XXXX

---

**Versão:** 1.0.0  
**Última atualização:** 19 de maio de 2026
