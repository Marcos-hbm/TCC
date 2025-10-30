<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user();
include __DIR__ . '/../../includes/header.php';

$userId = (int)$_SESSION['user_id'];
// Eventos de empresas com vínculo aprovado
$sql = 'SELECT e.id, e.nome, DATE_FORMAT(e.data_evento, "%d/%m/%Y") as data_fmt, emp.nome as empresa_nome
        FROM eventos e
        INNER JOIN vinculos v ON v.empresa_id = e.empresa_id AND v.user_id = ? AND v.status = "aprovado"
        INNER JOIN empresas emp ON emp.id = e.empresa_id
        WHERE e.data_evento >= CURDATE()
        ORDER BY e.data_evento ASC, e.id DESC';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Eventos disponíveis</h1>
    <a class="btn btn-outline-secondary" href="/sistema_escalacao/public/users/index.php">Voltar</a>
</div>
<div class="row g-3">
    <?php while ($ev = $res->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 mb-1"><?= htmlspecialchars($ev['nome']) ?></h2>
                    <p class="text-muted small mb-2">Empresa: <?= htmlspecialchars($ev['empresa_nome']) ?> · <?= htmlspecialchars($ev['data_fmt']) ?></p>
                    <form method="post" action="/sistema_escalacao/public/eventos/apply.php" class="d-grid">
                        <input type="hidden" name="evento_id" value="<?= (int)$ev['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-success">Inscrever-se</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    <?php if ($res->num_rows === 0): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center text-muted">Nenhum evento disponível no momento.</div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>



