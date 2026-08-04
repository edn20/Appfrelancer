<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="perfil">
    <div class="perfil__breadcrumb">
        <a href="/configuracion">Configuración</a>
        <span>/</span>
        <p>Mi perfil</p>
    </div>

    <div class="perfil__header">
        <div>
            <h1>Mi perfil</h1>
            <p>Actualiza la información principal de tu cuenta.</p>
        </div>

        <a href="/configuracion" class="perfil__volver">
            <i class="bi bi-arrow-left"></i>
            Volver a configuración
        </a>
    </div>

    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <div class="perfil__grid">
        <aside class="perfil-card perfil-card--usuario">
            <div class="perfil-card__avatar">
                <?php if (!empty($usuario->avatar)) : ?>
                    <img src="/uploads/avatars/<?php echo $usuario->avatar; ?>" alt="Avatar de usuario">
                <?php else : ?>
                    <?php echo strtoupper(substr($usuario->nombre ?? 'U', 0, 1)); ?>
                <?php endif; ?>
            </div>

            <h2>
                <?php echo trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? '')); ?>
            </h2>

            <p><?php echo $usuario->email ?? 'Correo no registrado'; ?></p>

            <div class="perfil-card__badges">
                <span class="perfil-badge perfil-badge--rol">
                    <i class="bi bi-shield-check"></i>
                    <?php echo $rol; ?>
                </span>

                <span class="perfil-badge perfil-badge--estado">
                    <i class="bi bi-check-circle"></i>
                    <?php echo $estado; ?>
                </span>
            </div>
        </aside>

        <div class="perfil-panel">
            <section class="perfil-seccion">
                <div class="perfil-seccion__header">
                    <h2>Información personal</h2>
                    <p>Estos datos se utilizan para identificar tu cuenta dentro del sistema.</p>
                </div>

                <form class="perfil-form" method="POST" action="/perfil" enctype="multipart/form-data">
                    <div class="perfil-form__grid">
                        <div class="perfil-form__campo">
                            <label for="nombre">Nombre</label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                value="<?php echo $usuario->nombre ?? ''; ?>"
                                placeholder="Tu nombre">
                        </div>

                        <div class="perfil-form__campo">
                            <label for="apellido">Apellido</label>
                            <input
                                type="text"
                                id="apellido"
                                name="apellido"
                                value="<?php echo $usuario->apellido ?? ''; ?>"
                                placeholder="Tu apellido">
                        </div>

                        <div class="perfil-form__campo perfil-form__campo--full">
                            <label for="email">Correo electrónico</label>
                            <input
                                type="email"
                                id="email"
                                value="<?php echo $usuario->email ?? ''; ?>"
                                disabled>
                            <small>El correo electrónico no se puede modificar para evitar conflictos con el acceso a la cuenta.</small>
                        </div>

                        <div class="perfil-form__campo perfil-form__campo--full">
                            <label>Imagen de perfil</label>

                            <div class="perfil-avatar-uploader">
                                <div class="perfil-avatar-uploader__preview" id="avatar-preview">
                                    <?php if (!empty($usuario->avatar)) : ?>
                                        <img src="/uploads/avatars/<?php echo $usuario->avatar; ?>" alt="Avatar actual">
                                    <?php else : ?>
                                        <span><?php echo strtoupper(substr($usuario->nombre ?? 'U', 0, 1)); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="perfil-avatar-uploader__info">
                                    <strong>
                                        <?php echo !empty($usuario->avatar) ? 'Foto de perfil actual' : 'Sin foto de perfil'; ?>
                                    </strong>

                                    <p>
                                        Sube una imagen JPG, PNG o WEBP. Podrás previsualizarla y recortarla antes de guardar.
                                    </p>

                                    <button type="button" class="perfil-avatar-uploader__boton" id="btn-seleccionar-avatar">
                                        <i class="bi bi-image"></i>
                                        <?php echo !empty($usuario->avatar) ? 'Cambiar foto de perfil' : 'Agregar foto de perfil'; ?>
                                    </button>
                                </div>
                            </div>

                            <input
                                type="file"
                                id="avatar"
                                accept="image/jpeg,image/png,image/webp"
                                hidden>

                            <input
                                type="hidden"
                                id="avatar_base64"
                                name="avatar_base64">

                            <small>La imagen se guardará recortada en formato cuadrado.</small>
                        </div>

                        <div class="perfil-form__campo">
                            <label>Rol</label>
                            <input type="text" value="<?php echo $rol; ?>" disabled>
                        </div>

                        <div class="perfil-form__campo">
                            <label>Estado</label>
                            <input type="text" value="<?php echo $estado; ?>" disabled>
                        </div>
                    </div>

                    <div class="perfil-form__acciones">
                        <a href="/configuracion" class="perfil-form__cancelar">
                            Cancelar
                        </a>

                        <button type="submit" class="perfil-form__submit">
                            <i class="bi bi-check-lg"></i>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </section>

            <section class="perfil-seccion" id="seguridad">
                <div class="perfil-seccion__header">
                    <h2>Seguridad de la cuenta</h2>
                    <p>Cambia tu contraseña de acceso de forma segura.</p>
                </div>

                <form class="perfil-password" method="POST" action="/perfil/password">
                    <div class="perfil-password__grid">
                        <div class="perfil-password__campo perfil-password__campo--full">
                            <label for="password_actual">Contraseña actual</label>

                            <input
                                type="password"
                                id="password_actual"
                                name="password_actual"
                                placeholder="Ingresa tu contraseña actual"
                                autocomplete="current-password">
                        </div>

                        <div class="perfil-password__campo">
                            <label for="password_nuevo">Nueva contraseña</label>

                            <input
                                type="password"
                                id="password_nuevo"
                                name="password_nuevo"
                                placeholder="Mínimo 8 caracteres"
                                autocomplete="new-password">
                        </div>

                        <div class="perfil-password__campo">
                            <label for="password_confirmar">Repetir nueva contraseña</label>

                            <input
                                type="password"
                                id="password_confirmar"
                                name="password_confirmar"
                                placeholder="Repite la nueva contraseña"
                                autocomplete="new-password">
                        </div>
                    </div>

                    <div class="perfil-password__aviso">
                        <i class="bi bi-info-circle"></i>
                        <p>
                            Después de cambiar la contraseña, se cerrará tu sesión y deberás ingresar nuevamente.
                        </p>
                    </div>

                    <div class="perfil-password__acciones">
                        <button type="submit" class="perfil-password__submit">
                            <i class="bi bi-shield-check"></i>
                            Cambiar contraseña
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</section>

<div class="avatar-modal" id="avatar-modal" aria-hidden="true">
    <div class="avatar-modal__overlay" data-cerrar-avatar></div>

    <div class="avatar-modal__card">
        <div class="avatar-modal__header">
            <div>
                <h2>Recortar foto de perfil</h2>
                <p>Ajusta la imagen dentro del recuadro para definir tu avatar.</p>
            </div>

            <button type="button" class="avatar-modal__cerrar" data-cerrar-avatar>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="avatar-crop">
            <div class="avatar-crop__area" id="avatar-crop-area">
                <img id="avatar-crop-img" alt="Vista previa del avatar">
                <div class="avatar-crop__marco"></div>
            </div>

            <p class="avatar-crop__ayuda">
                Arrastra la imagen para acomodarla dentro del recuadro.
            </p>

            <div class="avatar-crop__control">
                <label for="avatar-zoom">Zoom</label>
                <input type="range" id="avatar-zoom" min="1" max="3" step="0.01" value="1">
            </div>
        </div>

        <div class="avatar-modal__acciones">
            <button type="button" class="avatar-modal__cancelar" data-cerrar-avatar>
                Cancelar
            </button>

            <button type="button" class="avatar-modal__guardar" id="btn-guardar-recorte">
                <i class="bi bi-check-lg"></i>
                Usar esta foto
            </button>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>