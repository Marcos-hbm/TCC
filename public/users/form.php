<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
// Edição protegida, criação pública
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) { require_user_or_company(); }
include __DIR__ . '/../../includes/header.php';

$user = [
	'id' => 0,
	'nome' => '',
	'email' => '',
	'data_nascimento' => '',
	'cpf' => '',
	'telefone' => '',
	'genero' => ''
];
if ($id > 0) {
	$stmt = $mysqli->prepare('SELECT id, nome, email, DATE_FORMAT(data_nascimento, "%Y-%m-%d") AS data_nascimento, cpf, telefone, genero FROM users WHERE id = ?');
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($row = $res->fetch_assoc()) {
		$user = $row;
	} else {
		header('Location: /sistema_escalacao/public/users/index.php?err=Usuário não encontrado');
		exit;
	}
}
$pageTitle = $id ? 'Editar Usuário' : 'Novo Usuário';
$pageIcon = $id ? 'bi-pencil' : 'bi-plus-circle';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0">
		<i class="bi bi-<?= $id ? 'pencil' : 'person-plus' ?> me-2"></i>
		<?= $id ? 'Editar Usuário' : 'Novo Usuário' ?>
	</h1>
	<a class="btn btn-outline-secondary" href="/sistema_escalacao/public/users/index.php">
		<i class="bi bi-arrow-left me-1"></i>Voltar
	</a>
</div>
<form action="/sistema_escalacao/public/users/save.php" method="post" class="card needs-validation" novalidate>
	<div class="card-body">
		<input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
		<div class="row g-3">
			<div class="col-md-6">
				<label class="form-label">Nome</label>
				<input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($user['nome']) ?>">
				<div class="invalid-feedback">Informe o nome.</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">Email</label>
				<input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
				<div class="invalid-feedback">Informe um email válido.</div>
			</div>
			<div class="col-md-4">
				<label class="form-label">Data de Nascimento</label>
				<input type="date" name="data_nascimento" id="data_nascimento" class="form-control" required value="<?= htmlspecialchars($user['data_nascimento']) ?>">
				<div class="invalid-feedback">Informe a data de nascimento (18+).</div>
			</div>
			<div class="col-md-4">
				<label class="form-label">CPF</label>
				<input type="text" name="cpf" id="cpf" class="form-control" maxlength="14" required value="<?= htmlspecialchars($user['cpf']) ?>">
				<div class="invalid-feedback">Informe um CPF válido (11 dígitos).</div>
			</div>
			<div class="col-md-4">
				<label class="form-label">Telefone</label>
				<input type="text" name="telefone" id="telefone" class="form-control" maxlength="16" required value="<?= htmlspecialchars($user['telefone']) ?>">
				<div class="invalid-feedback">Informe um telefone válido (10-11 dígitos).</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">Gênero</label>
				<select name="genero" class="form-select" required>
					<?php
					$generos = ['Masculino','Feminino','Outro','Prefiro não dizer'];
					foreach ($generos as $g) {
						$sel = $user['genero'] === $g ? 'selected' : '';
						echo '<option value="'.htmlspecialchars($g).'" '.$sel.'>'.htmlspecialchars($g).'</option>';
					}
					?>
				</select>
				<div class="invalid-feedback">Selecione o gênero.</div>
			</div>
			<div class="col-md-6">
				<label class="form-label">Senha <?= $id ? '(opcional)' : '' ?></label>
				<input type="password" name="senha" id="senha" class="form-control" <?= $id ? 'data-optional="1"' : 'required' ?> minlength="6" placeholder="<?= $id ? 'Deixe em branco para manter' : 'Mínimo 6 caracteres' ?>">
				<div class="invalid-feedback">A senha deve ter pelo menos 6 caracteres.</div>
			</div>
		</div>
	</div>
	<div class="card-footer d-flex justify-content-between align-items-center gap-2">
		<div class="text-muted small">
			<?php if ($id): ?>
				<i class="bi bi-info-circle me-1"></i>Deixe a senha em branco para manter a atual.
			<?php endif; ?>
		</div>
		<div class="d-flex gap-2">
			<button type="submit" class="btn btn-primary">
				<i class="bi bi-save me-1"></i>Salvar
			</button>
			<a href="/sistema_escalacao/public/users/index.php" class="btn btn-outline-secondary">
				<i class="bi bi-x-circle me-1"></i>Cancelar
			</a>
		</div>
	</div>
</form>
<script>
(function(){
	'use strict';
	function formatDate(d){ return d.toISOString().slice(0,10); }
	var dob = document.getElementById('data_nascimento');
	if (dob) {
		var today = new Date();
		var cutoff = new Date(today.getFullYear()-18, today.getMonth(), today.getDate());
		dob.setAttribute('max', formatDate(cutoff));
		dob.addEventListener('change', function(){
			var chosen = new Date(this.value);
			if (this.value && chosen > cutoff) {
				this.setCustomValidity('Permitido apenas para maiores de 18 anos.');
			} else {
				this.setCustomValidity('');
			}
		});
	}
	var forms = document.querySelectorAll('.needs-validation');
	Array.prototype.slice.call(forms).forEach(function(form){
		form.addEventListener('submit', function (event) {
			var cpfInput = form.querySelector('#cpf');
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
			if (cpfInput) {
				var digits = (cpfInput.value||'').replace(/\D+/g,'');
				if (digits.length !== 11) {
					cpfInput.setCustomValidity('CPF inválido');
				} else {
					cpfInput.setCustomValidity('');
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
			form.classList.add('was-validated');
		}, false);
	});
	if (window.applyCpfMask) {
		window.applyCpfMask(document.getElementById('cpf'));
	}
	if (window.applyPhoneMask) {
		window.applyPhoneMask(document.getElementById('telefone'));
	}
})();
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
