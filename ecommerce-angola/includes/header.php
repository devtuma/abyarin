<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

iniciarSessao();
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo urlBase('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo urlBase(); ?>">
                        <h1><i class="fas fa-shopping-bag"></i> <?php echo SITE_NAME; ?></h1>
                    </a>
                </div>

                <nav class="nav-menu">
                    <ul>
                        <li><a href="<?php echo urlBase(); ?>">Início</a></li>
                        <li><a href="<?php echo urlBase('produtos.php'); ?>">Produtos</a></li>
                        <?php if (usuarioLogado()): ?>
                            <li><a href="<?php echo urlBase('minha-conta.php'); ?>">Minha Conta</a></li>
                            <li><a href="<?php echo urlBase('meus-pedidos.php'); ?>">Meus Pedidos</a></li>
                            <?php if (isAdmin()): ?>
                                <li><a href="<?php echo urlBase('admin/'); ?>">Admin</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo urlBase('logout.php'); ?>">Sair</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo urlBase('login.php'); ?>">Login</a></li>
                            <li><a href="<?php echo urlBase('registro.php'); ?>">Registrar</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="header-actions">
                    <a href="<?php echo urlBase('carrinho.php'); ?>" class="carrinho-icone">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        $itens = contarItensCarrinho();
                        if ($itens > 0):
                        ?>
                            <span class="badge"><?php echo $itens; ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <?php exibirMensagem(); ?>
