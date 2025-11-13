# Projetos do Repositório

## ⚠️ IMPORTANTE: GitHub Pages e E-commerce PHP

**Este repositório contém um e-commerce PHP que NÃO FUNCIONA no GitHub Pages!**

O GitHub Pages só suporta sites estáticos (HTML, CSS, JS). Este projeto precisa de PHP e MySQL.

---

## 🛍️ E-commerce Angola

Localização: `/ecommerce-angola/`

### Sistema de loja virtual completo em PHP para vendas em Angola

**Funcionalidades:**
- Carrinho de compras
- Sistema de usuários (login/registro)
- Painel administrativo
- Checkout completo
- Pagamentos: Transferência, Multicaixa, Pagamento na Entrega
- Moeda: Kwanza Angolano (Kz)
- Design responsivo

### 🚀 Como usar este e-commerce:

**OPÇÃO 1: Testar Localmente (no seu computador)**
1. Instale XAMPP, WAMP ou MAMP
2. Copie a pasta `ecommerce-angola` para `htdocs`
3. Crie banco de dados MySQL
4. Importe o arquivo `database.sql`
5. Configure `config/config.php`
6. Acesse: `http://localhost/ecommerce-angola/`

📄 **Instruções detalhadas:** Leia o arquivo `ecommerce-angola/TESTE-LOCAL.txt`

**OPÇÃO 2: Hospedar Online (produção)**
1. Contrate hospedagem com PHP + MySQL (Hostinger, InfinityFree, etc)
2. Faça upload dos arquivos via cPanel
3. Crie banco de dados MySQL
4. Importe o arquivo `database.sql`
5. Configure `config/config.php` com dados da hospedagem
6. Acesse seu domínio

📄 **Instruções detalhadas:** Leia o arquivo `ecommerce-angola/INSTALACAO.txt`

### 📖 Documentação Completa

Acesse a pasta `ecommerce-angola/` e leia:
- `README.md` - Documentação completa do projeto
- `INSTALACAO.txt` - Guia de instalação em hospedagem
- `TESTE-LOCAL.txt` - Como testar no seu computador

### 🔐 Acesso Administrativo (padrão)

- **URL:** `/admin/`
- **E-mail:** `admin@loja.ao`
- **Senha:** `admin123`

⚠️ Altere a senha após o primeiro acesso!

---

## 💡 Por que não funciona no GitHub Pages?

GitHub Pages é um serviço de hospedagem de sites **estáticos**:
- ✅ Serve arquivos HTML, CSS, JavaScript
- ❌ NÃO executa código PHP
- ❌ NÃO tem banco de dados MySQL
- ❌ NÃO tem processamento server-side

Este e-commerce precisa de:
- ✓ Servidor PHP
- ✓ Banco de dados MySQL
- ✓ Processamento backend

---

## 🌐 Hospedagens Recomendadas

Para hospedar este e-commerce, use:

**Gratuitas (para teste):**
- InfinityFree
- 000webhost
- AwardSpace

**Pagas (recomendadas):**
- Hostinger (ótima para Angola)
- HostGator
- Bluehost
- Locaweb

---

## 📞 Suporte

Para dúvidas sobre o e-commerce, consulte os arquivos de documentação em `ecommerce-angola/`.

---

**Desenvolvido para Angola 🇦🇴**
