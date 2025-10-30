<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_company();

$eventoId = isset($_GET['evento_id']) ? (int)$_GET['evento_id'] : 0;
if ($eventoId <= 0) {
    header('Location: /sistema_escalacao/public/empresa/dashboard.php?err=Evento inválido');
    exit;
}

$stmt = $mysqli->prepare('SELECT empresa_id, nome FROM eventos WHERE id = ?');
$stmt->bind_param('i', $eventoId);
$stmt->execute();
$ev = $stmt->get_result()->fetch_assoc();
if (!$ev || (int)$ev['empresa_id'] !== (int)$_SESSION['empresa_id']) {
    header('Location: /sistema_escalacao/public/empresa/dashboard.php?err=Sem permissão');
    exit;
}

$stmt = $mysqli->prepare('SELECT u.nome, u.cpf, u.data_nascimento FROM evento_inscricoes i INNER JOIN users u ON u.id = i.user_id WHERE i.evento_id = ? AND i.status = "aprovado" ORDER BY u.nome ASC');
$stmt->bind_param('i', $eventoId);
$stmt->execute();
$res = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="inscritos_'.preg_replace('/[^a-z0-9_\-]+/i','_', $ev['nome']).'_'.$eventoId.'.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['Nome', 'CPF', 'Data de Nascimento']);
while ($row = $res->fetch_assoc()) {
    fputcsv($out, [$row['nome'], $row['cpf'], $row['data_nascimento']]);
}
fclose($out);
exit;

