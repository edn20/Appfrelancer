<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="proyectos">
    <div class="proyectos__top">
        <div>
            <h1>Proyectos</h1>

            <div class="proyectos__breadcrumb">
                <a href="/dashboard">Inicio</a>
                <span>/</span>
                <p>Proyectos</p>
            </div>
        </div>

        <a href="/proyectos/crear" class="proyectos__nuevo">
            <i class="bi bi-plus-lg"></i>
            Nuevo proyecto
        </a>
    </div>

    <?php if (!empty($error)) : ?>
        <div class="proyectos__alerta proyectos__alerta--error">
            <i class="bi bi-exclamation-triangle"></i>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form id="form-filtros-proyectos" class="proyectos__filtros" method="GET" action="/proyectos">
        <input type="hidden" name="page" value="1">

        <?php if (!empty($filtros['alerta'])) : ?>
            <input type="hidden" name="alerta" value="<?php echo $filtros['alerta']; ?>">
        <?php endif; ?>

        <div class="proyectos__busqueda">
            <i class="bi bi-search"></i>

            <input
                type="text"
                id="busqueda-proyectos"
                name="busqueda"
                placeholder="Buscar proyecto o cliente..."
                autocomplete="off"
                value="<?php echo $filtros['busqueda'] ?? ''; ?>">
        </div>

        <div class="proyectos__select">
            <label for="cliente_id">Cliente</label>

            <select id="cliente_id" name="cliente_id">
                <option value="">Todos</option>

                <?php foreach ($clientes as $cliente) : ?>
                    <option
                        value="<?php echo $cliente->id; ?>"
                        <?php echo (int) ($filtros['cliente_id'] ?? 0) === (int) $cliente->id ? 'selected' : ''; ?>>
                        <?php echo trim($cliente->nombre . ' ' . $cliente->apellido); ?>
                        <?php echo $cliente->empresa ? ' - ' . $cliente->empresa : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="proyectos__select">
            <label for="estado">Estado</label>

            <select id="estado" name="estado">
                <option value="">Todos</option>
                <option value="Pendiente" <?php echo ($filtros['estado'] ?? '') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="En proceso" <?php echo ($filtros['estado'] ?? '') === 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
                <option value="En revisión" <?php echo ($filtros['estado'] ?? '') === 'En revisión' ? 'selected' : ''; ?>>En revisión</option>
                <option value="Entregado" <?php echo ($filtros['estado'] ?? '') === 'Entregado' ? 'selected' : ''; ?>>Entregado</option>
                <option value="Pausado" <?php echo ($filtros['estado'] ?? '') === 'Pausado' ? 'selected' : ''; ?>>Pausado</option>
                <option value="Cancelado" <?php echo ($filtros['estado'] ?? '') === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>

        <div class="proyectos__select">
            <label for="prioridad">Prioridad</label>

            <select id="prioridad" name="prioridad">
                <option value="">Todas</option>
                <option value="Baja" <?php echo ($filtros['prioridad'] ?? '') === 'Baja' ? 'selected' : ''; ?>>Baja</option>
                <option value="Media" <?php echo ($filtros['prioridad'] ?? '') === 'Media' ? 'selected' : ''; ?>>Media</option>
                <option value="Alta" <?php echo ($filtros['prioridad'] ?? '') === 'Alta' ? 'selected' : ''; ?>>Alta</option>
                <option value="Urgente" <?php echo ($filtros['prioridad'] ?? '') === 'Urgente' ? 'selected' : ''; ?>>Urgente</option>
            </select>
        </div>
    </form>

    <?php
    $hayBusqueda = !empty($filtros['busqueda']);
    $hayCliente = !empty($filtros['cliente_id']);
    $hayEstado = !empty($filtros['estado']);
    $hayPrioridad = !empty($filtros['prioridad']);
    $hayAlerta = !empty($filtros['alerta']);

    $hayFiltros = $hayBusqueda || $hayCliente || $hayEstado || $hayPrioridad || $hayAlerta;

    $clienteFiltroNombre = '';

    if ($hayCliente) {
        foreach ($clientes as $cliente) {
            if ((int) $cliente->id === (int) $filtros['cliente_id']) {
                $clienteFiltroNombre = trim($cliente->nombre . ' ' . $cliente->apellido);

                if (!empty($cliente->empresa)) {
                    $clienteFiltroNombre .= ' - ' . $cliente->empresa;
                }

                break;
            }
        }
    }
    ?>

    <?php if (($filtros['alerta'] ?? '') === 'atrasados') : ?>
        <div class="proyectos-filtro-alerta proyectos-filtro-alerta--warning">
            <i class="bi bi-bell"></i>
            <p>Mostrando solo proyectos atrasados.</p>
            <a href="/proyectos">Limpiar filtro</a>
        </div>
    <?php endif; ?>

    <?php if (($filtros['alerta'] ?? '') === 'proximos') : ?>
        <div class="proyectos-filtro-alerta proyectos-filtro-alerta--warning">
            <i class="bi bi-calendar-event"></i>
            <p>Mostrando proyectos que vencen hoy o dentro de los próximos 5 días.</p>
            <a href="/proyectos">Limpiar filtro</a>
        </div>
    <?php endif; ?>

    <?php if ($hayFiltros && !$hayAlerta) : ?>
        <?php if (!empty($proyectos)) : ?>
            <div class="proyectos-filtro-alerta proyectos-filtro-alerta--success">
                <i class="bi bi-check-circle"></i>

                <p>
                    Mostrando proyectos

                    <?php if ($hayCliente) : ?>
                        del cliente <strong><?php echo $clienteFiltroNombre; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayEstado) : ?>
                        <?php echo $hayCliente ? ' con estado ' : 'con estado '; ?>
                        <strong><?php echo strtolower($filtros['estado']); ?></strong>
                    <?php endif; ?>

                    <?php if ($hayPrioridad) : ?>
                        <?php echo ($hayCliente || $hayEstado) ? ' y prioridad ' : 'con prioridad '; ?>
                        <strong><?php echo strtolower($filtros['prioridad']); ?></strong>
                    <?php endif; ?>

                    <?php if ($hayBusqueda) : ?>
                        <?php echo ($hayCliente || $hayEstado || $hayPrioridad) ? ' que coinciden con ' : 'que coinciden con '; ?>
                        <strong>"<?php echo $filtros['busqueda']; ?>"</strong>
                        <?php endif; ?>.
                </p>

                <a href="/proyectos">Limpiar filtros</a>
            </div>
        <?php else : ?>
            <div class="proyectos-filtro-alerta proyectos-filtro-alerta--empty">
                <i class="bi bi-info-circle"></i>

                <p>
                    <?php if ($hayCliente && !$hayEstado && !$hayPrioridad && !$hayBusqueda) : ?>
                        No hay proyectos registrados para <strong><?php echo $clienteFiltroNombre; ?></strong>.
                    <?php elseif ($hayEstado && !$hayCliente && !$hayPrioridad && !$hayBusqueda) : ?>
                        No hay proyectos con estado <strong><?php echo strtolower($filtros['estado']); ?></strong>.
                    <?php elseif ($hayPrioridad && !$hayCliente && !$hayEstado && !$hayBusqueda) : ?>
                        No hay proyectos con prioridad <strong><?php echo strtolower($filtros['prioridad']); ?></strong>.
                    <?php elseif ($hayBusqueda) : ?>
                        No se encontraron proyectos que coincidan con <strong>"<?php echo $filtros['busqueda']; ?>"</strong>.
                    <?php else : ?>
                        No se encontraron proyectos con los filtros seleccionados.
                    <?php endif; ?>
                </p>

                <a href="/proyectos">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="proyectos__card">
        <?php if (empty($proyectos)) : ?>
            <div class="proyectos__empty">
                <div class="proyectos__empty-icon">
                    <i class="bi bi-folder"></i>
                </div>

                <h2>
                    <?php
                    if (($filtros['alerta'] ?? '') === 'atrasados') {
                        echo 'No hay proyectos atrasados';
                    } elseif (($filtros['alerta'] ?? '') === 'proximos') {
                        echo 'No hay proyectos próximos a entregar';
                    } elseif ($hayFiltros) {
                        echo 'No hay resultados para los filtros seleccionados';
                    } else {
                        echo 'No tienes proyectos registrados';
                    }
                    ?>
                </h2>

                <p>
                    <?php if (($filtros['alerta'] ?? '') === 'atrasados') : ?>
                        No existen proyectos con fecha de entrega vencida.
                    <?php elseif (($filtros['alerta'] ?? '') === 'proximos') : ?>
                        No existen proyectos que venzan hoy o dentro de los próximos 5 días.
                    <?php elseif ($hayFiltros) : ?>
                        Cambia los filtros o limpia la búsqueda para ver nuevamente todos tus proyectos.
                    <?php else : ?>
                        Cuando registres tu primer proyecto, aparecerá en esta sección.
                        Podrás relacionarlo con un cliente y luego agregar tareas y pagos.
                    <?php endif; ?>
                </p>

                <?php if ($hayFiltros) : ?>
                    <a href="/proyectos" class="proyectos__empty-button">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Limpiar filtros
                    </a>
                <?php else : ?>
                    <a href="/proyectos/crear" class="proyectos__empty-button">
                        <i class="bi bi-plus-lg"></i>
                        Crear primer proyecto
                    </a>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="proyectos-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Proyecto</th>
                            <th>Cliente</th>
                            <th>Inicio</th>
                            <th>Entrega estimada</th>
                            <th>Valor</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($proyectos as $proyecto) : ?>
                            <?php
                            $clienteNombre = trim(($proyecto->cliente_nombre ?? '') . ' ' . ($proyecto->cliente_apellido ?? ''));
                            $clienteNombre = $clienteNombre ?: ($proyecto->cliente_empresa ?? 'Sin cliente');

                            $fechaInicio = $proyecto->fecha_inicio ? formatearFechaGlobal($proyecto->fecha_inicio) : 'Sin fecha';
                            $fechaEntrega = $proyecto->fecha_entrega ? formatearFechaGlobal($proyecto->fecha_entrega) : 'Sin fecha';

                            $estadoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $proyecto->estado));
                            $prioridadClase = strtolower($proyecto->prioridad ?: 'sin-prioridad');
                            ?>

                            <tr>
                                <td><?php echo $proyecto->id; ?></td>

                                <td>
                                    <div class="proyecto-nombre">
                                        <a href="/proyectos/detalle?id=<?php echo $proyecto->id; ?>">
                                            <?php echo $proyecto->nombre; ?>
                                        </a>

                                        <p>
                                            <?php
                                            echo $proyecto->descripcion
                                                ? substr($proyecto->descripcion, 0, 70)
                                                : 'Sin descripción registrada.';
                                            ?>
                                        </p>
                                    </div>
                                </td>

                                <td><?php echo $clienteNombre; ?></td>
                                <td><?php echo $fechaInicio; ?></td>
                                <td><?php echo $fechaEntrega; ?></td>

                                <td>
                                    <strong>$<?php echo number_format($proyecto->valor_total, 2); ?></strong>

                                    <?php if ($proyecto->tipo_cobro) : ?>
                                        <span class="proyectos-tabla__tipo">
                                            / <?php echo strtolower($proyecto->tipo_cobro); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="proyecto-badge proyecto-badge--<?php echo $estadoClase; ?>">
                                        <?php echo $proyecto->estado; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($proyecto->prioridad) : ?>
                                        <span class="prioridad prioridad--<?php echo $prioridadClase; ?>">
                                            <?php echo $proyecto->prioridad; ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="prioridad prioridad--sin-prioridad">
                                            Sin prioridad
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="proyectos-tabla__acciones">
                                        <a href="/proyectos/detalle?id=<?php echo $proyecto->id; ?>" class="accion accion--ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="/proyectos/editar?id=<?php echo $proyecto->id; ?>" class="accion accion--editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form method="POST" action="/proyectos/eliminar" class="js-eliminar-proyecto">
                                            <input type="hidden" name="id" value="<?php echo $proyecto->id; ?>">
                                            <input type="hidden" name="nombre" value="<?php echo $proyecto->nombre; ?>">
                                            <input type="hidden" name="estado" value="<?php echo $proyecto->estado; ?>">

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

            <div class="proyectos__footer-tabla">
                <p>
                    Mostrando
                    <?php echo $paginacion->inicio(); ?>
                    a
                    <?php echo $paginacion->fin(); ?>
                    de
                    <?php echo $paginacion->totalRegistros; ?>
                    proyectos
                </p>

                <?php echo $paginacion->selectorPorPagina('form-filtros-proyectos'); ?>

                <?php if ($paginacion->totalPaginas() > 1) : ?>
                    <div class="proyectos__paginacion">

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
                                <span class="proyectos__paginacion-activo">
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

    <div class="proyectos-resumen">
        <div class="proyectos-resumen__item">
            <div class="proyectos-resumen__icon proyectos-resumen__icon--blue">
                <i class="bi bi-folder"></i>
            </div>

            <div>
                <p><?php echo !empty($filtros['cliente_id']) ? 'Proyectos del cliente' : 'Total proyectos'; ?></p>
                <strong><?php echo $resumen['total']; ?></strong>
            </div>
        </div>

        <div class="proyectos-resumen__item">
            <div class="proyectos-resumen__icon proyectos-resumen__icon--blue">
                <i class="bi bi-arrow-repeat"></i>
            </div>

            <div>
                <p>En proceso</p>
                <strong><?php echo $resumen['en_proceso']; ?></strong>
            </div>
        </div>

        <div class="proyectos-resumen__item">
            <div class="proyectos-resumen__icon proyectos-resumen__icon--orange">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <p>Pendientes</p>
                <strong><?php echo $resumen['pendientes']; ?></strong>
            </div>
        </div>

        <div class="proyectos-resumen__item">
            <div class="proyectos-resumen__icon proyectos-resumen__icon--green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <p>Entregados</p>
                <strong><?php echo $resumen['entregados']; ?></strong>
            </div>
        </div>

        <div class="proyectos-resumen__item">
            <div class="proyectos-resumen__icon proyectos-resumen__icon--red">
                <i class="bi bi-pause-circle"></i>
            </div>

            <div>
                <p>Pausados / Cancelados</p>
                <strong><?php echo $resumen['pausados_cancelados']; ?></strong>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>