<div class="form-proyecto__grid">
    <div class="form-proyecto__campo">
        <label for="nombre">Nombre del proyecto <span>*</span></label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            placeholder="Ingresa el nombre del proyecto"
            value="<?php echo $proyecto->nombre ?? ''; ?>">
    </div>

    <div class="form-proyecto__campo">
        <label for="cliente_id">Cliente <span>*</span></label>

        <?php if ($clienteSeleccionado) : ?>
            <input
                type="text"
                value="<?php echo trim($clienteSeleccionado->nombre . ' ' . $clienteSeleccionado->apellido); ?>"
                readonly>

            <input
                type="hidden"
                name="cliente_id"
                value="<?php echo $clienteSeleccionado->id; ?>">
        <?php else : ?>
            <select id="cliente_id" name="cliente_id">
                <option value="">Selecciona un cliente</option>

                <?php foreach ($clientes as $cliente) : ?>
                    <option
                        value="<?php echo $cliente->id; ?>"
                        <?php echo (int) ($proyecto->cliente_id ?? 0) === (int) $cliente->id ? 'selected' : ''; ?>>
                        <?php echo trim($cliente->nombre . ' ' . $cliente->apellido); ?>
                        <?php echo $cliente->empresa ? ' - ' . $cliente->empresa : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>

    <div class="form-proyecto__campo">
        <label for="fecha_inicio">Fecha de inicio</label>
        <input
            type="date"
            id="fecha_inicio"
            name="fecha_inicio"
            value="<?php echo $proyecto->fecha_inicio ?? ''; ?>"
            required
            >
            
    </div>

    <div class="form-proyecto__campo">
        <label for="fecha_entrega">Fecha de entrega estimada</label>
        <input
            type="date"
            id="fecha_entrega"
            name="fecha_entrega"
            value="<?php echo $proyecto->fecha_entrega ?? ''; ?>"
            required
            >
    </div>

    <div class="form-proyecto__campo">
        <label for="valor_total">Valor total</label>
        <input
            type="number"
            step="0.01"
            min="0"
            id="valor_total"
            name="valor_total"
            placeholder="Ej. 1200.00"
            value="<?php echo $proyecto->valor_total ?? ''; ?>">
    </div>

    <div class="form-proyecto__campo">
        <label for="prioridad">Prioridad</label>
        <select id="prioridad" name="prioridad">
            <option value="">Selecciona la prioridad</option>
            <option value="Baja" <?php echo ($proyecto->prioridad ?? '') === 'Baja' ? 'selected' : ''; ?>>Baja</option>
            <option value="Media" <?php echo ($proyecto->prioridad ?? '') === 'Media' ? 'selected' : ''; ?>>Media</option>
            <option value="Alta" <?php echo ($proyecto->prioridad ?? '') === 'Alta' ? 'selected' : ''; ?>>Alta</option>
            <option value="Urgente" <?php echo ($proyecto->prioridad ?? '') === 'Urgente' ? 'selected' : ''; ?>>Urgente</option>
        </select>
    </div>

    <div class="form-proyecto__campo">
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="Pendiente" <?php echo ($proyecto->estado ?? 'Pendiente') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
            <option value="En proceso" <?php echo ($proyecto->estado ?? '') === 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
            <option value="En revisión" <?php echo ($proyecto->estado ?? '') === 'En revisión' ? 'selected' : ''; ?>>En revisión</option>
            <option value="Entregado" <?php echo ($proyecto->estado ?? '') === 'Entregado' ? 'selected' : ''; ?>>Entregado</option>
            <option value="Pausado" <?php echo ($proyecto->estado ?? '') === 'Pausado' ? 'selected' : ''; ?>>Pausado</option>
            <option value="Cancelado" <?php echo ($proyecto->estado ?? '') === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
        </select>
    </div>

    <div class="form-proyecto__campo">
        <label for="tipo_cobro">Tipo de cobro</label>
        <select id="tipo_cobro" name="tipo_cobro">
            <option value="">Selecciona el tipo de cobro</option>
            <option value="Fijo" <?php echo ($proyecto->tipo_cobro ?? '') === 'Fijo' ? 'selected' : ''; ?>>Fijo</option>
            <option value="Por horas" <?php echo ($proyecto->tipo_cobro ?? '') === 'Por horas' ? 'selected' : ''; ?>>Por horas</option>
            <option value="Mensual" <?php echo ($proyecto->tipo_cobro ?? '') === 'Mensual' ? 'selected' : ''; ?>>Mensual</option>
            <option value="Por avance" <?php echo ($proyecto->tipo_cobro ?? '') === 'Por avance' ? 'selected' : ''; ?>>Por avance</option>
        </select>
    </div>
</div>

<div class="form-proyecto__campo form-proyecto__campo--full">
    <label for="descripcion">Descripción del proyecto</label>
    <textarea
        id="descripcion"
        name="descripcion"
        placeholder="Describe el proyecto en detalle"><?php echo $proyecto->descripcion ?? ''; ?></textarea>
</div>

<div class="form-proyecto__campo form-proyecto__campo--full">
    <label for="objetivos">Objetivos o alcance</label>
    <textarea
        id="objetivos"
        name="objetivos"
        placeholder="Define los objetivos y el alcance del proyecto"><?php echo $proyecto->objetivos ?? ''; ?></textarea>
</div>

<div class="form-proyecto__campo form-proyecto__campo--full">
    <label for="observaciones">Observaciones</label>
    <textarea
        id="observaciones"
        name="observaciones"
        placeholder="Agrega cualquier observación adicional (opcional)"><?php echo $proyecto->observaciones ?? ''; ?></textarea>
</div>