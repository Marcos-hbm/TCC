<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user();
include __DIR__ . '/../../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
	header('Location: /sistema_escalacao/public/users/index.php?err=Usuário inválido');
	exit;
}

$stmt = $mysqli->prepare("SELECT id, nome, email, DATE_FORMAT(data_nascimento, '%d/%m/%Y') AS data_nascimento, cpf, telefone, genero, foto_path FROM users WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
	header('Location: /sistema_escalacao/public/users/index.php?err=Usuário não encontrado');
	exit;
}

$fotoUrl = $user['foto_path'] ? '/sistema_escalacao/' . ltrim($user['foto_path'], '/') : 'https://via.placeholder.com/200x200?text=Foto';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0">Perfil do Usuário</h1>
	<a class="btn btn-outline-secondary" href="/sistema_escalacao/public/users/index.php">Voltar</a>
</div>
<div class="row g-4">
	<div class="col-md-3 col-lg-2">
		<div class="card text-center">
			<div class="card-body">
				<img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto do usuário" class="avatar-circle rounded-circle mb-3 mx-auto d-block">
				<form action="/sistema_escalacao/public/users/upload_photo.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
					<div class="mb-3 text-start">
						<label class="form-label">Alterar foto (JPG ou PNG, até 2MB)</label>
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
					<div class="col-md-6"><strong>Nome:</strong> <?= htmlspecialchars($user['nome']) ?></div>
					<div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
					<div class="col-md-4"><strong>Nascimento:</strong> <?= htmlspecialchars($user['data_nascimento']) ?></div>
					<div class="col-md-4"><strong>CPF:</strong> <?= htmlspecialchars($user['cpf']) ?></div>
					<div class="col-md-4"><strong>Telefone:</strong> <?= htmlspecialchars($user['telefone']) ?></div>
					<div class="col-md-6"><strong>Gênero:</strong> <?= htmlspecialchars($user['genero']) ?></div>
				</div>
			</div>
		</div>
		<div class="mt-3">
			<form action="/sistema_escalacao/public/users/delete.php" method="post" onsubmit="return confirm('Excluir conta permanentemente? Esta ação não pode ser desfeita.');">
				<input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
				<button type="submit" class="btn btn-outline-danger">Excluir conta</button>
			</form>
		</div>
	</div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
