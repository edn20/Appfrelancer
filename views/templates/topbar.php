<?php

use Model\Usuario;

$usuariosPendientesAlta = 0;
$esAdmin = (int) ($_SESSION['rol_id'] ?? 0) === 3;

if ($esAdmin) {
    $usuariosPendientesAlta = Usuario::totalPendientesAlta();
}
?>

<header class="topbar">
    <div class="topbar__izquierda">
        <button class="topbar__menu" type="button" id="btnSidebar">
            <i class="bi bi-list"></i>
        </button>

        <?php if ($esAdmin && $usuariosPendientesAlta > 0) : ?>
            <a href="/usuarios?pendientes=1" class="topbar-led">
                <span class="topbar-led__chip">
                    <i class="bi bi-exclamation-triangle"></i>
                    Alta
                </span>

                <div class="topbar-led__viewport">
                    <div class="topbar-led__track">
                        <span>
                            • Tienes <?php echo $usuariosPendientesAlta; ?>
                            usuario<?php echo $usuariosPendientesAlta === 1 ? '' : 's'; ?>
                            pendiente<?php echo $usuariosPendientesAlta === 1 ? '' : 's'; ?>
                            de dar de alta • Revisa el módulo de usuarios para aprobar accesos o asignar roles •
                        </span>

                        <span>
                            • Tienes <?php echo $usuariosPendientesAlta; ?>
                            usuario<?php echo $usuariosPendientesAlta === 1 ? '' : 's'; ?>
                            pendiente<?php echo $usuariosPendientesAlta === 1 ? '' : 's'; ?>
                            de dar de alta • Revisa el módulo de usuarios para aprobar accesos o asignar roles •
                        </span>
                    </div>
                </div>
            </a>
        <?php endif; ?>
    </div>

    <div class="topbar__acciones">

        <div class="notificaciones">
            <button class="topbar__notificacion" type="button" id="notificacionesBtn">
                <i class="bi bi-bell"></i>

                <?php if (!empty($totalNotificacionesTopbar)) : ?>
                    <span><?php echo $totalNotificacionesTopbar > 99 ? '99+' : $totalNotificacionesTopbar; ?></span>
                <?php endif; ?>
            </button>

            <div class="notificaciones__dropdown">
                <div class="notificaciones__header">
                    <h3>Notificaciones</h3>

                    <span>
                        <?php echo $totalNotificacionesTopbar; ?>
                        <?php echo $totalNotificacionesTopbar === 1 ? 'alerta pendiente' : 'alertas pendientes'; ?>
                    </span>
                </div>

                <div class="notificaciones__lista">
                    <?php if (!empty($notificacionesTopbar)) : ?>
                        <?php foreach ($notificacionesTopbar as $notificacion) : ?>
                            <a href="<?php echo $notificacion->url; ?>" class="notificaciones__item">
                                <div class="notificaciones__icono notificaciones__icono--<?php echo $notificacion->nivel; ?>">
                                    <i class="bi <?php echo $notificacion->icono; ?>"></i>
                                </div>

                                <div>
                                    <p><?php echo $notificacion->titulo; ?></p>
                                    <span><?php echo $notificacion->mensaje; ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="notificaciones__vacia">
                            <p>No tienes alertas pendientes.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <a href="/notificaciones" class="notificaciones__ver-todas">
                    Ver todas las notificaciones
                </a>
            </div>
        </div>

        <div class="usuario-menu">
            <button class="usuario-menu__boton" type="button" id="usuarioMenuBtn">
                <div class="usuario-menu__avatar">
                    <?php if (!empty($_SESSION['avatar'])) : ?>
                        <img
                            src="/uploads/avatars/<?php echo $_SESSION['avatar']; ?>"
                            alt="Avatar de usuario">
                    <?php else : ?>
                        <i class="bi bi-person-fill"></i>
                    <?php endif; ?>
                </div>

                <div class="usuario-menu__info">
                    <p><?php echo $_SESSION['nombre'] ?? 'Usuario'; ?></p>

                    <span>
                        <?php
                        $rolId = intval($_SESSION['rol_id'] ?? 0);

                        if ($rolId === 3) {
                            echo 'Administrador';
                        } elseif ($rolId === 2) {
                            echo 'Freelancer';
                        } else {
                            echo 'Usuario';
                        }
                        ?>
                    </span>
                </div>

                <i class="bi bi-chevron-down usuario-menu__flecha"></i>
            </button>

            <div class="usuario-menu__dropdown">
                <a href="/perfil">
                    <i class="bi bi-person"></i>
                    <span>Mi perfil</span>
                </a>

                <a href="/configuracion">
                    <i class="bi bi-gear"></i>
                    <span>Configuración</span>
                </a>

                <hr>

                <a href="/logout" class="usuario-menu__salir js-cerrar-sesion">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>

    </div>
</header>