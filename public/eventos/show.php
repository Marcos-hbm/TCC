<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();
include __DIR__ . '/../../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$empresaId = (int)($_SESSION['empresa_id'] ?? 0);
if ($id <= 0) {
    header('Location: /sistema_escalacao/public/empresa/dashboard.php?err=Evento inválido');
    exit;
}

$stmt = $mysqli->prepare('SELECT id, nome, descricao, DATE_FORMAT(data_evento, "%d/%m/%Y") as data_fmt, valor_cache, observacoes FROM eventos WHERE id = ? AND empresa_id = ?');
$stmt->bind_param('ii', $id, $empresaId);
$stmt->execute();
$evento = $stmt->get_result()->fetch_assoc();
if (!$evento) {
    header('Location: /sistema_escalacao/public/empresa/dashboard.php?err=Evento não encontrado');
    exit;
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Evento #<?= (int)$evento['id'] ?> - <?= htmlspecialchars($evento['nome']) ?></h1>
    <a class="btn btn-outline-secondary" href="/sistema_escalacao/public/empresa/dashboard.php">Voltar ao Dashboard</a>
  </div>
<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nome</label>
        <div class="form-control-plaintext"><?= htmlspecialchars($evento['nome']) ?></div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Data</label>
        <div class="form-control-plaintext"><?= htmlspecialchars($evento['data_fmt']) ?></div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Cachê</label>
        <div class="form-control-plaintext">R$ <?= number_format((float)$evento['valor_cache'], 2, ',', '.') ?></div>
      </div>
      <div class="col-12">
        <label class="form-label">Descrição</label>
        <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($evento['descricao'] ?? '')) ?></div>
      </div>
      <div class="col-12">
        <label class="form-label">Observações</label>
        <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($evento['observacoes'] ?? '')) ?></div>
      </div>
    </div>
  </div>
  <div class="card-footer d-flex gap-2 justify-content-end">
    <a class="btn btn-outline-primary" href="/sistema_escalacao/public/eventos/form.php?id=<?= (int)$evento['id'] ?>">Editar</a>
  </div>
 </div>
<?php
$stmtIns = $mysqli->prepare('SELECT i.id, i.user_id, u.nome, u.cpf, DATE_FORMAT(u.data_nascimento, "%d/%m/%Y") as nasc, i.status FROM evento_inscricoes i INNER JOIN users u ON u.id = i.user_id WHERE i.evento_id = ? ORDER BY u.nome ASC');
$stmtIns->bind_param('i', $id);
$stmtIns->execute();
$insc = $stmtIns->get_result();
?>
<div class="card mt-3">
  <div class="card-body">
    <h2 class="h6 mb-3">Inscritos</h2>
    <form method="post" action="/sistema_escalacao/public/eventos/bulk_approve.php" class="mb-2">
      <input type="hidden" name="evento_id" value="<?= (int)$id ?>">
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th style="width:40px"><input type="checkbox" onclick="document.querySelectorAll('.chk-app').forEach(c=>c.checked=this.checked)"></th>
              <th>Nome</th>
              <th>CPF</th>
              <th>Nascimento</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($r = $insc->fetch_assoc()): ?>
              <tr>
                <td><input class="form-check-input chk-app" type="checkbox" name="ids[]" value="<?= (int)$r['id'] ?>" <?= $r['status']==='aprovado'?'checked':'' ?>></td>
                <td><?= htmlspecialchars($r['nome']) ?></td>
                <td><?= htmlspecialchars($r['cpf']) ?></td>
                <td><?= htmlspecialchars($r['nasc']) ?></td>
                <td><?= htmlspecialchars($r['status']) ?></td>
              </tr>
            <?php endwhile; ?>
            <?php if ($insc->num_rows === 0): ?>
              <tr><td colspan="5" class="text-center text-muted">Sem inscritos</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex gap-2 justify-content-end">
        <button class="btn btn-success" type="submit" name="acao" value="finalizar">Finalizar escala</button>
        <a class="btn btn-outline-secondary" href="/sistema_escalacao/public/eventos/export_csv.php?evento_id=<?= (int)$id ?>">Gerar Excel (CSV)</a>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>



