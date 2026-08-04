<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="pago-crear">
    <div class="pago-crear__top">
        <div>
            <h1>Nuevo pago</h1>

            <div class="pago-crear__breadcrumb">
                <a href="/dashboard">Inicio</a>
                <span>/</span>
                <a href="/pagos">Pagos</a>
                <span>/</span>
                <p>Nuevo pago</p>
            </div>
        </div>

        <div class="pago-crear__acciones-top">
            <?php if ($proyectoSeleccionado) : ?>
                <a href="/proyectos/detalle?id=<?php echo $proyectoSeleccionado->id; ?>" class="pago-crear__cancelar-top">
                    Cancelar
                </a>
            <?php else : ?>
                <a href="/pagos" class="pago-crear__cancelar-top">
                    Cancelar
                </a>
            <?php endif; ?>

            <button type="submit" form="form-pago" class="pago-crear__guardar-top">
                <i class="bi bi-floppy"></i>
                Guardar pago
            </button>
        </div>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <form
        id="form-pago"
        class="form-pago"
        method="POST"
        enctype="multipart/form-data"
        action="<?php echo $proyectoSeleccionado ? '/pagos/crear?proyecto_id=' . $proyectoSeleccionado->id : '/pagos/crear'; ?>">
        <div class="pago-crear__card">
            <h2>Información del pago</h2>

            <?php include_once __DIR__ . '/formulario.php'; ?>
        </div>

        <div class="pago-crear__extra">
            <div class="pago-crear__card">
                <div class="form-pago__campo form-pago__campo--full">
                    <label for="notas_internas">Notas internas (opcional)</label>
                    <textarea
                        id="notas_internas"
                        name="notas_internas"
                        placeholder="Agrega notas internas relacionadas con este pago..."><?php echo $pago->notas_internas ?? ''; ?></textarea>

                    <small>Estas notas no serán visibles para el cliente.</small>
                </div>
            </div>

            <div class="pago-crear__card">
                <h2>Adjuntos (opcional)</h2>

                <label class="pago-upload" for="comprobantes">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <p>Haz clic para seleccionar comprobantes</p>
                    <small>Máximo 2 archivos. Formatos permitidos: PDF, JPG, PNG o WEBP. Máx. 10MB por archivo.</small>
                </label>

                <input
                    type="file"
                    id="comprobantes"
                    name="comprobantes[]"
                    accept=".pdf,.jpg,.jpeg,.png,.webp"
                    multiple
                    class="pago-upload__input">
            </div>
        </div>

        <div class="pago-crear__info">
            <i class="bi bi-info-circle-fill"></i>

            <div>
                <strong>Información</strong>
                <p>
                    El estado del pago puede ser: Pagado, Parcial, Pendiente, Vencido o Por definir.
                </p>
            </div>
        </div>
    </form>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>