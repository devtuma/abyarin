<?php
$titulo = "Carrinho de Compras";
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        adicionarMensagem('erro', 'Token de segurança inválido.');
    } else {
        if (isset($_POST['atualizar_carrinho'])) {
            foreach ($_POST['quantidade'] as $produto_id => $quantidade) {
                atualizarCarrinho($produto_id, (int)$quantidade);
            }
            adicionarMensagem('sucesso', 'Carrinho atualizado!');
            redirecionar(urlBase('carrinho.php'));
        } elseif (isset($_POST['remover_item'])) {
            $produto_id = (int)$_POST['produto_id'];
            removerDoCarrinho($produto_id);
            adicionarMensagem('sucesso', 'Item removido do carrinho!');
            redirecionar(urlBase('carrinho.php'));
        } elseif (isset($_POST['limpar_carrinho'])) {
            limparCarrinho();
            adicionarMensagem('sucesso', 'Carrinho esvaziado!');
            redirecionar(urlBase('carrinho.php'));
        }
    }
}

$carrinho = obterCarrinho();
$itensCarrinho = [];
$subtotal = 0;

if (!empty($carrinho)) {
    $ids = implode(',', array_keys($carrinho));
    $stmt = $db->query("SELECT * FROM produtos WHERE id IN ($ids) AND ativo = 1");

    while ($produto = $stmt->fetch()) {
        $preco = $produto['preco_promocional'] ?? $produto['preco'];
        $quantidade = $carrinho[$produto['id']];
        $total_item = $preco * $quantidade;

        $itensCarrinho[] = [
            'produto' => $produto,
            'quantidade' => $quantidade,
            'preco_unitario' => $preco,
            'total' => $total_item
        ];

        $subtotal += $total_item;
    }
}

$frete = 5000; // Frete fixo de 5.000 Kz (você pode implementar cálculo dinâmico)
$total = $subtotal + $frete;
?>

<div class="carrinho-container">
    <h1 class="page-title">
        <i class="fas fa-shopping-cart"></i> Carrinho de Compras
    </h1>

    <?php if (empty($itensCarrinho)): ?>
        <div class="carrinho-vazio">
            <i class="fas fa-shopping-cart"></i>
            <h2>Seu carrinho está vazio</h2>
            <p>Adicione produtos ao carrinho para continuar comprando.</p>
            <a href="<?php echo urlBase('produtos.php'); ?>" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Continuar Comprando
            </a>
        </div>
    <?php else: ?>
        <form method="POST" class="carrinho-form">
            <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">

            <div class="carrinho-content">
                <div class="carrinho-items">
                    <table class="carrinho-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Quantidade</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itensCarrinho as $item): ?>
                                <tr>
                                    <td class="item-produto">
                                        <img src="<?php echo urlImagem($item['produto']['imagem']); ?>"
                                             alt="<?php echo htmlspecialchars($item['produto']['nome']); ?>">
                                        <div class="item-info">
                                            <h3><?php echo htmlspecialchars($item['produto']['nome']); ?></h3>
                                            <p class="item-categoria"><?php echo htmlspecialchars($item['produto']['categoria_nome'] ?? ''); ?></p>
                                        </div>
                                    </td>
                                    <td class="item-preco">
                                        <?php echo formatarPreco($item['preco_unitario']); ?>
                                    </td>
                                    <td class="item-quantidade">
                                        <input type="number"
                                               name="quantidade[<?php echo $item['produto']['id']; ?>]"
                                               value="<?php echo $item['quantidade']; ?>"
                                               min="1"
                                               max="<?php echo $item['produto']['estoque']; ?>"
                                               class="quantidade-input">
                                    </td>
                                    <td class="item-total">
                                        <?php echo formatarPreco($item['total']); ?>
                                    </td>
                                    <td class="item-acoes">
                                        <button type="submit"
                                                name="remover_item"
                                                value="1"
                                                onclick="this.form.produto_id.value = <?php echo $item['produto']['id']; ?>"
                                                class="btn-remover"
                                                title="Remover item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <input type="hidden" name="produto_id" value="">

                    <div class="carrinho-acoes">
                        <a href="<?php echo urlBase('produtos.php'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Continuar Comprando
                        </a>
                        <div class="acoes-direita">
                            <button type="submit" name="limpar_carrinho" class="btn btn-outline"
                                    onclick="return confirm('Deseja realmente esvaziar o carrinho?')">
                                <i class="fas fa-trash"></i> Limpar Carrinho
                            </button>
                            <button type="submit" name="atualizar_carrinho" class="btn btn-primary">
                                <i class="fas fa-sync"></i> Atualizar Carrinho
                            </button>
                        </div>
                    </div>
                </div>

                <div class="carrinho-resumo">
                    <h2>Resumo do Pedido</h2>

                    <div class="resumo-item">
                        <span>Subtotal:</span>
                        <span class="resumo-valor"><?php echo formatarPreco($subtotal); ?></span>
                    </div>

                    <div class="resumo-item">
                        <span>Frete:</span>
                        <span class="resumo-valor"><?php echo formatarPreco($frete); ?></span>
                    </div>

                    <div class="resumo-divider"></div>

                    <div class="resumo-item resumo-total">
                        <span>Total:</span>
                        <span class="resumo-valor"><?php echo formatarPreco($total); ?></span>
                    </div>

                    <a href="<?php echo urlBase('checkout.php'); ?>" class="btn btn-success btn-block btn-large">
                        <i class="fas fa-check"></i> Finalizar Pedido
                    </a>

                    <div class="garantias">
                        <div class="garantia-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Compra Segura</span>
                        </div>
                        <div class="garantia-item">
                            <i class="fas fa-truck"></i>
                            <span>Entrega Garantida</span>
                        </div>
                        <div class="garantia-item">
                            <i class="fas fa-undo"></i>
                            <span>7 Dias de Garantia</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
