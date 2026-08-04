<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="proyecto-crear">
    <div class="proyecto-crear__breadcrumb">
        <a href="/proyectos">Proyectos</a>
        <span>/</span>
        <p>Editar</p>
    </div>

    <div class="proyecto-crear__header">
        <h1>Editar proyecto</h1>
        <p>Actualiza la información del proyecto seleccionado.</p>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <div class="proyecto-crear__grid">
        <div class="proyecto-crear__card">
            <form class="form-proyecto" method="POST" action="/proyectos/editar?id=<?php echo $proyecto->id; ?>">
                <?php include_once __DIR__ . '/formulario.php'; ?>

                <div class="form-proyecto__acciones">
                    <a href="/proyectos" class="form-proyecto__cancelar">Cancelar</a>

                    <button type="submit" class="form-proyecto__submit">
                        <i class="bi bi-floppy"></i>
                        Guardar cambios
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
                    <p>Estado actual</p>
                    <strong><?php echo $proyecto->estado ?: 'Pendiente'; ?></strong>
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
                    Los cambios realizados actualizarán la información principal del proyecto.
                    Las tareas y pagos se gestionarán desde sus módulos correspondientes.
                    Este proyecto está asociado a un cliente fijo. Para evitar errores de historial,
                    el cliente no puede cambiarse desde la edición del proyecto.
                </p>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>