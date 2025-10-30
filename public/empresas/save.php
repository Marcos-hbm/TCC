<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
// Edição protegida, criação pública
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0) { require_company(); }

function redirect_with($params){
	$base = '/sistema_escalacao/public/empresas/index.php';
	header('Location: ' . $base . '?' . http_build_query($params));
	exit;
}

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$cnpj = isset($_POST['cnpj']) ? preg_replace('/\D+/', '', $_POST['cnpj']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefone = isset($_POST['telefone']) ? preg_replace('/\D+/', '', $_POST['telefone']) : '';
$senha = isset($_POST['senha']) ? (string)$_POST['senha'] : '';

// Password validation
$passwordInvalid = false;
if ($id > 0) {
	if ($senha !== '' && strlen($senha) < 6) { $passwordInvalid = true; }
} else {
	if (strlen($senha) < 6) { $passwordInvalid = true; }
}

if ($nome === '' || strlen($cnpj) !== 14 || !filter_var($email, FILTER_VALIDATE_EMAIL) || ($telefone === '' || strlen($telefone) < 10 || strlen($telefone) > 11) || $passwordInvalid) {
	redirect_with(['err' => 'Dados inválidos. Verifique os campos.']);
}

// Duplicate checks
if ($id > 0) {
	$stmt = $mysqli->prepare('SELECT COUNT(1) AS c FROM empresas WHERE cnpj = ? AND id <> ?');
	$stmt->bind_param('si', $cnpj, $id);
} else {
	$stmt = $mysqli->prepare('SELECT COUNT(1) AS c FROM empresas WHERE cnpj = ?');
	$stmt->bind_param('s', $cnpj);
}
$stmt->execute();
$dup = $stmt->get_result()->fetch_assoc();
if (!empty($dup['c'])) {
	redirect_with(['err' => 'CNPJ já cadastrado.']);
}

try {
	if ($id > 0) {
		if ($senha !== '') {
			$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
			$stmt = $mysqli->prepare('UPDATE empresas SET nome = ?, cnpj = ?, email = ?, telefone = ?, senha_hash = ? WHERE id = ?');
			$stmt->bind_param('sssssi', $nome, $cnpj, $email, $telefone, $senha_hash, $id);
		} else {
			$stmt = $mysqli->prepare('UPDATE empresas SET nome = ?, cnpj = ?, email = ?, telefone = ? WHERE id = ?');
			$stmt->bind_param('ssssi', $nome, $cnpj, $email, $telefone, $id);
		}
		$stmt->execute();
		redirect_with(['msg' => 'Empresa atualizada com sucesso']);
	} else {
		$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
		$stmt = $mysqli->prepare('INSERT INTO empresas (nome, cnpj, email, telefone, senha_hash) VALUES (?, ?, ?, ?, ?)');
		$stmt->bind_param('sssss', $nome, $cnpj, $email, $telefone, $senha_hash);
		$stmt->execute();
		redirect_with(['msg' => 'Empresa criada com sucesso']);
	}
} catch (mysqli_sql_exception $e) {
	if (strpos($e->getMessage(), 'uniq_cnpj') !== false) {
		redirect_with(['err' => 'CNPJ já cadastrado.']);
	} elseif (strpos($e->getMessage(), 'uniq_email_empresa') !== false) {
		redirect_with(['err' => 'Email já cadastrado.']);
	}
	redirect_with(['err' => 'Erro no banco de dados.']);
}
