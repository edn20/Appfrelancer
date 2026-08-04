<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="cliente-crear">
    <div class="cliente-crear__breadcrumb">
        <a href="/clientes">Clientes</a>
        <span>/</span>
        <p>Crear</p>
    </div>

    <div class="cliente-crear__header">
        <h1>Nuevo cliente</h1>
        <p>Registra la información de un nuevo cliente.</p>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <div class="cliente-crear__grid">
        <div class="cliente-crear__card">
            <form id="form-crear-cliente" method="POST" action="/clientes/crear">
                <?php include_once __DIR__ . '/formulario.php'; ?>

                <div class="form-cliente__acciones">
                    <a href="/clientes" class="form-cliente__cancelar">Cancelar</a>

                    <button type="submit" class="form-cliente__submit">
                        <i class="bi bi-floppy"></i>
                        Guardar cliente
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
                    <p>Estado inicial</p>
                    <strong>Activo</strong>
                </div>
            </div>

            <div class="cliente-info-card__item">
                <div class="cliente-info-card__icon cliente-info-card__icon--blue">
                    <i class="bi bi-folder"></i>
                </div>

                <div>
                    <p>Proyectos asociados</p>
                    <strong>0</strong>
                </div>
            </div>

            <div class="cliente-info-card__item">
                <div class="cliente-info-card__icon cliente-info-card__icon--purple">
                    <i class="bi bi-currency-dollar"></i>
                </div>

                <div>
                    <p>Saldo pendiente</p>
                    <strong>$0.00</strong>
                </div>
            </div>

            <div class="cliente-info-card__nota">
                <i class="bi bi-info-circle"></i>
                <p>
                    Una vez guardado el cliente, podrás asociarle proyectos y gestionar
                    su información desde su perfil.
                </p>
            </div>
        </aside>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>