<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();
include __DIR__ . '/../../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
	header('Location: /sistema_escalacao/public/empresas/index.php?err=Empresa inválida');
	exit;
}

$stmt = $mysqli->prepare("SELECT id, nome, cnpj, email, telefone, foto_path FROM empresas WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();
if (!$empresa) {
	header('Location: /sistema_escalacao/public/empresas/index.php?err=Empresa não encontrada');
	exit;
}

$fotoUrl = $empresa['foto_path'] ? '/sistema_escalacao/' . ltrim($empresa['foto_path'], '/') : 'https://via.placeholder.com/200x200?text=Logo';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0">Perfil da Empresa</h1>
	<a class="btn btn-outline-secondary" href="/sistema_escalacao/public/empresa/dashboard.php">Voltar</a>
</div>
<div class="row g-4">
	<div class="col-md-3 col-lg-2">
		<div class="card text-center">
			<div class="card-body">
				<img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Logo da empresa" class="avatar-circle rounded-circle mb-3 mx-auto d-block">
				<form action="/sistema_escalacao/public/empresas/upload_logo.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="id" value="<?= (int)$empresa['id'] ?>">
					<div class="mb-3 text-start">
						<label class="form-label">Alterar logo (JPG ou PNG, até 2MB)</label>
						<input type="file" name="foto" accept="image/jpeg,image/png" class="form-control" required>
					</div>
					<button type="submit" class="btn btn-primary w-100">Enviar</button>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-9 col-lg-10">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title mb-3">Dados</h5>
				<div class="row g-3">
					<div class="col-md-6"><strong>Nome:</strong> <?= htmlspecialchars($empresa['nome']) ?></div>
					<div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($empresa['email']) ?></div>
					<div class="col-md-6"><strong>CNPJ:</strong> <?= htmlspecialchars($empresa['cnpj']) ?></div>
					<div class="col-md-6"><strong>Telefone:</strong> <?= htmlspecialchars($empresa['telefone']) ?></div>
				</div>
			</div>
		</div>
		<div class="mt-3">
			<form action="/sistema_escalacao/public/empresas/delete.php" method="post" onsubmit="return confirm('Excluir conta permanentemente? Esta ação não pode ser desfeita.');">
				<input type="hidden" name="id" value="<?= (int)$empresa['id'] ?>">
				<button type="submit" class="btn btn-outline-danger">Excluir conta</button>
			</form>
		</div>
	</div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>




