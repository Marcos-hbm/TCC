<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
// Edição protegida, criação pública
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0) { require_user_or_company(); }

function redirect_with($params){
	$base = '/sistema_escalacao/public/users/index.php';
	header('Location: ' . $base . '?' . http_build_query($params));
	exit;
}

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$data_nascimento = isset($_POST['data_nascimento']) ? trim($_POST['data_nascimento']) : '';
$cpf = isset($_POST['cpf']) ? preg_replace('/\D+/', '', $_POST['cpf']) : '';
$telefone = isset($_POST['telefone']) ? preg_replace('/\D+/', '', $_POST['telefone']) : '';
$genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';
$senha = isset($_POST['senha']) ? (string)$_POST['senha'] : '';

// Validate 18+
$dob = DateTime::createFromFormat('Y-m-d', $data_nascimento) ?: null;
$cutoff = new DateTime('now');
$cutoff->setTime(0,0,0);
$cutoff->modify('-18 years');
$underage = !$dob || $dob > $cutoff;


// Password validation: required on create, optional on update; minimum 6 chars when provided
$passwordInvalid = false;
if ($id > 0) {
	if ($senha !== '' && strlen($senha) < 6) { $passwordInvalid = true; }
} else {
	if (strlen($senha) < 6) { $passwordInvalid = true; }
}

if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $data_nascimento === '' || strlen($cpf) !== 11 || ($telefone === '' || strlen($telefone) < 10 || strlen($telefone) > 11) || $underage || $passwordInvalid) {
	redirect_with(['err' => $underage ? 'Permitido apenas para maiores de 18 anos.' : 'Dados inválidos. Verifique os campos.']);
}
$generos_validos = ['Masculino','Feminino','Outro','Prefiro não dizer'];
if (!in_array($genero, $generos_validos, true)) {
	redirect_with(['err' => 'Gênero inválido.']);
}

// Check duplicate CPF (excluding current id when editing)
if ($id > 0) {
	$stmt = $mysqli->prepare('SELECT COUNT(1) AS c FROM users WHERE cpf = ? AND id <> ?');
	$stmt->bind_param('si', $cpf, $id);
} else {
	$stmt = $mysqli->prepare('SELECT COUNT(1) AS c FROM users WHERE cpf = ?');
	$stmt->bind_param('s', $cpf);
}
$stmt->execute();
$dup = $stmt->get_result()->fetch_assoc();
if (!empty($dup['c'])) {
	redirect_with(['err' => 'CPF já cadastrado.']);
}

try {
	if ($id > 0) {
		if ($senha !== '') {
			$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
			$stmt = $mysqli->prepare('UPDATE users SET nome = ?, email = ?, data_nascimento = ?, cpf = ?, telefone = ?, genero = ?, senha_hash = ? WHERE id = ?');
			$stmt->bind_param('sssssssi', $nome, $email, $data_nascimento, $cpf, $telefone, $genero, $senha_hash, $id);
		} else {
			$stmt = $mysqli->prepare('UPDATE users SET nome = ?, email = ?, data_nascimento = ?, cpf = ?, telefone = ?, genero = ? WHERE id = ?');
			$stmt->bind_param('ssssssi', $nome, $email, $data_nascimento, $cpf, $telefone, $genero, $id);
		}
		$stmt->execute();
		redirect_with(['msg' => 'Usuário atualizado com sucesso']);
	} else {
		$senha_hash = password_hash($senha, PASSWORD_DEFAULT);
		$stmt = $mysqli->prepare('INSERT INTO users (nome, email, data_nascimento, cpf, telefone, genero, senha_hash) VALUES (?, ?, ?, ?, ?, ?, ?)');
		$stmt->bind_param('sssssss', $nome, $email, $data_nascimento, $cpf, $telefone, $genero, $senha_hash);
		$stmt->execute();
		redirect_with(['msg' => 'Usuário criado com sucesso']);
	}
} catch (mysqli_sql_exception $e) {
	if (strpos($e->getMessage(), 'uniq_email') !== false) {
		redirect_with(['err' => 'Email já cadastrado.']);
	} elseif (strpos($e->getMessage(), 'uniq_cpf') !== false) {
		redirect_with(['err' => 'CPF já cadastrado.']);
	}
	redirect_with(['err' => 'Erro no banco de dados.']);
}
