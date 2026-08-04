<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="tarea-crear">
    <div class="tarea-crear__breadcrumb">
        <a href="/tareas">Tareas</a>
        <span>/</span>
        <p>Crear</p>
    </div>

    <div class="tarea-crear__header">
        <h1>Nueva tarea</h1>
        <p>Registra la información de una nueva tarea.</p>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <div class="tarea-crear__grid">
        <div class="tarea-crear__card">
            <form class="form-tarea" method="POST" action="<?php echo $proyectoSeleccionado ? '/tareas/crear?proyecto_id=' . $proyectoSeleccionado->id : '/tareas/crear'; ?>">
                <?php include_once __DIR__ . '/formulario.php'; ?>

                <div class="form-tarea__acciones">
                    <?php if ($proyectoSeleccionado) : ?>
                        <a href="/proyectos/detalle?id=<?php echo $proyectoSeleccionado->id; ?>" class="form-tarea__cancelar">Cancelar</a>
                    <?php else : ?>
                        <a href="/tareas" class="form-tarea__cancelar">Cancelar</a>
                    <?php endif; ?>

                    <button type="submit" class="form-tarea__submit">
                        <i class="bi bi-floppy"></i>
                        Guardar tarea
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
                    <p>Estado inicial</p>
                    <strong>Pendiente</strong>
                </div>
            </div>

            <div class="tarea-info-card__item">
                <div class="tarea-info-card__icon tarea-info-card__icon--blue">
                    <i class="bi bi-folder"></i>
                </div>

                <div>
                    <p>Proyecto asociado</p>
                    <strong>
                        <?php
                        if ($proyectoSeleccionado) {
                            echo $proyectoSeleccionado->nombre;
                        } else {
                            echo 'Por seleccionar';
                        }
                        ?>
                    </strong>
                </div>
            </div>

            <div class="tarea-info-card__item">
                <div class="tarea-info-card__icon tarea-info-card__icon--purple">
                    <i class="bi bi-clock"></i>
                </div>

                <div>
                    <p>Avance inicial</p>
                    <strong><?php echo $tarea->avance ?? 0; ?>%</strong>
                </div>
            </div>

            
            <div class="tarea-info-card__nota">
                <i class="bi bi-info-circle"></i>
                <p>
                    Una vez guardada la tarea, podrás darle seguimiento, editar su estado
                    y registrar su avance.
                </p>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>