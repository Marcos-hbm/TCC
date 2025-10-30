<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user();
include __DIR__ . '/../../includes/header.php';

$userId = (int)$_SESSION['user_id'];
$stmt = $mysqli->prepare('SELECT i.id, i.status, e.id as evento_id, e.nome as evento_nome, DATE_FORMAT(e.data_evento, "%d/%m/%Y") as data_fmt, emp.nome as empresa_nome
                          FROM evento_inscricoes i
                          INNER JOIN eventos e ON e.id = i.evento_id
                          INNER JOIN empresas emp ON emp.id = e.empresa_id
                          WHERE i.user_id = ? ORDER BY e.data_evento DESC, i.id DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$inscr = $stmt->get_result();
?>
<div class="row">
    <main class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <h1 class="h4">Bem-vindo, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?></h1>
                <p class="text-muted mb-0">Acompanhe suas inscrições e descubra novos eventos.</p>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h6 mb-0">Suas inscrições</h2>
            <a class="btn btn-sm btn-primary" href="/sistema_escalacao/public/users/discover.php">Descobrir eventos</a>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Empresa</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $inscr->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['evento_nome']) ?></td>
                                    <td><?= htmlspecialchars($r['empresa_nome']) ?></td>
                                    <td><?= htmlspecialchars($r['data_fmt']) ?></td>
                                    <td><?= htmlspecialchars($r['status']) ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="/sistema_escalacao/public/eventos/show.php?id=<?= (int)$r['evento_id'] ?>" target="_blank" rel="noopener">Ver</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($inscr->num_rows === 0): ?>
                                <tr><td colspan="5" class="text-center text-muted">Você ainda não possui inscrições.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>


