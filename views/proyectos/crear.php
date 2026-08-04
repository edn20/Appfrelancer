<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="proyecto-crear">
    <div class="proyecto-crear__breadcrumb">
        <a href="/proyectos">Proyectos</a>
        <span>/</span>
        <p>Crear</p>
    </div>

    <div class="proyecto-crear__header">
        <h1>Nuevo proyecto</h1>
        <p>Registra la información de un nuevo proyecto.</p>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <div class="proyecto-crear__grid">
        <div class="proyecto-crear__card">
            <form class="form-proyecto" method="POST" action="<?php echo $clienteSeleccionado ? '/proyectos/crear?cliente_id=' . $clienteSeleccionado->id : '/proyectos/crear'; ?>">
                <?php include_once __DIR__ . '/formulario.php'; ?>

                <div class="form-proyecto__acciones">
                    <?php if ($clienteSeleccionado) : ?>
                        <a href="/clientes/detalle?id=<?php echo $clienteSeleccionado->id; ?>" class="form-proyecto__cancelar">Cancelar</a>
                    <?php else : ?>
                        <a href="/proyectos" class="form-proyecto__cancelar">Cancelar</a>
                    <?php endif; ?>

                    <button type="submit" class="form-proyecto__submit">
                        <i class="bi bi-floppy"></i>
                        Guardar proyecto
                    </button>
                </div>
            </form>
        </div>

        <aside class="proyecto-info-card">
            <h2>Información</h2>

            <div class="proyecto-info-card__linea"></div>

            <div class="proyecto-info-card__item">
                <div class="proyecto-info-card__icon proyecto-info-card__icon--success">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>
                    <p>Estado inicial</p>
                    <strong>Pendiente</strong>
                </div>
            </div>

            <div class="proyecto-info-card__item">
                <div class="proyecto-info-card__icon proyecto-info-card__icon--blue">
                    <i class="bi bi-folder"></i>
                </div>

                <div>
                    <p>Tareas asociadas</p>
                    <strong>0</strong>
                </div>
            </div>

            <div class="proyecto-info-card__item">
                <div class="proyecto-info-card__icon proyecto-info-card__icon--purple">
                    <i class="bi bi-currency-dollar"></i>
                </div>

                <div>
                    <p>Pagos registrados</p>
                    <strong>$0.00</strong>
                </div>
            </div>

            <div class="proyecto-info-card__nota">
                <i class="bi bi-info-circle"></i>
                <p>
                    Una vez guardado el proyecto, podrás agregar tareas, pagos y dar
                    seguimiento al avance desde su detalle.
                </p>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>