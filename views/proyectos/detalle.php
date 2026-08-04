<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<?php
$clienteNombre = trim(($proyecto->cliente_nombre ?? '') . ' ' . ($proyecto->cliente_apellido ?? ''));
$clienteNombre = $clienteNombre ?: ($proyecto->cliente_empresa ?? 'Cliente no registrado');

$fechaInicio = $proyecto->fecha_inicio ? date('d M Y', strtotime($proyecto->fecha_inicio)) : 'Sin fecha';
$fechaEntrega = $proyecto->fecha_entrega ? date('d M Y', strtotime($proyecto->fecha_entrega)) : 'Sin fecha';

$tipoCobro = $proyecto->tipo_cobro ?: 'Sin tipo';
$valorTotal = (float) ($proyecto->valor_total ?? 0);

$descripcion = $proyecto->descripcion ?: 'Sin descripción registrada.';
$objetivos = $proyecto->objetivos ?: 'Sin objetivos o alcance registrados.';
$observaciones = $proyecto->observaciones ?: 'Sin observaciones registradas.';

$estadoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $proyecto->estado));
$prioridadClase = strtolower($proyecto->prioridad ?: 'sin-prioridad');
?>

<section class="proyecto-detalle">
    <div class="proyecto-detalle__breadcrumb">
        <a href="/proyectos">Proyectos</a>
        <span>/</span>
        <p>Detalle</p>
    </div>

    <div class="proyecto-detalle__header">
        <h1>Detalle del proyecto</h1>
        <p>Consulta la información completa y el seguimiento del proyecto.</p>
    </div>

    <div class="proyecto-detalle__grid">
        <article class="proyecto-perfil">
            <div class="proyecto-perfil__top">
                <div class="proyecto-perfil__icono">
                    <i class="bi bi-folder"></i>
                </div>

                <div class="proyecto-perfil__titulo">
                    <h2><?php echo $proyecto->nombre; ?></h2>

                    <div class="proyecto-perfil__meta">
                        <span>
                            <i class="bi bi-person-fill"></i>
                            Cliente: <?php echo $clienteNombre; ?>
                        </span>

                        <span class="proyecto-badge proyecto-badge--<?php echo $estadoClase; ?>">
                            <?php echo $proyecto->estado; ?>
                        </span>

                        <?php if ($proyecto->prioridad) : ?>
                            <span class="prioridad prioridad--<?php echo $prioridadClase; ?>">
                                <?php echo $proyecto->prioridad; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="proyecto-perfil__acciones">
                    <a href="/proyectos/editar?id=<?php echo $proyecto->id; ?>" class="proyecto-perfil__btn proyecto-perfil__btn--secundario">
                        <i class="bi bi-pencil"></i>
                        Editar proyecto
                    </a>

                    <a href="/tareas/crear?proyecto_id=<?php echo $proyecto->id; ?>" class="proyecto-perfil__btn proyecto-perfil__btn--primario">
                        <i class="bi bi-plus-lg"></i>
                        Nueva tarea
                    </a>
                </div>
            </div>

            <div class="proyecto-perfil__separador"></div>

            <div class="proyecto-perfil__datos">
                <div class="proyecto-perfil__dato">
                    <i class="bi bi-calendar"></i>
                    <div>
                        <span>Fecha de inicio</span>
                        <p><?php echo $fechaInicio; ?></p>
                    </div>
                </div>

                <div class="proyecto-perfil__dato">
                    <i class="bi bi-calendar-event"></i>
                    <div>
                        <span>Fecha de entrega</span>
                        <p><?php echo $fechaEntrega; ?></p>
                    </div>
                </div>

                <div class="proyecto-perfil__dato">
                    <i class="bi bi-tag"></i>
                    <div>
                        <span>Tipo de cobro</span>
                        <p><?php echo $tipoCobro; ?></p>
                    </div>
                </div>

                <div class="proyecto-perfil__dato">
                    <i class="bi bi-currency-dollar"></i>
                    <div>
                        <span>Valor total</span>
                        <p>$<?php echo number_format($valorTotal, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="proyecto-perfil__separador"></div>

            <div class="proyecto-perfil__texto">
                <h3>Descripción breve</h3>
                <p><?php echo $descripcion; ?></p>
            </div>

            <div class="proyecto-perfil__texto">
                <h3>Objetivos o alcance</h3>
                <p><?php echo $objetivos; ?></p>
            </div>

            <div class="proyecto-perfil__texto">
                <h3>Observaciones</h3>
                <p><?php echo $observaciones; ?></p>
            </div>
        </article>

        <aside class="proyecto-resumen">
            <div class="proyecto-resumen__item">
                <div class="proyecto-resumen__icon proyecto-resumen__icon--blue">
                    <i class="bi bi-folder"></i>
                </div>

                <div>
                    <p>Tareas asociadas</p>
                    <strong><?php echo $resumen['tareas_asociadas']; ?></strong>
                </div>
            </div>

            <div class="proyecto-resumen__item">
                <div class="proyecto-resumen__icon proyecto-resumen__icon--green">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div>
                    <p>Tareas completadas</p>
                    <strong><?php echo $resumen['tareas_completadas']; ?></strong>
                </div>
            </div>

            <div class="proyecto-resumen__item">
                <div class="proyecto-resumen__icon proyecto-resumen__icon--purple">
                    <i class="bi bi-currency-dollar"></i>
                </div>

                <div>
                    <p>Saldo pendiente</p>
                    <strong>$<?php echo number_format($resumen['saldo_pendiente'], 2); ?></strong>
                </div>
            </div>

            <div class="proyecto-resumen__item">
                <div class="proyecto-resumen__icon proyecto-resumen__icon--green">
                    <i class="bi bi-cash-coin"></i>
                </div>

                <div>
                    <p>Total pagado</p>
                    <strong>$<?php echo number_format($resumen['total_pagado'], 2); ?></strong>
                </div>
            </div>

            <div class="proyecto-resumen__item">
                <div class="proyecto-resumen__icon proyecto-resumen__icon--orange">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <div>
                    <p>Último pago</p>
                    <strong><?php echo $resumen['ultimo_pago']; ?></strong>
                </div>
            </div>

            <div class="proyecto-resumen__item proyecto-resumen__item--avance">
                <div class="proyecto-resumen__icon proyecto-resumen__icon--blue">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>

                <div>
                    <p>Avance general</p>
                    <strong><?php echo $resumen['avance_general']; ?>%</strong>

                    <div class="proyecto-resumen__barra">
                        <span style="width: <?php echo $resumen['avance_general']; ?>%;"></span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <div class="proyecto-detalle__paneles">
        <section class="proyecto-detalle-card">
            <div class="proyecto-detalle-card__header">
                <h2>
                    <i class="bi bi-clipboard-check"></i>
                    Tareas recientes
                </h2>

                <a href="/tareas?proyecto_id=<?php echo $proyecto->id; ?>">
                    Ver todas las tareas
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>

            <?php if (empty($tareas)) : ?>
                <div class="proyecto-detalle-card__empty">
                    <p>Este proyecto todavía no tiene tareas registradas.</p>
                    <a href="/tareas/crear?proyecto_id=<?php echo $proyecto->id; ?>">
                        Crear primera tarea
                    </a>
                </div>
            <?php else : ?>
                <div class="detalle-tabla detalle-tabla--tareas">
                    <?php if (empty($tareas)) : ?>
                        <div class="detalle-empty">
                            <p>No hay tareas registradas para este proyecto.</p>
                            <a href="/tareas/crear?proyecto_id=<?php echo $proyecto->id; ?>">Crear tarea</a>
                        </div>
                    <?php else : ?>
                        <table class="detalle-tareas-tabla">
                            <thead>
                                <tr>
                                    <th>Tarea</th>
                                    <th>Estado</th>
                                    <th>Prioridad</th>
                                    <th>Fecha límite</th>
                                    <th>% de Tarea</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($tareas as $tarea) : ?>
                                    <?php
                                    $fechaLimite = !empty($tarea->tarea_fecha_limite)
                                        ? date('d/m/Y', strtotime($tarea->tarea_fecha_limite))
                                        : 'Sin fecha';

                                    $estadoTareaClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $tarea->tarea_estado ?? 'pendiente'));
                                    $prioridadTareaClase = strtolower($tarea->tarea_prioridad ?? 'sin-prioridad');
                                    ?>

                                    <tr>
                                        <td>
                                            <a
                                                href="/tareas/detalle?id=<?php echo $tarea->tarea_id; ?>"
                                                class="detalle-tarea-nombre"
                                                title="<?php echo $tarea->tarea_nombre; ?>">
                                                <?php echo $tarea->tarea_nombre; ?>
                                            </a>
                                        </td>

                                        <td>
                                            <span class="tarea-badge tarea-badge--<?php echo $estadoTareaClase; ?>">
                                                <?php echo $tarea->tarea_estado; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="prioridad prioridad--<?php echo $prioridadTareaClase; ?>">
                                                <?php echo $tarea->tarea_prioridad ?: 'Sin prioridad'; ?>
                                            </span>
                                        </td>

                                        <td><?php echo $fechaLimite; ?></td>

                                        <td>
                                            <div class="detalle-avance-mini">
                                                <span><?php echo (int) ($tarea->tarea_avance ?? 0); ?>%</span>
                                                <div>
                                                    <span style="width: <?php echo (int) ($tarea->tarea_avance ?? 0); ?>%;"></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="proyecto-detalle-card">
            <div class="proyecto-detalle-card__header">
                <h2>
                    <i class="bi bi-currency-dollar"></i>
                    Pagos registrados
                </h2>

                <a href="/pagos/crear?proyecto_id=<?php echo $proyecto->id; ?>">
                    <i class="bi bi-plus-lg"></i>
                    Registrar pago

                </a>

                <a href="/pagos?proyecto_id=<?php echo $proyecto->id; ?>">
                    Ver todos los pagos
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>

            <?php if (empty($pagos)) : ?>
                <div class="detalle-empty">
                    <p>No hay pagos registrados para este proyecto.</p>
                    <a href="/pagos/crear?proyecto_id=<?php echo $proyecto->id; ?>">Registrar pago</a>
                </div>
            <?php else : ?>
                <div class="detalle-tabla">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Referencia</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($pagos as $pago) : ?>
                                <?php
                                $fechaPago = !empty($pago->pago_fecha)
                                    ? date('d/m/Y', strtotime($pago->pago_fecha))
                                    : 'Sin fecha';

                                $estadoPagoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $pago->pago_estado ?? 'por-confirmar'));
                                ?>

                                <tr>
                                    <td><?php echo $fechaPago; ?></td>

                                    <td>
                                        <?php echo $pago->pago_referencia ?: 'Sin referencia'; ?>
                                    </td>

                                    <td>
                                        <?php echo $pago->pago_metodo ?: 'Sin método'; ?>
                                    </td>

                                    <td>
                                        <strong>$<?php echo number_format((float) ($pago->pago_monto ?? 0), 2); ?></strong>
                                    </td>

                                    <td>
                                        <span class="pago-badge pago-badge--<?php echo $estadoPagoClase; ?>">
                                            <?php echo $pago->pago_estado; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <a href="/pagos/detalle?id=<?php echo $pago->pago_id; ?>" class="accion accion--ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <section class="detalle-card detalle-card--notas">
        <div class="detalle-card__header">
            <h2>
                <i class="bi bi-journal-text"></i>
                Notas del proyecto
            </h2>

            <div class="detalle-card__acciones">
                <a href="/notas/crear?proyecto_id=<?php echo $proyecto->id; ?>">
                    <i class="bi bi-plus-lg"></i>
                    Crear nota
                </a>

                <?php if (($notasDesbloqueadas ?? false) && ($totalNotasProyecto ?? 0) > 0) : ?>
                    <a href="/notas?proyecto_id=<?php echo $proyecto->id; ?>">
                        Ver notas
                        <i class="bi bi-arrow-right"></i>
                    </a>
                <?php elseif (($totalNotasProyecto ?? 0) > 0) : ?>
                    <button
                        type="button"
                        class="detalle-card__link js-desbloquear-notas"
                        data-proyecto-id="<?php echo $proyecto->id; ?>"
                        data-redireccion="/proyectos/detalle?id=<?php echo $proyecto->id; ?>">
                        Desbloquear notas
                        <i class="bi bi-lock"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (($totalNotasProyecto ?? 0) === 0) : ?>

            <div class="detalle-empty">
                <p>No hay notas registradas para este proyecto.</p>

                <a href="/notas/crear?proyecto_id=<?php echo $proyecto->id; ?>">
                    Crear primera nota
                </a>
            </div>

        <?php elseif (!($notasDesbloqueadas ?? false)) : ?>

            <div class="detalle-notas-lock">
                <div class="detalle-notas-lock__icon">
                    <i class="bi bi-lock-fill"></i>
                </div>

                <div>
                    <h3>Este proyecto tiene notas protegidas</h3>
                    <p>
                        Hay <?php echo $totalNotasProyecto; ?> nota<?php echo (int) $totalNotasProyecto === 1 ? '' : 's'; ?> registrada<?php echo (int) $totalNotasProyecto === 1 ? '' : 's'; ?> para este proyecto.
                        Desbloquea las notas para poder visualizarlas.
                    </p>
                </div>

                <button
                    type="button"
                    class="detalle-notas-lock__button js-desbloquear-notas"
                    data-proyecto-id="<?php echo $proyecto->id; ?>"
                    data-redireccion="/proyectos/detalle?id=<?php echo $proyecto->id; ?>">
                    <i class="bi bi-unlock"></i>
                    Desbloquear notas
                </button>
            </div>

        <?php elseif (empty($notas)) : ?>

            <div class="detalle-empty">
                <p>
                    Las notas están desbloqueadas, pero no se pudieron cargar en esta vista.
                </p>

                <a href="/notas?proyecto_id=<?php echo $proyecto->id; ?>">
                    Ver notas en el módulo de notas
                </a>
            </div>

        <?php else : ?>

            <ul class="detalle-notas">
                <?php foreach ($notas as $nota) : ?>
                    <?php
                    $fechaNota = !empty($nota->nota_creado)
                        ? date('d/m/Y', strtotime($nota->nota_creado))
                        : 'Sin fecha';

                    $tituloNota = !empty($nota->nota_titulo)
                        ? $nota->nota_titulo
                        : 'Sin título';

                    $proyectoNota = !empty($nota->nota_proyecto_nombre)
                        ? $nota->nota_proyecto_nombre
                        : $proyecto->nombre;
                    ?>

                    <li>
                        <span><?php echo $fechaNota; ?></span>

                        <p>
                            <strong>
                                <a href="/notas/detalle?id=<?php echo $nota->nota_id; ?>">
                                    <?php echo $tituloNota; ?>
                                </a>
                            </strong>

                            <small>
                                Proyecto: <?php echo $proyectoNota; ?>
                            </small>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>