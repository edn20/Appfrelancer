<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="cliente-crear">
    <div class="cliente-crear__breadcrumb">
        <a href="/clientes">Clientes</a>
        <span>/</span>
        <p>Editar</p>
    </div>

    <div class="cliente-crear__header">
        <h1>Editar cliente</h1>
        <p>Actualiza únicamente la información que necesites corregir.</p>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <div class="cliente-crear__grid">
        <div class="cliente-crear__card">
            <form class="form-cliente" method="POST" action="/clientes/editar?id=<?php echo $cliente->id; ?>">
                <?php include_once __DIR__ . '/formulario.php'; ?>

                <div class="form-cliente__acciones">
                    <a href="/clientes" class="form-cliente__cancelar">Cancelar</a>

                    <button type="submit" class="form-cliente__submit">
                        <i class="bi bi-floppy"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <aside class="cliente-info-card">
            <h2>Información</h2>

            <div class="cliente-info-card__linea"></div>

            <div class="cliente-info-card__item">
                <div class="cliente-info-card__icon cliente-info-card__icon--success">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>
                    <p>Estado actual</p>

                    <?php if ((int) $cliente->estado === 1) : ?>
                        <strong>Activo</strong>
                    <?php else : ?>
                        <strong>Inactivo</strong>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cliente-info-card__item">
                <div class="cliente-info-card__icon cliente-info-card__icon--blue">
                    <i class="bi bi-folder"></i>
                </div>

                <div>
                    <p>Proyectos asociados</p>
                    <strong><?php echo $resumen['proyectos_asociados'] ?? 0; ?></strong>
                </div>
            </div>

            <div class="cliente-info-card__item">
                <div class="cliente-info-card__icon cliente-info-card__icon--purple">
                    <i class="bi bi-currency-dollar"></i>
                </div>

                <div>
                    <p>Saldo pendiente</p>
                    <strong>$<?php echo number_format((float) ($resumen['saldo_pendiente'] ?? 0), 2); ?></strong>
                </div>
            </div>

            <div class="cliente-info-card__nota">
                <i class="bi bi-info-circle"></i>
                <p>
                    Los cambios realizados en esta pantalla actualizarán la información
                    principal del cliente. Sus proyectos, tareas y pagos se gestionarán
                    desde sus módulos correspondientes.
                </p>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>