<?php
$titulo = "Produtos";
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Buscar todos os produtos ativos
$stmt = $db->prepare("
    SELECT p.*, c.nome as categoria_nome
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.ativo = 1
    ORDER BY p.data_criacao DESC
");
$stmt->execute();
$produtos = $stmt->fetchAll();
?>

<div class="produtos-page">
    <h1 class="page-title">Todos os Produtos</h1>

    <?php if (empty($produtos)): ?>
        <p class="texto-vazio">Nenhum produto disponível no momento.</p>
    <?php else: ?>
        <div class="produtos-grid">
            <?php foreach ($produtos as $produto): ?>
                <div class="produto-card">
                    <div class="produto-imagem">
                        <a href="<?php echo urlBase('produto.php?id=' . $produto['id']); ?>">
                            <img src="<?php echo urlImagem($produto['imagem']); ?>"
                                 alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        </a>
                        <?php if ($produto['preco_promocional']): ?>
                            <span class="badge-promocao">OFERTA</span>
                        <?php endif; ?>
                    </div>

                    <div class="produto-info">
                        <?php if ($produto['categoria_nome']): ?>
                            <span class="produto-categoria"><?php echo htmlspecialchars($produto['categoria_nome']); ?></span>
                        <?php endif; ?>

                        <h3 class="produto-nome">
                            <a href="<?php echo urlBase('produto.php?id=' . $produto['id']); ?>">
                                <?php echo htmlspecialchars($produto['nome']); ?>
                            </a>
                        </h3>

                        <p class="produto-descricao">
                            <?php echo truncar(htmlspecialchars($produto['descricao']), 80); ?>
                        </p>

                        <div class="produto-preco">
                            <?php if ($produto['preco_promocional']): ?>
                                <span class="preco-antigo"><?php echo formatarPreco($produto['preco']); ?></span>
                                <span class="preco-atual"><?php echo formatarPreco($produto['preco_promocional']); ?></span>
                            <?php else: ?>
                                <span class="preco-atual"><?php echo formatarPreco($produto['preco']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="produto-acoes">
                            <a href="<?php echo urlBase('produto.php?id=' . $produto['id']); ?>"
                               class="btn btn-secondary btn-block">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
