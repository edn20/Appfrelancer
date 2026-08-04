<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<?php
$clienteNombre = trim(($tarea->cliente_nombre ?? '') . ' ' . ($tarea->cliente_apellido ?? ''));
$clienteNombre = $clienteNombre ?: ($tarea->cliente_empresa ?? 'Cliente no registrado');

$fechaLimite = $tarea->fecha_limite ? date('d/m/Y', strtotime($tarea->fecha_limite)) : 'Sin fecha límite';
$avance = (int) ($tarea->avance ?? 0);
$avance = max(0, min(100, $avance));

$descripcion = $tarea->descripcion ?: 'Sin descripción registrada.';
$objetivo = $tarea->objetivo ?: 'Sin objetivo o entregable registrado.';
$observaciones = $tarea->observaciones ?: 'Sin observaciones registradas.';

$estadoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $tarea->estado));
$prioridadClase = strtolower($tarea->prioridad ?: 'sin-prioridad');

$error = $_SESSION['error_tarea_detalle'] ?? null;
$exito = $_SESSION['exito_tarea_detalle'] ?? null;

unset($_SESSION['error_tarea_detalle']);
unset($_SESSION['exito_tarea_detalle']);
?>

<section class="tarea-detalle">
    <div class="tarea-detalle__volver">
        <a href="/tareas">
            <i class="bi bi-arrow-left"></i>
            Volver a tareas
        </a>
    </div>

    <div class="tarea-detalle__top">
        <div>
            <h1><?php echo $tarea->nombre; ?></h1>

            <div class="tarea-detalle__breadcrumb">
                <a href="/dashboard">Inicio</a>
                <span>/</span>
                <a href="/proyectos">Proyectos</a>
                <span>/</span>
                <a href="/proyectos/detalle?id=<?php echo $tarea->proyecto_id; ?>">
                    <?php echo $tarea->proyecto_nombre; ?>
                </a>
                <span>/</span>
                <p>Tareas</p>
            </div>
        </div>

        <div class="tarea-detalle__acciones">
            <a href="/tareas/editar?id=<?php echo $tarea->id; ?>" class="tarea-detalle__btn tarea-detalle__btn--secundario">
                <i class="bi bi-pencil"></i>
                Editar
            </a>
        </div>
    </div>

    <?php if (!empty($error)) : ?>
        <div class="tarea-detalle__alerta tarea-detalle__alerta--error">
            <i class="bi bi-exclamation-triangle"></i>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($exito)) : ?>
        <div class="tarea-detalle__alerta tarea-detalle__alerta--exito">
            <i class="bi bi-check-circle"></i>
            <p><?php echo $exito; ?></p>
        </div>
    <?php endif; ?>

    <div class="tarea-detalle__grid">
        <div class="tarea-detalle__principal">
            <section class="tarea-card">
                <h2>Información de la tarea</h2>

                <div class="tarea-info-grid">
                    <div class="tarea-info-grid__item">
                        <span>Proyecto</span>
                        <p>
                            <i class="bi bi-folder"></i>
                            <?php echo $tarea->proyecto_nombre; ?>
                        </p>
                    </div>

                    <div class="tarea-info-grid__item">
                        <span>Cliente</span>
                        <p>
                            <i class="bi bi-person"></i>
                            <?php echo $clienteNombre; ?>
                        </p>
                    </div>

                    <div class="tarea-info-grid__item">
                        <span>Estado</span>
                        <p>
                            <span class="tarea-badge tarea-badge--<?php echo $estadoClase; ?>">
                                <?php echo $tarea->estado; ?>
                            </span>
                        </p>
                    </div>

                    <div class="tarea-info-grid__item">
                        <span>Progreso</span>

                        <div class="tarea-detalle-progreso">
                            <strong><?php echo $avance; ?>%</strong>

                            <div class="tarea-detalle-progreso__barra">
                                <div style="width: <?php echo $avance; ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="tarea-info-grid__item">
                        <span>Fecha límite</span>
                        <p>
                            <i class="bi bi-calendar"></i>
                            <?php echo $fechaLimite; ?>
                        </p>
                    </div>

                    <div class="tarea-info-grid__item">
                        <span>Prioridad</span>
                        <p>
                            <?php if ($tarea->prioridad) : ?>
                                <span class="prioridad prioridad--<?php echo $prioridadClase; ?>">
                                    <?php echo $tarea->prioridad; ?>
                                </span>
                            <?php else : ?>
                                <span class="prioridad prioridad--sin-prioridad">Sin prioridad</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </section>

            <section class="tarea-card">
                <h2>Descripción</h2>
                <p class="tarea-card__texto"><?php echo $descripcion; ?></p>
            </section>

            <section class="tarea-card">
                <h2>Objetivo o entregable</h2>
                <p class="tarea-card__texto"><?php echo $objetivo; ?></p>
            </section>

            <section class="tarea-card">
                <h2>Observaciones</h2>
                <p class="tarea-card__texto"><?php echo $observaciones; ?></p>
            </section>
        </div>

        <aside class="tarea-detalle__lateral">
            <section class="tarea-card tarea-card--adjuntos">
                <div class="tarea-card__header">
                    <h2>Archivos adjuntos</h2>
                    <span><?php echo count($adjuntos); ?></span>
                </div>

                <form class="tarea-upload" method="POST" action="/tareas/adjunto" enctype="multipart/form-data">
                    <input type="hidden" name="tarea_id" value="<?php echo $tarea->id; ?>">

                    <label for="archivo">
                        <i class="bi bi-cloud-arrow-up"></i>
                        Seleccionar archivo
                    </label>

                    <input type="file" id="archivo" name="archivo" required>

                    <button type="submit">
                        Subir archivo
                    </button>
                </form>

                <?php if (empty($adjuntos)) : ?>
                    <div class="tarea-adjuntos-empty">
                        <i class="bi bi-paperclip"></i>
                        <p>Esta tarea todavía no tiene archivos adjuntos.</p>
                    </div>
                <?php else : ?>
                    <div class="tarea-adjuntos">
                        <?php foreach ($adjuntos as $adjunto) : ?>
                            <div class="tarea-adjuntos__item">
                                <div class="tarea-adjuntos__icon">
                                    <i class="bi bi-file-earmark"></i>
                                </div>

                                <div class="tarea-adjuntos__info">
                                    <a href="/tareas/adjunto/descargar?id=<?php echo $adjunto->id; ?>">
                                        <?php echo $adjunto->nombre_original; ?>
                                    </a>

                                    <span>
                                        <?php echo number_format(($adjunto->peso ?? 0) / 1024, 1); ?> KB
                                    </span>
                                </div>

                                <div class="tarea-adjuntos__acciones">
                                    <a href="/tareas/adjunto/descargar?id=<?php echo $adjunto->id; ?>" title="Descargar">
                                        <i class="bi bi-download"></i>
                                    </a>

                                    <form method="POST" action="/tareas/adjunto/eliminar">
                                        <input type="hidden" name="adjunto_id" value="<?php echo $adjunto->id; ?>">
                                        <input type="hidden" name="tarea_id" value="<?php echo $tarea->id; ?>">

                                        <button type="submit" title="Eliminar adjunto">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="tarea-card">
                <h2>Resumen</h2>

                <div class="tarea-resumen-mini">
                    <div>
                        <span>Estado</span>
                        <strong><?php echo $tarea->estado; ?></strong>
                    </div>

                    <div>
                        <span>Avance</span>
                        <strong><?php echo $avance; ?>%</strong>
                    </div>

                    <div>
                        <span>Proyecto</span>
                        <strong><?php echo $tarea->proyecto_nombre; ?></strong>
                    </div>

                    <div>
                        <span>Cliente</span>
                        <strong><?php echo $clienteNombre; ?></strong>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>