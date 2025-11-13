<?php
$titulo = "Finalizar Pedido";
require_once __DIR__ . '/includes/header.php';

// Verificar se está logado
if (!usuarioLogado()) {
    adicionarMensagem('erro', 'Você precisa estar logado para finalizar o pedido.');
    redirecionar(urlBase('login.php?redirect=checkout.php'));
}

// Verificar se há itens no carrinho
$carrinho = obterCarrinho();
if (empty($carrinho)) {
    adicionarMensagem('erro', 'Seu carrinho está vazio.');
    redirecionar(urlBase('carrinho.php'));
}

$db = getDB();

// Buscar dados do usuário
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Calcular totais
$itensCarrinho = [];
$subtotal = 0;

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

$frete = 5000;
$total = $subtotal + $frete;

// Processar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        adicionarMensagem('erro', 'Token de segurança inválido.');
    } else {
        $endereco = limpar($_POST['endereco'] ?? '');
        $cidade = limpar($_POST['cidade'] ?? '');
        $provincia = limpar($_POST['provincia'] ?? '');
        $telefone = limpar($_POST['telefone'] ?? '');
        $metodo_pagamento = limpar($_POST['metodo_pagamento'] ?? '');
        $observacoes = limpar($_POST['observacoes'] ?? '');

        $erros = [];

        if (empty($endereco)) $erros[] = 'O endereço é obrigatório.';
        if (empty($cidade)) $erros[] = 'A cidade é obrigatória.';
        if (empty($provincia)) $erros[] = 'A província é obrigatória.';
        if (empty($telefone)) $erros[] = 'O telefone é obrigatório.';
        if (empty($metodo_pagamento)) $erros[] = 'Selecione um método de pagamento.';

        if (empty($erros)) {
            try {
                $db->beginTransaction();

                // Criar pedido
                $stmt = $db->prepare("
                    INSERT INTO pedidos (usuario_id, total, metodo_pagamento, endereco_entrega, cidade, provincia, telefone, observacoes)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_SESSION['usuario_id'],
                    $total,
                    $metodo_pagamento,
                    $endereco,
                    $cidade,
                    $provincia,
                    $telefone,
                    $observacoes
                ]);

                $pedido_id = $db->lastInsertId();

                // Adicionar itens do pedido
                $stmt = $db->prepare("
                    INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ");

                foreach ($itensCarrinho as $item) {
                    $stmt->execute([
                        $pedido_id,
                        $item['produto']['id'],
                        $item['quantidade'],
                        $item['preco_unitario'],
                        $item['total']
                    ]);

                    // Atualizar estoque
                    $stmt2 = $db->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
                    $stmt2->execute([$item['quantidade'], $item['produto']['id']]);
                }

                $db->commit();

                // Limpar carrinho
                limparCarrinho();

                adicionarMensagem('sucesso', 'Pedido realizado com sucesso! Número do pedido: #' . $pedido_id);
                redirecionar(urlBase('pedido-confirmado.php?id=' . $pedido_id));

            } catch (Exception $e) {
                $db->rollBack();
                adicionarMensagem('erro', 'Erro ao processar pedido. Tente novamente.');
            }
        } else {
            foreach ($erros as $erro) {
                adicionarMensagem('erro', $erro);
            }
        }
    }
}
?>

<div class="checkout-container">
    <h1 class="page-title">
        <i class="fas fa-credit-card"></i> Finalizar Pedido
    </h1>

    <form method="POST" class="checkout-form">
        <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">

        <div class="checkout-content">
            <div class="checkout-dados">
                <div class="checkout-section">
                    <h2><i class="fas fa-map-marker-alt"></i> Dados de Entrega</h2>

                    <div class="form-group">
                        <label for="endereco">Endereço Completo</label>
                        <input type="text"
                               id="endereco"
                               name="endereco"
                               value="<?php echo htmlspecialchars($usuario['endereco'] ?? ''); ?>"
                               placeholder="Rua, número, bairro"
                               required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cidade">Cidade</label>
                            <input type="text"
                                   id="cidade"
                                   name="cidade"
                                   value="<?php echo htmlspecialchars($usuario['cidade'] ?? ''); ?>"
                                   placeholder="Ex: Luanda"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="provincia">Província</label>
                            <select id="provincia" name="provincia" required>
                                <option value="">Selecione...</option>
                                <option value="Luanda" <?php echo ($usuario['provincia'] ?? '') == 'Luanda' ? 'selected' : ''; ?>>Luanda</option>
                                <option value="Benguela" <?php echo ($usuario['provincia'] ?? '') == 'Benguela' ? 'selected' : ''; ?>>Benguela</option>
                                <option value="Huambo" <?php echo ($usuario['provincia'] ?? '') == 'Huambo' ? 'selected' : ''; ?>>Huambo</option>
                                <option value="Huíla" <?php echo ($usuario['provincia'] ?? '') == 'Huíla' ? 'selected' : ''; ?>>Huíla</option>
                                <option value="Cabinda" <?php echo ($usuario['provincia'] ?? '') == 'Cabinda' ? 'selected' : ''; ?>>Cabinda</option>
                                <option value="Cuando Cubango" <?php echo ($usuario['provincia'] ?? '') == 'Cuando Cubango' ? 'selected' : ''; ?>>Cuando Cubango</option>
                                <option value="Cunene" <?php echo ($usuario['provincia'] ?? '') == 'Cunene' ? 'selected' : ''; ?>>Cunene</option>
                                <option value="Bié" <?php echo ($usuario['provincia'] ?? '') == 'Bié' ? 'selected' : ''; ?>>Bié</option>
                                <option value="Bengo" <?php echo ($usuario['provincia'] ?? '') == 'Bengo' ? 'selected' : ''; ?>>Bengo</option>
                                <option value="Malanje" <?php echo ($usuario['provincia'] ?? '') == 'Malanje' ? 'selected' : ''; ?>>Malanje</option>
                                <option value="Moxico" <?php echo ($usuario['provincia'] ?? '') == 'Moxico' ? 'selected' : ''; ?>>Moxico</option>
                                <option value="Namibe" <?php echo ($usuario['provincia'] ?? '') == 'Namibe' ? 'selected' : ''; ?>>Namibe</option>
                                <option value="Uíge" <?php echo ($usuario['provincia'] ?? '') == 'Uíge' ? 'selected' : ''; ?>>Uíge</option>
                                <option value="Zaire" <?php echo ($usuario['provincia'] ?? '') == 'Zaire' ? 'selected' : ''; ?>>Zaire</option>
                                <option value="Lunda Norte" <?php echo ($usuario['provincia'] ?? '') == 'Lunda Norte' ? 'selected' : ''; ?>>Lunda Norte</option>
                                <option value="Lunda Sul" <?php echo ($usuario['provincia'] ?? '') == 'Lunda Sul' ? 'selected' : ''; ?>>Lunda Sul</option>
                                <option value="Cuanza Norte" <?php echo ($usuario['provincia'] ?? '') == 'Cuanza Norte' ? 'selected' : ''; ?>>Cuanza Norte</option>
                                <option value="Cuanza Sul" <?php echo ($usuario['provincia'] ?? '') == 'Cuanza Sul' ? 'selected' : ''; ?>>Cuanza Sul</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone de Contato</label>
                        <input type="tel"
                               id="telefone"
                               name="telefone"
                               value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>"
                               placeholder="923 456 789"
                               required>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2><i class="fas fa-money-bill-wave"></i> Método de Pagamento</h2>

                    <div class="metodo-pagamento-opcoes">
                        <label class="metodo-pagamento">
                            <input type="radio" name="metodo_pagamento" value="transferencia" required>
                            <div class="metodo-info">
                                <i class="fas fa-exchange-alt"></i>
                                <div>
                                    <strong>Transferência Bancária</strong>
                                    <small>BAI, BFA, BPC, Atlantico</small>
                                </div>
                            </div>
                        </label>

                        <label class="metodo-pagamento">
                            <input type="radio" name="metodo_pagamento" value="multicaixa" required>
                            <div class="metodo-info">
                                <i class="fas fa-credit-card"></i>
                                <div>
                                    <strong>Multicaixa Express</strong>
                                    <small>Pagamento via Multicaixa</small>
                                </div>
                            </div>
                        </label>

                        <label class="metodo-pagamento">
                            <input type="radio" name="metodo_pagamento" value="entrega" required>
                            <div class="metodo-info">
                                <i class="fas fa-money-bill"></i>
                                <div>
                                    <strong>Pagamento na Entrega</strong>
                                    <small>Pague em dinheiro ao receber</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2><i class="fas fa-comment"></i> Observações</h2>

                    <div class="form-group">
                        <textarea id="observacoes"
                                  name="observacoes"
                                  rows="4"
                                  placeholder="Alguma observação sobre o pedido? (opcional)"></textarea>
                    </div>
                </div>
            </div>

            <div class="checkout-resumo">
                <h2>Resumo do Pedido</h2>

                <div class="resumo-produtos">
                    <?php foreach ($itensCarrinho as $item): ?>
                        <div class="resumo-produto-item">
                            <img src="<?php echo urlImagem($item['produto']['imagem']); ?>"
                                 alt="<?php echo htmlspecialchars($item['produto']['nome']); ?>">
                            <div class="resumo-produto-info">
                                <h4><?php echo htmlspecialchars($item['produto']['nome']); ?></h4>
                                <p>Qtd: <?php echo $item['quantidade']; ?> x <?php echo formatarPreco($item['preco_unitario']); ?></p>
                            </div>
                            <div class="resumo-produto-total">
                                <?php echo formatarPreco($item['total']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="resumo-totais">
                    <div class="resumo-item">
                        <span>Subtotal:</span>
                        <span><?php echo formatarPreco($subtotal); ?></span>
                    </div>

                    <div class="resumo-item">
                        <span>Frete:</span>
                        <span><?php echo formatarPreco($frete); ?></span>
                    </div>

                    <div class="resumo-divider"></div>

                    <div class="resumo-item resumo-total">
                        <span>Total:</span>
                        <span><?php echo formatarPreco($total); ?></span>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-block btn-large">
                    <i class="fas fa-check-circle"></i> Confirmar Pedido
                </button>

                <a href="<?php echo urlBase('carrinho.php'); ?>" class="btn btn-outline btn-block">
                    <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
                </a>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
