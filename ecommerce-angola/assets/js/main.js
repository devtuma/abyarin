// JavaScript para E-commerce Angola

document.addEventListener('DOMContentLoaded', function() {
    // Menu Mobile Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Auto-fechar mensagens após 5 segundos
    const mensagens = document.querySelectorAll('.mensagem');
    mensagens.forEach(function(mensagem) {
        setTimeout(function() {
            mensagem.style.opacity = '0';
            setTimeout(function() {
                mensagem.remove();
            }, 300);
        }, 5000);
    });

    // Validação de formulários
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger-color)';
                } else {
                    field.style.borderColor = 'var(--border-color)';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
        });
    });

    // Quantidade do produto
    const quantidadeInputs = document.querySelectorAll('.quantidade-input, input[name="quantidade"]');
    quantidadeInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 999;
            let value = parseInt(this.value) || min;

            if (value < min) value = min;
            if (value > max) value = max;

            this.value = value;
        });
    });

    // Confirmação de exclusão
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Tem certeza que deseja excluir?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Preview de imagem no upload
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.image-preview');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Smooth scroll para âncoras
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Atualizar badge do carrinho via localStorage (opcional)
    function atualizarBadgeCarrinho() {
        const badge = document.querySelector('.carrinho-icone .badge');
        // Implementar lógica se necessário
    }

    // Métodos de pagamento - highlight selecionado
    const metodoPagamento = document.querySelectorAll('.metodo-pagamento');
    metodoPagamento.forEach(function(metodo) {
        metodo.addEventListener('click', function() {
            metodoPagamento.forEach(m => m.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Formatação de telefone (Angola)
    const telefoneInputs = document.querySelectorAll('input[type="tel"]');
    telefoneInputs.forEach(function(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 9) value = value.slice(0, 9);

            // Formato: XXX XXX XXX
            if (value.length > 6) {
                value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
            } else if (value.length > 3) {
                value = value.slice(0, 3) + ' ' + value.slice(3);
            }

            e.target.value = value;
        });
    });

    // Animação de scroll reveal (opcional)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    const elementsToAnimate = document.querySelectorAll('.produto-card, .dashboard-card');
    elementsToAnimate.forEach(function(element) {
        observer.observe(element);
    });
});

// Função auxiliar para formatar moeda
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-AO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(valor) + ' Kz';
}

// Função para adicionar ao carrinho via AJAX (se implementar)
function adicionarAoCarrinhoAjax(produtoId, quantidade) {
    // Implementar chamada AJAX se necessário
    console.log('Adicionar produto', produtoId, 'quantidade', quantidade);
}
