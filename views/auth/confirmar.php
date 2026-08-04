<main class="auth-page">
    <header class="auth-page__topbar">
        <a href="/login" class="auth-page__brand">
            <div class="auth-page__brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1m-9 0h14a1 1 0 0 1 1 1v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Zm-1 5h16M9 11v2h6v-2" />
                </svg>
            </div>

            <p class="auth-page__brand-text">Freelance Manager EDN</p>
        </a>
    </header>

    <section class="auth-page__content">
        <div class="auth-card">
            <div class="auth-card__icon auth-card__icon--success">
                <svg xmlns="http://www.w3.org/2000/svg" width="62" height="62" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M9 12.5 11 14.5 15.5 9.5" />
                    <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                </svg>
            </div>

            <h1 class="auth-card__title">Confirmar cuenta</h1>

            <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

            <p class="auth-card__text">
                Si tu cuenta fue confirmada correctamente, ya puedes iniciar sesión
                y acceder al sistema.
            </p>

            <a href="/login" class="auth-card__button">
                Iniciar sesión
            </a>
        </div>
    </section>

    <footer class="auth-page__footer">
        <p>© 2026 Freelance Manager EDN. Todos los derechos reservados.</p>
    </footer>
</main>