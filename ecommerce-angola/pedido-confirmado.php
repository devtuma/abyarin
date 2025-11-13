<?php
$titulo = "Pedido Confirmado";
require_once __DIR__ . '/includes/header.php';

if (!isset($_GET['id']) || !usuarioLogado()) {
    redirecionar(urlBase());
}

$pedido_id = (int)$_GET['id'];
$db = getDB();

// Buscar pedido
$stmt = $db->prepare("
    SELECT p.*, u.nome as cliente_nome, u.email as cliente_email
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.id = ? AND p.usuario_id = ?
");
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
$pedido = $stmt->fetch();

if (!$pedido) {
    adicionarMensagem('erro', 'Pedido não encontrado.');
    redirecionar(urlBase());
}

// Buscar itens do pedido
$stmt = $db->prepare("
    SELECT pi.*, p.nome as produto_nome, p.imagem as produto_imagem
    FROM pedido_itens pi
    JOIN produtos p ON pi.produto_id = p.id
    WHERE pi.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$itens = $stmt->fetchAll();
?>

<div class="pedido-confirmado">
    <div class="confirmacao-header">
        <div class="icone-sucesso">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Pedido Confirmado!</h1>
        <p>Obrigado pela sua compra. Seu pedido foi recebido com sucesso.</p>
        <p class="numero-pedido">Número do Pedido: <strong>#<?php echo $pedido_id; ?></strong></p>
    </div>

    <div class="pedido-detalhes-confirmacao">
        <div class="pedido-info-box">
            <h2><i class="fas fa-info-circle"></i> Informações do Pedido</h2>

            <div class="info-grid">
                <div class="info-item">
                    <strong>Status:</strong>
                    <span class="status-badge status-<?php echo $pedido['status']; ?>">
                        <?php echo ucfirst($pedido['status']); ?>
                    </span>
                </div>

                <div class="info-item">
                    <strong>Data:</strong>
                    <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                </div>

                <div class="info-item">
                    <strong>Método de Pagamento:</strong>
                    <?php echo ucfirst($pedido['metodo_pagamento']); ?>
                </div>

                <div class="info-item">
                    <strong>Total:</strong>
                    <span class="total-valor"><?php echo formatarPreco($pedido['total']); ?></span>
                </div>
            </div>
        </div>

        <div class="pedido-info-box">
            <h2><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h2>

            <p>
                <?php echo htmlspecialchars($pedido['endereco_entrega']); ?><br>
                <?php echo htmlspecialchars($pedido['cidade']); ?>, <?php echo htmlspecialchars($pedido['provincia']); ?><br>
                Telefone: <?php echo htmlspecialchars($pedido['telefone']); ?>
            </p>
        </div>

        <div class="pedido-info-box">
            <h2><i class="fas fa-box"></i> Itens do Pedido</h2>

            <div class="itens-pedido">
                <?php foreach ($itens as $item): ?>
                    <div class="item-pedido-confirmado">
                        <img src="<?php echo urlImagem($item['produto_imagem']); ?>"
                             alt="<?php echo htmlspecialchars($item['produto_nome']); ?>">
                        <div class="item-info-confirmado">
                            <h3><?php echo htmlspecialchars($item['produto_nome']); ?></h3>
                            <p>Quantidade: <?php echo $item['quantidade']; ?> x <?php echo formatarPreco($item['preco_unitario']); ?></p>
                            <p class="item-total-confirmado"><strong><?php echo formatarPreco($item['subtotal']); ?></strong></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="confirmacao-acoes">
        <a href="<?php echo urlBase('meus-pedidos.php'); ?>" class="btn btn-primary">
            <i class="fas fa-list"></i> Ver Meus Pedidos
        </a>
        <a href="<?php echo urlBase(); ?>" class="btn btn-secondary">
            <i class="fas fa-home"></i> Voltar à Loja
        </a>
    </div>

    <div class="proximos-passos">
        <h2>Próximos Passos</h2>
        <div class="passos-grid">
            <div class="passo-item">
                <i class="fas fa-envelope"></i>
                <h3>1. Confirmação por E-mail</h3>
                <p>Você receberá um e-mail de confirmação com os detalhes do pedido.</p>
            </div>
            <div class="passo-item">
                <i class="fas fa-credit-card"></i>
                <h3>2. Pagamento</h3>
                <p>Complete o pagamento conforme o método escolhido.</p>
            </div>
            <div class="passo-item">
                <i class="fas fa-truck"></i>
                <h3>3. Entrega</h3>
                <p>Seu pedido será enviado após a confirmação do pagamento.</p>
            </div>
        </div>
    </div>
</div>

<style>
.pedido-confirmado {
    max-width: 900px;
    margin: 0 auto;
}

.confirmacao-header {
    text-align: center;
    padding: 3rem 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.icone-sucesso {
    font-size: 5rem;
    color: var(--success-color);
    margin-bottom: 1rem;
}

.confirmacao-header h1 {
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.numero-pedido {
    font-size: 1.2rem;
    margin-top: 1rem;
    color: var(--primary-color);
}

.pedido-detalhes-confirmacao {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 2rem;
}

.pedido-info-box {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.pedido-info-box h2 {
    margin-bottom: 1.5rem;
    color: var(--dark-color);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.total-valor {
    font-size: 1.5rem;
    color: var(--primary-color);
    font-weight: bold;
}

.itens-pedido {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.item-pedido-confirmado {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 5px;
}

.item-pedido-confirmado img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.item-info-confirmado {
    flex: 1;
}

.item-info-confirmado h3 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.item-total-confirmado {
    color: var(--primary-color);
}

.confirmacao-acoes {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.proximos-passos {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.proximos-passos h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--dark-color);
}

.passos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.passo-item {
    text-align: center;
}

.passo-item i {
    font-size: 3rem;
    color: var(--secondary-color);
    margin-bottom: 1rem;
}

.passo-item h3 {
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.passo-item p {
    color: #666;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    .confirmacao-acoes {
        flex-direction: column;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
