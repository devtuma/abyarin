<?php
// Configurações do E-commerce Angola

// Configurações do Banco de Dados
// IMPORTANTE: Altere estas informações com os dados da sua Hostinger
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_angola');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');

// Configurações do Site
define('SITE_NAME', 'Loja Angola');
define('SITE_URL', 'http://localhost/ecommerce-angola');
define('CURRENCY', 'Kz'); // Kwanza Angolano
define('CURRENCY_CODE', 'AOA');

// Configurações de Email
define('EMAIL_FROM', 'noreply@loja.ao');
define('EMAIL_NAME', 'Loja Angola');

// Configurações de Upload
define('UPLOAD_DIR', __DIR__ . '/../assets/images/produtos/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Configurações de Sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 se usar HTTPS

// Timezone
date_default_timezone_set('Africa/Luanda');

// Modo de Debug (desative em produção)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
