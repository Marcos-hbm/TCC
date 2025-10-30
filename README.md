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
  │  └─ header.php
  └─ public\
     ├─ assets\
     │  ├─ css\styles.css
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

## Convenções
- PHP procedural com includes para cabeçalho/rodapé/guards.
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

