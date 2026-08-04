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
        <div class="auth-card auth-card--wide">
            <div class="auth-card__icon auth-card__icon--whatsapp">
                <svg xmlns="http://www.w3.org/2000/svg" width="62" height="62" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.52 3.48A11.86 11.86 0 0 0 12.08 0C5.5 0 .15 5.35.15 11.93c0 2.1.55 4.15 1.6 5.96L0 24l6.28-1.65a11.9 11.9 0 0 0 5.8 1.48h.01c6.58 0 11.93-5.35 11.93-11.93 0-3.18-1.24-6.18-3.5-8.42ZM12.09 21.8h-.01a9.88 9.88 0 0 1-5.04-1.38l-.36-.21-3.73.98 1-3.64-.23-.37a9.86 9.86 0 0 1-1.51-5.25c0-5.45 4.43-9.88 9.88-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.98c0 5.44-4.43 9.87-9.88 9.87Zm5.42-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.48.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z" />
                </svg>
            </div>

            <h1 class="auth-card__title">Cuenta confirmada</h1>

            <p class="auth-card__text">
                Tu correo fue confirmado correctamente. Ahora debes solicitar al administrador
                que te dé de alta en el sistema.
            </p>

            <div class="auth-card__user">
                <p><strong>Nombre:</strong> <?php echo $nombre . ' ' . $apellido; ?></p>
                <p><strong>Correo:</strong> <?php echo $email; ?></p>
                <p><strong>Estado actual:</strong> Pendiente de aprobación</p>
            </div>

            <div class="auth-card__notice">
                <strong>Siguiente paso:</strong>
                Presiona el botón de WhatsApp para enviar el mensaje al administrador.
                Cuando el administrador apruebe tu cuenta, podrás ingresar al dashboard.
            </div>

            <a href="<?php echo $whatsappUrl; ?>" target="_blank" rel="noopener noreferrer" class="auth-card__button auth-card__button--whatsapp">
                Solicitar alta por WhatsApp
            </a>

            <a href="/login" class="auth-card__link">
                Volver al login
            </a>
        </div>
    </section>

    <footer class="auth-page__footer">
        <p>© 2026 Freelance Manager EDN. Todos los derechos reservados.</p>
    </footer>
</main>