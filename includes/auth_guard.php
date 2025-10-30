<?php
session_start();

function require_user(){
	if (empty($_SESSION['user_id'])) {
		header('Location: /sistema_escalacao/public/auth/login.php?err=Faça login como usuário para acessar.');
		exit;
	}
}

function require_company(){
	if (empty($_SESSION['empresa_id'])) {
		header('Location: /sistema_escalacao/public/auth/login.php?err=Faça login como empresa para acessar.');
		exit;
	}
}

function require_user_or_company(){
    if (empty($_SESSION['user_id']) && empty($_SESSION['empresa_id'])) {
        header('Location: /sistema_escalacao/public/auth/login.php?err=Faça login para acessar.');
        exit;
    }
}




