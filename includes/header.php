<?php
// ... existing code ...
session_start();
require_once __DIR__ . '/../config/db.php';

$pendingVinculos = 0;
if (!empty($_SESSION['user_id'])) {
	$uid = (int)$_SESSION['user_id'];
	if ($stmt = $mysqli->prepare('SELECT COUNT(1) AS c FROM vinculos WHERE user_id = ? AND status = "pendente" AND solicitado_por = "empresa"')) {
		$stmt->bind_param('i', $uid);
		$stmt->execute();
		$row = $stmt->get_result()->fetch_assoc();
		$pendingVinculos = (int)($row['c'] ?? 0);
	}
} elseif (!empty($_SESSION['empresa_id'])) {
	$eid = (int)$_SESSION['empresa_id'];
	if ($stmt = $mysqli->prepare('SELECT COUNT(1) AS c FROM vinculos WHERE empresa_id = ? AND status = "pendente" AND solicitado_por = "user"')) {
		$stmt->bind_param('i', $eid);
		$stmt->execute();
		$row = $stmt->get_result()->fetch_assoc();
		$pendingVinculos = (int)($row['c'] ?? 0);
	}
}
?><!doctype html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sistema de Escalação</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link href="/sistema_escalacao/public/assets/css/custom.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<aside class="col-12 col-md-3 col-lg-2 sidebar p-0 d-flex flex-column">
			<div class="p-3 border-bottom d-flex align-items-center gap-2">
				<?php
					$avatarUrl = '';
					if (!empty($_SESSION['user_id'])) {
						$avatarUrl = !empty($_SESSION['user_foto']) ? '/sistema_escalacao/'.ltrim($_SESSION['user_foto'], '/') : 'https://via.placeholder.com/40x40?text=U';
					} elseif (!empty($_SESSION['empresa_id'])) {
						$avatarUrl = !empty($_SESSION['empresa_foto']) ? '/sistema_escalacao/'.ltrim($_SESSION['empresa_foto'], '/') : 'https://via.placeholder.com/40x40?text=E';
					}
				?>
				<?php if ($avatarUrl): ?>
					<img src="<?= htmlspecialchars($avatarUrl) ?>" alt="avatar" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
				<?php endif; ?>
				<a class="text-white text-decoration-none h5 mb-0 brand" href="<?= !empty($_SESSION['empresa_id']) ? '/sistema_escalacao/public/eventos/index.php' : '/sistema_escalacao/public/users/discover.php' ?>">Sistema de Escalação</a>
			</div>
			<div class="list-group list-group-flush flex-grow-1">
				<?php if (!empty($_SESSION['empresa_id'])): ?>
					<a class="list-group-item list-group-item-action" href="/sistema_escalacao/public/eventos/index.php"><i class="bi bi-calendar-event me-2"></i>Meus Eventos</a>
					<a class="list-group-item list-group-item-action" href="/sistema_escalacao/public/empresa/inscricoes.php"><i class="bi bi-people me-2"></i>Parceiros</a>
					<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/sistema_escalacao/public/vinculos/index.php">
						<span><i class="bi bi-link-45deg me-2"></i>Vínculos</span>
						<?= $pendingVinculos>0 ? '<span class="badge bg-warning text-dark">'.$pendingVinculos.'</span>' : '' ?>
					</a>
					<a class="list-group-item list-group-item-action" href="/sistema_escalacao/public/empresas/profile.php?id=<?= (int)($_SESSION['empresa_id'] ?? 0) ?>"><i class="bi bi-building me-2"></i>Perfil</a>
				<?php elseif (!empty($_SESSION['user_id'])): ?>
					<a class="list-group-item list-group-item-action" href="/sistema_escalacao/public/users/discover.php"><i class="bi bi-compass me-2"></i>Descobrir Eventos</a>
					<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="/sistema_escalacao/public/vinculos/index.php">
						<span><i class="bi bi-link-45deg me-2"></i>Vínculos</span>
						<?= $pendingVinculos>0 ? '<span class=\"badge bg-warning text-dark\">'.$pendingVinculos.'</span>' : '' ?>
					</a>
					<a class="list-group-item list-group-item-action" href="/sistema_escalacao/public/users/profile.php?id=<?= (int)($_SESSION['user_id'] ?? 0) ?>"><i class="bi bi-person me-2"></i>Perfil</a>
				<?php else: ?>
					<a class="list-group-item list-group-item-action" href="/sistema_escalacao/public/empresas/index.php"><i class="bi bi-building me-2"></i>Empresas</a>
				<?php endif; ?>
			</div>
			<div class="p-3 border-top mt-auto">
				<?php if (!empty($_SESSION['user_id']) || !empty($_SESSION['empresa_id'])): ?>
					<a class="btn btn-outline-light w-100" href="/sistema_escalacao/public/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sair</a>
				<?php else: ?>
					<a class="btn btn-outline-light w-100" href="/sistema_escalacao/public/auth/login.php"><i class="bi bi-box-arrow-in-right me-2"></i>Entrar</a>
				<?php endif; ?>
			</div>
		</aside>
		<main class="col-12 col-md-9 col-lg-10 p-4">
			<?php if (isset($_GET['msg']) && $_GET['msg'] !== ''): ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?= htmlspecialchars($_GET['msg']) ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
			<?php endif; ?>
			<?php if (isset($_GET['err']) && $_GET['err'] !== ''): ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<?= htmlspecialchars($_GET['err']) ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
			<?php endif; ?>
