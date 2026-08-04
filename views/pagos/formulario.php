<div class="form-pago__grid">
    <div class="form-pago__campo">
        <label for="proyecto_id">Proyecto <span>*</span></label>

        <?php if ($proyectoSeleccionado) : ?>
            <input
                type="text"
                value="<?php echo $proyectoSeleccionado->nombre; ?>"
                readonly>

            <input
                type="hidden"
                id="proyecto_id"
                name="proyecto_id"
                value="<?php echo $proyectoSeleccionado->id; ?>"
                data-monto-total="<?php echo $montoTotalProyecto ?? 0; ?>"
                data-total-pagado="<?php echo $totalPagadoProyecto ?? 0; ?>"
                data-saldo-pendiente="<?php echo $saldoPendienteActual ?? 0; ?>">
        <?php else : ?>
            <select id="proyecto_id" name="proyecto_id">
                <option value="">Selecciona un proyecto</option>

                <?php foreach ($proyectos as $proyecto) : ?>
                    <?php
                    $clienteNombre = trim(($proyecto->cliente_nombre ?? '') . ' ' . ($proyecto->cliente_apellido ?? ''));
                    $clienteNombre = $clienteNombre ?: ($proyecto->cliente_empresa ?? '');

                    $montoTotal = (float) ($proyecto->valor_total ?? 0);
                    $totalPagado = \Model\Pago::totalPagadoPorProyecto($proyecto->id);
                    $saldoPendiente = max($montoTotal - $totalPagado, 0);
                    ?>

                    <option
                        value="<?php echo $proyecto->id; ?>"
                        data-monto-total="<?php echo $montoTotal; ?>"
                        data-total-pagado="<?php echo $totalPagado; ?>"
                        data-saldo-pendiente="<?php echo $saldoPendiente; ?>"
                        <?php echo (int) ($pago->proyecto_id ?? 0) === (int) $proyecto->id ? 'selected' : ''; ?>>
                        <?php echo $proyecto->nombre; ?>
                        <?php echo $clienteNombre ? ' - ' . $clienteNombre : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>

    <div class="form-pago__campo">
        <label for="metodo_pago">Método de pago <span>*</span></label>

        <select id="metodo_pago" name="metodo_pago">
            <option value="">Selecciona un método</option>
            <option value="Transferencia" <?php echo ($pago->metodo_pago ?? '') === 'Transferencia' ? 'selected' : ''; ?>>Transferencia</option>
            <option value="Efectivo" <?php echo ($pago->metodo_pago ?? '') === 'Efectivo' ? 'selected' : ''; ?>>Efectivo</option>
            <option value="Depósito" <?php echo ($pago->metodo_pago ?? '') === 'Depósito' ? 'selected' : ''; ?>>Depósito</option>
            <option value="Tarjeta" <?php echo ($pago->metodo_pago ?? '') === 'Tarjeta' ? 'selected' : ''; ?>>Tarjeta</option>
            <option value="PayPal" <?php echo ($pago->metodo_pago ?? '') === 'PayPal' ? 'selected' : ''; ?>>PayPal</option>
            <option value="Otro" <?php echo ($pago->metodo_pago ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
        </select>
    </div>

    <div class="form-pago__campo">
        <label for="estado">Estado <span>*</span></label>

        <select id="estado" name="estado">
            <option value="Cobrado" <?php echo ($pago->estado ?? 'Cobrado') === 'Cobrado' ? 'selected' : ''; ?>>Cobrado</option>
            <option value="Por confirmar" <?php echo ($pago->estado ?? '') === 'Por confirmar' ? 'selected' : ''; ?>>Por confirmar</option>
            <option value="Anulado" <?php echo ($pago->estado ?? '') === 'Anulado' ? 'selected' : ''; ?>>Anulado</option>
        </select>
    </div>

    <div class="form-pago__campo">
        <label for="fecha_pago">Fecha de pago</label>

        <input
            type="date"
            id="fecha_pago"
            name="fecha_pago"
            value="<?php echo $pago->fecha_pago ?? ''; ?>">
    </div>

    <div class="form-pago__campo">
        <label for="fecha_vencimiento">Vencimiento</label>

        <input
            type="date"
            id="fecha_vencimiento"
            name="fecha_vencimiento"
            value="<?php echo $pago->fecha_vencimiento ?? ''; ?>">
    </div>

    <div class="form-pago__campo">
        <label for="referencia">Factura / Referencia </label>

        <input
            type="text"
            id="referencia"
            name="referencia"
            placeholder="Ej: FAC-2026-001 o deja vacío si aún no hay factura"
            value="<?php echo ($pago->referencia ?? '') === 'Sin factura registrada' ? '' : ($pago->referencia ?? ''); ?>">
    </div>

    <div class="form-pago__campo">
        <label for="monto_total">Monto total del proyecto <span>*</span></label>

        <div class="form-pago__money">
            <span>$</span>
            <input
                type="number"
                step="0.01"
                min="0"
                id="monto_total"
                name="monto_total"
                placeholder="0.00"
                value="<?php echo $montoTotalProyecto ?? $pago->monto_total ?? 0; ?>"
                readonly>
        </div>
    </div>

    <div class="form-pago__campo">
        <label for="saldo_pendiente_actual">Saldo pendiente actual</label>

        <div class="form-pago__money">
            <span>$</span>
            <input
                type="number"
                step="0.01"
                min="0"
                id="saldo_pendiente_actual"
                value="<?php echo $saldoPendienteActual ?? 0; ?>"
                readonly>
        </div>
    </div>

    <div class="form-pago__campo">
        <label for="monto_pagado">Monto pagado <span>*</span></label>

        <div class="form-pago__money">
            <span>$</span>
            <input
                type="number"
                step="0.01"
                min="0"
                id="monto_pagado"
                name="monto_pagado"
                placeholder="0.00"
                value="<?php echo $pago->monto_pagado ?? ''; ?>">
        </div>
    </div>

    <div class="form-pago__resumen">
        <h3>Resumen del pago</h3>

        <div>
            <p>Monto total del proyecto:</p>
            <strong id="resumenMontoTotal">$0.00</strong>
        </div>

        <div>
            <p>Saldo pendiente actual:</p>
            <strong id="resumenSaldoActual">$0.00</strong>
        </div>

        <div>
            <p>Monto pagado ahora:</p>
            <strong id="resumenMontoPagado">$0.00</strong>
        </div>

        <div class="form-pago__resumen-saldo">
            <p>Saldo después de este pago:</p>
            <strong id="resumenSaldo">$0.00</strong>
        </div>
    </div>
</div>

<div class="form-pago__campo form-pago__campo--full">
    <label for="descripcion">Descripción (opcional)</label>

    <textarea
        id="descripcion"
        name="descripcion"
        placeholder="Describe el concepto del pago..."><?php echo $pago->descripcion ?? ''; ?></textarea>
</div>