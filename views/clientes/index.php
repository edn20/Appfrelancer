<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="clientes">
    <div class="clientes__header">
        <div>
            <h1>Clientes</h1>
            <p>Gestiona la información de tus clientes</p>
        </div>

        <a href="/clientes/crear" class="clientes__nuevo">
            <i class="bi bi-plus-lg"></i>
            <span>Nuevo cliente</span>
        </a>
    </div>

    <form id="form-filtros-clientes" class="clientes__filtros" method="GET" action="/clientes">
        <input type="hidden" name="page" value="1">

        <div class="clientes__busqueda">
            <i class="bi bi-search"></i>

            <input
                type="text"
                id="busqueda-clientes"
                name="busqueda"
                placeholder="Buscar cliente..."
                autocomplete="off"
                value="<?php echo $filtros['busqueda'] ?? ''; ?>">
        </div>

        <div class="clientes__select">
            <label for="estado">Estado:</label>

            <select id="estado" name="estado">
                <option value="">Todos</option>
                <option value="1" <?php echo ($filtros['estado'] ?? '') === '1' ? 'selected' : ''; ?>>Activos</option>
                <option value="0" <?php echo ($filtros['estado'] ?? '') === '0' ? 'selected' : ''; ?>>Inactivos</option>
            </select>
        </div>

        <div class="clientes__select">
            <label for="tipo_cliente">Tipo:</label>

            <select id="tipo_cliente" name="tipo_cliente">
                <option value="">Todos</option>
                <option value="Recurrente" <?php echo ($filtros['tipo_cliente'] ?? '') === 'Recurrente' ? 'selected' : ''; ?>>Recurrente</option>
                <option value="Ocasional" <?php echo ($filtros['tipo_cliente'] ?? '') === 'Ocasional' ? 'selected' : ''; ?>>Ocasional</option>
                <option value="Prospecto" <?php echo ($filtros['tipo_cliente'] ?? '') === 'Prospecto' ? 'selected' : ''; ?>>Prospecto</option>
            </select>
        </div>
    </form>

    <div class="clientes__card">

        <?php
        $hayBusqueda = !empty($filtros['busqueda']);
        $hayEstado = ($filtros['estado'] ?? '') !== '';
        $hayTipo = !empty($filtros['tipo_cliente']);
        $hayFiltros = $hayBusqueda || $hayEstado || $hayTipo;

        $textoEstado = '';

        if ($hayEstado) {
            $textoEstado = ($filtros['estado'] ?? '') === '1'
                ? 'clientes activos'
                : 'clientes inactivos';
        }

        $textoTipo = '';

        if ($hayTipo) {
            $textoTipo = strtolower($filtros['tipo_cliente']);
        }
        ?>

        <?php
        $hayBusqueda = !empty($filtros['busqueda']);
        $hayEstado = isset($filtros['estado']) && $filtros['estado'] !== '';
        $hayTipo = !empty($filtros['tipo_cliente']);
        $hayFiltros = $hayBusqueda || $hayEstado || $hayTipo;

        $textoEstado = '';

        if ($hayEstado) {
            $textoEstado = $filtros['estado'] === '1'
                ? 'clientes activos'
                : 'clientes inactivos';
        }

        $textoTipo = '';

        if ($hayTipo) {
            $textoTipo = strtolower($filtros['tipo_cliente']);
        }
        ?>

        <?php if ($hayFiltros) : ?>
            <?php if (!empty($clientes)) : ?>
                <div class="clientes-filtro-alerta clientes-filtro-alerta--success">
                    <i class="bi bi-check-circle"></i>

                    <p>
                        Mostrando

                        <?php if ($hayEstado) : ?>
                            <strong><?php echo $textoEstado; ?></strong>
                        <?php endif; ?>

                        <?php if ($hayTipo) : ?>
                            <?php echo $hayEstado ? ' de tipo ' : 'clientes de tipo '; ?>
                            <strong><?php echo $textoTipo; ?></strong>
                        <?php endif; ?>

                        <?php if ($hayBusqueda) : ?>
                            <?php echo ($hayEstado || $hayTipo) ? ' que coinciden con ' : 'resultados para '; ?>
                            <strong>"<?php echo $filtros['busqueda']; ?>"</strong>
                            <?php endif; ?>.
                    </p>

                    <a href="/clientes">Limpiar filtros</a>
                </div>
            <?php else : ?>
                <div class="clientes-filtro-alerta clientes-filtro-alerta--empty">
                    <i class="bi bi-info-circle"></i>

                    <p>
                        <?php if ($hayEstado && !$hayTipo && !$hayBusqueda) : ?>
                            No hay <?php echo $textoEstado; ?> registrados.
                        <?php elseif ($hayTipo && !$hayEstado && !$hayBusqueda) : ?>
                            No hay clientes de tipo <strong><?php echo $textoTipo; ?></strong> registrados.
                        <?php elseif ($hayEstado && $hayTipo && !$hayBusqueda) : ?>
                            No hay <?php echo $textoEstado; ?> de tipo <strong><?php echo $textoTipo; ?></strong>.
                        <?php elseif ($hayBusqueda) : ?>
                            No se encontraron clientes que coincidan con <strong>"<?php echo $filtros['busqueda']; ?>"</strong>.
                        <?php else : ?>
                            No se encontraron clientes con los filtros seleccionados.
                        <?php endif; ?>
                    </p>

                    <a href="/clientes">Limpiar filtros</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (empty($clientes)) : ?>
            <div class="clientes__empty">
                <div class="clientes__empty-icon">
                    <i class="bi bi-people"></i>
                </div>

                <h2>
                    <?php echo $hayFiltros ? 'No hay resultados para los filtros seleccionados' : 'No tienes clientes registrados'; ?>
                </h2>

                <p>
                    <?php if ($hayFiltros) : ?>
                        Cambia los filtros o limpia la búsqueda para ver nuevamente todos tus clientes.
                    <?php else : ?>
                        Cuando registres tu primer cliente, aparecerá en esta sección.
                        Desde aquí podrás ver su información, proyectos, tareas y pagos asociados.
                    <?php endif; ?>
                </p>

                <?php if ($hayFiltros) : ?>
                    <a href="/clientes" class="clientes__empty-button">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Limpiar filtros
                    </a>
                <?php else : ?>
                    <a href="/clientes/crear" class="clientes__empty-button">
                        <i class="bi bi-plus-lg"></i>
                        Registrar primer cliente
                    </a>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="tabla-responsive">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Empresa</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th>Proyectos</th>
                            <th>Saldo pendiente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($clientes as $cliente) : ?>
                            <tr>
                                <td><?php echo str_pad($cliente->id, 3, '0', STR_PAD_LEFT); ?></td>

                                <td>
                                    <div class="cliente-info">
                                        <div class="cliente-info__avatar">
                                            <?php echo strtoupper(substr($cliente->nombre, 0, 1) . substr($cliente->apellido ?? '', 0, 1)); ?>
                                        </div>

                                        <div>
                                            <p><?php echo $cliente->nombre . ' ' . $cliente->apellido; ?></p>
                                            <span><?php echo $cliente->tipo_cliente ?: 'Sin tipo'; ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td><?php echo $cliente->empresa ?: 'Sin empresa'; ?></td>
                                <td><?php echo $cliente->telefono ?: 'Sin teléfono'; ?></td>
                                <td><?php echo $cliente->email ?: 'Sin correo'; ?></td>

                                <td>
                                    <?php if ($cliente->estado == 1) : ?>
                                        <span class="badge badge--activo">Activo</span>
                                    <?php else : ?>
                                        <span class="badge badge--inactivo">Inactivo</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo $cliente->total_proyectos ?? 0; ?>
                                </td>
                                <td class="tabla__monto"><strong class="clientes-tabla__saldo">
                                        $<?php echo number_format((float) ($cliente->saldo_pendiente ?? 0), 2); ?>
                                    </strong></td>

                                <td>
                                    <div class="tabla__acciones">
                                        <a href="/clientes/detalle?id=<?php echo $cliente->id; ?>" class="accion accion--ver">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="/clientes/editar?id=<?php echo $cliente->id; ?>" class="accion accion--editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form method="POST" action="/clientes/eliminar" class="js-eliminar-cliente">
                                            <input type="hidden" name="id" value="<?php echo $cliente->id; ?>">
                                            <input type="hidden" name="nombre" value="<?php echo $cliente->nombre . ' ' . $cliente->apellido; ?>">

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
            <div class="clientes__footer-tabla">
                <p>
                    Mostrando
                    <?php echo $paginacion->inicio(); ?>
                    a
                    <?php echo $paginacion->fin(); ?>
                    de
                    <?php echo $paginacion->totalRegistros; ?>
                    clientes
                </p>

                <?php echo $paginacion->selectorPorPagina('form-filtros-clientes'); ?>

                <?php if ($paginacion->totalPaginas() > 1) : ?>
                    <div class="clientes__paginacion">

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
                                <span class="clientes__paginacion-activo">
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
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>