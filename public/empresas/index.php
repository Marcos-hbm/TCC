<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();
include __DIR__ . '/../../includes/header.php';

$result = $mysqli->query("SELECT id, nome, cnpj, email, telefone FROM empresas ORDER BY id DESC");
?>
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0">Empresas</h1>
	<div class="d-flex gap-2">
		<a class="btn btn-success" href="/sistema_escalacao/public/eventos/form.php">Criar Eventos</a>
		<a class="btn btn-outline-primary" href="/sistema_escalacao/public/eventos/index.php">Ver Eventos</a>
		<a class="btn btn-primary" href="/sistema_escalacao/public/empresas/form.php">Nova Empresa</a>
	</div>
</div>
<div class="card">
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nome</th>
						<th>CNPJ</th>
						<th>Email</th>
						<th>Telefone</th>
						<th class="text-end">Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = $result->fetch_assoc()): ?>
						<tr>
							<td><?= (int)$row['id'] ?></td>
							<td><?= htmlspecialchars($row['nome']) ?></td>
							<td><?= htmlspecialchars($row['cnpj']) ?></td>
							<td><?= htmlspecialchars($row['email']) ?></td>
							<td><?= htmlspecialchars($row['telefone']) ?></td>
						<td class="text-end table-actions">
							<a class="btn btn-sm btn-outline-secondary" href="/sistema_escalacao/public/empresas/form.php?id=<?= (int)$row['id'] ?>">Editar</a>
							<a class="btn btn-sm btn-outline-primary" href="/sistema_escalacao/public/empresas/profile.php?id=<?= (int)$row['id'] ?>">Perfil</a>
								<form action="/sistema_escalacao/public/empresas/delete.php" method="post" class="d-inline" onsubmit="return confirm('Deseja realmente excluir esta empresa?');">
									<input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
									<button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
								</form>
							</td>
						</tr>
					<?php endwhile; ?>
					<?php if ($result->num_rows === 0): ?>
						<tr><td colspan="6" class="text-center text-muted">Nenhum registro encontrado</td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
