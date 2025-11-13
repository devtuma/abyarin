-- Banco de Dados para E-commerce Angola
-- Execute este script no phpMyAdmin da sua Hostinger

CREATE DATABASE IF NOT EXISTS ecommerce_angola CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_angola;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    endereco TEXT,
    cidade VARCHAR(50),
    provincia VARCHAR(50),
    is_admin TINYINT(1) DEFAULT 0,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    ativo TINYINT(1) DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    preco_promocional DECIMAL(10,2),
    categoria_id INT,
    estoque INT DEFAULT 0,
    imagem VARCHAR(255),
    destaque TINYINT(1) DEFAULT 0,
    ativo TINYINT(1) DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'confirmado', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    metodo_pagamento VARCHAR(50),
    endereco_entrega TEXT,
    cidade VARCHAR(50),
    provincia VARCHAR(50),
    telefone VARCHAR(20),
    observacoes TEXT,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de itens do pedido
CREATE TABLE IF NOT EXISTS pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir categorias iniciais
INSERT INTO categorias (nome, descricao) VALUES
('Eletrônicos', 'Smartphones, tablets, computadores e acessórios'),
('Moda e Vestuário', 'Roupas, calçados e acessórios de moda'),
('Casa e Decoração', 'Móveis, decoração e utensílios domésticos'),
('Beleza e Saúde', 'Cosméticos, perfumes e produtos de higiene'),
('Alimentos e Bebidas', 'Produtos alimentícios e bebidas'),
('Livros e Papelaria', 'Livros, material escolar e de escritório'),
('Esportes e Lazer', 'Equipamentos esportivos e produtos de lazer'),
('Automóveis', 'Peças e acessórios para veículos');

-- Criar usuário admin padrão (senha: admin123)
-- A senha será 'admin123' com hash
INSERT INTO usuarios (nome, email, senha, is_admin) VALUES
('Administrador', 'admin@loja.ao', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Inserir produtos de exemplo
INSERT INTO produtos (nome, descricao, preco, categoria_id, estoque, imagem, destaque) VALUES
('Smartphone Samsung Galaxy A54', 'Smartphone com tela de 6.4", 128GB, 6GB RAM, Câmera 50MP', 180000.00, 1, 15, 'samsung-a54.jpg', 1),
('iPhone 13 128GB', 'iPhone 13 com chip A15 Bionic, câmera dupla de 12MP', 450000.00, 1, 8, 'iphone-13.jpg', 1),
('Notebook Dell Inspiron 15', 'Notebook Intel Core i5, 8GB RAM, 256GB SSD, Tela 15.6"', 350000.00, 1, 5, 'dell-inspiron.jpg', 1),
('Tênis Nike Air Max', 'Tênis esportivo confortável para corrida e caminhada', 25000.00, 2, 30, 'nike-airmax.jpg', 0),
('Camisa Social Masculina', 'Camisa social de alta qualidade, diversos tamanhos', 8500.00, 2, 50, 'camisa-social.jpg', 0),
('Sofá 3 Lugares', 'Sofá confortável para sala de estar, cor cinza', 120000.00, 3, 10, 'sofa-3lugares.jpg', 1),
('Conjunto de Panelas 5 Peças', 'Panelas antiaderentes de alta qualidade', 15000.00, 3, 25, 'conjunto-panelas.jpg', 0),
('Perfume Importado 100ml', 'Fragrância sofisticada de longa duração', 12000.00, 4, 40, 'perfume.jpg', 0);
