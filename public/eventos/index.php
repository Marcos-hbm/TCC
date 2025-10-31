<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();
include __DIR__ . '/../../includes/header.php';

$empresaId = (int)$_SESSION['empresa_id'];
// Auto-remove events expired more than 15 days ago
$mysqli->query("DELETE FROM eventos WHERE empresa_id = $empresaId AND data_evento < (CURDATE() - INTERVAL 15 DAY)");
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to = isset($_GET['to']) ? trim($_GET['to']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build filters
$where = 'empresa_id = ?';
$params = [$empresaId];
$types = 'i';
if ($q !== '') { $where .= ' AND nome LIKE ?'; $params[] = '%'.$q.'%'; $types .= 's'; }
if ($from !== '') { $where .= ' AND data_evento >= ?'; $params[] = $from; $types .= 's'; }
if ($to !== '') { $where .= ' AND data_evento <= ?'; $params[] = $to; $types .= 's'; }

// Count total
$sqlCount = 'SELECT COUNT(1) as c FROM eventos WHERE ' . $where;
$stmt = $mysqli->prepare($sqlCount);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['c'];
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

// Fetch page
$sql = 'SELECT id, nome, DATE_FORMAT(data_evento, "%d/%m/%Y") as data_evento, valor_cache, 
        DATEDIFF(CURDATE(), data_evento) AS dias_desde
        FROM eventos WHERE ' . $where . ' ORDER BY data_evento DESC, id DESC LIMIT ? OFFSET ?';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types . 'ii', ...array_merge($params, [$perPage, $offset]));
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0"><i class="bi bi-calendar-check me-2"></i>Meus Eventos</h1>
	<a class="btn btn-success" href="/sistema_escalacao/public/eventos/form.php">
		<i class="bi bi-plus-circle me-1"></i>Novo Evento
	</a>
</div>
<form class="card mb-3" method="get">
	<div class="card-body">
		<div class="row g-2 align-items-end">
			<div class="col-md-4">
				<label class="form-label">Nome</label>
				<input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control" placeholder="Buscar por nome">
			</div>
			<div class="col-md-3">
				<label class="form-label">De</label>
				<input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control">
			</div>
			<div class="col-md-3">
				<label class="form-label">Até</label>
				<input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control">
			</div>
			<div class="col-md-2 d-grid">
				<button class="btn btn-outline-primary" type="submit">
					<i class="bi bi-funnel me-1"></i>Filtrar
				</button>
			</div>
		</div>
	</div>
</form>
<div class="card">
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-striped table-hover mb-0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nome</th>
						<th>Data</th>
						<th>Valor Cachê</th>
						<th class="text-end">Ações</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = $result->fetch_assoc()): ?>
						<tr>
							<td><?= (int)$row['id'] ?></td>
							<td><?= htmlspecialchars($row['nome']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['data_evento']) ?>
                                <?php 
                                $diasDesde = (int)($row['dias_desde'] ?? 0);
                                if ($diasDesde > 0 && $diasDesde <= 15):
                                    $faltam = 15 - $diasDesde;
                                ?>
                                    <span class="badge bg-warning text-dark ms-1">Remove em <?= $faltam ?> dia<?= $faltam===1?'':'s' ?></span>
                                <?php endif; ?>
                            </td>
							<td>R$ <?= number_format((float)$row['valor_cache'], 2, ',', '.') ?></td>
							<td class="text-end table-actions">
								<a class="btn btn-sm btn-outline-secondary" href="/sistema_escalacao/public/eventos/form.php?id=<?= (int)$row['id'] ?>">
									<i class="bi bi-pencil"></i> Editar
								</a>
								<form action="/sistema_escalacao/public/eventos/delete.php" method="post" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este evento?');">
									<input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
									<button type="submit" class="btn btn-sm btn-outline-danger">
										<i class="bi bi-trash"></i> Excluir
									</button>
								</form>
							</td>
						</tr>
					<?php endwhile; ?>
					<?php if ($result->num_rows === 0): ?>
						<tr><td colspan="5" class="text-center text-muted">Nenhum evento cadastrado</td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php if ($totalPages > 1): ?>
<nav aria-label="Paginação">
	<ul class="pagination justify-content-center mt-3">
		<?php
		function build_qs($overrides){
			$params = $_GET;
			foreach ($overrides as $k=>$v){ $params[$k]=$v; }
			return '?' . http_build_query($params);
		}
		?>
		<li class="page-item <?= $page<=1?'disabled':'' ?>">
			<a class="page-link" href="<?= $page<=1?'#':build_qs(['page'=>$page-1]) ?>">Anterior</a>
		</li>
		<?php for($p=max(1,$page-2); $p<=min($totalPages,$page+2); $p++): ?>
			<li class="page-item <?= $p===$page?'active':'' ?>">
				<a class="page-link" href="<?= build_qs(['page'=>$p]) ?>"><?= $p ?></a>
			</li>
		<?php endfor; ?>
		<li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
			<a class="page-link" href="<?= $page>=$totalPages?'#':build_qs(['page'=>$page+1]) ?>">Próxima</a>
		</li>
	</ul>
</nav>
<?php endif; ?>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
