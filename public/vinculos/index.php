<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user_or_company();
include __DIR__ . '/../../includes/header.php';

$isUser = !empty($_SESSION['user_id']);
$isEmpresa = !empty($_SESSION['empresa_id']);

if ($isUser) {
    $id = (int)$_SESSION['user_id'];
    $sql = 'SELECT v.id, v.status, v.solicitado_por, e.nome AS outra_parte
            FROM vinculos v INNER JOIN empresas e ON e.id = v.empresa_id
            WHERE v.user_id = ? ORDER BY v.updated_at DESC, v.created_at DESC';
} else {
    $id = (int)$_SESSION['empresa_id'];
    $sql = 'SELECT v.id, v.status, v.solicitado_por, u.nome AS outra_parte
            FROM vinculos v INNER JOIN users u ON u.id = v.user_id
            WHERE v.empresa_id = ? ORDER BY v.updated_at DESC, v.created_at DESC';
}
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
// Busca por novas conexões
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q !== '') {
    if ($isUser) {
        $sqlSearch = 'SELECT e.id, e.nome, e.email
                      FROM empresas e
                      LEFT JOIN vinculos v ON v.empresa_id = e.id AND v.user_id = ?
                      WHERE (e.nome LIKE ? OR e.email LIKE ?) AND (v.id IS NULL OR v.status <> "aprovado")
                      ORDER BY e.nome ASC LIMIT 10';
        $stmtS = $mysqli->prepare($sqlSearch);
        $like = '%'.$q.'%';
        $stmtS->bind_param('iss', $id, $like, $like);
    } else {
        $sqlSearch = 'SELECT u.id, u.nome, u.email
                      FROM users u
                      LEFT JOIN vinculos v ON v.user_id = u.id AND v.empresa_id = ?
                      WHERE (u.nome LIKE ? OR u.email LIKE ?) AND (v.id IS NULL OR v.status <> "aprovado")
                      ORDER BY u.nome ASC LIMIT 10';
        $stmtS = $mysqli->prepare($sqlSearch);
        $like = '%'.$q.'%';
        $stmtS->bind_param('iss', $id, $like, $like);
    }
    $stmtS->execute();
    $searchRes = $stmtS->get_result();
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Vínculos</h1>
    <a class="btn btn-outline-secondary" href="<?= $isEmpresa ? '/sistema_escalacao/public/empresa/dashboard.php' : '/sistema_escalacao/public/users/index.php' ?>">Voltar</a>
</div>
<form class="card mb-3" method="get">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Buscar <?= $isEmpresa ? 'usuários' : 'empresas' ?></label>
                <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" class="form-control" placeholder="Nome ou email">
            </div>
            <div class="col-md-4 d-grid">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </div>
    </div>
</form>
<?php if ($q !== ''): ?>
<div class="card mb-3">
    <div class="card-body">
        <h2 class="h6 mb-3">Resultados da busca</h2>
        <div class="list-group">
            <?php if (!empty($searchRes) && $searchRes->num_rows > 0): ?>
                <?php while ($row = $searchRes->fetch_assoc()): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold"><?= htmlspecialchars($row['nome']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                        </div>
                        <form method="post" action="/sistema_escalacao/public/vinculos/request.php">
                            <?php if ($isUser): ?>
                                <input type="hidden" name="empresa_id" value="<?= (int)$row['id'] ?>">
                            <?php else: ?>
                                <input type="hidden" name="user_id" value="<?= (int)$row['id'] ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-sm btn-success">Solicitar vínculo</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-muted">Nenhum resultado encontrado.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th><?= $isEmpresa ? 'Usuário' : 'Empresa' ?></th>
                        <th>Status</th>
                        <th>Solicitado por</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($v = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($v['outra_parte']) ?></td>
                            <td><?= htmlspecialchars($v['status']) ?></td>
                            <td><?= htmlspecialchars($v['solicitado_por']) ?></td>
                            <td class="text-end">
                                <?php if ($v['status']==='pendente'): ?>
                                    <form class="d-inline" method="post" action="/sistema_escalacao/public/vinculos/respond.php">
                                        <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                                        <button name="acao" value="aprovar" class="btn btn-sm btn-success">Aprovar</button>
                                        <button name="acao" value="recusar" class="btn btn-sm btn-outline-danger">Recusar</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Sem ações</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($res->num_rows === 0): ?>
                        <tr><td colspan="4" class="text-center text-muted">Sem vínculos</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>



