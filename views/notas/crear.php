<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="notas-page">
    <div class="notas-page__top">
        <div>
            <h1>Crear nota</h1>
            <p>Guarda recordatorios importantes del cliente o proyecto.</p>
        </div>

        <a href="<?php echo $proyectoSeleccionado ? '/proyectos/detalle?id=' . $proyectoSeleccionado->id : '/notas/desbloquear'; ?>">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form method="POST" class="nota-form">
        <div class="nota-form__grid">
            <div class="nota-form__campo">
                <label for="titulo">Título <span>*</span></label>
                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    placeholder="Ej: Claves SRI, puntos pendientes, accesos..."
                    value="<?php echo $nota->titulo ?? ''; ?>">
            </div>

            <?php if ($proyectoSeleccionado) : ?>
                <div class="nota-form__campo">
                    <label>Proyecto</label>
                    <input type="text" value="<?php echo $proyectoSeleccionado->nombre; ?>" readonly>
                </div>
            <?php else : ?>
                <div class="nota-form__campo">
                    <label for="proyecto_id">Proyecto</label>
                    <select id="proyecto_id" name="proyecto_id">
                        <option value="">Nota general</option>

                        <?php foreach ($proyectos as $proyecto) : ?>
                            <option value="<?php echo $proyecto->id; ?>">
                                <?php echo $proyecto->nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="nota-form__campo">
                    <label for="cliente_id">Cliente</label>
                    <select id="cliente_id" name="cliente_id">
                        <option value="">Sin cliente específico</option>

                        <?php foreach ($clientes as $cliente) : ?>
                            <?php
                            $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
                            $clienteNombre = $clienteNombre ?: ($cliente->empresa ?? 'Cliente');
                            ?>

                            <option value="<?php echo $cliente->id; ?>">
                                <?php echo $clienteNombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="nota-form__campo">
                <label for="color">Color de nota</label>

                <select id="color" name="color">
                    <option value="amarillo">Amarillo</option>
                    <option value="verde">Verde</option>
                    <option value="azul">Azul</option>
                    <option value="rosa">Rosa</option>
                    <option value="gris">Gris</option>
                </select>
            </div>

            <div class="nota-form__check">
                <label>
                    <input type="checkbox" name="fija" value="1">
                    Fijar nota arriba
                </label>
            </div>
        </div>

        <div class="nota-form__campo nota-form__campo--full">
            <label for="contenido">Contenido <span>*</span></label>

            <textarea
                id="contenido"
                name="contenido"
                placeholder="Escribe aquí la nota importante..."><?php echo $nota->contenido ?? ''; ?></textarea>
        </div>

        <button type="submit" class="nota-form__submit">
            <i class="bi bi-sticky"></i>
            Guardar nota
        </button>
    </form>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>