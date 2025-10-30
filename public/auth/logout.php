<?php
session_start();
session_unset();
session_destroy();
header('Location: /sistema_escalacao/public/auth/login.php?msg=Você saiu da sua conta.');
exit;

