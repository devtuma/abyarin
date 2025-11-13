        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Sobre Nós</h3>
                    <p>A melhor loja online de Angola. Produtos de qualidade com entrega garantida.</p>
                </div>

                <div class="footer-section">
                    <h3>Links Rápidos</h3>
                    <ul>
                        <li><a href="<?php echo urlBase(); ?>">Início</a></li>
                        <li><a href="<?php echo urlBase('produtos.php'); ?>">Produtos</a></li>
                        <li><a href="<?php echo urlBase('sobre.php'); ?>">Sobre Nós</a></li>
                        <li><a href="<?php echo urlBase('contato.php'); ?>">Contato</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Atendimento</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> +244 923 456 789</li>
                        <li><i class="fas fa-envelope"></i> contato@loja.ao</li>
                        <li><i class="fas fa-map-marker-alt"></i> Luanda, Angola</li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Redes Sociais</h3>
                    <div class="social-links">
                        <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo urlBase('assets/js/main.js'); ?>"></script>
</body>
</html>
