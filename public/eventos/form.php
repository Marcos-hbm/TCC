<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();
include __DIR__ . '/../../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$evento = [
	'id' => 0,
	'nome' => '',
	'descricao' => '',
	'data_evento' => '',
	'valor_cache' => '0.00',
	'observacoes' => ''
];
if ($id > 0) {
	$empresaId = (int)$_SESSION['empresa_id'];
	$stmt = $mysqli->prepare('SELECT id, nome, descricao, DATE_FORMAT(data_evento, "%Y-%m-%d") as data_evento, valor_cache, observacoes FROM eventos WHERE id = ? AND empresa_id = ?');
	$stmt->bind_param('ii', $id, $empresaId);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) {
		$evento = $row;
	} else {
		header('Location: /sistema_escalacao/public/eventos/index.php?err=Evento não encontrado');
		exit;
	}
}
$pageTitle = $id ? 'Editar Evento' : 'Criar Evento';
$pageIcon = $id ? 'bi-pencil' : 'bi-plus-circle';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
<h1 class="h3 mb-0"><i class="bi <?= $pageIcon ?> me-2"></i><?= $pageTitle ?></h1>
    <a class="btn btn-outline-secondary" href="/sistema_escalacao/public/eventos/index.php"><i class="bi bi-arrow-left me-2"></i>Voltar</a>
</div>
<form action="/sistema_escalacao/public/eventos/save.php" method="post" class="card needs-validation" novalidate>
	<div class="card-body">
		<div class="row g-3">
			<input type="hidden" name="id" value="<?= (int)$evento['id'] ?>">
			<div class="col-md-6">
				<label class="form-label">Nome do Evento</label>
				<input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($evento['nome']) ?>">
				<div class="invalid-feedback">Informe o nome do evento.</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">Data do Evento</label>
				<input type="date" name="data_evento" class="form-control" required value="<?= htmlspecialchars($evento['data_evento']) ?>">
				<div class="invalid-feedback">Informe a data do evento.</div>
			</div>
			<div class="col-12">
				<label class="form-label">Descrição</label>
				<textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($evento['descricao']) ?></textarea>
			</div>
			<div class="col-md-6">
				<label class="form-label">Valor do Cachê (R$)</label>
				<input type="number" name="valor_cache" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($evento['valor_cache']) ?>" required>
				<div class="invalid-feedback">Informe um valor válido.</div>
			</div>
			<div class="col-12">
				<label class="form-label">Observações</label>
				<textarea name="observacoes" class="form-control" rows="3"><?= htmlspecialchars($evento['observacoes']) ?></textarea>
			</div>
		</div>
	</div>
	<div class="card-footer d-flex justify-content-end gap-2">
		<button type="submit" class="btn btn-success">Salvar Evento</button>
	</div>
</form>
<script>
(function(){
	'use strict';
	var forms = document.querySelectorAll('.needs-validation');
	Array.prototype.slice.call(forms).forEach(function(form){
		form.addEventListener('submit', function (event) {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		}, false);
	});
})();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
