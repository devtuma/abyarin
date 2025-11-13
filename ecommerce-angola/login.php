<?php
$titulo = "Login";
require_once __DIR__ . '/includes/header.php';

// Redirecionar se já estiver logado
if (usuarioLogado()) {
    redirecionar(urlBase('minha-conta.php'));
}

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        adicionarMensagem('erro', 'Token de segurança inválido.');
    } else {
        $email = limpar($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            adicionarMensagem('erro', 'Preencha todos os campos.');
        } elseif (!validarEmail($email)) {
            adicionarMensagem('erro', 'E-mail inválido.');
        } else {
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();

            if ($usuario && verificarSenha($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['is_admin'] = $usuario['is_admin'];

                adicionarMensagem('sucesso', 'Login realizado com sucesso!');

                // Redirecionar para página anterior ou minha conta
                $redirect = $_GET['redirect'] ?? 'minha-conta.php';
                redirecionar(urlBase($redirect));
            } else {
                adicionarMensagem('erro', 'E-mail ou senha incorretos.');
            }
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-box">
        <h1><i class="fas fa-user-lock"></i> Login</h1>
        <p class="auth-subtitle">Entre com sua conta para continuar</p>

        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> E-mail
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="seu@email.com"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label for="senha">
                    <i class="fas fa-lock"></i> Senha
                </label>
                <input type="password"
                       id="senha"
                       name="senha"
                       placeholder="Digite sua senha"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-large">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>

        <div class="auth-links">
            <p>Não tem uma conta? <a href="<?php echo urlBase('registro.php'); ?>">Registre-se</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
