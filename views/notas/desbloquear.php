<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="notas-lock">
    <div class="notas-lock__card">
        <div class="notas-lock__icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h1>Notas protegidas</h1>

        <p>
            Para ver tus notas debes confirmar tu contraseña.
            Esta protección ayuda a evitar que otra persona vea información sensible.
        </p>

        <?php if (!empty($error)) : ?>
            <div class="notas-lock__error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/notas/verificar" class="notas-lock__form">
            <label for="password">Contraseña</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Ingresa tu contraseña"
                autocomplete="current-password">

            <button type="submit">
                <i class="bi bi-unlock"></i>
                Desbloquear notas
            </button>
        </form>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>