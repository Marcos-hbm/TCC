<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();

function redirect_with($params){
	$base = '/sistema_escalacao/public/eventos/index.php';
	header('Location: ' . $base . '?' . http_build_query($params));
	exit;
}

$empresaId = (int)$_SESSION['empresa_id'];
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
	redirect_with(['err' => 'ID inválido.']);
}

$check = $mysqli->prepare('SELECT id FROM eventos WHERE id = ? AND empresa_id = ?');
$check->bind_param('ii', $id, $empresaId);
$check->execute();
if (!$check->get_result()->fetch_assoc()) {
	redirect_with(['err' => 'Evento inválido.']);
}

$stmt = $mysqli->prepare('DELETE FROM eventos WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

redirect_with(['msg' => 'Evento excluído com sucesso']);










