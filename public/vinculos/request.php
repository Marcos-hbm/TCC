<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user_or_company();

function redirect_with($params){
    $base = '/sistema_escalacao/public/empresa/dashboard.php';
    header('Location: ' . $base . '?' . http_build_query($params));
    exit;
}

$empresaId = isset($_POST['empresa_id']) ? (int)$_POST['empresa_id'] : 0;
$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($empresaId <= 0 && $userId <= 0) {
    redirect_with(['err' => 'Dados inválidos.']);
}

if (!empty($_SESSION['user_id'])) {
    $user = (int)$_SESSION['user_id'];
    if ($empresaId <= 0) redirect_with(['err'=>'Empresa inválida']);
    $who = 'user';
    $stmt = $mysqli->prepare('INSERT INTO vinculos (user_id, empresa_id, status, solicitado_por) VALUES (?, ?, "pendente", ?) ON DUPLICATE KEY UPDATE solicitado_por = VALUES(solicitado_por), updated_at = CURRENT_TIMESTAMP');
    $stmt->bind_param('iis', $user, $empresaId, $who);
    $stmt->execute();
    redirect_with(['msg'=>'Solicitação enviada à empresa.']);
}

if (!empty($_SESSION['empresa_id'])) {
    $empresa = (int)$_SESSION['empresa_id'];
    if ($userId <= 0) redirect_with(['err'=>'Usuário inválido']);
    $who = 'empresa';
    $stmt = $mysqli->prepare('INSERT INTO vinculos (user_id, empresa_id, status, solicitado_por) VALUES (?, ?, "pendente", ?) ON DUPLICATE KEY UPDATE solicitado_por = VALUES(solicitado_por), updated_at = CURRENT_TIMESTAMP');
    $stmt->bind_param('iis', $userId, $empresa, $who);
    $stmt->execute();
    redirect_with(['msg'=>'Solicitação enviada ao usuário.']);
}
redirect_with(['err'=>'Não autenticado.']);


