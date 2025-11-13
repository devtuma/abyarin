<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

iniciarSessao();

if (!isAdmin()) {
    adicionarMensagem('erro', 'Acesso negado.');
    redirecionar(urlBase());
}

$db = getDB();

// Atualizar status do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    if (verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $pedido_id = (int)$_POST['pedido_id'];
        $novo_status = limpar($_POST['status']);

        $stmt = $db->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        if ($stmt->execute([$novo_status, $pedido_id])) {
            adicionarMensagem('sucesso', 'Status do pedido atualizado!');
        } else {
            adicionarMensagem('erro', 'Erro ao atualizar status.');
        }
        redirecionar(urlBase('admin/pedidos.php'));
    }
}

// Buscar pedidos
$filtro = $_GET['filtro'] ?? 'todos';
$sql = "
    SELECT p.*, u.nome as cliente_nome, u.email as cliente_email
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
";

if ($filtro !== 'todos') {
    $sql .= " WHERE p.status = :status";
}

$sql .= " ORDER BY p.data_pedido DESC";

$stmt = $db->prepare($sql);
if ($filtro !== 'todos') {
    $stmt->bindParam(':status', $filtro);
}
$stmt->execute();
$pedidos = $stmt->fetchAll();

$titulo = "Gerenciar Pedidos";
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo urlBase('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo urlBase('assets/css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2><i class="fas fa-shield-alt"></i> Admin</h2>
            </div>
            <nav class="admin-nav">
                <a href="<?php echo urlBase('admin/'); ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="<?php echo urlBase('admin/produtos.php'); ?>">
                    <i class="fas fa-box"></i> Produtos
                </a>
                <a href="<?php echo urlBase('admin/pedidos.php'); ?>" class="active">
                    <i class="fas fa-shopping-bag"></i> Pedidos
                </a>
                <a href="<?php echo urlBase('admin/categorias.php'); ?>">
                    <i class="fas fa-tags"></i> Categorias
                </a>
                <a href="<?php echo urlBase(); ?>" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Ver Loja
                </a>
                <a href="<?php echo urlBase('logout.php'); ?>">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Pedidos</h1>
                <div class="admin-user">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </div>
            </header>

            <div class="admin-content">
                <?php exibirMensagem(); ?>

                <div class="filtros-pedidos" style="margin-bottom: 1.5rem;">
                    <a href="?filtro=todos" class="btn <?php echo $filtro === 'todos' ? 'btn-primary' : 'btn-outline'; ?>">
                        Todos
                    </a>
                    <a href="?filtro=pendente" class="btn <?php echo $filtro === 'pendente' ? 'btn-primary' : 'btn-outline'; ?>">
                        Pendentes
                    </a>
                    <a href="?filtro=confirmado" class="btn <?php echo $filtro === 'confirmado' ? 'btn-primary' : 'btn-outline'; ?>">
                        Confirmados
                    </a>
                    <a href="?filtro=enviado" class="btn <?php echo $filtro === 'enviado' ? 'btn-primary' : 'btn-outline'; ?>">
                        Enviados
                    </a>
                    <a href="?filtro=entregue" class="btn <?php echo $filtro === 'entregue' ? 'btn-primary' : 'btn-outline'; ?>">
                        Entregues
                    </a>
                </div>

                <?php if (empty($pedidos)): ?>
                    <p>Nenhum pedido encontrado.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Método Pagamento</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td>#<?php echo $pedido['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($pedido['cliente_nome']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($pedido['cliente_email']); ?></small>
                                    </td>
                                    <td><?php echo formatarPreco($pedido['total']); ?></td>
                                    <td><?php echo ucfirst($pedido['metodo_pagamento']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">
                                            <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" class="status-select">
                                                <option value="pendente" <?php echo $pedido['status'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                <option value="confirmado" <?php echo $pedido['status'] === 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                                <option value="enviado" <?php echo $pedido['status'] === 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                                <option value="entregue" <?php echo $pedido['status'] === 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                                <option value="cancelado" <?php echo $pedido['status'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                            </select>
                                            <button type="submit" name="atualizar_status" style="display: none;"></button>
                                        </form>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                    <td>
                                        <button onclick="verDetalhes(<?php echo $pedido['id']; ?>)"
                                                class="btn-action btn-view"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal de Detalhes (implementação básica) -->
    <div id="modalDetalhes" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 800px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>Detalhes do Pedido</h2>
                <button onclick="fecharModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div id="conteudoDetalhes">Carregando...</div>
        </div>
    </div>

    <script>
    function verDetalhes(pedidoId) {
        const modal = document.getElementById('modalDetalhes');
        const conteudo = document.getElementById('conteudoDetalhes');

        modal.style.display = 'flex';
        conteudo.innerHTML = 'Carregando detalhes do pedido #' + pedidoId + '...';

        // Aqui você pode fazer uma requisição AJAX para buscar os detalhes
        // Por enquanto, mostramos uma mensagem simples
        setTimeout(() => {
            conteudo.innerHTML = `
                <p>Detalhes completos do pedido #${pedidoId} podem ser implementados via AJAX.</p>
                <p>Por enquanto, você pode visualizar os pedidos no banco de dados ou implementar uma página dedicada de detalhes.</p>
            `;
        }, 500);
    }

    function fecharModal() {
        document.getElementById('modalDetalhes').style.display = 'none';
    }
    </script>

    <style>
    .filtros-pedidos {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .status-select {
        padding: 0.5rem;
        border: 2px solid var(--border-color);
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .status-select:focus {
        outline: none;
        border-color: var(--primary-color);
    }
    </style>
</body>
</html>
