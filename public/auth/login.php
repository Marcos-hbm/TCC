<?php
require_once __DIR__ . '/../../config/db.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar - Sistema de Escalação</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Custom Dark Theme -->
    <link href="/sistema_escalacao/public/assets/css/styles.css" rel="stylesheet">
    <style>
        body { 
            min-height: 100vh; 
            display: flex; 
            align-items: center;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(6, 182, 212, 0.1));
        }
        .login-card { 
            box-shadow: 0 1rem 3rem rgba(0,0,0,.5); 
            border-color: var(--bs-border-color);
        }
        .brand { 
            font-weight: 700; 
            letter-spacing: .5px;
            color: var(--bs-primary);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script defer>
    (function(){
        'use strict';
        document.addEventListener('DOMContentLoaded', function(){
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form){
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    })();
    </script>
    </head>
<body>
    <div class="container">
        <?php if (isset($_GET['msg']) && $_GET['msg'] !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <?php if (isset($_GET['err']) && $_GET['err'] !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_GET['err']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        <div class="row justify-content-center py-4">
            <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <i class="bi bi-calendar-event" style="font-size: 3rem; color: var(--bs-primary);"></i>
                    <div class="brand h3 mb-1 mt-2">Sistema de Escalação</div>
                    <div class="text-muted">Acesse sua conta</div>
                </div>
                <div class="card login-card">
                    <div class="card-body p-4">
                        <h1 class="h5 mb-3 text-center"><i class="bi bi-box-arrow-in-right me-2"></i>Entrar</h1>
                        <form action="/sistema_escalacao/public/auth/login_handler.php" method="post" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Entrar como</label>
                                <select class="form-select" name="tipo" required>
                                    <option value="usuario">Usuário</option>
                                    <option value="empresa">Empresa</option>
                                </select>
                                <div class="invalid-feedback">Selecione o tipo de login.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                                <div class="invalid-feedback">Informe um email válido.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" name="senha" class="form-control" required minlength="6">
                                <div class="invalid-feedback">Informe sua senha.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                        <hr class="my-4">
                        <div class="d-grid gap-2">
                            <a href="/sistema_escalacao/public/users/form.php" class="btn btn-outline-secondary">
                                <i class="bi bi-person-plus me-2"></i>Criar cadastro de usuário
                            </a>
                            <a href="/sistema_escalacao/public/empresas/form.php" class="btn btn-outline-secondary">
                                <i class="bi bi-building-add me-2"></i>Criar cadastro de empresa
                            </a>
                        </div>
                    </div>
                </div>
                <div class="text-center text-muted small mt-3">
                    <i class="bi bi-shield-check me-1"></i>© <?= date('Y') ?> Sistema de Escalação
                </div>
            </div>
        </div>
    </div>
</body>
</html>
