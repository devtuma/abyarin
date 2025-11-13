<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

iniciarSessao();

// Verificar se é admin
if (!isAdmin()) {
    adicionarMensagem('erro', 'Acesso negado.');
    redirecionar(urlBase());
}

$db = getDB();

// Estatísticas
$stmt = $db->query("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1");
$totalProdutos = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM pedidos WHERE status != 'cancelado'");
$totalPedidos = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM pedidos WHERE status = 'pendente'");
$pedidosPendentes = $stmt->fetch()['total'];

$stmt = $db->query("SELECT SUM(total) as total FROM pedidos WHERE status != 'cancelado'");
$totalVendas = $stmt->fetch()['total'] ?? 0;

$titulo = "Painel Administrativo";
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
                <a href="<?php echo urlBase('admin/'); ?>" class="active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="<?php echo urlBase('admin/produtos.php'); ?>">
                    <i class="fas fa-box"></i> Produtos
                </a>
                <a href="<?php echo urlBase('admin/pedidos.php'); ?>">
                    <i class="fas fa-shopping-bag"></i> Pedidos
                    <?php if ($pedidosPendentes > 0): ?>
                        <span class="badge"><?php echo $pedidosPendentes; ?></span>
                    <?php endif; ?>
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
                <h1>Dashboard</h1>
                <div class="admin-user">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </div>
            </header>

            <div class="admin-content">
                <?php exibirMensagem(); ?>

                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <div class="card-icon blue">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="card-info">
                            <h3><?php echo $totalProdutos; ?></h3>
                            <p>Produtos Ativos</p>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon green">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="card-info">
                            <h3><?php echo $totalPedidos; ?></h3>
                            <p>Total de Pedidos</p>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-info">
                            <h3><?php echo $pedidosPendentes; ?></h3>
                            <p>Pedidos Pendentes</p>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon purple">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="card-info">
                            <h3><?php echo formatarPreco($totalVendas); ?></h3>
                            <p>Total em Vendas</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-sections">
                    <section class="dashboard-section">
                        <h2>Últimos Pedidos</h2>
                        <?php
                        $stmt = $db->query("
                            SELECT p.*, u.nome as cliente_nome
                            FROM pedidos p
                            JOIN usuarios u ON p.usuario_id = u.id
                            ORDER BY p.data_pedido DESC
                            LIMIT 10
                        ");
                        $ultimosPedidos = $stmt->fetchAll();
                        ?>

                        <?php if (empty($ultimosPedidos)): ?>
                            <p>Nenhum pedido encontrado.</p>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimosPedidos as $pedido): ?>
                                        <tr>
                                            <td>#<?php echo $pedido['id']; ?></td>
                                            <td><?php echo htmlspecialchars($pedido['cliente_nome']); ?></td>
                                            <td><?php echo formatarPreco($pedido['total']); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $pedido['status']; ?>">
                                                    <?php echo ucfirst($pedido['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                            <td>
                                                <a href="<?php echo urlBase('admin/pedidos.php?id=' . $pedido['id']); ?>"
                                                   class="btn-action btn-view">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
