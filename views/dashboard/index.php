<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="dashboard">
    <div class="dashboard__header">
        <h1>Dashboard</h1>
        <p>Resumen general de tu trabajo</p>
    </div>

    <div class="dashboard-cards">
        <div class="dashboard-card">
            <div class="dashboard-card__icon dashboard-card__icon--blue">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <p>Clientes activos</p>
                <strong><?php echo $resumenGeneral['clientes_activos'] ?? 0; ?></strong>
            </div>

            <a href="/clientes">
                Ver detalle
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card__icon dashboard-card__icon--blue">
                <i class="bi bi-folder"></i>
            </div>

            <div>
                <p>Proyectos en proceso</p>
                <strong><?php echo $resumenGeneral['proyectos_en_proceso'] ?? 0; ?></strong>
            </div>

            <a href="/proyectos?estado=En+proceso">
                Ver detalle
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card__icon dashboard-card__icon--green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <p>Proyectos completados</p>
                <strong><?php echo $resumenGeneral['proyectos_completados'] ?? 0; ?></strong>
            </div>

            <a href="/proyectos?estado=Entregado">
                Ver detalle
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card__icon dashboard-card__icon--orange">
                <i class="bi bi-list-check"></i>
            </div>

            <div>
                <p>Tareas pendientes</p>
                <strong><?php echo $resumenGeneral['tareas_pendientes'] ?? 0; ?></strong>
            </div>

            <a href="/tareas?estado=Pendiente">
                Ver detalle
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-card">
            <div class="dashboard-card__icon dashboard-card__icon--red">
                <i class="bi bi-calendar-x"></i>
            </div>

            <div>
                <p>Tareas vencidas</p>
                <strong><?php echo $resumenGeneral['tareas_vencidas'] ?? 0; ?></strong>
            </div>

            <a href="/tareas?vencidas=1">
                Ver detalle
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-panel dashboard-panel--finanzas">
            <div class="dashboard-panel__header">
                <h2>Resumen financiero</h2>

                <div class="dashboard-panel__icon dashboard-panel__icon--blue">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>

            <div class="dashboard-finanzas">
                <div class="dashboard-finanzas__item">
                    <div>
                        <p>Total cobrado este mes</p>
                        <strong>$<?php echo number_format((float) ($resumenFinanciero['cobrado_mes'] ?? 0), 2); ?></strong>
                    </div>

                    <span class="dashboard-finanzas__icon dashboard-finanzas__icon--blue">
                        <i class="bi bi-download"></i>
                    </span>
                </div>

                <div class="dashboard-finanzas__item">
                    <div>
                        <p>Total por cobrar</p>
                        <strong>$<?php echo number_format((float) ($resumenFinanciero['por_cobrar'] ?? 0), 2); ?></strong>
                    </div>

                    <span class="dashboard-finanzas__icon dashboard-finanzas__icon--orange">
                        <i class="bi bi-hourglass-split"></i>
                    </span>
                </div>

                <div class="dashboard-finanzas__item">
                    <div>
                        <p>Total de ingresos</p>
                        <strong>$<?php echo number_format((float) ($resumenFinanciero['total_ingresos'] ?? 0), 2); ?></strong>
                    </div>

                    <span class="dashboard-finanzas__icon dashboard-finanzas__icon--green">
                        <i class="bi bi-graph-up-arrow"></i>
                    </span>
                </div>
            </div>

            <a href="/reportes?seccion=financiero" class="dashboard-link">
                Ver reporte financiero
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-panel dashboard-panel--estados">
            <div class="dashboard-panel__header">
                <h2>Proyectos por estado</h2>

                <div class="dashboard-panel__icon dashboard-panel__icon--blue">
                    <i class="bi bi-pie-chart"></i>
                </div>
            </div>

            <?php
            $proyectosPorEstado = $proyectosPorEstado ?? [
                'Pendiente' => 0,
                'En proceso' => 0,
                'En revisión' => 0,
                'Entregado' => 0,
                'Pausado' => 0,
                'Cancelado' => 0
            ];

            $enProceso = (int) ($proyectosPorEstado['En proceso'] ?? 0);
            $enRevision = (int) ($proyectosPorEstado['En revisión'] ?? 0);
            $entregado = (int) ($proyectosPorEstado['Entregado'] ?? 0);
            $pausado = (int) ($proyectosPorEstado['Pausado'] ?? 0);
            $cancelado = (int) ($proyectosPorEstado['Cancelado'] ?? 0);

            $totalEstados = array_sum($proyectosPorEstado);

            $p1 = $totalEstados > 0 ? round(($enProceso / $totalEstados) * 100, 2) : 0;
            $p2 = $totalEstados > 0 ? round(($enRevision / $totalEstados) * 100, 2) : 0;
            $p3 = $totalEstados > 0 ? round(($entregado / $totalEstados) * 100, 2) : 0;
            $p4 = $totalEstados > 0 ? round(($pausado / $totalEstados) * 100, 2) : 0;

            $c1 = $p1;
            $c2 = $p1 + $p2;
            $c3 = $p1 + $p2 + $p3;
            $c4 = $p1 + $p2 + $p3 + $p4;

            $grafico = "conic-gradient(
                    #2563eb 0 {$c1}%,
                    #f59e0b {$c1}% {$c2}%,
                    #22c55e {$c2}% {$c3}%,
                    #8b5cf6 {$c3}% {$c4}%,
                    #94a3b8 {$c4}% 100%
                )";
            ?>

            <div class="dashboard-estados">
                <div class="dashboard-estados__chart" style="background: <?php echo $grafico; ?>;">
                    <span><?php echo $totalEstados; ?></span>
                </div>

                <div class="dashboard-estados__legend">
                    <div>
                        <span class="estado-color estado-color--blue"></span>
                        <p>En proceso</p>
                        <strong><?php echo $enProceso; ?></strong>
                    </div>

                    <div>
                        <span class="estado-color estado-color--orange"></span>
                        <p>En revisión</p>
                        <strong><?php echo $enRevision; ?></strong>
                    </div>

                    <div>
                        <span class="estado-color estado-color--green"></span>
                        <p>Entregado</p>
                        <strong><?php echo $entregado; ?></strong>
                    </div>

                    <div>
                        <span class="estado-color estado-color--purple"></span>
                        <p>Pausado</p>
                        <strong><?php echo $pausado; ?></strong>
                    </div>

                    <div>
                        <span class="estado-color estado-color--gray"></span>
                        <p>Cancelado</p>
                        <strong><?php echo $cancelado; ?></strong>
                    </div>
                </div>
            </div>

            <a href="/reportes?seccion=proyectos" class="dashboard-link">
                Ver todos los proyectos
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-panel dashboard-panel--clientes-mes">
            <div class="dashboard-panel__header">
                <div>
                    <h2>Clientes por mes</h2>
                    <p class="dashboard-panel__subtitulo">Clientes registrados durante el año.</p>
                </div>

                <form method="GET" action="/dashboard" class="dashboard-year">
                    <label for="anio_clientes">Año</label>

                    <select id="anio_clientes" name="anio_clientes" onchange="this.form.submit()">
                        <?php foreach ($aniosClientes as $anio) : ?>
                            <option
                                value="<?php echo $anio; ?>"
                                <?php echo (int) $anioClientes === (int) $anio ? 'selected' : ''; ?>>
                                <?php echo $anio; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php
            $totalClientesAnio = 0;

            foreach ($clientesPorMes as $mes) {
                $totalClientesAnio += (int) $mes['total'];
            }

            $coloresMeses = [
                '#2563eb',
                '#f59e0b',
                '#22c55e',
                '#8b5cf6',
                '#ef4444',
                '#06b6d4',
                '#84cc16',
                '#ec4899',
                '#14b8a6',
                '#f97316',
                '#64748b',
                '#0f172a'
            ];

            $inicio = 0;
            $partesGrafico = [];

            foreach ($clientesPorMes as $indice => $mes) {
                $totalMes = (int) $mes['total'];
                $porcentaje = $totalClientesAnio > 0 ? ($totalMes / $totalClientesAnio) * 100 : 0;
                $fin = $inicio + $porcentaje;
                $color = $coloresMeses[$indice - 1];

                if ($porcentaje > 0) {
                    $partesGrafico[] = "{$color} {$inicio}% {$fin}%";
                }

                $inicio = $fin;
            }

            $graficoClientes = !empty($partesGrafico)
                ? 'conic-gradient(' . implode(', ', $partesGrafico) . ')'
                : 'conic-gradient(#e2e8f0 0% 100%)';
            ?>

            <div class="dashboard-clientes-mes">
                <div class="dashboard-clientes-mes__chart" style="background: <?php echo $graficoClientes; ?>;">
                    <span><?php echo $totalClientesAnio; ?></span>
                </div>

                <div class="dashboard-clientes-mes__legend">
                    <?php foreach ($clientesPorMes as $indice => $mes) : ?>
                        <div>
                            <span style="background: <?php echo $coloresMeses[$indice - 1]; ?>;"></span>
                            <p><?php echo $mes['corto']; ?></p>
                            <strong><?php echo $mes['total']; ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="/clientes" class="dashboard-link">
                Ver todos los clientes
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-panel dashboard-panel--entregas">
            <div class="dashboard-panel__header">
                <h2>Próximas entregas</h2>

                <div class="dashboard-panel__icon dashboard-panel__icon--blue">
                    <i class="bi bi-calendar"></i>
                </div>
            </div>

            <?php if (empty($proximasEntregas)) : ?>
                <div class="dashboard-empty">
                    <i class="bi bi-calendar-check"></i>
                    <p>No hay entregas próximas registradas.</p>
                </div>
            <?php else : ?>
                <div class="dashboard-entregas">
                    <?php foreach ($proximasEntregas as $entrega) : ?>
                        <?php
                        $fecha = !empty($entrega->fecha_entrega)
                            ? date('d M Y', strtotime($entrega->fecha_entrega))
                            : 'Sin fecha';

                        $cliente = trim(($entrega->cliente_nombre ?? '') . ' ' . ($entrega->cliente_apellido ?? ''));
                        $cliente = $cliente ?: ($entrega->cliente_empresa ?? 'Sin cliente');
                        ?>

                        <a href="/proyectos/detalle?id=<?php echo $entrega->proyecto_id; ?>" class="dashboard-entrega">
                            <span>
                                <i class="bi bi-calendar-event"></i>
                            </span>

                            <div>
                                <strong><?php echo $entrega->proyecto_nombre; ?></strong>
                                <p><?php echo $fecha; ?> · <?php echo $cliente; ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a href="/proyectos" class="dashboard-link">
                Ver todas las entregas
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>