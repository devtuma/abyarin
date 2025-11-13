<?php
$titulo = "Início";
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Buscar produtos em destaque
$stmt = $db->prepare("
    SELECT p.*, c.nome as categoria_nome
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.ativo = 1 AND p.destaque = 1
    ORDER BY p.data_criacao DESC
    LIMIT 8
");
$stmt->execute();
$produtosDestaque = $stmt->fetchAll();

// Buscar últimos produtos
$stmt = $db->prepare("
    SELECT p.*, c.nome as categoria_nome
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    WHERE p.ativo = 1
    ORDER BY p.data_criacao DESC
    LIMIT 12
");
$stmt->execute();
$ultimosProdutos = $stmt->fetchAll();
?>

<!-- Banner Principal -->
<section class="hero-banner">
    <div class="hero-content">
        <h2>Bem-vindo à Melhor Loja de Angola</h2>
        <p>Produtos de qualidade com os melhores preços</p>
        <a href="<?php echo urlBase('produtos.php'); ?>" class="btn btn-primary">Ver Produtos</a>
    </div>
</section>

<!-- Produtos em Destaque -->
<section class="produtos-section">
    <h2 class="section-title">Produtos em Destaque</h2>

    <?php if (empty($produtosDestaque)): ?>
        <p class="texto-vazio">Nenhum produto em destaque no momento.</p>
    <?php else: ?>
        <div class="produtos-grid">
            <?php foreach ($produtosDestaque as $produto): ?>
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
</section>

<!-- Últimos Produtos -->
<section class="produtos-section">
    <h2 class="section-title">Últimos Produtos</h2>

    <?php if (empty($ultimosProdutos)): ?>
        <p class="texto-vazio">Nenhum produto disponível no momento.</p>
    <?php else: ?>
        <div class="produtos-grid">
            <?php foreach ($ultimosProdutos as $produto): ?>
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
</section>

<!-- Benefícios -->
<section class="beneficios-section">
    <div class="beneficios-grid">
        <div class="beneficio-item">
            <i class="fas fa-truck"></i>
            <h3>Entrega Rápida</h3>
            <p>Entregamos em toda Angola</p>
        </div>
        <div class="beneficio-item">
            <i class="fas fa-shield-alt"></i>
            <h3>Compra Segura</h3>
            <p>Ambiente 100% seguro</p>
        </div>
        <div class="beneficio-item">
            <i class="fas fa-headset"></i>
            <h3>Suporte 24/7</h3>
            <p>Atendimento sempre disponível</p>
        </div>
        <div class="beneficio-item">
            <i class="fas fa-undo"></i>
            <h3>Devolução Grátis</h3>
            <p>7 dias para trocar ou devolver</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
