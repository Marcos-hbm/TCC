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
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$data_evento = isset($_POST['data_evento']) ? trim($_POST['data_evento']) : '';
$valor_cache = isset($_POST['valor_cache']) ? (string)$_POST['valor_cache'] : '0.00';
$valor_cache_float = (float)$valor_cache;
$observacoes = isset($_POST['observacoes']) ? trim($_POST['observacoes']) : '';

if ($nome === '' || $data_evento === '' || !is_numeric($valor_cache) || (float)$valor_cache < 0) {
	redirect_with(['err' => 'Dados inválidos do evento.']);
}

if ($id > 0) {
	// Ensure the event belongs to the logged company
	$check = $mysqli->prepare('SELECT id FROM eventos WHERE id = ? AND empresa_id = ?');
	$check->bind_param('ii', $id, $empresaId);
	$check->execute();
	if (!$check->get_result()->fetch_assoc()) {
		redirect_with(['err' => 'Evento inválido.']);
	}
    $stmt = $mysqli->prepare('UPDATE eventos SET nome = ?, descricao = ?, data_evento = ?, valor_cache = ?, observacoes = ? WHERE id = ?');
    $stmt->bind_param('sssdsi', $nome, $descricao, $data_evento, $valor_cache_float, $observacoes, $id);
	$stmt->execute();
	redirect_with(['msg' => 'Evento atualizado com sucesso']);
} else {
	$stmt = $mysqli->prepare('INSERT INTO eventos (empresa_id, nome, descricao, data_evento, valor_cache, observacoes) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssds', $empresaId, $nome, $descricao, $data_evento, $valor_cache_float, $observacoes);
	$stmt->execute();
	redirect_with(['msg' => 'Evento criado com sucesso']);
}
