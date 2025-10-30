<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();

function redirect_with($params, $to){ header('Location: ' . $to . '?' . http_build_query($params)); exit; }

$eventoId = isset($_POST['evento_id']) ? (int)$_POST['evento_id'] : 0;
$ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
if ($eventoId <= 0) { redirect_with(['err'=>'Evento inválido'], '/sistema_escalacao/public/empresa/dashboard.php'); }

// Validate event belongs to company
$stmt = $mysqli->prepare('SELECT empresa_id FROM eventos WHERE id = ?');
$stmt->bind_param('i', $eventoId);
$stmt->execute();
$ev = $stmt->get_result()->fetch_assoc();
if (!$ev || (int)$ev['empresa_id'] !== (int)$_SESSION['empresa_id']) {
    redirect_with(['err'=>'Sem permissão'], '/sistema_escalacao/public/empresa/dashboard.php');
}

// Approve selected, set others to recusado
$mysqli->begin_transaction();
try {
    // First set all to recusado
    $stmt = $mysqli->prepare('UPDATE evento_inscricoes SET status = "recusado" WHERE evento_id = ?');
    $stmt->bind_param('i', $eventoId);
    $stmt->execute();
    if (!empty($ids)) {
        // Approve selected
        $in = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $sql = 'UPDATE evento_inscricoes SET status = "aprovado" WHERE id IN ('.$in.') AND evento_id = ?';
        $stmt2 = $mysqli->prepare($sql);
        $types2 = $types . 'i';
        $params = array_merge($ids, [$eventoId]);
        $stmt2->bind_param($types2, ...$params);
        $stmt2->execute();
    }
    $mysqli->commit();
} catch (Throwable $e) {
    $mysqli->rollback();
    redirect_with(['err'=>'Erro ao finalizar escala'], '/sistema_escalacao/public/eventos/show.php?id='.$eventoId);
}

// Após finalizar, gerar arquivo automaticamente
header('Location: /sistema_escalacao/public/eventos/export_csv.php?evento_id='.$eventoId);
exit;

