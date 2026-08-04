<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<?php
$nombreCompleto = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
$nombreMostrar = $nombreCompleto ?: 'Cliente sin nombre';

$iniciales = strtoupper(
    substr($cliente->nombre ?? 'C', 0, 1) .
        substr($cliente->apellido ?? '', 0, 1)
);

if (strlen($iniciales) < 2 && !empty($cliente->empresa)) {
    $iniciales = strtoupper(substr($cliente->empresa, 0, 2));
}

$empresa = $cliente->empresa ?: 'Sin empresa registrada';
$telefono = $cliente->telefono ?: 'Sin teléfono';
$email = $cliente->email ?: 'Sin correo';
$identificacion = $cliente->identificacion ?: 'Sin identificación';
$direccion = $cliente->direccion ?: 'Sin dirección';
$ciudad = $cliente->ciudad ?: 'Sin ciudad';
$tipoCliente = $cliente->tipo_cliente ?: 'Sin tipo';
$fuenteContacto = $cliente->fuente_contacto ?: 'Sin fuente';
$observaciones = $cliente->observaciones ?: 'Sin observaciones registradas.';
?>

<section class="cliente-detalle">
    <div class="cliente-detalle__breadcrumb">
        <a href="/clientes">Clientes</a>
        <span>/</span>
        <p>Detalle</p>
    </div>

    <div class="cliente-detalle__header">
        <h1>Detalle del cliente</h1>
        <p>Consulta la información completa y el resumen de actividad del cliente.</p>
    </div>

    <div class="cliente-detalle__grid">
        <article class="cliente-perfil">
            <div class="cliente-perfil__top">
                <div class="cliente-perfil__avatar">
                    <?php echo $iniciales; ?>
                </div>

                <div class="cliente-perfil__titulo">
                    <h2><?php echo $nombreMostrar; ?></h2>
                    <p><?php echo $empresa; ?></p>

                    <?php if ((int) $cliente->estado === 1) : ?>
                        <span class="badge badge--activo">
                            <i class="bi bi-circle-fill"></i>
                            Activo
                        </span>
                    <?php else : ?>
                        <span class="badge badge--inactivo">
                            <i class="bi bi-circle-fill"></i>
                            Inactivo
                        </span>
                    <?php endif; ?>
                </div>

                <div class="cliente-perfil__acciones">
                    <a href="/clientes/editar?id=<?php echo $cliente->id; ?>" class="cliente-perfil__btn cliente-perfil__btn--secundario">
                        <i class="bi bi-pencil"></i>
                        Editar cliente
                    </a>

                    <a href="/proyectos/crear?cliente_id=<?php echo $cliente->id; ?>" class="cliente-perfil__btn cliente-perfil__btn--primario">
                        <i class="bi bi-plus-lg"></i>
                        Nuevo proyecto
                    </a>
                </div>
            </div>

            <div class="cliente-perfil__separador"></div>

            <div class="cliente-perfil__datos">
                <div class="cliente-perfil__dato">
                    <i class="bi bi-telephone"></i>
                    <div>
                        <span>Teléfono</span>
                        <p><?php echo $telefono; ?></p>
                    </div>
                </div>

                <div class="cliente-perfil__dato">
                    <i class="bi bi-envelope"></i>
                    <div>
                        <span>Correo</span>
                        <p><?php echo $email; ?></p>
                    </div>
                </div>

                <div class="cliente-perfil__dato">
                    <i class="bi bi-card-text"></i>
                    <div>
                        <span>Identificación</span>
                        <p><?php echo $identificacion; ?></p>
                    </div>
                </div>

                <div class="cliente-perfil__dato">
                    <i class="bi bi-buildings"></i>
                    <div>
                        <span>Ciudad</span>
                        <p><?php echo $ciudad; ?></p>
                    </div>
                </div>

                <div class="cliente-perfil__dato">
                    <i class="bi bi-geo-alt"></i>
                    <div>
                        <span>Dirección</span>
                        <p><?php echo $direccion; ?></p>
                    </div>
                </div>

                <div class="cliente-perfil__dato">
                    <i class="bi bi-person"></i>
                    <div>
                        <span>Tipo de cliente</span>
                        <p><?php echo $tipoCliente; ?></p>
                    </div>
                </div>
            </div>

            <div class="cliente-perfil__separador"></div>

            <div class="cliente-perfil__extra">
                <div class="cliente-perfil__dato">
                    <i class="bi bi-chat-dots"></i>
                    <div>
                        <span>Fuente de contacto</span>
                        <p><?php echo $fuenteContacto; ?></p>
                    </div>
                </div>

                <div class="cliente-perfil__dato">
                    <i class="bi bi-card-list"></i>
                    <div>
                        <span>Observaciones</span>
                        <p><?php echo $observaciones; ?></p>
                    </div>
                </div>
            </div>
        </article>

        <aside class="cliente-resumen">
            <div class="cliente-resumen__item">
                <div class="cliente-resumen__icon cliente-resumen__icon--blue">
                    <i class="bi bi-folder"></i>
                </div>

                <p>Proyectos asociados</p>
                <strong><?php echo $resumen['proyectos_asociados']; ?></strong>
            </div>

            <div class="cliente-resumen__item">
                <div class="cliente-resumen__icon cliente-resumen__icon--green">
                    <i class="bi bi-pie-chart"></i>
                </div>

                <p>Proyectos activos</p>
                <strong><?php echo $resumen['proyectos_activos']; ?></strong>
            </div>

            <div class="cliente-resumen__item">
                <div class="cliente-resumen__icon cliente-resumen__icon--purple">
                    <i class="bi bi-check-circle"></i>
                </div>

                <p>Proyectos entregados</p>
                <strong><?php echo $resumen['proyectos_entregados']; ?></strong>
            </div>

            <div class="cliente-resumen__item">
                <div class="cliente-resumen__icon cliente-resumen__icon--violet">
                    <i class="bi bi-currency-dollar"></i>
                </div>

                <p>Saldo pendiente</p>
                <strong>$<?php echo number_format($resumen['saldo_pendiente'], 2); ?></strong>
            </div>

            <div class="cliente-resumen__item">
                <div class="cliente-resumen__icon cliente-resumen__icon--blue">
                    <i class="bi bi-calendar-week"></i>
                </div>

                <p>Total facturado</p>
                <strong>$<?php echo number_format($resumen['total_facturado'], 2); ?></strong>
            </div>

            <div class="cliente-resumen__item">
                <div class="cliente-resumen__icon cliente-resumen__icon--orange">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <p>Último pago</p>
                <strong><?php echo $resumen['ultimo_pago']; ?></strong>
            </div>
        </aside>
    </div>

    <div class="cliente-detalle__paneles">
        <section class="detalle-card detalle-card--proyectos">
            <div class="detalle-card__header">
                <h2>
                    <i class="bi bi-folder"></i>
                    Proyectos recientes
                </h2>

                <a href="/proyectos?cliente_id=<?php echo $cliente->id; ?>">
                    Ver todos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($proyectos)) : ?>
                <div class="detalle-card__empty">
                    <p>Este cliente todavía no tiene proyectos registrados.</p>
                    <a href="/proyectos/crear?cliente_id=<?php echo $cliente->id; ?>">
                        Crear primer proyecto
                    </a>
                </div>
            <?php else : ?>
                <div class="detalle-tabla">
                    <table>
                        <thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>Estado</th>
                                <th>Fecha entrega</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($proyectos as $proyecto) : ?>
                                <tr>
                                    <td>
                                        <a href="/proyectos/detalle?id=<?php echo $proyecto->proyecto_id; ?>">
                                            <?php echo $proyecto->proyecto_nombre; ?>
                                        </a>
                                    </td>

                                    <td>
                                        <?php echo $proyecto->proyecto_estado ?: 'Pendiente'; ?>
                                    </td>

                                    <td>
                                        <?php echo !empty($proyecto->proyecto_fecha_entrega) ? date('d/m/Y', strtotime($proyecto->proyecto_fecha_entrega)) : 'Sin fecha'; ?>
                                    </td>

                                    <td>
                                        <strong>
                                            $<?php echo number_format((float) ($proyecto->proyecto_valor_total ?? 0), 2); ?>
                                        </strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section class="detalle-card detalle-card--pagos">
            <div class="detalle-card__header">
                <h2>
                    <i class="bi bi-currency-dollar"></i>
                    Últimos pagos
                </h2>

                <a href="/pagos?cliente_id=<?php echo $cliente->id; ?>">
                    Ver todos los pagos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($pagos)) : ?>
                <div class="detalle-card__empty">
                    <p>Este cliente todavía no tiene pagos registrados.</p>
                    <a href="/pagos/crear?cliente_id=<?php echo $cliente->id; ?>">
                        Registrar primer pago
                    </a>
                </div>
            <?php else : ?>
                <div class="detalle-tabla">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($pagos as $pago) : ?>
                                <tr>
                                    <td>
                                        <?php echo !empty($pago->pago_fecha) ? date('d/m/Y', strtotime($pago->pago_fecha)) : 'Sin fecha'; ?>
                                    </td>

                                    <td>
                                        <?php echo $pago->pago_metodo ?: 'Sin método'; ?>
                                    </td>

                                    <td>
                                        <strong>
                                            $<?php echo number_format((float) ($pago->pago_monto ?? 0), 2); ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?php echo $pago->pago_estado ?: 'Sin estado'; ?>
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
                Notas recientes
            </h2>

            <div class="detalle-card__acciones">
                <a href="/notas/crear?cliente_id=<?php echo $cliente->id; ?>">
                    <i class="bi bi-plus-lg"></i>
                    Crear nota
                </a>

                <?php if (($notasDesbloqueadas ?? false) && ($totalNotasCliente ?? 0) > 0) : ?>
                    <a href="/notas?cliente_id=<?php echo $cliente->id; ?>">
                        Ver todas las notas
                        <i class="bi bi-arrow-right"></i>
                    </a>
                <?php elseif (($totalNotasCliente ?? 0) > 0) : ?>
                    <button
                        type="button"
                        class="detalle-card__link js-desbloquear-notas"
                        data-cliente-id="<?php echo $cliente->id; ?>"
                        data-redireccion="/clientes/detalle?id=<?php echo $cliente->id; ?>">
                        Desbloquear notas
                        <i class="bi bi-lock"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (($totalNotasCliente ?? 0) === 0) : ?>

            <div class="detalle-card__empty detalle-card__empty--inline">
                <p>No hay notas registradas para este cliente.</p>
                <a href="/notas/crear?cliente_id=<?php echo $cliente->id; ?>">Crear primera nota</a>
            </div>

        <?php elseif (!($notasDesbloqueadas ?? false)) : ?>

            <div class="detalle-notas-lock">
                <div class="detalle-notas-lock__icon">
                    <i class="bi bi-lock-fill"></i>
                </div>

                <div>
                    <h3>Este cliente tiene notas protegidas</h3>
                    <p>
                        Hay <?php echo $totalNotasCliente; ?> nota<?php echo (int) $totalNotasCliente === 1 ? '' : 's'; ?> registrada<?php echo (int) $totalNotasCliente === 1 ? '' : 's'; ?> para este cliente.
                        Desbloquea las notas para poder visualizarlas.
                    </p>
                </div>

                <button
                    type="button"
                    class="detalle-notas-lock__button js-desbloquear-notas"
                    data-cliente-id="<?php echo $cliente->id; ?>"
                    data-redireccion="/clientes/detalle?id=<?php echo $cliente->id; ?>">
                    <i class="bi bi-unlock"></i>
                    Desbloquear notas
                </button>
            </div>

        <?php elseif (empty($notas)) : ?>

            <div class="detalle-card__empty detalle-card__empty--inline">
                <p>Las notas están desbloqueadas, pero no se pudieron cargar en esta vista.</p>
                <a href="/notas?cliente_id=<?php echo $cliente->id; ?>">Ver notas en el módulo de notas</a>
            </div>

        <?php else : ?>

            <ul class="detalle-notas">
                <?php foreach ($notas as $nota) : ?>
                    <?php
                    $fechaNota = !empty($nota->nota_creado)
                        ? date('d/m/Y', strtotime($nota->nota_creado))
                        : 'Sin fecha';

                    $tituloNota = $nota->nota_titulo ?? 'Sin título';

                    $proyectoNota = !empty($nota->nota_proyecto_nombre)
                        ? $nota->nota_proyecto_nombre
                        : 'Nota general';
                    ?>

                    <li>
                        <span><?php echo $fechaNota; ?></span>

                        <p>
                            <strong>
                                <a href="/notas/detalle?id=<?php echo $nota->nota_id; ?>">
                                    <?php echo $tituloNota; ?>
                                </a>
                            </strong>

                            <small>Proyecto: <?php echo $proyectoNota; ?></small>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>