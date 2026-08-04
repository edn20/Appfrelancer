<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="notificaciones-page">
    <div class="notificaciones-page__header">
        <div>
            <p class="notificaciones-page__eyebrow">Centro de alertas</p>
            <h1>Notificaciones</h1>
            <p>Revisa tareas próximas a vencer, pagos vencidos y proyectos atrasados.</p>
        </div>
    </div>

    <div class="notificaciones-resumen">
        <div class="notificaciones-resumen__card">
            <span>Total</span>
            <strong><?php echo $resumen['total'] ?? 0; ?></strong>
        </div>

        <div class="notificaciones-resumen__card">
            <span>Tareas</span>
            <strong><?php echo $resumen['tareas'] ?? 0; ?></strong>
        </div>

        <div class="notificaciones-resumen__card">
            <span>Pagos</span>
            <strong><?php echo $resumen['pagos'] ?? 0; ?></strong>
        </div>

        <div class="notificaciones-resumen__card">
            <span>Proyectos</span>
            <strong><?php echo $resumen['proyectos'] ?? 0; ?></strong>
        </div>
    </div>

    <div class="notificaciones-layout">
        <div class="notificaciones-listado">
            <?php if (!empty($notificaciones)) : ?>
                <?php foreach ($notificaciones as $notificacion) : ?>
                    <article class="notificacion-card notificacion-card--<?php echo $notificacion->nivel; ?>">
                        <div class="notificacion-card__icono">
                            <i class="bi <?php echo $notificacion->icono; ?>"></i>
                        </div>

                        <div class="notificacion-card__contenido">
                            <div class="notificacion-card__top">
                                <h3><?php echo $notificacion->titulo; ?></h3>
                                <p><?php echo $notificacion->mensaje; ?></p>
                            </div>

                            <div class="notificacion-card__meta">
                                <span>
                                    <i class="bi bi-calendar3"></i>
                                    <?php echo date('d/m/Y', strtotime($notificacion->fecha)); ?>
                                </span>

                                <span>
                                    <i class="bi bi-tag"></i>
                                    <?php echo ucfirst($notificacion->tipo); ?>
                                </span>
                            </div>
                        </div>

                        <div class="notificacion-card__acciones">
                            <a href="<?php echo $notificacion->url; ?>">
                                Ver y gestionar
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="notificaciones-empty">
                    <div class="notificaciones-empty__icono">
                        <i class="bi bi-bell-slash"></i>
                    </div>

                    <h2>No tienes notificaciones pendientes</h2>
                    <p>Cuando existan tareas por vencer, pagos vencidos o proyectos atrasados, aparecerán aquí.</p>
                </div>
            <?php endif; ?>
        </div>

        <aside class="notificaciones-panel">
            <h2>Accesos rápidos</h2>
            <p>Gestiona directamente las áreas donde se originan las alertas.</p>

            <div class="notificaciones-panel__lista">
                <a href="/tareas?alerta=vencidas">
                    Tareas vencidas / próximas
                    <span><?php echo $resumen['tareas'] ?? 0; ?></span>
                </a>

                <a href="/pagos?alerta=vencidos">
                    Pagos vencidos / próximos
                    <span><?php echo $resumen['pagos'] ?? 0; ?></span>
                </a>

                <a href="/proyectos?alerta=atrasados">
                    Proyectos atrasados
                    <span><?php echo $resumen['proyectos_atrasados'] ?? 0; ?></span>
                </a>

                <a href="/proyectos?alerta=proximos">
                    Próximos a entregar
                    <span><?php echo $resumen['proyectos_proximos'] ?? 0; ?></span>
                </a>

                <a href="/configuracion#notificaciones">
                    Preferencias
                    <i class="bi bi-sliders"></i>
                </a>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>