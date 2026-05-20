# SAFE - Sistema de Autorização e Fluxo Escolar

## Visão Geral

O **SAFE** é um sistema web de controle de entrada e saída de alunos com fluxo de autorização em múltiplas etapas, notificações automáticas e rastreabilidade completa.

## Stack Tecnológico

- **Backend:** Laravel 11 + PHP 8.2
- **Frontend:** Vue 3 + Vite
- **Banco de Dados:** SQLite
- **Autenticação:** Laravel Sanctum + JWT
- **Notificações:** Laravel Mail + Mailpit (SMTP local)
- **UI:** Tailwind CSS
- **Testes:** Pest + Vitest

## Requisitos

- PHP 8.2+
- Node.js 18+
- Composer
- SQLite3

## Instalação

### 1. Clonar/Extrair o Projeto

```bash
cd safe-laravel
```

### 2. Instalar Dependências PHP

```bash
composer install
```

### 3. Instalar Dependências Node.js

```bash
npm install
# ou
pnpm install
```

### 4. Configurar Ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Criar Banco de Dados SQLite

```bash
touch database/database.sqlite
php artisan migrate --seed
```

### 6. Iniciar Mailpit (para notificações)

```bash
# Docker
docker run -d -p 1025:1025 -p 8025:8025 mailpit/mailpit

# Ou instalar localmente
# https://mailpit.axllent.org/
```

### 7. Iniciar Servidor de Desenvolvimento

**Terminal 1 - Laravel Backend:**
```bash
php artisan serve
```

**Terminal 2 - Vue.js Frontend:**
```bash
npm run dev
```

Acesse em: `http://localhost:8000`

## Estrutura do Projeto

```
safe-laravel/
├── app/
│   ├── Models/              # Modelos Eloquent
│   ├── Http/
│   │   ├── Controllers/     # Controllers da API
│   │   └── Requests/        # Form Requests
│   ├── Events/              # Eventos Laravel
│   ├── Listeners/           # Listeners de eventos
│   └── Mail/                # Mailable classes
├── database/
│   ├── migrations/          # Migrações SQLite
│   ├── seeders/             # Seeders para dados de teste
│   └── database.sqlite      # Arquivo SQLite
├── resources/
│   ├── js/
│   │   ├── components/      # Componentes Vue
│   │   ├── pages/           # Páginas Vue
│   │   ├── stores/          # Pinia stores
│   │   └── app.js           # App Vue
│   └── views/               # Views Blade (fallback)
├── routes/
│   ├── api.php              # Rotas da API
│   └── web.php              # Rotas web
├── tests/
│   ├── Feature/             # Testes de funcionalidade
│   └── Unit/                # Testes unitários
├── vite.config.js           # Configuração Vite
├── tailwind.config.js       # Configuração Tailwind
└── README.md
```

## Fluxo de Autorização

### Etapa 1: AQV/Responsável cria solicitação
- Acessa dashboard
- Cria solicitação de entrada/saída
- Sistema notifica professor

### Etapa 2: Professor aprova ou rejeita
- Recebe notificação
- Visualiza solicitação
- Aprova ou rejeita com motivo
- Sistema notifica responsável

### Etapa 3: Porteiro valida
- Visualiza autorizações prontas
- Registra entrada/saída
- Sistema notifica responsável

## Perfis de Usuário

| Perfil | Permissões |
|--------|-----------|
| **Admin** | Gerenciar tudo, visualizar logs |
| **AQV/Responsável** | Criar solicitações, acompanhar status |
| **Professor** | Aprovar/rejeitar solicitações da turma |
| **Porteiro** | Validar e registrar movimentações |

## Notificações

O sistema envia e-mails em cada etapa:
- ✉️ Solicitação criada (para professor)
- ✉️ Aprovada (para responsável)
- ✉️ Rejeitada (para responsável)
- ✉️ Validada (para responsável)

**Mailpit Web UI:** http://localhost:8025

## Logs e Auditoria

Todos os eventos são registrados com:
- Timestamp
- Usuário responsável
- Ação realizada
- Dados relacionados

Acesse em: Dashboard Admin → Logs

## Testes

### Executar Testes Pest (Backend)

```bash
php artisan test
```

### Executar Testes Vitest (Frontend)

```bash
npm run test
```

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

### Criar Evento

```bash
php artisan make:event EventName
```

### Criar Mailable

```bash
php artisan make:mail MailName
```

## Troubleshooting

### Erro: "SQLSTATE[HY000]: General error: 1 unable to open database file"

**Solução:**
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

- Verifique se Mailpit está rodando: `http://localhost:8025`
- Confirme `MAIL_HOST=localhost` e `MAIL_PORT=1025` no `.env`

## Documentação Completa

Veja `MANUAL_TECNICO.md` para documentação técnica e operacional detalhada.

## Suporte

Para dúvidas ou problemas:
- 📧 E-mail: suporte@safe-sistema.local
- 📞 Telefone: (XX) XXXX-XXXX

## Licença

MIT License - Veja LICENSE para detalhes.

---

**Versão:** 1.0.0  
**Última atualização:** 19 de maio de 2026
