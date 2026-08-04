<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<?php
$clienteNombre = trim(($pago->cliente_nombre ?? '') . ' ' . ($pago->cliente_apellido ?? ''));
$clienteNombre = $clienteNombre ?: ($pago->cliente_empresa ?? 'Cliente no registrado');

$fechaPago = $pago->fecha_pago ? date('d/m/Y', strtotime($pago->fecha_pago)) : 'Sin fecha';
$vencimiento = $pago->fecha_vencimiento ? date('d/m/Y', strtotime($pago->fecha_vencimiento)) : 'Sin vencimiento';

$estadoClase = strtolower(str_replace([' ', 'ó'], ['-', 'o'], $pago->estado));
$saldoDespues = $saldoDisponible;

if ($pago->estado === 'Cobrado') {
    $saldoDespues = max((float) $saldoDisponible - (float) $pago->monto_pagado, 0);
}
?>

<section class="pago-detalle">
    <div class="pago-detalle__volver">
        <a href="/pagos<?php echo $pago->proyecto_id ? '?proyecto_id=' . $pago->proyecto_id : ''; ?>">
            <i class="bi bi-arrow-left"></i>
            Volver a pagos
        </a>
    </div>

    <div class="pago-detalle__top">
        <div>
            <h1><?php echo $pago->referencia ?: 'Sin factura registrada'; ?></h1>

            <div class="pago-detalle__breadcrumb">
                <a href="/dashboard">Inicio</a>
                <span>/</span>
                <a href="/pagos">Pagos</a>
                <span>/</span>
                <p>Detalle</p>
            </div>
        </div>

        <span class="pago-badge pago-badge--<?php echo $estadoClase; ?>">
            <?php echo $pago->estado; ?>
        </span>
    </div>

    <?php if (!empty($error)) : ?>
        <div class="pago-detalle__alerta pago-detalle__alerta--error">
            <i class="bi bi-exclamation-triangle"></i>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($exito)) : ?>
        <div class="pago-detalle__alerta pago-detalle__alerta--exito">
            <i class="bi bi-check-circle"></i>
            <p><?php echo $exito; ?></p>
        </div>
    <?php endif; ?>

    <div class="pago-detalle__grid">
        <div class="pago-detalle__principal">
            <section class="pago-card">
                <h2>Información del pago</h2>

                <div class="pago-info-grid">
                    <div class="pago-info-grid__item">
                        <span>Proyecto</span>
                        <p>
                            <i class="bi bi-folder"></i>
                            <a href="/proyectos/detalle?id=<?php echo $pago->proyecto_id; ?>">
                                <?php echo $pago->proyecto_nombre; ?>
                            </a>
                        </p>
                    </div>

                    <div class="pago-info-grid__item">
                        <span>Cliente</span>
                        <p>
                            <i class="bi bi-person"></i>
                            <?php echo $clienteNombre; ?>
                        </p>
                    </div>

                    <div class="pago-info-grid__item">
                        <span>Método</span>
                        <p>
                            <i class="bi bi-credit-card"></i>
                            <?php echo $pago->metodo_pago ?: 'Por definir'; ?>
                        </p>
                    </div>

                    <div class="pago-info-grid__item">
                        <span>Fecha de pago</span>
                        <p>
                            <i class="bi bi-calendar"></i>
                            <?php echo $fechaPago; ?>
                        </p>
                    </div>

                    <div class="pago-info-grid__item">
                        <span>Vencimiento</span>
                        <p>
                            <i class="bi bi-calendar-event"></i>
                            <?php echo $vencimiento; ?>
                        </p>
                    </div>

                    <div class="pago-info-grid__item">
                        <span>Referencia</span>
                        <p>
                            <i class="bi bi-receipt"></i>
                            <?php echo $pago->referencia ?: 'Sin factura registrada'; ?>
                        </p>
                    </div>
                </div>
            </section>

            <section class="pago-card">
                <h2>Actualizar pago</h2>

                <form id="form-actualizar-pago" class="pago-update" method="POST" action="/pagos/actualizar">
                    <input type="hidden" name="id" value="<?php echo $pago->id; ?>">

                    <div class="pago-update__grid">
                        <div class="pago-update__campo">
                            <label for="monto_pagado">Valor cobrado</label>

                            <div class="pago-update__money">
                                <span>$</span>
                                <input
                                    type="number"
                                    id="monto_pagado"
                                    name="monto_pagado"
                                    step="0.01"
                                    min="0"
                                    max="<?php echo $saldoDisponible; ?>"
                                    value="<?php echo $pago->monto_pagado; ?>">
                            </div>

                            <small>Disponible máximo: $<?php echo number_format($saldoDisponible, 2); ?></small>
                        </div>

                        <div class="pago-update__campo">
                            <label for="estado">Estado</label>

                            <select id="estado" name="estado">
                                <option value="Cobrado" <?php echo $pago->estado === 'Cobrado' ? 'selected' : ''; ?>>Cobrado</option>
                                <option value="Por confirmar" <?php echo $pago->estado === 'Por confirmar' ? 'selected' : ''; ?>>Por confirmar</option>
                                <option value="Anulado" <?php echo $pago->estado === 'Anulado' ? 'selected' : ''; ?>>Anulado</option>
                            </select>
                        </div>
                    </div>

                    <div class="pago-update__campo pago-update__campo--full">
                        <label for="notas_internas">Notas internas</label>

                        <textarea
                            id="notas_internas"
                            name="notas_internas"
                            placeholder="Agrega una observación sobre la modificación del pago..."><?php echo $pago->notas_internas ?? ''; ?></textarea>
                    </div>

                    <button type="button" id="btnActualizarPago" class="pago-update__submit">
                        <i class="bi bi-shield-lock"></i>
                        Actualizar pago
                    </button>
                </form>
            </section>

            <section class="pago-card">
                <h2>Descripción</h2>
                <p class="pago-card__texto">
                    <?php echo $pago->descripcion ?: 'Sin descripción registrada.'; ?>
                </p>
            </section>
        </div>

        <aside class="pago-detalle__lateral">
            <section class="pago-card">
                <h2>Resumen</h2>

                <div class="pago-resumen-mini">
                    <div>
                        <span>Monto total</span>
                        <strong>$<?php echo number_format((float) $pago->monto_total, 2); ?></strong>
                    </div>

                    <div>
                        <span>Valor cobrado</span>
                        <strong>$<?php echo number_format((float) $pago->monto_pagado, 2); ?></strong>
                    </div>

                    <div>
                        <span>Saldo disponible antes de este pago</span>
                        <strong>$<?php echo number_format((float) $saldoDisponible, 2); ?></strong>
                    </div>

                    <div>
                        <span>Saldo después de este pago</span>
                        <strong>$<?php echo number_format((float) $saldoDespues, 2); ?></strong>
                    </div>
                </div>
            </section>

            <section class="pago-card">
                <div class="pago-card__header">
                    <h2>Comprobantes</h2>
                    <span><?php echo count($adjuntos); ?></span>
                </div>

                <?php if (empty($adjuntos)) : ?>
                    <div class="pago-adjuntos-empty">
                        <i class="bi bi-paperclip"></i>
                        <p>Este pago no tiene comprobantes adjuntos.</p>
                    </div>
                <?php else : ?>
                    <div class="pago-adjuntos">
                        <?php foreach ($adjuntos as $adjunto) : ?>
                            <div class="pago-adjuntos__item">
                                <div class="pago-adjuntos__icon">
                                    <i class="bi bi-file-earmark-check"></i>
                                </div>

                                <div class="pago-adjuntos__info">
                                    <a href="/pagos/adjunto/descargar?id=<?php echo $adjunto->id; ?>">
                                        <?php echo $adjunto->nombre_original; ?>
                                    </a>

                                    <span>
                                        <?php echo number_format(($adjunto->peso ?? 0) / 1024, 1); ?> KB
                                    </span>
                                </div>

                                <a class="pago-adjuntos__download" href="/pagos/adjunto/descargar?id=<?php echo $adjunto->id; ?>">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>