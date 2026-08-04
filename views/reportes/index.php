<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="reportes">
    <div class="reportes__top">
        <div>
            <h1>Reportes</h1>
            <p>Analiza tus clientes, proyectos y movimientos financieros.</p>
        </div>
    </div>

    <div class="reportes-tabs">
        <a href="/reportes?seccion=general" class="<?php echo $seccion === 'general' ? 'activo' : ''; ?>">
            <i class="bi bi-grid"></i>
            General
        </a>

        <a href="/reportes?seccion=financiero" class="<?php echo $seccion === 'financiero' ? 'activo' : ''; ?>">
            <i class="bi bi-currency-dollar"></i>
            Financiero
        </a>

        <a href="/reportes?seccion=clientes" class="<?php echo $seccion === 'clientes' ? 'activo' : ''; ?>">
            <i class="bi bi-people"></i>
            Clientes
        </a>

        <a href="/reportes?seccion=proyectos" class="<?php echo $seccion === 'proyectos' ? 'activo' : ''; ?>">
            <i class="bi bi-folder"></i>
            Proyectos
        </a>
    </div>

    <?php if ($seccion === 'general') : ?>
        <div class="reportes-grid reportes-grid--cards">
            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--blue">
                    <i class="bi bi-people"></i>
                </span>

                <div>
                    <p>Clientes registrados</p>
                    <strong><?php echo $resumenGeneral['total_clientes']; ?></strong>
                </div>
            </div>

            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--blue">
                    <i class="bi bi-folder"></i>
                </span>

                <div>
                    <p>Proyectos registrados</p>
                    <strong><?php echo $resumenGeneral['total_proyectos']; ?></strong>
                </div>
            </div>

            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--orange">
                    <i class="bi bi-list-check"></i>
                </span>

                <div>
                    <p>Tareas registradas</p>
                    <strong><?php echo $resumenGeneral['total_tareas']; ?></strong>
                </div>
            </div>

            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--green">
                    <i class="bi bi-cash-coin"></i>
                </span>

                <div>
                    <p>Pagos registrados</p>
                    <strong><?php echo $resumenGeneral['total_pagos']; ?></strong>
                </div>
            </div>
        </div>

        <div class="reportes-panel">
            <div class="reportes-panel__header">
                <h2>Resumen financiero</h2>
                <a href="/reportes?seccion=financiero">Ver reporte financiero</a>
            </div>

            <div class="reportes-finanzas">
                <div>
                    <p>Total facturado</p>
                    <strong>$<?php echo number_format($reporteFinanciero['total_facturado'], 2); ?></strong>
                </div>

                <div>
                    <p>Total cobrado</p>
                    <strong>$<?php echo number_format($reporteFinanciero['total_cobrado'], 2); ?></strong>
                </div>

                <div>
                    <p>Por confirmar</p>
                    <strong>$<?php echo number_format($reporteFinanciero['total_por_confirmar'], 2); ?></strong>
                </div>

                <div>
                    <p>Saldo pendiente</p>
                    <strong>$<?php echo number_format($reporteFinanciero['saldo_pendiente'], 2); ?></strong>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($seccion === 'financiero') : ?>
        <div class="reportes-filtro-periodo">
            <div>
                <h2>Reporte financiero</h2>
                <p>Analiza ingresos cobrados y pendientes por periodo.</p>
            </div>

            <form method="GET" action="/reportes">
                <input type="hidden" name="seccion" value="financiero">

                <label for="meses">Periodo</label>

                <select id="meses" name="meses" onchange="this.form.submit()">
                    <option value="3" <?php echo (int) $mesesFinanciero === 3 ? 'selected' : ''; ?>>Últimos 3 meses</option>
                    <option value="6" <?php echo (int) $mesesFinanciero === 6 ? 'selected' : ''; ?>>Últimos 6 meses</option>
                    <option value="12" <?php echo (int) $mesesFinanciero === 12 ? 'selected' : ''; ?>>Últimos 12 meses</option>
                    <option value="24" <?php echo (int) $mesesFinanciero === 24 ? 'selected' : ''; ?>>Últimos 24 meses</option>
                </select>
            </form>
        </div>

        <div class="reportes-grid reportes-grid--cards">
            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--green">
                    <i class="bi bi-wallet2"></i>
                </span>

                <div>
                    <p>Total cobrado periodo</p>
                    <strong>$<?php echo number_format($analisisIngresos['total_periodo'], 2); ?></strong>
                </div>
            </div>

            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--blue">
                    <i class="bi bi-graph-up-arrow"></i>
                </span>

                <div>
                    <p>Promedio mensual</p>
                    <strong>$<?php echo number_format($analisisIngresos['promedio_mensual'], 2); ?></strong>
                </div>
            </div>

            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--orange">
                    <i class="bi bi-calendar-check"></i>
                </span>

                <div>
                    <p>Meses con ingresos</p>
                    <strong><?php echo $analisisIngresos['meses_con_ingresos']; ?> / <?php echo count($ingresosMensuales); ?></strong>
                </div>
            </div>

            <div class="reporte-card">
                <span class="reporte-card__icon reporte-card__icon--red">
                    <i class="bi bi-hourglass-split"></i>
                </span>

                <div>
                    <p>Por confirmar</p>
                    <strong>$<?php echo number_format($reporteFinanciero['total_por_confirmar'], 2); ?></strong>
                </div>
            </div>
        </div>

        <div class="reportes-financiero-layout">
            <div class="reportes-panel reportes-panel--wide">
                <div class="reportes-panel__header">
                    <h2>Ingresos mensuales</h2>
                    <span>Periodo: últimos <?php echo $mesesFinanciero; ?> meses</span>
                </div>

                <div class="reporte-lineas">
                    <?php foreach ($ingresosMensuales as $mes) : ?>
                        <?php
                        $totalMes = (float) $mes['total_cobrado'];
                        $porcentaje = $analisisIngresos['maximo_grafico'] > 0
                            ? ($totalMes / $analisisIngresos['maximo_grafico']) * 100
                            : 0;

                        $porcentaje = max($porcentaje, $totalMes > 0 ? 6 : 1);
                        ?>

                        <div class="reporte-linea">
                            <div class="reporte-linea__mes">
                                <?php echo $mes['nombre_mes']; ?>
                            </div>

                            <div class="reporte-linea__barra">
                                <span style="width: <?php echo $porcentaje; ?>%;"></span>
                            </div>

                            <div class="reporte-linea__valor">
                                $<?php echo number_format($totalMes, 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="reportes-panel reportes-panel--insights">
                <div class="reportes-panel__header">
                    <h2>Lectura del periodo</h2>
                </div>

                <div class="reporte-insights">
                    <div class="reporte-insight reporte-insight--green">
                        <span>
                            <i class="bi bi-arrow-up-right"></i>
                        </span>

                        <div>
                            <p>Mayor ingreso</p>

                            <?php if (!empty($analisisIngresos['mayor_ingreso'])) : ?>
                                <strong><?php echo $analisisIngresos['mayor_ingreso']['nombre_mes']; ?></strong>
                                <small>$<?php echo number_format($analisisIngresos['mayor_ingreso']['total_cobrado'], 2); ?></small>
                            <?php else : ?>
                                <strong>Sin datos</strong>
                                <small>$0.00</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="reporte-insight reporte-insight--orange">
                        <span>
                            <i class="bi bi-arrow-down-right"></i>
                        </span>

                        <div>
                            <p>Menor ingreso</p>

                            <?php if (!empty($analisisIngresos['menor_ingreso'])) : ?>
                                <strong><?php echo $analisisIngresos['menor_ingreso']['nombre_mes']; ?></strong>
                                <small>$<?php echo number_format($analisisIngresos['menor_ingreso']['total_cobrado'], 2); ?></small>
                            <?php else : ?>
                                <strong>Sin ingresos</strong>
                                <small>$0.00</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="reporte-insight reporte-insight--blue">
                        <span>
                            <i class="bi bi-cash-stack"></i>
                        </span>

                        <div>
                            <p>Saldo pendiente</p>
                            <strong>$<?php echo number_format($reporteFinanciero['saldo_pendiente'], 2); ?></strong>
                            <small>Total por cobrar</small>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <div class="reportes-panel">
            <div class="reportes-panel__header">
                <h2>Detalle mensual</h2>
                <a href="/pagos">Ver pagos detallados</a>
            </div>

            <div class="reportes-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>Mes</th>
                            <th>Pagos registrados</th>
                            <th>Cobrado</th>
                            <th>Por confirmar</th>
                            <th>Total movimiento</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($ingresosMensuales as $mes) : ?>
                            <tr>
                                <td><strong><?php echo $mes['nombre_mes']; ?></strong></td>
                                <td><?php echo $mes['total_pagos']; ?></td>
                                <td>$<?php echo number_format($mes['total_cobrado'], 2); ?></td>
                                <td>$<?php echo number_format($mes['total_por_confirmar'], 2); ?></td>
                                <td>$<?php echo number_format($mes['total_cobrado'] + $mes['total_por_confirmar'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($seccion === 'clientes') : ?>
        <div class="reportes-panel">
            <div class="reportes-panel__header">
                <h2>Top clientes por facturación</h2>
                <a href="/clientes">Ver clientes</a>
            </div>

            <div class="reportes-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Empresa</th>
                            <th>Tipo</th>
                            <th>Proyectos</th>
                            <th>Facturado</th>
                            <th>Cobrado</th>
                            <th>Pendiente</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($reporteClientes as $cliente) : ?>
                            <?php
                            $nombreCliente = trim(($cliente->cliente_nombre ?? '') . ' ' . ($cliente->cliente_apellido ?? ''));
                            $nombreCliente = $nombreCliente ?: 'Sin nombre';
                            ?>

                            <tr>
                                <td>
                                    <strong><?php echo $nombreCliente; ?></strong>
                                </td>
                                <td><?php echo $cliente->cliente_empresa ?: 'Sin empresa'; ?></td>
                                <td><?php echo $cliente->tipo_cliente ?: 'Sin tipo'; ?></td>
                                <td><?php echo $cliente->total_proyectos; ?></td>
                                <td>$<?php echo number_format((float) $cliente->total_facturado, 2); ?></td>
                                <td>$<?php echo number_format((float) $cliente->total_cobrado, 2); ?></td>
                                <td>$<?php echo number_format((float) $cliente->saldo_pendiente, 2); ?></td>
                                <td>
                                    <a href="/clientes/detalle?id=<?php echo $cliente->cliente_id; ?>" class="reportes-accion">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($seccion === 'proyectos') : ?>
        <div class="reportes-layout">
            <div class="reportes-panel">
                <div class="reportes-panel__header">
                    <h2>Proyectos por estado</h2>
                    <a href="/proyectos">Ver proyectos</a>
                </div>

                <div class="reportes-estados">
                    <?php foreach ($proyectosPorEstado as $estado => $total) : ?>
                        <a href="/proyectos?estado=<?php echo urlencode($estado); ?>">
                            <span><?php echo $estado; ?></span>
                            <strong><?php echo $total; ?></strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="reportes-panel">
                <div class="reportes-panel__header">
                    <h2>Últimos proyectos registrados</h2>
                </div>

                <div class="reportes-tabla">
                    <table>
                        <thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Entrega</th>
                                <th>Valor</th>
                                <th>Cobrado</th>
                                <th>Pendiente</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($reporteProyectos as $proyecto) : ?>
                                <?php
                                $clienteNombre = trim(($proyecto->cliente_nombre ?? '') . ' ' . ($proyecto->cliente_apellido ?? ''));
                                $clienteNombre = $clienteNombre ?: ($proyecto->cliente_empresa ?? 'Sin cliente');

                                $fechaEntrega = !empty($proyecto->fecha_entrega)
                                    ? formatearFechaGlobal($proyecto->fecha_entrega)
                                    : 'Sin fecha';
                                ?>

                                <tr>
                                    <td>
                                        <strong><?php echo $proyecto->proyecto_nombre; ?></strong>
                                    </td>
                                    <td><?php echo $clienteNombre; ?></td>
                                    <td><?php echo $proyecto->proyecto_estado; ?></td>
                                    <td><?php echo $proyecto->proyecto_prioridad ?: 'Sin prioridad'; ?></td>
                                    <td><?php echo $fechaEntrega; ?></td>
                                    <td>$<?php echo number_format((float) $proyecto->valor_total, 2); ?></td>
                                    <td>$<?php echo number_format((float) $proyecto->total_cobrado, 2); ?></td>
                                    <td>$<?php echo number_format((float) $proyecto->saldo_pendiente, 2); ?></td>
                                    <td>
                                        <a href="/proyectos/detalle?id=<?php echo $proyecto->proyecto_id; ?>" class="reportes-accion">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>