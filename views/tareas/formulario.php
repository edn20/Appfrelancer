<div class="form-tarea__grid">
    <div class="form-tarea__campo">
        <label for="nombre">Nombre de la tarea <span>*</span></label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            placeholder="Ingresa el nombre de la tarea"
            value="<?php echo $tarea->nombre ?? ''; ?>">
    </div>

    <div class="form-tarea__campo">
        <label for="proyecto_id">Proyecto <span>*</span></label>

        <?php if ($proyectoSeleccionado) : ?>
            <input
                type="text"
                value="<?php echo $proyectoSeleccionado->nombre; ?>"
                readonly>

            <input
                type="hidden"
                name="proyecto_id"
                value="<?php echo $proyectoSeleccionado->id; ?>">
        <?php else : ?>
            <select id="proyecto_id" name="proyecto_id">
                <option value="">Selecciona un proyecto</option>

                <?php foreach ($proyectos as $proyecto) : ?>
                    <option
                        value="<?php echo $proyecto->id; ?>"
                        <?php echo (int) ($tarea->proyecto_id ?? 0) === (int) $proyecto->id ? 'selected' : ''; ?>>
                        <?php echo $proyecto->nombre; ?>
                        <?php
                        $cliente = trim(($proyecto->cliente_nombre ?? '') . ' ' . ($proyecto->cliente_apellido ?? ''));
                        echo $cliente ? ' - ' . $cliente : '';
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>

    <div class="form-tarea__campo">
        <label for="fecha_limite">Fecha límite</label>
        <input
            type="date"
            id="fecha_limite"
            name="fecha_limite"
            value="<?php echo $tarea->fecha_limite ?? ''; ?>">
    </div>

    <div class="form-tarea__campo">
        <label for="prioridad">Prioridad</label>
        <select id="prioridad" name="prioridad">
            <option value="">Selecciona la prioridad</option>
            <option value="Baja" <?php echo ($tarea->prioridad ?? '') === 'Baja' ? 'selected' : ''; ?>>Baja</option>
            <option value="Media" <?php echo ($tarea->prioridad ?? '') === 'Media' ? 'selected' : ''; ?>>Media</option>
            <option value="Alta" <?php echo ($tarea->prioridad ?? '') === 'Alta' ? 'selected' : ''; ?>>Alta</option>
            <option value="Urgente" <?php echo ($tarea->prioridad ?? '') === 'Urgente' ? 'selected' : ''; ?>>Urgente</option>
        </select>
    </div>

    <div class="form-tarea__campo">
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="Pendiente" <?php echo ($tarea->estado ?? 'Pendiente') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
            <option value="En proceso" <?php echo ($tarea->estado ?? '') === 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
            <option value="En revisión" <?php echo ($tarea->estado ?? '') === 'En revisión' ? 'selected' : ''; ?>>En revisión</option>
            <option value="Completada" <?php echo ($tarea->estado ?? '') === 'Completada' ? 'selected' : ''; ?>>Completada</option>
            <option value="Anulada" <?php echo ($tarea->estado ?? '') === 'Anulada' ? 'selected' : ''; ?>>Anulada</option>
        </select>
    </div>


    <div class="form-tarea__campo">
        <label for="avance">Porcentaje de avance</label>
        <input
            type="number"
            min="0"
            max="100"
            id="avance"
            name="avance"
            placeholder="0%"
            value="<?php echo $tarea->avance ?? 0; ?>">
    </div>
</div>

<div class="form-tarea__campo form-tarea__campo--full">
    <label for="descripcion">Descripción de la tarea</label>
    <textarea
        id="descripcion"
        name="descripcion"
        placeholder="Describe la tarea en detalle"><?php echo $tarea->descripcion ?? ''; ?></textarea>
</div>

<div class="form-tarea__campo form-tarea__campo--full">
    <label for="objetivo">Objetivo o entregable</label>
    <textarea
        id="objetivo"
        name="objetivo"
        placeholder="Define el objetivo o entregable de esta tarea"><?php echo $tarea->objetivo ?? ''; ?></textarea>
</div>

<div class="form-tarea__campo form-tarea__campo--full">
    <label for="observaciones">Observaciones</label>
    <textarea
        id="observaciones"
        name="observaciones"
        placeholder="Agrega observaciones adicionales. Si esta tarea fue asignada a alguien más, puedes escribirlo aquí como dato opcional."><?php echo $tarea->observaciones ?? ''; ?></textarea>
</div>