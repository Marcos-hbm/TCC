<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();

function redirect_with($params, $to){
    header('Location: ' . $to . '?' . http_build_query($params));
    exit;
}

$inscId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$acao = isset($_POST['acao']) ? $_POST['acao'] : '';
if ($inscId <= 0 || !in_array($acao, ['aprovar','recusar'], true)) {
    redirect_with(['err'=>'Requisição inválida'], '/sistema_escalacao/public/empresa/dashboard.php');
}

// Load inscrição + evento
$stmt = $mysqli->prepare('SELECT i.id, i.evento_id, e.empresa_id FROM evento_inscricoes i INNER JOIN eventos e ON e.id = i.evento_id WHERE i.id = ?');
$stmt->bind_param('i', $inscId);
$stmt->execute();
$insc = $stmt->get_result()->fetch_assoc();
if (!$insc || (int)$insc['empresa_id'] !== (int)$_SESSION['empresa_id']) {
    redirect_with(['err'=>'Sem permissão'], '/sistema_escalacao/public/empresa/dashboard.php');
}

$status = $acao === 'aprovar' ? 'aprovado' : 'recusado';
$stmt = $mysqli->prepare('UPDATE evento_inscricoes SET status = ? WHERE id = ?');
$stmt->bind_param('si', $status, $inscId);
$stmt->execute();

redirect_with(['msg'=>'Inscrição ' . ($acao==='aprovar'?'aprovada':'recusada')], '/sistema_escalacao/public/empresa/dashboard.php');


