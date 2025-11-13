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

// Buscar produtos
$stmt = $db->query("
    SELECT p.*, c.nome as categoria_nome
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    ORDER BY p.data_criacao DESC
");
$produtos = $stmt->fetchAll();

$titulo = "Gerenciar Produtos";
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
                <a href="<?php echo urlBase('admin/produtos.php'); ?>" class="active">
                    <i class="fas fa-box"></i> Produtos
                </a>
                <a href="<?php echo urlBase('admin/pedidos.php'); ?>">
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
                <h1>Produtos</h1>
                <div class="admin-user">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                </div>
            </header>

            <div class="admin-content">
                <?php exibirMensagem(); ?>

                <div class="admin-actions">
                    <a href="<?php echo urlBase('admin/produto-adicionar.php'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Adicionar Produto
                    </a>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?php echo $produto['id']; ?></td>
                                <td>
                                    <img src="<?php echo urlImagem($produto['imagem']); ?>"
                                         alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                         class="produto-thumb">
                                </td>
                                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td><?php echo htmlspecialchars($produto['categoria_nome'] ?? '-'); ?></td>
                                <td><?php echo formatarPreco($produto['preco']); ?></td>
                                <td><?php echo $produto['estoque']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $produto['ativo'] ? 'ativo' : 'inativo'; ?>">
                                        <?php echo $produto['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <a href="<?php echo urlBase('admin/produto-editar.php?id=' . $produto['id']); ?>"
                                       class="btn-action btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
