<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user_or_company();

function redirect_with($params, $to){
    header('Location: ' . $to . '?' . http_build_query($params));
    exit;
}

if (empty($_SESSION['user_id'])) {
    redirect_with(['err'=>'Apenas usuários podem se inscrever.'], '/sistema_escalacao/public/auth/login.php');
}

$userId = (int)$_SESSION['user_id'];
$eventoId = isset($_POST['evento_id']) ? (int)$_POST['evento_id'] : 0;
if ($eventoId <= 0) {
    redirect_with(['err'=>'Evento inválido'], '/sistema_escalacao/public/empresa/dashboard.php');
}

// Get company from event
$stmt = $mysqli->prepare('SELECT empresa_id FROM eventos WHERE id = ?');
$stmt->bind_param('i', $eventoId);
$stmt->execute();
$ev = $stmt->get_result()->fetch_assoc();
if (!$ev) {
    redirect_with(['err'=>'Evento não encontrado'], '/sistema_escalacao/public/empresa/dashboard.php');
}
$empresaId = (int)$ev['empresa_id'];

// Check vínculo aprovado
$stmt = $mysqli->prepare('SELECT status FROM vinculos WHERE user_id = ? AND empresa_id = ?');
$stmt->bind_param('ii', $userId, $empresaId);
$stmt->execute();
$v = $stmt->get_result()->fetch_assoc();
if (!$v || $v['status'] !== 'aprovado') {
    redirect_with(['err'=>'Vínculo com a empresa não aprovado.'], '/sistema_escalacao/public/empresa/dashboard.php');
}

// Create inscrição (or ignore if exists)
$stmt = $mysqli->prepare('INSERT INTO evento_inscricoes (evento_id, user_id, status) VALUES (?, ?, "inscrito") ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP');
$stmt->bind_param('ii', $eventoId, $userId);
$stmt->execute();

redirect_with(['msg'=>'Inscrição enviada. Aguarde aprovação da empresa.'], '/sistema_escalacao/public/users/discover.php');


