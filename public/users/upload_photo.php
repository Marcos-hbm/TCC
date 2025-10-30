<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_user();

function redirect_with($params){
	$base = '/sistema_escalacao/public/users/profile.php';
	$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
	header('Location: ' . $base . '?id=' . $id . '&' . http_build_query($params));
	exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
	redirect_with(['err' => 'Usuário inválido']);
}

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
	redirect_with(['err' => 'Arquivo inválido.']);
}

$file = $_FILES['foto'];
$maxSize = 2 * 1024 * 1024; // 2MB
if ($file['size'] > $maxSize) {
	redirect_with(['err' => 'Arquivo muito grande (máx 2MB).']);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$ext = '';
if ($mime === 'image/jpeg') { $ext = 'jpg'; }
elseif ($mime === 'image/png') { $ext = 'png'; }
else { redirect_with(['err' => 'Tipo de arquivo não suportado.']); }

$uploadsDir = __DIR__ . '/../uploads';
if (!is_dir($uploadsDir)) {
	mkdir($uploadsDir, 0777, true);
}

$filename = 'user_' . $id . '_' . time() . '.' . $ext;
$relativePath = 'public/uploads/' . $filename;
$target = __DIR__ . '/../uploads/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $target)) {
	redirect_with(['err' => 'Falha ao salvar o arquivo.']);
}

$stmt = $mysqli->prepare('UPDATE users SET foto_path = ? WHERE id = ?');
$stmt->bind_param('si', $relativePath, $id);
$stmt->execute();

redirect_with(['msg' => 'Foto atualizada com sucesso']);
