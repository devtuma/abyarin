# E-commerce Angola 🛍️

E-commerce completo desenvolvido em PHP, HTML e CSS para vendas em Angola. Sistema otimizado para a Hostinger com moeda em Kwanza Angolano (Kz).

## 📋 Características

- ✅ Sistema completo de e-commerce
- ✅ Carrinho de compras
- ✅ Sistema de login e registro de usuários
- ✅ Painel administrativo
- ✅ Gerenciamento de produtos e categorias
- ✅ Sistema de pedidos
- ✅ Métodos de pagamento para Angola (Transferência, Multicaixa, Pagamento na Entrega)
- ✅ Design responsivo (mobile-friendly)
- ✅ Moeda em Kwanza Angolano (Kz)
- ✅ Todas as províncias de Angola disponíveis

## 🚀 Instalação na Hostinger

### Passo 1: Upload dos Arquivos

1. Acesse o **cPanel** da sua conta Hostinger
2. Vá em **Gerenciador de Arquivos**
3. Navegue até a pasta `public_html`
4. Faça upload de todos os arquivos do e-commerce para esta pasta
   - Se quiser em uma subpasta, crie uma pasta (ex: `loja`) e faça upload lá

### Passo 2: Criar Banco de Dados

1. No cPanel, acesse **MySQL Databases** ou **phpMyAdmin**
2. Crie um novo banco de dados:
   - Nome: `ecommerce_angola` (ou outro nome de sua preferência)
3. Crie um usuário MySQL:
   - Nome de usuário: escolha um nome
   - Senha: escolha uma senha forte
4. Adicione o usuário ao banco de dados com todas as permissões

### Passo 3: Importar Estrutura do Banco

1. Acesse o **phpMyAdmin** no cPanel
2. Selecione o banco de dados criado
3. Clique em **Importar**
4. Escolha o arquivo `database.sql`
5. Clique em **Executar**

### Passo 4: Configurar Conexão

1. Abra o arquivo `config/config.php`
2. Altere as seguintes linhas com seus dados:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_angola'); // Nome do seu banco
define('DB_USER', 'seu_usuario');      // Usuário do MySQL
define('DB_PASS', 'sua_senha');        // Senha do MySQL
```

3. Altere também a URL do site:

```php
define('SITE_URL', 'https://seudominio.com'); // Sua URL
```

### Passo 5: Criar Pasta de Imagens

1. No Gerenciador de Arquivos, navegue até `assets/images/`
2. Crie uma pasta chamada `produtos`
3. Defina permissões 755 ou 777 para esta pasta (botão direito > Permissões)

## 🔐 Acesso Administrativo

Após a instalação, você pode acessar o painel administrativo:

- **URL**: `https://seudominio.com/admin/`
- **E-mail**: `admin@loja.ao`
- **Senha**: `admin123`

⚠️ **IMPORTANTE**: Após o primeiro acesso, altere a senha do administrador!

Para alterar a senha:
1. Acesse phpMyAdmin
2. Vá na tabela `usuarios`
3. Edite o registro do admin
4. Gere uma nova senha com hash em: https://bcrypt-generator.com/
5. Substitua o valor do campo `senha`

## 📁 Estrutura de Arquivos

```
ecommerce-angola/
├── admin/                  # Painel administrativo
│   ├── index.php          # Dashboard
│   ├── produtos.php       # Gerenciar produtos
│   └── pedidos.php        # Gerenciar pedidos
├── assets/
│   ├── css/
│   │   ├── style.css      # Estilos principais
│   │   └── admin.css      # Estilos do admin
│   ├── js/
│   │   └── main.js        # JavaScript
│   └── images/            # Imagens do site
│       └── produtos/      # Imagens dos produtos
├── config/
│   ├── config.php         # Configurações gerais
│   └── database.php       # Conexão com banco
├── includes/
│   ├── header.php         # Cabeçalho do site
│   ├── footer.php         # Rodapé do site
│   └── functions.php      # Funções auxiliares
├── index.php              # Página inicial
├── produto.php            # Detalhes do produto
├── produtos.php           # Listagem de produtos
├── carrinho.php           # Carrinho de compras
├── checkout.php           # Finalizar pedido
├── login.php              # Login
├── registro.php           # Registro de usuário
├── logout.php             # Logout
├── database.sql           # Estrutura do banco
└── README.md              # Esta documentação
```

## 🎨 Personalização

### Alterar Cores

Edite o arquivo `assets/css/style.css` e modifique as variáveis CSS:

```css
:root {
    --primary-color: #e74c3c;    /* Cor principal */
    --secondary-color: #3498db;   /* Cor secundária */
    --success-color: #27ae60;     /* Cor de sucesso */
}
```

### Alterar Logo

1. Edite o arquivo `includes/header.php`
2. Modifique a linha com o nome da loja:

```php
<h1><i class="fas fa-shopping-bag"></i> Loja Angola</h1>
```

### Adicionar Imagens de Produtos

As imagens devem ser colocadas em `assets/images/produtos/`. Os formatos suportados são:
- JPG, JPEG
- PNG
- GIF
- WEBP

Tamanho recomendado: 800x800 pixels

## 📦 Produtos de Exemplo

O sistema já vem com 8 produtos de exemplo. Você pode:

1. Editar esses produtos no painel admin
2. Adicionar suas próprias imagens
3. Ajustar preços e descrições
4. Adicionar novos produtos

## 💳 Métodos de Pagamento

O sistema suporta:

1. **Transferência Bancária** - BAI, BFA, BPC, Atlântico
2. **Multicaixa Express** - Pagamento via Multicaixa
3. **Pagamento na Entrega** - Dinheiro ao receber

Para integrar com gateway de pagamento real, você precisará:
- Contratar um serviço de gateway (ex: Expresspay, KudiPay)
- Implementar a API do gateway escolhido

## 🌍 Províncias de Angola

O sistema já inclui todas as 18 províncias de Angola no checkout:
- Luanda, Benguela, Huambo, Huíla, Cabinda, Cuando Cubango, Cunene, Bié, Bengo, Malanje, Moxico, Namibe, Uíge, Zaire, Lunda Norte, Lunda Sul, Cuanza Norte, Cuanza Sul

## 🔒 Segurança

O sistema implementa várias medidas de segurança:

- ✅ Proteção CSRF em formulários
- ✅ Senhas criptografadas com bcrypt
- ✅ Prepared statements (prevenção SQL Injection)
- ✅ Validação de dados
- ✅ Sanitização de inputs
- ✅ Sessões seguras

### Recomendações Adicionais:

1. **Use HTTPS**: Configure SSL/TLS na Hostinger
2. **Atualize Senhas**: Altere todas as senhas padrão
3. **Backup Regular**: Faça backup do banco de dados regularmente
4. **Permissões**: Configure permissões corretas (755 para pastas, 644 para arquivos)

## 🐛 Resolução de Problemas

### Erro de Conexão com Banco de Dados

- Verifique as credenciais em `config/config.php`
- Confirme que o banco de dados foi criado
- Teste a conexão no phpMyAdmin

### Imagens Não Aparecem

- Verifique se a pasta `assets/images/produtos/` existe
- Confirme as permissões da pasta (755 ou 777)
- Verifique o caminho das imagens no banco de dados

### Erro 500

- Ative o modo debug em `config/config.php`:
```php
define('DEBUG_MODE', true);
```
- Verifique os logs de erro do PHP no cPanel

### Sessão Não Funciona

- Verifique as permissões da pasta de sessões do PHP
- Configure o `session.save_path` se necessário

## 📧 Suporte

Para dúvidas e suporte:
- Verifique os logs de erro no cPanel
- Consulte a documentação da Hostinger
- Entre em contato com o suporte da hospedagem

## 📝 Licença

Este projeto é de uso livre para fins comerciais e pessoais.

## 🔄 Próximas Melhorias Sugeridas

- [ ] Integração com APIs de pagamento
- [ ] Sistema de avaliações de produtos
- [ ] Wishlist (lista de desejos)
- [ ] Cupons de desconto
- [ ] Newsletter
- [ ] Chat de atendimento
- [ ] Múltiplas imagens por produto
- [ ] Sistema de rastreamento de pedidos
- [ ] Relatórios de vendas
- [ ] Exportação de pedidos para PDF

## 🎓 Tecnologias Utilizadas

- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript (Vanilla)
- Font Awesome 6.4
- PDO para conexão com banco

---

**Desenvolvido para Angola 🇦🇴**

Boa sorte com suas vendas! 🚀
