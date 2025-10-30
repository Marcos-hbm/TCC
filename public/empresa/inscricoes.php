<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();
include __DIR__ . '/../../includes/header.php';

$empresaId = (int)$_SESSION['empresa_id'];
// Parceiros: solicitações de vínculo pendentes enviadas por usuários
$sql = 'SELECT v.id, u.nome as user_nome, u.email, v.created_at
        FROM vinculos v
        INNER JOIN users u ON u.id = v.user_id
        WHERE v.empresa_id = ? AND v.status = "pendente" AND v.solicitado_por = "user"
        ORDER BY v.created_at DESC';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $empresaId);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Parceiros</h1>
    <a class="btn btn-outline-secondary" href="/sistema_escalacao/public/empresa/dashboard.php">Voltar</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Email</th>
                        <th>Solicitado em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['user_nome']) ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['created_at']))) ?></td>
                            <td class="text-end">
                                <form class="d-inline" method="post" action="/sistema_escalacao/public/vinculos/respond.php">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <button name="acao" value="aprovar" class="btn btn-sm btn-success">Aceitar</button>
                                    <button name="acao" value="recusar" class="btn btn-sm btn-outline-danger">Recusar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($res->num_rows === 0): ?>
                        <tr><td colspan="4" class="text-center text-muted">Sem convites pendentes</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>



