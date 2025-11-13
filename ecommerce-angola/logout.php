<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

iniciarSessao();
session_destroy();

header("Location: " . urlBase());
exit;
