<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="tarea-crear">
    <div class="tarea-crear__breadcrumb">
        <a href="/tareas">Tareas</a>
        <span>/</span>
        <p>Editar</p>
    </div>

    <div class="tarea-crear__header">
        <h1>Editar tarea</h1>
        <p>Actualiza la información de la tarea seleccionada.</p>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <div class="tarea-crear__grid">
        <div class="tarea-crear__card">
            <form class="form-tarea" method="POST" action="/tareas/editar?id=<?php echo $tarea->id; ?>">
                <?php include_once __DIR__ . '/formulario.php'; ?>

                <div class="form-tarea__acciones">
                    <a href="/tareas" class="form-tarea__cancelar">Cancelar</a>

                    <button type="submit" class="form-tarea__submit">
                        <i class="bi bi-floppy"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <aside class="tarea-info-card">
            <h2>Información</h2>

            <div class="tarea-info-card__linea"></div>

            <div class="tarea-info-card__item">
                <div class="tarea-info-card__icon tarea-info-card__icon--success">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>
                    <p>Estado actual</p>
                    <strong><?php echo $tarea->estado ?: 'Pendiente'; ?></strong>
                </div>
            </div>

            <div class="tarea-info-card__item">
                <div class="tarea-info-card__icon tarea-info-card__icon--blue">
                    <i class="bi bi-folder"></i>
                </div>

                <div>
                    <p>Proyecto asociado</p>
                    <strong>
                        <?php echo $proyectoSeleccionado->nombre ?? 'Proyecto no encontrado'; ?>
                    </strong>
                </div>
            </div>

            <div class="tarea-info-card__item">
                <div class="tarea-info-card__icon tarea-info-card__icon--purple">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>

                <div>
                    <p>Avance actual</p>
                    <strong><?php echo (int) ($tarea->avance ?? 0); ?>%</strong>
                </div>
            </div>

            <div class="tarea-info-card__item">
                <div class="tarea-info-card__icon tarea-info-card__icon--purple">
                    <i class="bi bi-lock"></i>
                </div>

                <div>
                    <p>Proyecto fijo</p>
                    <strong>No editable</strong>
                </div>
            </div>

            <div class="tarea-info-card__nota">
                <i class="bi bi-info-circle"></i>
                <p>
                    Esta tarea está asociada a un proyecto fijo. Para evitar errores de historial,
                    el proyecto no puede cambiarse desde la edición.
                </p>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>