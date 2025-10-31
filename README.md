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
  │  ├─ footer.php       (rodapé com Bootstrap 5)
  │  └─ header.php       (cabeçalho com navbar dark e sidebar)
  └─ public\
     ├─ assets\
     │  ├─ css\styles.css  (tema escuro customizado)
     │  └─ js\app.js       (máscaras de CPF/CNPJ/telefone)
     ├─ auth\ (login/logout)
     ├─ empresa\ (dashboard da empresa)
     ├─ empresas\ (CRUD de empresas e upload de logo)
     ├─ eventos\ (CRUD de eventos)
     ├─ users\ (CRUD de usuários e upload de foto)
     └─ vinculos\ (gestão de vínculos)
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

## UI e Design System

### Tema Dark
O sistema utiliza um tema escuro (dark theme) consistente com paleta centrada em preto:
- **Background principal**: `#0b0d10` (near-black)
- **Superfícies**: `#0f1115`, `#111317`, `#1a1d21`
- **Texto**: `#e5e7eb` (cinza claro de alto contraste)
- **Acento primário**: `#0ea5e9` (cyan)
- **Status colors**: Verde `#22c55e`, Vermelho `#ef4444`, Amarelo `#f59e0b`, Ciano `#06b6d4`

### Stack de UI
- **Bootstrap 5.3.3** (CDN) - Framework CSS com componentes responsivos
- **Bootstrap Icons 1.11.3** (CDN) - Biblioteca de ícones
- **Google Fonts Inter** - Tipografia moderna e legível

### Como Criar Páginas

Para criar novas páginas que usem o layout padrão:

1. **Incluir o cabeçalho no início**:
   ```php
   <?php
   require_once __DIR__ . '/../../config/db.php';
   require_once __DIR__ . '/../../includes/auth_guard.php';
   // Adicione guards conforme necessário (require_user, require_company, etc)
   include __DIR__ . '/../../includes/header.php';
   ?>
   ```

2. **Usar componentes Bootstrap 5** com classes dark-theme:
   - Cards: `<div class="card">` (automaticamente com fundo escuro)
   - Tabelas: `<table class="table table-striped table-hover">` 
   - Formulários: `<input class="form-control">`, `<select class="form-select">`
   - Botões: `<button class="btn btn-primary">`, `.btn-success`, `.btn-danger`, `.btn-outline-secondary`
   - Alertas: `<div class="alert alert-success">`, `.alert-danger`, `.alert-warning`

3. **Usar ícones Bootstrap Icons**:
   ```html
   <i class="bi bi-calendar-event"></i>
   <i class="bi bi-person-circle"></i>
   <i class="bi bi-building"></i>
   ```
   Veja lista completa em: https://icons.getbootstrap.com/

4. **Incluir o rodapé no final**:
   ```php
   <?php include __DIR__ . '/../../includes/footer.php'; ?>
   ```

### Estrutura de Página Padrão
```php
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user(); // ou require_company() conforme necessário
include __DIR__ . '/../../includes/header.php';

// Seu código PHP aqui
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Título da Página</h1>
    <a class="btn btn-primary" href="#">Ação</a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Seu conteúdo aqui -->
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
```

### Validação de Formulários
Usar classe `.needs-validation` no form e adicionar script de validação Bootstrap 5:
```html
<form class="needs-validation" novalidate>
    <input type="text" class="form-control" required>
    <div class="invalid-feedback">Mensagem de erro</div>
</form>
```

## Convenções
- PHP procedural com includes para cabeçalho/rodapé/guards.
- Assets em `public/assets/`.
- Endpoints públicos em `public/` (separados por módulo).
- Use classes Bootstrap 5 ao invés de estilos inline.
- Ícones devem usar Bootstrap Icons para consistência visual.

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

