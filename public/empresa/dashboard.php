<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();
include __DIR__ . '/../../includes/header.php';
?>
<div class="row">
	<main class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h1 class="h4">Bem-vindo, <?= htmlspecialchars($_SESSION['empresa_nome'] ?? 'Empresa') ?></h1>
				<p class="text-muted mb-0">Use o menu lateral para acessar as funcionalidades.</p>
			</div>
		</div>

		<?php
		$empresaId = (int)($_SESSION['empresa_id'] ?? 0);
		$cardsStmt = $mysqli->prepare('SELECT id, nome, DATE_FORMAT(data_evento, "%d/%m/%Y") as data_fmt, descricao FROM eventos WHERE empresa_id = ? ORDER BY data_evento DESC, id DESC LIMIT 12');
		$cardsStmt->bind_param('i', $empresaId);
		$cardsStmt->execute();
		$cardsRes = $cardsStmt->get_result();
		?>
		<div class="d-flex justify-content-between align-items-center mb-2">
			<h2 class="h5 mb-0">Seus eventos</h2>
			<div class="d-flex gap-2">
				<a class="btn btn-sm btn-success" href="/sistema_escalacao/public/eventos/form.php">Criar evento</a>
			</div>
		</div>
		<?php if ($cardsRes->num_rows > 0): ?>
			<div class="row g-3">
				<?php while ($ev = $cardsRes->fetch_assoc()): ?>
					<div class="col-sm-6 col-lg-4">
						<a class="text-decoration-none" href="/sistema_escalacao/public/eventos/show.php?id=<?= (int)$ev['id'] ?>" target="_blank" rel="noopener">
							<div class="card h-100">
								<div class="card-body">
									<h3 class="h6 mb-1"><?= htmlspecialchars($ev['nome']) ?></h3>
									<p class="text-muted small mb-2">Data: <?= htmlspecialchars($ev['data_fmt']) ?></p>
									<p class="text-truncate mb-0" style="-webkit-line-clamp: 2; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;">
										<?= htmlspecialchars($ev['descricao'] ?? '') ?>
									</p>
								</div>
							</div>
						</a>
					</div>
				<?php endwhile; ?>
			</div>
			
		<?php else: ?>
			<div class="card">
				<div class="card-body text-center text-muted">
					Nenhum evento cadastrado ainda.
				</div>
			</div>
		<?php endif; ?>
	</main>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>




