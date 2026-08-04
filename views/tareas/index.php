<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="tareas">
    <div class="tareas__top">
        <div>
            <h1>Tareas</h1>

            <div class="tareas__breadcrumb">
                <a href="/dashboard">Inicio</a>
                <span>/</span>
                <p>Tareas</p>
            </div>
        </div>

        <a href="/tareas/crear" class="tareas__nuevo">
            <i class="bi bi-plus-lg"></i>
            Nueva tarea
        </a>
    </div>

    <?php if (!empty($error)) : ?>
        <div class="tareas__alerta tareas__alerta--error">
            <i class="bi bi-exclamation-triangle"></i>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form id="form-filtros-tareas" class="tareas__filtros" method="GET" action="/tareas">
        <input type="hidden" name="page" value="1">

        <div class="tareas__busqueda">
            <i class="bi bi-search"></i>
            <input
                type="text"
                id="busqueda-tareas"
                name="busqueda"
                placeholder="Buscar tarea o proyecto..."
                autocomplete="off"
                value="<?php echo $filtros['busqueda'] ?? ''; ?>">
        </div>

        <div class="tareas__select">
            <label for="proyecto_id">Proyecto</label>

            <select id="proyecto_id" name="proyecto_id">
                <option value="">Todos</option>

                <?php foreach ($proyectos ?? [] as $proyecto) : ?>
                    <option
                        value="<?php echo $proyecto->id; ?>"
                        <?php echo (string) ($filtros['proyecto_id'] ?? '') === (string) $proyecto->id ? 'selected' : ''; ?>>
                        <?php echo $proyecto->nombre; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="tareas__select">
            <label for="estado">Estado</label>

            <select id="estado" name="estado">
                <option value="">Todos</option>
                <option value="Pendiente" <?php echo ($filtros['estado'] ?? '') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="En proceso" <?php echo ($filtros['estado'] ?? '') === 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
                <option value="En revisión" <?php echo ($filtros['estado'] ?? '') === 'En revisión' ? 'selected' : ''; ?>>En revisión</option>
                <option value="Completada" <?php echo ($filtros['estado'] ?? '') === 'Completada' ? 'selected' : ''; ?>>Completada</option>
                <option value="Anulada" <?php echo ($filtros['estado'] ?? '') === 'Anulada' ? 'selected' : ''; ?>>Anulada</option>
            </select>
        </div>

        <div class="tareas__select">
            <label for="prioridad">Prioridad</label>

            <select id="prioridad" name="prioridad">
                <option value="">Todas</option>
                <option value="Baja" <?php echo ($filtros['prioridad'] ?? '') === 'Baja' ? 'selected' : ''; ?>>Baja</option>
                <option value="Media" <?php echo ($filtros['prioridad'] ?? '') === 'Media' ? 'selected' : ''; ?>>Media</option>
                <option value="Alta" <?php echo ($filtros['prioridad'] ?? '') === 'Alta' ? 'selected' : ''; ?>>Alta</option>
                <option value="Urgente" <?php echo ($filtros['prioridad'] ?? '') === 'Urgente' ? 'selected' : ''; ?>>Urgente</option>
            </select>
        </div>

        <?php if (!empty($filtros['vencidas'])) : ?>
            <input type="hidden" name="vencidas" value="1">
        <?php endif; ?>
    </form>

    <?php
    $hayBusqueda = !empty($filtros['busqueda']);
    $hayProyecto = !empty($filtros['proyecto_id']);
    $hayEstado = !empty($filtros['estado']);
    $hayPrioridad = !empty($filtros['prioridad']);
    $hayVencidas = !empty($filtros['vencidas']);

    $hayFiltros = $hayBusqueda || $hayProyecto || $hayEstado || $hayPrioridad || $hayVencidas;

    $nombreProyectoFiltro = '';

    if ($hayProyecto && !empty($proyectos)) {
        foreach ($proyectos as $proyecto) {
            if ((string) $proyecto->id === (string) $filtros['proyecto_id']) {
                $nombreProyectoFiltro = $proyecto->nombre;
                break;
            }
        }
    }
    ?>

    <?php if ($hayFiltros) : ?>
        <?php if (!empty($tareas)) : ?>
            <div class="tareas-filtro-alerta tareas-filtro-alerta--success">
                <i class="bi bi-check-circle"></i>
                <p>
                    Mostrando tareas filtradas
                    <?php if ($hayBusqueda) : ?>
                        por búsqueda <strong>"<?php echo $filtros['busqueda']; ?>"</strong>
                    <?php endif; ?>

                    <?php if ($hayProyecto && $nombreProyectoFiltro) : ?>
                        del proyecto <strong><?php echo $nombreProyectoFiltro; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayEstado) : ?>
                        con estado <strong><?php echo $filtros['estado']; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayPrioridad) : ?>
                        con prioridad <strong><?php echo $filtros['prioridad']; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayVencidas) : ?>
                        <strong>vencidas o retrasadas</strong>
                        <?php endif; ?>.
                </p>
                <a href="/tareas">Limpiar filtros</a>
            </div>
        <?php else : ?>
            <div class="tareas-filtro-alerta tareas-filtro-alerta--empty">
                <i class="bi bi-info-circle"></i>

                <p>
                    No se encontraron tareas con los filtros seleccionados.
                </p>

                <a href="/tareas">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="tareas__card">
        <?php if (empty($tareas)) : ?>
            <div class="tareas__empty">
                <div class="tareas__empty-icon">
                    <i class="bi bi-list-check"></i>
                </div>

                <h2>No tienes tareas registradas</h2>

                <p>
                    Cuando registres tu primera tarea, aparecerá en esta sección.
                    Podrás controlar su estado, prioridad, fecha límite y avance.
                </p>

                <a href="/tareas/crear" class="tareas__empty-button">
                    <i class="bi bi-plus-lg"></i>
                    Crear primera tarea
                </a>
            </div>
        <?php else : ?>
            <div class="tareas-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tarea</th>
                            <th>Proyecto</th>
                            <th>Fecha límite</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($tareas as $tarea) : ?>
                            <?php
                            $fechaLimite = $tarea->fecha_limite ? formatearFechaGlobal($tarea->fecha_limite) : 'Sin fecha';

                            $estadoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $tarea->estado));
                            $prioridadClase = strtolower($tarea->prioridad ?: 'sin-prioridad');

                            $avance = (int) ($tarea->avance ?? 0);

                            if ($avance < 0) {
                                $avance = 0;
                            }

                            if ($avance > 100) {
                                $avance = 100;
                            }
                            ?>

                            <tr>
                                <td><?php echo $tarea->id; ?></td>

                                <td>
                                    <div class="tarea-nombre">
                                        <a href="/tareas/detalle?id=<?php echo $tarea->id; ?>">
                                            <?php echo $tarea->nombre; ?>
                                        </a>

                                        <p>
                                            <?php
                                            echo $tarea->descripcion
                                                ? substr($tarea->descripcion, 0, 80)
                                                : 'Sin descripción registrada.';
                                            ?>
                                        </p>
                                    </div>
                                </td>

                                <td>
                                    <div class="tarea-proyecto">
                                        <p><?php echo $tarea->proyecto_nombre ?: 'Sin proyecto'; ?></p>

                                        <span>
                                            <?php
                                            $clienteNombre = trim(($tarea->cliente_nombre ?? '') . ' ' . ($tarea->cliente_apellido ?? ''));

                                            if (!$clienteNombre && !empty($tarea->cliente_empresa)) {
                                                $clienteNombre = $tarea->cliente_empresa;
                                            }

                                            echo $clienteNombre ? 'Cliente: ' . $clienteNombre : 'Cliente no registrado';
                                            ?>
                                        </span>
                                    </div>
                                </td>
                                <td><?php echo $fechaLimite; ?></td>

                                <td>
                                    <?php if ($tarea->prioridad) : ?>
                                        <span class="prioridad prioridad--<?php echo $prioridadClase; ?>">
                                            <?php echo $tarea->prioridad; ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="prioridad prioridad--sin-prioridad">
                                            Sin prioridad
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="tarea-badge tarea-badge--<?php echo $estadoClase; ?>">
                                        <?php echo $tarea->estado; ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="tarea-progreso">
                                        <span><?php echo $avance; ?>%</span>

                                        <div class="tarea-progreso__barra">
                                            <div style="width: <?php echo $avance; ?>%;"></div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="tareas-tabla__acciones">
                                        <a href="/tareas/detalle?id=<?php echo $tarea->id; ?>" class="accion accion--ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="/tareas/editar?id=<?php echo $tarea->id; ?>" class="accion accion--editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form method="POST" action="/tareas/eliminar" class="js-eliminar-tarea">
                                            <input type="hidden" name="id" value="<?php echo $tarea->id; ?>">
                                            <input type="hidden" name="nombre" value="<?php echo $tarea->nombre; ?>">
                                            <input type="hidden" name="estado" value="<?php echo $tarea->estado; ?>">

                                            <button type="submit" class="accion accion--eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tareas__footer-tabla">
                <p>
                    Mostrando
                    <?php echo $paginacion->inicio(); ?>
                    a
                    <?php echo $paginacion->fin(); ?>
                    de
                    <?php echo $paginacion->totalRegistros; ?>
                    tareas
                </p>

                <?php echo $paginacion->selectorPorPagina('form-filtros-tareas'); ?>

                <?php if ($paginacion->totalPaginas() > 1) : ?>
                    <div class="tareas__paginacion">

                        <?php if ($paginacion->paginaAnterior()) : ?>
                            <a href="<?php echo $paginacion->crearUrl($paginacion->paginaAnterior()); ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php
                        $paginaActual = (int) $paginacion->paginaActual;
                        $totalPaginas = (int) $paginacion->totalPaginas();

                        $paginasVisibles = [];

                        if ($paginaActual > 1) {
                            $paginasVisibles[] = $paginaActual - 1;
                        }

                        $paginasVisibles[] = $paginaActual;

                        if ($paginaActual < $totalPaginas) {
                            $paginasVisibles[] = $paginaActual + 1;
                        }
                        ?>

                        <?php foreach ($paginasVisibles as $numeroPagina) : ?>
                            <?php if ($numeroPagina === $paginaActual) : ?>
                                <span class="tareas__paginacion-activo">
                                    <?php echo $numeroPagina; ?>
                                </span>
                            <?php else : ?>
                                <a href="<?php echo $paginacion->crearUrl($numeroPagina); ?>">
                                    <?php echo $numeroPagina; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ($paginacion->paginaSiguiente()) : ?>
                            <a href="<?php echo $paginacion->crearUrl($paginacion->paginaSiguiente()); ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="tareas-resumen">
        <div class="tareas-resumen__item">
            <div class="tareas-resumen__icon tareas-resumen__icon--blue">
                <i class="bi bi-check2-square"></i>
            </div>

            <div>
                <p><?php echo !empty($filtros['proyecto_id']) ? 'Tareas del proyecto' : 'Total tareas'; ?></p>
                <strong><?php echo $resumen['total']; ?></strong>
            </div>
        </div>

        <div class="tareas-resumen__item">
            <div class="tareas-resumen__icon tareas-resumen__icon--blue">
                <i class="bi bi-arrow-repeat"></i>
            </div>

            <div>
                <p>En progreso</p>
                <strong><?php echo $resumen['en_progreso']; ?></strong>
            </div>
        </div>

        <div class="tareas-resumen__item">
            <div class="tareas-resumen__icon tareas-resumen__icon--orange">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <p>Pendientes</p>
                <strong><?php echo $resumen['pendientes']; ?></strong>
            </div>
        </div>

        <div class="tareas-resumen__item">
            <div class="tareas-resumen__icon tareas-resumen__icon--green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <p>Completadas</p>
                <strong><?php echo $resumen['completadas']; ?></strong>
            </div>
        </div>

        <div class="tareas-resumen__item">
            <div class="tareas-resumen__icon tareas-resumen__icon--red">
                <i class="bi bi-exclamation-circle"></i>
            </div>

            <div>
                <p>Retrasadas</p>
                <strong><?php echo $resumen['retrasadas']; ?></strong>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>