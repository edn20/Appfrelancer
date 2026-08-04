<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="configuracion">
    <div class="configuracion__header">
        <h1>Configuración</h1>
        <p>Administra tu cuenta, preferencias y opciones generales del sistema.</p>
    </div>

    <div class="configuracion-panel">
        <aside class="configuracion-menu">
            <div class="configuracion-menu__perfil">
                <div class="configuracion-menu__avatar">
                    <?php if (!empty($usuario->avatar)) : ?>
                        <img src="/uploads/avatars/<?php echo $usuario->avatar; ?>" alt="Avatar de usuario">
                    <?php else : ?>
                        <?php echo strtoupper(substr($usuario->nombre ?? 'U', 0, 1)); ?>
                    <?php endif; ?>
                </div>

                <div>
                    <strong>
                        <?php echo trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? '')); ?>
                    </strong>
                    <span><?php echo $rol; ?></span>
                </div>
            </div>

            <nav class="configuracion-menu__nav">
                <a href="#perfil" class="activo">
                    <i class="bi bi-person-circle"></i>
                    Cuenta
                </a>

                <a href="#preferencias">
                    <i class="bi bi-sliders"></i>
                    Preferencias
                </a>

                <a href="#seguridad">
                    <i class="bi bi-shield-lock"></i>
                    Seguridad
                </a>

                <a href="#notificaciones">
                    <i class="bi bi-bell"></i>
                    Notificaciones
                </a>

                <a href="#sistema">
                    <i class="bi bi-info-circle"></i>
                    Sistema
                </a>
            </nav>
        </aside>

        <div class="configuracion-contenido">
            <section id="perfil" class="configuracion-seccion">
                <div class="configuracion-seccion__header">
                    <div>
                        <h2>Cuenta</h2>
                        <p>Información básica de tu perfil dentro del sistema.</p>
                    </div>
                </div>

                <div class="configuracion-opciones">
                    <div class="configuracion-opcion">
                        <div>
                            <strong>Nombre</strong>
                            <span>
                                <?php echo trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? '')); ?>
                            </span>
                        </div>
                    </div>

                    <div class="configuracion-opcion">
                        <div>
                            <strong>Correo electrónico</strong>
                            <span><?php echo $usuario->email ?? 'Correo no registrado'; ?></span>
                        </div>
                    </div>

                    <div class="configuracion-opcion">
                        <div>
                            <strong>Rol de usuario</strong>
                            <span><?php echo $rol; ?></span>
                        </div>
                    </div>

                    <div class="configuracion-opcion configuracion-opcion--accion">
                        <div>
                            <strong>Editar perfil</strong>
                            <span>Actualiza tus datos personales y la información de tu cuenta.</span>
                        </div>

                        <a href="/perfil" class="configuracion-opcion__boton">
                            Editar
                        </a>
                    </div>
                </div>
            </section>

            <section class="configuracion-seccion" id="preferencias">
                <div class="configuracion-seccion__header">
                    <h2>Preferencias</h2>
                    <p>Define el formato global con el que deseas visualizar las fechas en el sistema.</p>
                </div>

                <?php if (isset($_GET['preferencias'])) : ?>
                    <div class="configuracion-alerta configuracion-alerta--exito">
                        <i class="bi bi-check-circle"></i>
                        <p>Preferencias actualizadas correctamente.</p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error_preferencias'])) : ?>
                    <div class="configuracion-alerta configuracion-alerta--error">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p>No se pudieron actualizar las preferencias.</p>
                    </div>
                <?php endif; ?>

                <form class="configuracion-preferencias" method="POST" action="/configuracion/preferencias">
                    <div class="configuracion-preferencia">
                        <div class="configuracion-preferencia__info">
                            <strong>Formato global de fecha</strong>
                            <span>Este formato se aplicará en proyectos, tareas, pagos, notificaciones y reportes.</span>
                        </div>

                        <div class="configuracion-preferencia__control">
                            <select name="formato_fecha" id="formato_fecha">
                                <?php foreach ($formatosFecha as $valor => $ejemplo) : ?>
                                    <option
                                        value="<?php echo $valor; ?>"
                                        <?php echo ($configuracionPreferencias->formato_fecha ?? 'dd_mm_yyyy') === $valor ? 'selected' : ''; ?>>
                                        <?php echo $ejemplo; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="configuracion-preview-fecha">
                        <div>
                            <span>Vista previa</span>
                            <strong id="preview-formato-fecha">
                                <?php echo $formatosFecha[$configuracionPreferencias->formato_fecha ?? 'dd_mm_yyyy'] ?? '03/08/2026'; ?>
                            </strong>
                        </div>

                        <i class="bi bi-calendar-date"></i>
                    </div>

                    <div class="configuracion-preferencias__acciones">
                        <button type="submit">
                            <i class="bi bi-check2-circle"></i>
                            Guardar preferencias
                        </button>
                    </div>
                </form>
            </section>

            <section id="seguridad" class="configuracion-seccion">
                <div class="configuracion-seccion__header">
                    <div>
                        <h2>Seguridad</h2>
                        <p>Control de acceso y protección de información sensible.</p>
                    </div>
                </div>

                <div class="configuracion-opciones">
                    <div class="configuracion-opcion">
                        <div>
                            <strong>Notas protegidas</strong>
                            <span>Las notas se bloquean automáticamente al salir del módulo.</span>
                        </div>

                        <span class="configuracion-badge configuracion-badge--activo">Activo</span>
                    </div>

                    <div class="configuracion-opcion">
                        <div>
                            <strong>Tiempo de desbloqueo</strong>
                            <span>Duración temporal para visualizar notas protegidas.</span>
                        </div>

                        <em>10 minutos</em>
                    </div>

                    <div class="configuracion-opcion configuracion-opcion--accion">
                        <div>
                            <strong>Cambiar contraseña</strong>
                            <span>Administra la contraseña desde tu perfil de usuario.</span>
                        </div>

                        <a href="/perfil#seguridad" class="configuracion-opcion__boton">
                            Gestionar
                        </a>
                    </div>
                </div>
            </section>

            <section class="configuracion-seccion" id="notificaciones">
                <div class="configuracion-seccion__header">
                    <h2>Notificaciones</h2>
                    <p>Elige qué alertas deseas recibir en la campana y en el centro de notificaciones.</p>
                </div>

                <?php if (isset($_GET['notificaciones'])) : ?>
                    <div class="configuracion-alerta configuracion-alerta--exito">
                        <i class="bi bi-check-circle"></i>
                        <p>Preferencias de notificaciones actualizadas correctamente.</p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error_notificaciones'])) : ?>
                    <div class="configuracion-alerta configuracion-alerta--error">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p>No se pudieron actualizar las preferencias de notificaciones.</p>
                    </div>
                <?php endif; ?>

                <form class="configuracion-switches" method="POST" action="/configuracion/notificaciones">
                    <div class="configuracion-switch">
                        <div>
                            <strong>Tareas vencidas</strong>
                            <span>Alertas para tareas cuya fecha límite ya pasó.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="tareas_vencidas"
                                <?php echo (int) $configuracionNotificaciones->tareas_vencidas === 1 ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switch">
                        <div>
                            <strong>Tareas que vencen hoy</strong>
                            <span>Alertas para tareas que vencen durante el día actual.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="tareas_hoy"
                                <?php echo (int) $configuracionNotificaciones->tareas_hoy === 1 ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switch">
                        <div>
                            <strong>Tareas próximas a vencer</strong>
                            <span>Alertas para tareas que vencen dentro de los próximos 5 días.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="tareas_proximas"
                                <?php echo (int) $configuracionNotificaciones->tareas_proximas === 1 ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switch">
                        <div>
                            <strong>Pagos vencidos</strong>
                            <span>Alertas para pagos pendientes cuya fecha máxima ya pasó.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="pagos_vencidos"
                                <?php echo (int) $configuracionNotificaciones->pagos_vencidos === 1 ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switch">
                        <div>
                            <strong>Pagos próximos a vencer</strong>
                            <span>Alertas para pagos pendientes que vencen dentro de los próximos 5 días.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="pagos_proximos"
                                <?php echo (int) $configuracionNotificaciones->pagos_proximos === 1 ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switch">
                        <div>
                            <strong>Proyectos atrasados</strong>
                            <span>Alertas para proyectos cuya fecha de entrega ya pasó.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="proyectos_atrasados"
                                <?php echo (int) $configuracionNotificaciones->proyectos_atrasados === 1 ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switch">
                        <div>
                            <strong>Proyectos próximos a entregar</strong>
                            <span>Alertas para proyectos que vencen dentro de los próximos 5 días.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="proyectos_proximos"
                                <?php echo (int) $configuracionNotificaciones->proyectos_proximos === 1 ? 'checked' : ''; ?>>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switch configuracion-switch--disabled">
                        <div>
                            <strong>Obligaciones por vencer</strong>
                            <span>Recordatorios contables para obligaciones registradas por cliente.</span>
                        </div>

                        <label class="switch">
                            <input
                                type="checkbox"
                                name="obligaciones_proximas"
                                <?php echo (int) $configuracionNotificaciones->obligaciones_proximas === 1 ? 'checked' : ''; ?>
                                disabled>
                            <span></span>
                        </label>
                    </div>

                    <div class="configuracion-switches__acciones">
                        <button type="submit">
                            <i class="bi bi-check2-circle"></i>
                            Guardar preferencias
                        </button>
                    </div>
                </form>
            </section>

            <section id="sistema" class="configuracion-seccion">
                <div class="configuracion-seccion__header">
                    <div>
                        <h2>Información del sistema</h2>
                        <p>Datos técnicos generales de la aplicación.</p>
                    </div>
                </div>

                <div class="configuracion-opciones">
                    <div class="configuracion-opcion">
                        <div>
                            <strong>Nombre del sistema</strong>
                            <span>Freelance Manager EDN</span>
                        </div>
                    </div>

                    <div class="configuracion-opcion">
                        <div>
                            <strong>Versión</strong>
                            <span>1.0.0</span>
                        </div>
                    </div>

                    <div class="configuracion-opcion">
                        <div>
                            <strong>Arquitectura</strong>
                            <span>PHP MVC propio, MySQL, Sass y JavaScript</span>
                        </div>
                    </div>

                    <div class="configuracion-opcion">
                        <div>
                            <strong>Desarrollado por</strong>
                            <span>Edinson Aguirre</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>