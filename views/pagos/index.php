<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="pagos">
    <div class="pagos__top">
        <div>
            <h1>Pagos</h1>

            <div class="pagos__breadcrumb">
                <a href="/dashboard">Inicio</a>
                <span>/</span>

                <?php if ($proyectoSeleccionado) : ?>
                    <a href="/proyectos">Proyectos</a>
                    <span>/</span>
                    <a href="/proyectos/detalle?id=<?php echo $proyectoSeleccionado->id; ?>">
                        <?php echo $proyectoSeleccionado->nombre; ?>
                    </a>
                    <span>/</span>
                    <p>Pagos</p>
                <?php else : ?>
                    <?php if (!empty($clienteSeleccionado)) : ?>
                        <div class="pagos__contexto">
                            <i class="bi bi-person"></i>

                            <div>
                                <p>Mostrando pagos del cliente</p>
                                <strong>
                                    <?php echo trim($clienteSeleccionado->nombre . ' ' . $clienteSeleccionado->apellido); ?>
                                    <?php echo $clienteSeleccionado->empresa ? ' - ' . $clienteSeleccionado->empresa : ''; ?>
                                </strong>
                            </div>

                            <a href="/pagos">Ver todos los pagos</a>
                        </div>
                    <?php endif; ?>
                    <p>Pagos</p>
                <?php endif; ?>
            </div>
        </div>

        <a
            href="<?php echo $proyectoSeleccionado ? '/pagos/crear?proyecto_id=' . $proyectoSeleccionado->id : '/pagos/crear'; ?>"
            class="pagos__nuevo">
            <i class="bi bi-plus-lg"></i>
            Nuevo pago
        </a>
    </div>

    <?php if ($proyectoSeleccionado) : ?>
        <div class="pagos__contexto">
            <i class="bi bi-folder"></i>

            <div>
                <p>Mostrando pagos del proyecto</p>
                <strong><?php echo $proyectoSeleccionado->nombre; ?></strong>
            </div>

            <a href="/pagos">Ver todos los pagos</a>
        </div>
    <?php endif; ?>

    <form id="form-filtros-pagos" class="pagos__filtros" method="GET" action="/pagos">
        <input type="hidden" name="page" value="1">

        <div class="pagos__busqueda">
            <i class="bi bi-search"></i>
            <input
                type="text"
                id="busqueda-pagos"
                name="busqueda"
                placeholder="Buscar pago, referencia o proyecto..."
                autocomplete="off"
                value="<?php echo $filtros['busqueda'] ?? ''; ?>">
        </div>

        <div class="pagos__select">
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


        <div class="pagos__select">
            <label for="cliente_id">Cliente</label>

            <select id="cliente_id" name="cliente_id">
                <option value="">Todos</option>

                <?php foreach ($clientes ?? [] as $cliente) : ?>
                    <?php
                    $nombreCliente = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));

                    if (!empty($cliente->empresa)) {
                        $nombreCliente .= $nombreCliente
                            ? ' - ' . $cliente->empresa
                            : $cliente->empresa;
                    }

                    $nombreCliente = $nombreCliente ?: 'Cliente sin nombre';
                    ?>

                    <option
                        value="<?php echo $cliente->id; ?>"
                        <?php echo (string) ($filtros['cliente_id'] ?? '') === (string) $cliente->id ? 'selected' : ''; ?>>
                        <?php echo $nombreCliente; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="pagos__select">
            <label for="estado">Estado</label>

            <select id="estado" name="estado">
                <option value="">Todos</option>
                <option value="Cobrado" <?php echo ($filtros['estado'] ?? '') === 'Cobrado' ? 'selected' : ''; ?>>Cobrado</option>
                <option value="Por confirmar" <?php echo ($filtros['estado'] ?? '') === 'Por confirmar' ? 'selected' : ''; ?>>Por confirmar</option>
                <option value="Anulado" <?php echo ($filtros['estado'] ?? '') === 'Anulado' ? 'selected' : ''; ?>>Anulado</option>
            </select>
        </div>

        <div class="pagos__select">
            <label for="metodo_pago">Método</label>

            <select id="metodo_pago" name="metodo_pago">
                <option value="">Todos</option>
                <option value="Efectivo" <?php echo ($filtros['metodo_pago'] ?? '') === 'Efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                <option value="Transferencia" <?php echo ($filtros['metodo_pago'] ?? '') === 'Transferencia' ? 'selected' : ''; ?>>Transferencia</option>
                <option value="Depósito" <?php echo ($filtros['metodo_pago'] ?? '') === 'Depósito' ? 'selected' : ''; ?>>Depósito</option>
                <option value="Tarjeta" <?php echo ($filtros['metodo_pago'] ?? '') === 'Tarjeta' ? 'selected' : ''; ?>>Tarjeta</option>
                <option value="Cheque" <?php echo ($filtros['metodo_pago'] ?? '') === 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
            </select>
        </div>
    </form>

    <?php
    $hayBusqueda = !empty($filtros['busqueda']);
    $hayProyecto = !empty($filtros['proyecto_id']);
    $hayCliente = !empty($filtros['cliente_id']);
    $hayEstado = !empty($filtros['estado']);
    $hayMetodo = !empty($filtros['metodo_pago']);

    $hayFiltros = $hayBusqueda || $hayProyecto || $hayCliente || $hayEstado || $hayMetodo;

    $nombreProyectoFiltro = '';
    $nombreClienteFiltro = '';

    if ($hayProyecto && !empty($proyectos)) {
        foreach ($proyectos as $proyecto) {
            if ((string) $proyecto->id === (string) $filtros['proyecto_id']) {
                $nombreProyectoFiltro = $proyecto->nombre;
                break;
            }
        }
    }

    if ($hayCliente && !empty($clientes)) {
        foreach ($clientes as $cliente) {
            if ((string) $cliente->id === (string) $filtros['cliente_id']) {
                $nombreClienteFiltro = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));

                if (!empty($cliente->empresa)) {
                    $nombreClienteFiltro .= $nombreClienteFiltro
                        ? ' - ' . $cliente->empresa
                        : $cliente->empresa;
                }

                $nombreClienteFiltro = $nombreClienteFiltro ?: 'Cliente seleccionado';
                break;
            }
        }
    }
    ?>

    <?php if ($hayFiltros) : ?>
        <?php if (!empty($pagos)) : ?>
            <div class="pagos-filtro-alerta pagos-filtro-alerta--success">
                <i class="bi bi-check-circle"></i>

                <p>
                    Mostrando pagos filtrados
                    <?php if ($hayBusqueda) : ?>
                        por búsqueda <strong>"<?php echo $filtros['busqueda']; ?>"</strong>
                    <?php endif; ?>

                    <?php if ($hayProyecto && $nombreProyectoFiltro) : ?>
                        del proyecto <strong><?php echo $nombreProyectoFiltro; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayCliente && $nombreClienteFiltro) : ?>
                        del cliente <strong><?php echo $nombreClienteFiltro; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayEstado) : ?>
                        con estado <strong><?php echo $filtros['estado']; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayMetodo) : ?>
                        con método <strong><?php echo $filtros['metodo_pago']; ?></strong>
                        <?php endif; ?>.
                </p>

                <a href="/pagos">Limpiar filtros</a>
            </div>
        <?php else : ?>
            <div class="pagos-filtro-alerta pagos-filtro-alerta--empty">
                <i class="bi bi-info-circle"></i>

                <p>No se encontraron pagos con los filtros seleccionados.</p>

                <a href="/pagos">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="pagos__card">
        <?php if (empty($pagos)) : ?>
            <div class="pagos__empty">
                <div class="pagos__empty-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>

                <h2>No tienes pagos registrados</h2>

                <p>
                    Cuando registres pagos, aparecerán en esta sección con su proyecto,
                    cliente, método, estado y valores.
                </p>

                <a
                    href="<?php echo $proyectoSeleccionado ? '/pagos/crear?proyecto_id=' . $proyectoSeleccionado->id : '/pagos/crear'; ?>"
                    class="pagos__empty-button">
                    <i class="bi bi-plus-lg"></i>
                    Registrar primer pago
                </a>
            </div>
        <?php else : ?>
            <div class="pagos-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Factura / Referencia</th>
                            <th>Proyecto</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Vencimiento</th>
                            <th>Monto</th>
                            <th>Pagado</th>
                            <th>Estado</th>
                            <th>Método</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($pagos as $pago) : ?>
                            <?php
                            $clienteNombre = trim(($pago->cliente_nombre ?? '') . ' ' . ($pago->cliente_apellido ?? ''));
                            $clienteNombre = $clienteNombre ?: ($pago->cliente_empresa ?? 'Sin cliente');

                            $fechaPago = $pago->fecha_pago ? formatearFechaGlobal($pago->fecha_pago) : 'Sin fecha';
                            $vencimiento = $pago->fecha_vencimiento ? formatearFechaGlobal($pago->fecha_vencimiento) : 'Sin vencimiento';

                            $estadoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $pago->estado));
                            $metodoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $pago->metodo_pago));
                            ?>

                            <tr>
                                <td><?php echo $pago->id; ?></td>

                                <td>
                                    <div class="pago-referencia">
                                        <a href="/pagos/detalle?id=<?php echo $pago->id; ?>">
                                            <?php echo $pago->referencia ?: 'Sin factura registrada'; ?>
                                        </a>

                                        <p>
                                            <?php echo $pago->descripcion ?: 'Pago registrado'; ?>
                                        </p>
                                    </div>
                                </td>

                                <td>
                                    <a class="pago-proyecto" href="/proyectos/detalle?id=<?php echo $pago->proyecto_id; ?>">
                                        <?php echo $pago->proyecto_nombre ?? 'Sin proyecto'; ?>
                                    </a>
                                </td>

                                <td><?php echo $clienteNombre; ?></td>

                                <td><?php echo $fechaPago; ?></td>

                                <td><?php echo $vencimiento; ?></td>

                                <td>
                                    <strong>$<?php echo number_format((float) $pago->monto_total, 2); ?></strong>
                                </td>

                                <td>
                                    <strong class="pagos-tabla__pagado">
                                        $<?php echo number_format((float) $pago->monto_pagado, 2); ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="pago-badge pago-badge--<?php echo $estadoClase; ?>">
                                        <?php echo $pago->estado; ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="pago-metodo pago-metodo--<?php echo $metodoClase; ?>">
                                        <?php if ($pago->metodo_pago === 'Transferencia') : ?>
                                            <i class="bi bi-bank"></i>
                                        <?php elseif ($pago->metodo_pago === 'Efectivo') : ?>
                                            <i class="bi bi-cash"></i>
                                        <?php elseif ($pago->metodo_pago === 'Tarjeta') : ?>
                                            <i class="bi bi-credit-card"></i>
                                        <?php elseif ($pago->metodo_pago === 'PayPal') : ?>
                                            <i class="bi bi-paypal"></i>
                                        <?php else : ?>
                                            <i class="bi bi-question-circle"></i>
                                        <?php endif; ?>

                                        <?php echo $pago->metodo_pago ?: 'Por definir'; ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="pagos-tabla__acciones">
                                        <a href="/pagos/detalle?id=<?php echo $pago->id; ?>" class="accion accion--ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagos__footer-tabla">
                <p>
                    Mostrando
                    <?php echo $paginacion->inicio(); ?>
                    a
                    <?php echo $paginacion->fin(); ?>
                    de
                    <?php echo $paginacion->totalRegistros; ?>
                    pagos
                </p>

                <?php echo $paginacion->selectorPorPagina('form-filtros-pagos'); ?>

                <?php if ($paginacion->totalPaginas() > 1) : ?>
                    <div class="pagos__paginacion">

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
                                <span class="pagos__paginacion-activo">
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

    <div class="pagos-resumen">
        <div class="pagos-resumen__item">
            <div class="pagos-resumen__icon pagos-resumen__icon--green">
                <i class="bi bi-currency-dollar"></i>
            </div>

            <div>
                <p>Total facturado</p>
                <strong>$<?php echo number_format($resumen['total_facturado'], 2); ?></strong>
            </div>
        </div>

        <div class="pagos-resumen__item">
            <div class="pagos-resumen__icon pagos-resumen__icon--blue">
                <i class="bi bi-credit-card"></i>
            </div>

            <div>
                <p>Total recibido</p>
                <strong>$<?php echo number_format($resumen['total_recibido'], 2); ?></strong>
            </div>
        </div>

        <div class="pagos-resumen__item">
            <div class="pagos-resumen__icon pagos-resumen__icon--orange">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <p>Pendiente</p>
                <strong>$<?php echo number_format($resumen['pendiente'], 2); ?></strong>
            </div>
        </div>

        <div class="pagos-resumen__item">
            <div class="pagos-resumen__icon pagos-resumen__icon--red">
                <i class="bi bi-exclamation-circle"></i>
            </div>

            <div>
                <p>Vencido</p>
                <strong>$<?php echo number_format($resumen['vencido'], 2); ?></strong>
            </div>
        </div>

        <div class="pagos-resumen__item">
            <div class="pagos-resumen__icon pagos-resumen__icon--purple">
                <i class="bi bi-pie-chart-fill"></i>
            </div>

            <div>
                <p>Por cobrar</p>
                <strong>$<?php echo number_format($resumen['por_cobrar'], 2); ?></strong>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>