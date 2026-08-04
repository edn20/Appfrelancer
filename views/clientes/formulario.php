<div class="form-cliente__grid">
    <div class="form-cliente__campo">
        <label for="nombre">Nombre del cliente <span>*</span></label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            placeholder="Ingresa el nombre del cliente"
            value="<?php echo $cliente->nombre ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="apellido">Apellido del cliente</label>
        <input
            type="text"
            id="apellido"
            name="apellido"
            placeholder="Ingresa el apellido del cliente"
            value="<?php echo $cliente->apellido ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="empresa">Empresa</label>
        <input
            type="text"
            id="empresa"
            name="empresa"
            placeholder="Nombre de la empresa"
            value="<?php echo $cliente->empresa ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="identificacion">Identificación</label>
        <input
            type="text"
            id="identificacion"
            name="identificacion"
            placeholder="Ej. 101-1234567-8"
            value="<?php echo $cliente->identificacion ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="telefono">Teléfono</label>
        <input
            type="text"
            id="telefono"
            name="telefono"
            placeholder="Ej. 809-555-0123"
            value="<?php echo $cliente->telefono ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Ej. info@empresa.com"
            value="<?php echo $cliente->email ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="direccion">Dirección</label>
        <input
            type="text"
            id="direccion"
            name="direccion"
            placeholder="Ingresa la dirección completa"
            value="<?php echo $cliente->direccion ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="ciudad">Ciudad</label>
        <input
            type="text"
            id="ciudad"
            name="ciudad"
            placeholder="Ingresa la ciudad"
            value="<?php echo $cliente->ciudad ?? ''; ?>">
    </div>

    <div class="form-cliente__campo">
        <label for="tipo_cliente">Tipo de cliente</label>
        <select id="tipo_cliente" name="tipo_cliente">
            <option value="">Selecciona el tipo de cliente</option>
            <option value="Recurrente" <?php echo ($cliente->tipo_cliente ?? '') === 'Recurrente' ? 'selected' : ''; ?>>Recurrente</option>
            <option value="Ocasional" <?php echo ($cliente->tipo_cliente ?? '') === 'Ocasional' ? 'selected' : ''; ?>>Ocasional</option>
            <option value="Prospecto" <?php echo ($cliente->tipo_cliente ?? '') === 'Prospecto' ? 'selected' : ''; ?>>Prospecto</option>
        </select>
    </div>

    <div class="form-cliente__campo">
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="1" <?php echo (int) ($cliente->estado ?? 1) === 1 ? 'selected' : ''; ?>>Activo</option>
            <option value="0" <?php echo (string) ($cliente->estado ?? '') === '0' ? 'selected' : ''; ?>>Inactivo</option>
        </select>
    </div>

    <div class="form-cliente__campo">
        <label for="fuente_contacto">Fuente de contacto</label>
        <select id="fuente_contacto" name="fuente_contacto">
            <option value="">Selecciona la fuente de contacto</option>
            <option value="Referido" <?php echo ($cliente->fuente_contacto ?? '') === 'Referido' ? 'selected' : ''; ?>>Referido</option>
            <option value="Redes sociales" <?php echo ($cliente->fuente_contacto ?? '') === 'Redes sociales' ? 'selected' : ''; ?>>Redes sociales</option>
            <option value="WhatsApp" <?php echo ($cliente->fuente_contacto ?? '') === 'WhatsApp' ? 'selected' : ''; ?>>WhatsApp</option>
            <option value="Correo" <?php echo ($cliente->fuente_contacto ?? '') === 'Correo' ? 'selected' : ''; ?>>Correo</option>
            <option value="Otro" <?php echo ($cliente->fuente_contacto ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
        </select>
    </div>
</div>

<div class="form-cliente__campo form-cliente__campo--full">
    <label for="observaciones">Observaciones</label>
    <textarea
        id="observaciones"
        name="observaciones"
        placeholder="Agrega observaciones adicionales (opcional)"><?php echo $cliente->observaciones ?? ''; ?></textarea>
</div>