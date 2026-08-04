<main class="login">
    <header class="login__topbar">
        <div class="login__brand">
            <div class="login__brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1m-9 0h14a1 1 0 0 1 1 1v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Zm-1 5h16M9 11v2h6v-2" />
                </svg>
            </div>
            <p class="login__brand-text">Freelance Manager EDN</p>
        </div>
    </header>

    <div class="login__grid">
        <section class="login__hero">
            <div class="login__hero-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1m-9 0h14a1 1 0 0 1 1 1v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a1 1 0 0 1 1-1Zm-1 5h16M9 11v2h6v-2" />
                </svg>
            </div>

            <h1 class="login__hero-title">Freelance Manager EDN</h1>

            <p class="login__hero-text">
                Gestiona tus clientes, proyectos, tareas<br>
                y cobros de forma ordenada.
            </p>
        </section>

        <section class="login__panel">
            <h2 class="login__heading">Iniciar sesión</h2>
            <p class="login__description">Ingresa tus credenciales para acceder</p>

            <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

            <?php if (isset($_GET['password_actualizada'])) : ?>
                <div class="alerta exito">
                    Contraseña actualizada correctamente. Ingresa con tu nueva contraseña.
                </div>
            <?php endif; ?>

            <form class="formulario" method="POST" action="/login">
                <div class="formulario__campo">
                    <label class="formulario__label" for="email">Correo electrónico</label>

                    <div class="formulario__input-wrapper">
                        <span class="formulario__icono">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4V6Zm0 0 8 7 8-7" />
                            </svg>
                        </span>

                        <input
                            class="formulario__input"
                            type="email"
                            id="email"
                            name="email"
                            placeholder="admin@edn.com"
                            value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                </div>

                <div class="formulario__campo">
                    <label class="formulario__label" for="password">Contraseña</label>

                    <div class="formulario__input-wrapper">
                        <span class="formulario__icono">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M7 10V8a5 5 0 0 1 10 0v2m-11 0h12v10H6V10Z" />
                            </svg>
                        </span>

                        <input
                            class="formulario__input formulario__input--password"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••">

                        <span class="formulario__toggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" />
                                <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="login__extras">
                    <label class="login__remember">
                        <input type="checkbox" name="recordarme">
                        <span>Recordarme</span>
                    </label>

                    <a href="/olvide-password" class="login__link">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <input
                    type="submit"
                    class="formulario__submit"
                    value="Iniciar sesión">
            </form>

            <div class="login__bottom">
                <p>¿No tienes cuenta?</p>
                <a href="/registro">Crear cuenta</a>
            </div>
        </section>
    </div>

    <footer class="login__footer">
        <p>© 2026 Freelance Manager EDN. Todos los derechos reservados.</p>
    </footer>
</main>