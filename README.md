# Sistema de Escalação

Aplicação PHP (procedural) para gestão de empresas, usuários e eventos, com autenticação e upload de arquivos. Projetada para rodar em ambiente local com XAMPP no Windows.

## Requisitos
- Windows 10+
- XAMPP (Apache + MariaDB/MySQL + PHP 8.x)
- Navegador moderno (Chrome/Edge/Firefox)

## Estrutura do Projeto
```
c:\xampp\htdocs\sistema_escalacao\
  ├─ config\
  │  └─ db.php
  ├─ database\
  │  └─ schema.sql
  ├─ includes\
  │  ├─ auth_guard.php
  │  ├─ footer.php
  │  └─ header.php (layout base com Bootstrap 5 e dark theme)
  └─ public\
     ├─ assets\
     │  ├─ css\
     │  │  ├─ custom.css (tema escuro personalizado)
     │  │  └─ styles.css (legado)
     │  └─ js\app.js
     ├─ auth\ (login/logout)
     ├─ empresa\ (dashboard da empresa)
     ├─ empresas\ (CRUD de empresas e upload de logo)
     ├─ eventos\ (CRUD de eventos)
     └─ users\ (CRUD de usuários e upload de foto)
```

## Instalação (Local com XAMPP)
1. Pare o Apache/MySQL no XAMPP se estiverem rodando.
2. Coloque a pasta `sistema_escalacao` dentro de `C:\xampp\htdocs\`.
3. Inicie Apache e MySQL no XAMPP Control Panel.
4. Acesse `http://localhost/phpmyadmin` e crie um banco (ex.: `sistema_escalacao`).
5. Importe `database/schema.sql` no banco criado.
6. Configure credenciais do banco em `config/db.php`:
   - host, dbname, username, password conforme seu ambiente XAMPP.
7. Acesse a aplicação em `http://localhost/sistema_escalacao/public/auth/login.php`.

## Configuração de Banco
- Arquivo: `config/db.php` aponta para o banco criado (padrão: `localhost`, usuário `root`, senha vazia no XAMPP).
- Esquema: `database/schema.sql` contém criação de tabelas básicas e relações necessárias.

## Funcionalidades
- Autenticação de usuários e empresas (`public/auth/*`).
- Painel da empresa (`public/empresa/dashboard.php`).
- Gestão de empresas (`public/empresas/*`): listar, criar, editar, excluir, upload de logo.
- Gestão de eventos (`public/eventos/*`): listar, criar, editar, excluir.
- Gestão de usuários (`public/users/*`): listar, criar, editar, excluir, upload de foto.
- Uploads armazenados em `public/uploads/`.

## Como Usar
- Login geral: `public/auth/login.php`.
- Login de empresa: `public/auth/company_login.php`.
- Após login, navegue pelos módulos via cabeçalho (`includes/header.php`).
- Proteção de rotas via `includes/auth_guard.php`.

## Variáveis e Configurações Importantes
- `config/db.php`: ajuste as credenciais do MySQL.
- Permissões de pasta `public/uploads/`: deve permitir escrita pelo servidor web.

## UI Stack e Estilo

### Framework e Componentes
- **Bootstrap 5.3.3** (via CDN) - framework CSS responsivo
- **Bootstrap Icons 1.11.3** (via CDN) - biblioteca de ícones
- **Inter Font** (Google Fonts) - tipografia moderna
- **custom.css** - tema escuro personalizado com paleta preta

### Tema Escuro
O sistema utiliza um tema escuro moderno com as seguintes cores:
- **Background principal**: `#0b0d10` (preto quase absoluto)
- **Superfícies**: `#0f1115`, `#111317`, `#1a1d21` (tons de preto elevados)
- **Texto principal**: `#e5e7eb` (cinza claro para contraste)
- **Cor primária (accent)**: `#0ea5e9` (ciano/azul claro)
- **Secundária**: `#9ca3af` (cinza neutro)
- **Success**: `#22c55e` (verde)
- **Danger**: `#ef4444` (vermelho)
- **Warning**: `#f59e0b` (laranja)
- **Info**: `#06b6d4` (ciano)
- **Bordas**: `#2a2f36` (cinza escuro)

### Como Criar Novas Páginas com o Layout

#### Páginas Autenticadas
Para páginas que requerem autenticação, use o padrão:

```php
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user(); // ou require_company() ou require_user_or_company()
include __DIR__ . '/../../includes/header.php';

// Seu código PHP e HTML aqui
// O layout já inclui Bootstrap, ícones e o tema escuro

// Exemplo de estrutura:
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="bi bi-icon-name me-2"></i>Título da Página</h1>
    <a class="btn btn-primary" href="#">Ação Principal</a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Conteúdo -->
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
```

#### Páginas Públicas (ex: Login)
Para páginas que não usam o layout padrão:

```php
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Título - Sistema de Escalação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="/sistema_escalacao/public/assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Seu conteúdo -->
</body>
</html>
```

### Classes e Componentes Úteis

#### Tabelas
Use sempre `.table-dark` para consistência:
```html
<table class="table table-dark table-striped table-hover">
```

#### Formulários
```html
<div class="mb-3">
    <label class="form-label">Campo</label>
    <input type="text" class="form-control" required>
</div>
```

#### Botões com Ícones
```html
<button class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>Texto
</button>
```

#### Cards
```html
<div class="card">
    <div class="card-body">
        Conteúdo
    </div>
</div>
```

#### Alerts
```html
<div class="alert alert-success alert-dismissible fade show" role="alert">
    Mensagem de sucesso
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

### Ícones Bootstrap
Principais ícones utilizados:
- `bi-calendar-event` - eventos
- `bi-people` - usuários/parceiros
- `bi-building` - empresas
- `bi-link-45deg` - vínculos
- `bi-plus-circle` - criar/adicionar
- `bi-pencil` - editar
- `bi-trash` - excluir
- `bi-eye` - visualizar
- `bi-arrow-left` - voltar
- `bi-box-arrow-right` - sair
- `bi-check-circle` - aprovar
- `bi-x-circle` - recusar

Veja todos os ícones em: https://icons.getbootstrap.com/

## Convenções
- PHP procedural com includes para cabeçalho/rodapé/guards.
- Layout base em `includes/header.php` e `includes/footer.php`.
- Tema escuro em `public/assets/css/custom.css`.
- Assets em `public/assets/`.
- Endpoints públicos em `public/` (separados por módulo).

## Dicas de Desenvolvimento
- Habilite exibição de erros no `php.ini` em ambiente local durante desenvolvimento.
- Utilize o `Chrome DevTools` para inspecionar requisições e erros de JS/CSS.

## Segurança (recomendações)
- Valide e sanitize inputs no servidor.
- Restrinja tipos e tamanhos de arquivos em uploads.
- Armazene senhas com hash seguro (password_hash/password_verify) e use prepared statements.
- Regeneração de sessão após login e uso de `auth_guard.php` nas rotas sensíveis.

## Problemas Comuns
- Página em branco/erro 500: verifique `config/db.php` e logs do Apache (`xampp\apache\logs\error.log`).
- Erro de conexão: confirme nome do banco/usuário/senha no XAMPP.
- Upload falhando: ajuste permissões de `public/uploads/`.

## Scripts Úteis
- Reset do banco: drope/recrie o banco e reimporte `database/schema.sql` pelo phpMyAdmin.

## Licença
Este projeto é fornecido "como está" para fins educacionais/demonstração. Adapte a licença conforme necessidade.

## Créditos
- Stack: PHP + MySQL (XAMPP), HTML/CSS/JS.
- Autor(es): equipe do projeto.

