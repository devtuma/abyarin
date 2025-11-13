<?php
// Funções Auxiliares do E-commerce

// Iniciar sessão se não estiver iniciada
function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Verificar se usuário está logado
function usuarioLogado() {
    iniciarSessao();
    return isset($_SESSION['usuario_id']);
}

// Verificar se é admin
function isAdmin() {
    iniciarSessao();
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Redirecionar
function redirecionar($url) {
    header("Location: " . $url);
    exit;
}

// Limpar dados de entrada
function limpar($dados) {
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

// Formatar preço
function formatarPreco($valor) {
    return number_format($valor, 2, ',', '.') . ' ' . CURRENCY;
}

// Gerar token CSRF
function gerarTokenCSRF() {
    iniciarSessao();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verificarTokenCSRF($token) {
    iniciarSessao();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Adicionar mensagem flash
function adicionarMensagem($tipo, $mensagem) {
    iniciarSessao();
    $_SESSION['mensagem'] = [
        'tipo' => $tipo,
        'texto' => $mensagem
    ];
}

// Exibir e limpar mensagem flash
function exibirMensagem() {
    iniciarSessao();
    if (isset($_SESSION['mensagem'])) {
        $tipo = $_SESSION['mensagem']['tipo'];
        $texto = $_SESSION['mensagem']['texto'];
        $classe = $tipo === 'sucesso' ? 'success' : ($tipo === 'erro' ? 'error' : 'info');

        echo "<div class='mensagem {$classe}'>{$texto}</div>";
        unset($_SESSION['mensagem']);
    }
}

// Upload de imagem
function uploadImagem($arquivo, $pasta = 'produtos') {
    $diretorio = __DIR__ . "/../assets/images/{$pasta}/";

    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extensao, $permitidos)) {
        return ['sucesso' => false, 'mensagem' => 'Formato de arquivo não permitido'];
    }

    if ($arquivo['size'] > MAX_FILE_SIZE) {
        return ['sucesso' => false, 'mensagem' => 'Arquivo muito grande'];
    }

    $nomeArquivo = uniqid() . '.' . $extensao;
    $caminhoCompleto = $diretorio . $nomeArquivo;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return ['sucesso' => true, 'arquivo' => $nomeArquivo];
    }

    return ['sucesso' => false, 'mensagem' => 'Erro ao fazer upload'];
}

// Obter carrinho
function obterCarrinho() {
    iniciarSessao();
    return isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
}

// Adicionar ao carrinho
function adicionarAoCarrinho($produto_id, $quantidade = 1) {
    iniciarSessao();

    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }
}

// Remover do carrinho
function removerDoCarrinho($produto_id) {
    iniciarSessao();
    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }
}

// Atualizar quantidade no carrinho
function atualizarCarrinho($produto_id, $quantidade) {
    iniciarSessao();
    if ($quantidade <= 0) {
        removerDoCarrinho($produto_id);
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }
}

// Limpar carrinho
function limparCarrinho() {
    iniciarSessao();
    unset($_SESSION['carrinho']);
}

// Contar itens do carrinho
function contarItensCarrinho() {
    $carrinho = obterCarrinho();
    return array_sum($carrinho);
}

// Calcular total do carrinho
function calcularTotalCarrinho() {
    $carrinho = obterCarrinho();
    $total = 0;

    if (!empty($carrinho)) {
        $db = getDB();
        $ids = implode(',', array_keys($carrinho));
        $stmt = $db->query("SELECT id, preco, preco_promocional FROM produtos WHERE id IN ($ids)");

        while ($produto = $stmt->fetch()) {
            $preco = $produto['preco_promocional'] ?? $produto['preco'];
            $total += $preco * $carrinho[$produto['id']];
        }
    }

    return $total;
}

// Validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Gerar senha hash
function gerarHashSenha($senha) {
    return password_hash($senha, PASSWORD_DEFAULT);
}

// Verificar senha
function verificarSenha($senha, $hash) {
    return password_verify($senha, $hash);
}

// Obter URL base
function urlBase($caminho = '') {
    return SITE_URL . '/' . ltrim($caminho, '/');
}

// Obter URL de imagem
function urlImagem($imagem, $pasta = 'produtos') {
    if (empty($imagem)) {
        return urlBase('assets/images/sem-imagem.jpg');
    }
    return urlBase("assets/images/{$pasta}/{$imagem}");
}

// Truncar texto
function truncar($texto, $limite = 100, $sufixo = '...') {
    if (strlen($texto) <= $limite) {
        return $texto;
    }
    return substr($texto, 0, $limite) . $sufixo;
}

// Validar telefone angolano
function validarTelefone($telefone) {
    // Remove caracteres especiais
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    // Telefones em Angola geralmente têm 9 dígitos
    return strlen($telefone) >= 9;
}
