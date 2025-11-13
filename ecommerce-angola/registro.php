<?php
$titulo = "Registrar";
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
        $nome = limpar($_POST['nome'] ?? '');
        $email = limpar($_POST['email'] ?? '');
        $telefone = limpar($_POST['telefone'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';

        $erros = [];

        if (empty($nome)) {
            $erros[] = 'O nome é obrigatório.';
        }

        if (empty($email)) {
            $erros[] = 'O e-mail é obrigatório.';
        } elseif (!validarEmail($email)) {
            $erros[] = 'E-mail inválido.';
        } else {
            // Verificar se email já existe
            $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $erros[] = 'Este e-mail já está cadastrado.';
            }
        }

        if (empty($telefone)) {
            $erros[] = 'O telefone é obrigatório.';
        } elseif (!validarTelefone($telefone)) {
            $erros[] = 'Telefone inválido.';
        }

        if (empty($senha)) {
            $erros[] = 'A senha é obrigatória.';
        } elseif (strlen($senha) < 6) {
            $erros[] = 'A senha deve ter no mínimo 6 caracteres.';
        } elseif ($senha !== $confirmar_senha) {
            $erros[] = 'As senhas não coincidem.';
        }

        if (empty($erros)) {
            $senha_hash = gerarHashSenha($senha);

            $stmt = $db->prepare("
                INSERT INTO usuarios (nome, email, telefone, senha)
                VALUES (?, ?, ?, ?)
            ");

            if ($stmt->execute([$nome, $email, $telefone, $senha_hash])) {
                adicionarMensagem('sucesso', 'Cadastro realizado com sucesso! Faça login para continuar.');
                redirecionar(urlBase('login.php'));
            } else {
                adicionarMensagem('erro', 'Erro ao criar conta. Tente novamente.');
            }
        } else {
            foreach ($erros as $erro) {
                adicionarMensagem('erro', $erro);
            }
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-box">
        <h1><i class="fas fa-user-plus"></i> Criar Conta</h1>
        <p class="auth-subtitle">Preencha os dados abaixo para criar sua conta</p>

        <form method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">

            <div class="form-group">
                <label for="nome">
                    <i class="fas fa-user"></i> Nome Completo
                </label>
                <input type="text"
                       id="nome"
                       name="nome"
                       value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                       placeholder="Seu nome completo"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> E-mail
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="seu@email.com"
                       required>
            </div>

            <div class="form-group">
                <label for="telefone">
                    <i class="fas fa-phone"></i> Telefone
                </label>
                <input type="tel"
                       id="telefone"
                       name="telefone"
                       value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>"
                       placeholder="923 456 789"
                       required>
            </div>

            <div class="form-group">
                <label for="senha">
                    <i class="fas fa-lock"></i> Senha
                </label>
                <input type="password"
                       id="senha"
                       name="senha"
                       placeholder="Mínimo 6 caracteres"
                       required>
            </div>

            <div class="form-group">
                <label for="confirmar_senha">
                    <i class="fas fa-lock"></i> Confirmar Senha
                </label>
                <input type="password"
                       id="confirmar_senha"
                       name="confirmar_senha"
                       placeholder="Digite a senha novamente"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-large">
                <i class="fas fa-user-plus"></i> Criar Conta
            </button>
        </form>

        <div class="auth-links">
            <p>Já tem uma conta? <a href="<?php echo urlBase('login.php'); ?>">Faça login</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
