<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user();

function redirect_with($params){
	$base = '/sistema_escalacao/public/users/index.php';
	header('Location: ' . $base . '?' . http_build_query($params));
	exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
	redirect_with(['err' => 'ID inválido.']);
}

$stmt = $mysqli->prepare('DELETE FROM users WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
// Logout and go to login
session_destroy();
header('Location: /sistema_escalacao/public/auth/login.php?msg=Conta excluída com sucesso');
exit;
