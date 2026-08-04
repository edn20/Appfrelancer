<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<?php
$clienteNombre = trim(($nota->cliente_nombre ?? '') . ' ' . ($nota->cliente_apellido ?? ''));
$clienteNombre = $clienteNombre ?: ($nota->cliente_empresa ?? '');

$fechaCreacion = $nota->creado ? date('d/m/Y H:i', strtotime($nota->creado)) : 'Sin fecha';
$fechaActualizacion = $nota->actualizado ? date('d/m/Y H:i', strtotime($nota->actualizado)) : 'Sin actualización';
?>

<section class="nota-detalle">
    <div class="nota-detalle__volver">
        <a href="/notas">
            <i class="bi bi-arrow-left"></i>
            Volver a notas
        </a>
    </div>

    <div class="nota-detalle__top">
        <div>
            <h1>Detalle de nota</h1>

            <div class="nota-detalle__breadcrumb">
                <a href="/dashboard">Inicio</a>
                <span>/</span>
                <a href="/notas">Notas</a>
                <span>/</span>
                <p>Detalle</p>
            </div>
        </div>
    </div>

    <?php if (!empty($error)) : ?>
        <div class="nota-detalle__alerta nota-detalle__alerta--error">
            <i class="bi bi-exclamation-triangle"></i>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($exito)) : ?>
        <div class="nota-detalle__alerta nota-detalle__alerta--exito">
            <i class="bi bi-check-circle"></i>
            <p><?php echo $exito; ?></p>
        </div>
    <?php endif; ?>

    <div class="nota-detalle__grid">
        <div class="nota-paper nota-paper--<?php echo $nota->color; ?>">
            <form id="form-eliminar-nota" method="POST" action="/notas/eliminar">
                <input type="hidden" name="id" value="<?php echo $nota->id; ?>">

                <button type="button" id="btnEliminarNota" class="nota-paper__delete" title="Eliminar nota">
                    <i class="bi bi-trash"></i>
                </button>
            </form>

            <?php if ((int) $nota->fija === 1) : ?>
                <span class="nota-paper__pin">
                    <i class="bi bi-pin-angle-fill"></i>
                </span>
            <?php endif; ?>

            <form id="form-actualizar-nota" method="POST" action="/notas/actualizar" class="nota-editor">
                <input type="hidden" name="id" value="<?php echo $nota->id; ?>">

                <div class="nota-editor__header">
                    <input
                        type="text"
                        name="titulo"
                        id="titulo"
                        value="<?php echo $nota->titulo; ?>"
                        placeholder="Título de la nota">

                    <select name="color" id="color">
                        <option value="amarillo" <?php echo $nota->color === 'amarillo' ? 'selected' : ''; ?>>Amarillo</option>
                        <option value="verde" <?php echo $nota->color === 'verde' ? 'selected' : ''; ?>>Verde</option>
                        <option value="azul" <?php echo $nota->color === 'azul' ? 'selected' : ''; ?>>Azul</option>
                        <option value="rosa" <?php echo $nota->color === 'rosa' ? 'selected' : ''; ?>>Rosa</option>
                        <option value="gris" <?php echo $nota->color === 'gris' ? 'selected' : ''; ?>>Gris</option>
                    </select>
                </div>

                <textarea
                    name="contenido"
                    id="contenido"
                    placeholder="Escribe el contenido de la nota..."><?php echo $nota->contenido; ?></textarea>

                <div class="nota-editor__footer">
                    <label class="nota-editor__check">
                        <input
                            type="checkbox"
                            name="fija"
                            value="1"
                            <?php echo (int) $nota->fija === 1 ? 'checked' : ''; ?>>
                        Fijar nota arriba
                    </label>

                    <button type="button" id="btnActualizarNota">
                        <i class="bi bi-shield-lock"></i>
                        Actualizar nota
                    </button>
                </div>
            </form>
        </div>

        <aside class="nota-detalle__info">
            <div class="nota-info-card">
                <h2>Información</h2>

                <div class="nota-info-card__item">
                    <span>Proyecto</span>

                    <?php if (!empty($nota->proyecto_nombre)) : ?>
                        <a href="/proyectos/detalle?id=<?php echo $nota->proyecto_id; ?>">
                            <i class="bi bi-folder"></i>
                            <?php echo $nota->proyecto_nombre; ?>
                        </a>
                    <?php else : ?>
                        <p>
                            <i class="bi bi-folder"></i>
                            Nota general
                        </p>
                    <?php endif; ?>
                </div>

                <div class="nota-info-card__item">
                    <span>Cliente</span>

                    <p>
                        <i class="bi bi-person"></i>
                        <?php echo $clienteNombre ?: 'Sin cliente específico'; ?>
                    </p>
                </div>

                <div class="nota-info-card__item">
                    <span>Creada</span>

                    <p>
                        <i class="bi bi-calendar"></i>
                        <?php echo $fechaCreacion; ?>
                    </p>
                </div>

                <div class="nota-info-card__item">
                    <span>Última actualización</span>

                    <p>
                        <i class="bi bi-clock-history"></i>
                        <?php echo $fechaActualizacion; ?>
                    </p>
                </div>

                <div class="nota-info-card__warning">
                    <i class="bi bi-shield-lock"></i>
                    <p>
                        Para actualizar o eliminar esta nota debes confirmar tu contraseña.
                    </p>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>