<?php
require_once __DIR__ . '/includes/header.php';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirecionar(urlBase());
}

$produto_id = (int)$_GET['id'];
$db = getDB();

// Buscar produto
$stmt = $db->prepare("
    SELECT p.*, c.nome as categoria_nome
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.id = ? AND p.ativo = 1
");
$stmt->execute([$produto_id]);
$produto = $stmt->fetch();

if (!$produto) {
    adicionarMensagem('erro', 'Produto não encontrado.');
    redirecionar(urlBase());
}

$titulo = $produto['nome'];

// Processar adição ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_carrinho'])) {
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        adicionarMensagem('erro', 'Token de segurança inválido.');
    } else {
        $quantidade = (int)($_POST['quantidade'] ?? 1);

        if ($quantidade <= 0) {
            adicionarMensagem('erro', 'Quantidade inválida.');
        } elseif ($quantidade > $produto['estoque']) {
            adicionarMensagem('erro', 'Quantidade não disponível em estoque.');
        } else {
            adicionarAoCarrinho($produto_id, $quantidade);
            adicionarMensagem('sucesso', 'Produto adicionado ao carrinho!');
            redirecionar(urlBase('carrinho.php'));
        }
    }
}

// Buscar produtos relacionados (mesma categoria)
$stmt = $db->prepare("
    SELECT p.*, c.nome as categoria_nome
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.categoria_id = ? AND p.id != ? AND p.ativo = 1
    ORDER BY RAND()
    LIMIT 4
");
$stmt->execute([$produto['categoria_id'], $produto_id]);
$produtosRelacionados = $stmt->fetchAll();
?>

<div class="produto-detalhes">
    <div class="produto-detalhes-container">
        <div class="produto-imagem-principal">
            <img src="<?php echo urlImagem($produto['imagem']); ?>"
                 alt="<?php echo htmlspecialchars($produto['nome']); ?>">
            <?php if ($produto['preco_promocional']): ?>
                <span class="badge-promocao-grande">OFERTA</span>
            <?php endif; ?>
        </div>

        <div class="produto-info-principal">
            <?php if ($produto['categoria_nome']): ?>
                <span class="produto-categoria"><?php echo htmlspecialchars($produto['categoria_nome']); ?></span>
            <?php endif; ?>

            <h1 class="produto-titulo"><?php echo htmlspecialchars($produto['nome']); ?></h1>

            <div class="produto-preco-detalhes">
                <?php if ($produto['preco_promocional']): ?>
                    <span class="preco-antigo-grande"><?php echo formatarPreco($produto['preco']); ?></span>
                    <span class="preco-atual-grande"><?php echo formatarPreco($produto['preco_promocional']); ?></span>
                    <?php
                    $desconto = (($produto['preco'] - $produto['preco_promocional']) / $produto['preco']) * 100;
                    ?>
                    <span class="badge-desconto">-<?php echo round($desconto); ?>%</span>
                <?php else: ?>
                    <span class="preco-atual-grande"><?php echo formatarPreco($produto['preco']); ?></span>
                <?php endif; ?>
            </div>

            <div class="produto-descricao-completa">
                <h3>Descrição</h3>
                <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
            </div>

            <div class="produto-estoque">
                <?php if ($produto['estoque'] > 0): ?>
                    <span class="estoque-disponivel">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $produto['estoque']; ?> unidade(s) disponível(is)
                    </span>
                <?php else: ?>
                    <span class="estoque-indisponivel">
                        <i class="fas fa-times-circle"></i>
                        Produto indisponível
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($produto['estoque'] > 0): ?>
                <form method="POST" class="form-adicionar-carrinho">
                    <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">

                    <div class="quantidade-selector">
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" id="quantidade" name="quantidade"
                               value="1" min="1" max="<?php echo $produto['estoque']; ?>" required>
                    </div>

                    <button type="submit" name="adicionar_carrinho" class="btn btn-primary btn-large">
                        <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary btn-large" disabled>
                    <i class="fas fa-ban"></i> Indisponível
                </button>
            <?php endif; ?>

            <div class="produto-info-extra">
                <div class="info-item">
                    <i class="fas fa-truck"></i>
                    <span>Entrega em toda Angola</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Compra 100% segura</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-undo"></i>
                    <span>7 dias para devolução</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($produtosRelacionados)): ?>
<section class="produtos-section">
    <h2 class="section-title">Produtos Relacionados</h2>

    <div class="produtos-grid">
        <?php foreach ($produtosRelacionados as $prod): ?>
            <div class="produto-card">
                <div class="produto-imagem">
                    <a href="<?php echo urlBase('produto.php?id=' . $prod['id']); ?>">
                        <img src="<?php echo urlImagem($prod['imagem']); ?>"
                             alt="<?php echo htmlspecialchars($prod['nome']); ?>">
                    </a>
                    <?php if ($prod['preco_promocional']): ?>
                        <span class="badge-promocao">OFERTA</span>
                    <?php endif; ?>
                </div>

                <div class="produto-info">
                    <h3 class="produto-nome">
                        <a href="<?php echo urlBase('produto.php?id=' . $prod['id']); ?>">
                            <?php echo htmlspecialchars($prod['nome']); ?>
                        </a>
                    </h3>

                    <div class="produto-preco">
                        <?php if ($prod['preco_promocional']): ?>
                            <span class="preco-antigo"><?php echo formatarPreco($prod['preco']); ?></span>
                            <span class="preco-atual"><?php echo formatarPreco($prod['preco_promocional']); ?></span>
                        <?php else: ?>
                            <span class="preco-atual"><?php echo formatarPreco($prod['preco']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="produto-acoes">
                        <a href="<?php echo urlBase('produto.php?id=' . $prod['id']); ?>"
                           class="btn btn-secondary btn-block">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
