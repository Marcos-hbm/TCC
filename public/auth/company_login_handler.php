<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

function redirect_with($params, $to = '/sistema_escalacao/public/auth/company_login.php'){
	header('Location: ' . $to . '?' . http_build_query($params));
	exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? (string)$_POST['senha'] : '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
	redirect_with(['err' => 'Credenciais inválidas.']);
}

$stmt = $mysqli->prepare('SELECT id, nome, email, senha_hash FROM empresas WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();
if (!$empresa || !password_verify($senha, $empresa['senha_hash'])) {
	redirect_with(['err' => 'Email ou senha incorretos.']);
}

$_SESSION['empresa_id'] = (int)$empresa['id'];
$_SESSION['empresa_nome'] = $empresa['nome'];

redirect_with(['msg' => 'Login realizado com sucesso'], '/sistema_escalacao/public/empresa/dashboard.php');




