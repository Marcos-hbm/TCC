<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user_or_company();

function redirect_with($params){
    $base = '/sistema_escalacao/public/empresa/dashboard.php';
    header('Location: ' . $base . '?' . http_build_query($params));
    exit;
}

$vinculoId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$acao = isset($_POST['acao']) ? $_POST['acao'] : '';
if ($vinculoId <= 0 || !in_array($acao, ['aprovar','recusar'], true)) {
    redirect_with(['err'=>'Requisição inválida']);
}

// Load vinculo
$stmt = $mysqli->prepare('SELECT id, user_id, empresa_id, status, solicitado_por FROM vinculos WHERE id = ?');
$stmt->bind_param('i', $vinculoId);
$stmt->execute();
$v = $stmt->get_result()->fetch_assoc();
if (!$v) { redirect_with(['err'=>'Vínculo não encontrado']); }

$isUser = !empty($_SESSION['user_id']);
$isEmpresa = !empty($_SESSION['empresa_id']);
if ($isUser && (int)$_SESSION['user_id'] !== (int)$v['user_id']) {
    redirect_with(['err'=>'Sem permissão']);
}
if ($isEmpresa && (int)$_SESSION['empresa_id'] !== (int)$v['empresa_id']) {
    redirect_with(['err'=>'Sem permissão']);
}

$newStatus = $acao === 'aprovar' ? 'aprovado' : 'recusado';
$stmt = $mysqli->prepare('UPDATE vinculos SET status = ? WHERE id = ?');
$stmt->bind_param('si', $newStatus, $vinculoId);
$stmt->execute();

redirect_with(['msg'=>'Vínculo ' . ($acao==='aprovar'?'aprovado':'recusado')]);


