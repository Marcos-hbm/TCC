<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
// Edição protegida, criação pública
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { require_company(); }
include __DIR__ . '/../../includes/header.php';

$empresa = [
	'id' => 0,
	'nome' => '',
	'cnpj' => '',
	'email' => '',
	'telefone' => ''
];
if ($id > 0) {
	$stmt = $mysqli->prepare('SELECT id, nome, cnpj, email, telefone FROM empresas WHERE id = ?');
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) {
		$empresa = $row;
	} else {
		header('Location: /sistema_escalacao/public/empresas/index.php?err=Empresa não encontrada');
		exit;
	}
}
$pageTitle = $id ? 'Editar Empresa' : 'Nova Empresa';
$pageIcon = $id ? 'bi-pencil' : 'bi-plus-circle';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0"><i class="bi <?= $pageIcon ?> me-2"></i><?= $pageTitle ?></h1>
	<a class="btn btn-outline-secondary" href="/sistema_escalacao/public/empresa/dashboard.php"><i class="bi bi-arrow-left me-2"></i>Voltar</a>
</div>
<form action="/sistema_escalacao/public/empresas/save.php" method="post" class="card needs-validation" novalidate>
	<div class="card-body">
		<input type="hidden" name="id" value="<?= (int)$empresa['id'] ?>">
		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label">Nome</label>
				<input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($empresa['nome']) ?>">
				<div class="invalid-feedback">Informe o nome.</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">CNPJ</label>
				<input type="text" name="cnpj" id="cnpj" class="form-control" maxlength="18" required value="<?= htmlspecialchars($empresa['cnpj']) ?>">
				<div class="invalid-feedback">Informe um CNPJ válido (14 dígitos).</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">Email</label>
				<input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($empresa['email']) ?>">
				<div class="invalid-feedback">Informe um email válido.</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">Telefone</label>
				<input type="text" name="telefone" id="telefone" class="form-control" maxlength="16" required value="<?= htmlspecialchars($empresa['telefone']) ?>">
				<div class="invalid-feedback">Informe um telefone válido (10-11 dígitos).</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">Senha <?= $id ? '(opcional)' : '' ?></label>
				<input type="password" name="senha" id="senha" class="form-control" <?= $id ? 'data-optional="1"' : 'required' ?> minlength="6" placeholder="<?= $id ? 'Deixe em branco para manter' : 'Mínimo 6 caracteres' ?>">
				<div class="invalid-feedback">A senha deve ter pelo menos 6 caracteres.</div>
			</div>
		</div>
	</div>
	<div class="card-footer d-flex justify-content-end gap-2">
		<button type="submit" class="btn btn-primary">Salvar</button>
		<a href="/sistema_escalacao/public/empresas/index.php" class="btn btn-outline-secondary">Cancelar</a>
	</div>
</form>
<script>
(function(){
	'use strict';
	var forms = document.querySelectorAll('.needs-validation');
	Array.prototype.slice.call(forms).forEach(function(form){
		form.addEventListener('submit', function (event) {
			var cnpjInput = form.querySelector('#cnpj');
			if (cnpjInput) {
				var digits = (cnpjInput.value||'').replace(/\D+/g,'');
				if (digits.length !== 14) {
					cnpjInput.setCustomValidity('CNPJ inválido');
				} else {
					cnpjInput.setCustomValidity('');
				}
			}
			var telInput = form.querySelector('#telefone');
			if (telInput) {
				var tdigits = (telInput.value||'').replace(/\D+/g,'');
				if (tdigits.length < 10 || tdigits.length > 11) {
					telInput.setCustomValidity('Telefone inválido');
				} else {
					telInput.setCustomValidity('');
				}
			}
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			var passInput = form.querySelector('#senha');
			if (passInput) {
				var val = (passInput.value||'');
				if (!passInput.dataset.optional || val.length > 0) {
					if (val.length < 6) {
						passInput.setCustomValidity('Senha deve ter pelo menos 6 caracteres');
					} else {
						passInput.setCustomValidity('');
					}
				} else {
					passInput.setCustomValidity('');
				}
			}
			form.classList.add('was-validated');
		}, false);
	});
	if (window.applyCnpjMask) {
		window.applyCnpjMask(document.getElementById('cnpj'));
	}
	if (window.applyPhoneMask) {
		window.applyPhoneMask(document.getElementById('telefone'));
	}
})();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
