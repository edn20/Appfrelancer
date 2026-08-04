<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="usuarios">
    <div class="usuarios__header">
        <div>
            <p class="usuarios__eyebrow">Administración</p>
            <h1>Gestión de usuarios</h1>
            <p>Administra altas, roles y estados de acceso al sistema.</p>
        </div>
    </div>

    <?php if (isset($_GET['actualizado'])) : ?>
        <div class="usuarios-alerta usuarios-alerta--exito">
            <i class="bi bi-check-circle"></i>
            <p>Usuario actualizado correctamente.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])) : ?>
        <div class="usuarios-alerta usuarios-alerta--error">
            <i class="bi bi-exclamation-triangle"></i>
            <p>No se pudo actualizar el usuario.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['sin_confirmar'])) : ?>
        <div class="usuarios-alerta usuarios-alerta--error">
            <i class="bi bi-exclamation-triangle"></i>
            <p>No puedes activar un usuario que aún no ha confirmado su correo.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['no_auto_desactivar'])) : ?>
        <div class="usuarios-alerta usuarios-alerta--error">
            <i class="bi bi-shield-exclamation"></i>
            <p>No puedes desactivar tu propia cuenta.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['no_auto_rol'])) : ?>
        <div class="usuarios-alerta usuarios-alerta--error">
            <i class="bi bi-shield-exclamation"></i>
            <p>No puedes quitarte a ti mismo el rol de administrador.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['ultimo_admin'])) : ?>
        <div class="usuarios-alerta usuarios-alerta--error">
            <i class="bi bi-shield-lock"></i>
            <p>No puedes dejar el sistema sin al menos un administrador activo.</p>
        </div>
    <?php endif; ?>

    <div class="usuarios-resumen">
        <div class="usuarios-resumen__card">
            <span>Total usuarios</span>
            <strong><?php echo $resumen['total'] ?? 0; ?></strong>
        </div>

        <a href="/usuarios?pendientes=1" class="usuarios-resumen__card usuarios-resumen__card--link">
            <span>Pendientes de alta</span>
            <strong><?php echo $resumen['pendientes'] ?? 0; ?></strong>
        </a>

        <div class="usuarios-resumen__card">
            <span>Activos</span>
            <strong><?php echo $resumen['activos'] ?? 0; ?></strong>
        </div>

        <div class="usuarios-resumen__card">
            <span>Freelancers</span>
            <strong><?php echo $resumen['freelancers'] ?? 0; ?></strong>
        </div>

        <div class="usuarios-resumen__card">
            <span>Administradores</span>
            <strong><?php echo $resumen['administradores'] ?? 0; ?></strong>
        </div>
    </div>

    <form id="form-filtros-usuarios" class="usuarios__filtros" method="GET" action="/usuarios">
        <div class="usuarios__busqueda">
            <label for="busqueda">Buscar usuario</label>
            <input
                type="text"
                id="busqueda-usuarios"
                name="busqueda"
                placeholder="Nombre, apellido o email"
                value="<?php echo $filtros['busqueda'] ?? ''; ?>">
        </div>

        <div class="usuarios__select">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos</option>
                <option value="1" <?php echo (string) ($filtros['estado'] ?? '') === '1' ? 'selected' : ''; ?>>Activos</option>
                <option value="0" <?php echo (string) ($filtros['estado'] ?? '') === '0' ? 'selected' : ''; ?>>Inactivos/Pendientes</option>
            </select>
        </div>

        <div class="usuarios__select">
            <label for="confirmado">Confirmación</label>
            <select id="confirmado" name="confirmado">
                <option value="">Todos</option>
                <option value="1" <?php echo (string) ($filtros['confirmado'] ?? '') === '1' ? 'selected' : ''; ?>>Confirmados</option>
                <option value="0" <?php echo (string) ($filtros['confirmado'] ?? '') === '0' ? 'selected' : ''; ?>>Sin confirmar</option>
            </select>
        </div>

        <div class="usuarios__select">
            <label for="rol_id">Rol</label>
            <select id="rol_id" name="rol_id">
                <option value="">Todos</option>
                <option value="1" <?php echo (string) ($filtros['rol_id'] ?? '') === '1' ? 'selected' : ''; ?>>Usuario</option>
                <option value="2" <?php echo (string) ($filtros['rol_id'] ?? '') === '2' ? 'selected' : ''; ?>>Freelancer</option>
                <option value="3" <?php echo (string) ($filtros['rol_id'] ?? '') === '3' ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>

        <?php if (!empty($filtros['pendientes'])) : ?>
            <input type="hidden" name="pendientes" value="1">
        <?php endif; ?>
    </form>

    <?php if (!empty($filtros['busqueda']) || $filtros['estado'] !== '' || $filtros['confirmado'] !== '' || !empty($filtros['rol_id']) || !empty($filtros['pendientes'])) : ?>
        <div class="usuarios-filtro-alerta">
            <i class="bi bi-funnel"></i>
            <p>Mostrando usuarios según los filtros aplicados.</p>
            <a href="/usuarios">Limpiar filtros</a>
        </div>
    <?php endif; ?>

    <div class="usuarios-tabla">
        <?php if (!empty($usuarios)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Confirmación</th>
                        <th>Estado</th>
                        <th>Rol actual</th>
                        <th>Administrar acceso</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($usuarios as $usuario) : ?>
                        <?php
                        $nombreCompleto = trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? ''));
                        $nombreCompleto = $nombreCompleto ?: 'Usuario sin nombre';

                        $esActual = (int) $usuario->id === (int) ($_SESSION['id'] ?? 0);
                        ?>

                        <tr>
                            <td>
                                <div class="usuarios-tabla__usuario">
                                    <div class="usuarios-tabla__avatar">
                                        <?php if (!empty($usuario->avatar)) : ?>
                                            <img src="/uploads/avatars/<?php echo $usuario->avatar; ?>" alt="Avatar de usuario">
                                        <?php else : ?>
                                            <?php echo strtoupper(substr($usuario->nombre ?? 'U', 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <strong>
                                            <?php echo $nombreCompleto; ?>
                                            <?php if ($esActual) : ?>
                                                <small>Tú</small>
                                            <?php endif; ?>
                                        </strong>

                                        <span>ID: <?php echo $usuario->id; ?></span>
                                    </div>
                                </div>
                            </td>

                            <td><?php echo $usuario->email; ?></td>

                            <td>
                                <?php if ((int) $usuario->confirmado === 1) : ?>
                                    <span class="usuarios-badge usuarios-badge--success">Confirmado</span>
                                <?php else : ?>
                                    <span class="usuarios-badge usuarios-badge--warning">Sin confirmar</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ((int) $usuario->estado === 1) : ?>
                                    <span class="usuarios-badge usuarios-badge--success">Activo</span>
                                <?php else : ?>
                                    <span class="usuarios-badge usuarios-badge--muted">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                if ((int) $usuario->rol_id === 3) {
                                    echo '<span class="usuarios-badge usuarios-badge--admin">Administrador</span>';
                                } elseif ((int) $usuario->rol_id === 2) {
                                    echo '<span class="usuarios-badge usuarios-badge--primary">Freelancer</span>';
                                } else {
                                    echo '<span class="usuarios-badge usuarios-badge--muted">Usuario</span>';
                                }
                                ?>
                            </td>

                            <td>
                                <form class="usuarios-acciones" method="POST" action="/usuarios/actualizar">
                                    <input type="hidden" name="id" value="<?php echo $usuario->id; ?>">

                                    <select name="rol_id" <?php echo ((int) $usuario->confirmado !== 1) ? 'disabled' : ''; ?>>
                                        <option value="1" <?php echo (int) $usuario->rol_id === 1 ? 'selected' : ''; ?>>Usuario</option>
                                        <option value="2" <?php echo (int) $usuario->rol_id === 2 ? 'selected' : ''; ?>>Freelancer</option>
                                        <option value="3" <?php echo (int) $usuario->rol_id === 3 ? 'selected' : ''; ?>>Administrador</option>
                                    </select>

                                    <select name="estado" <?php echo ((int) $usuario->confirmado !== 1) ? 'disabled' : ''; ?>>
                                        <option value="0" <?php echo (int) $usuario->estado === 0 ? 'selected' : ''; ?>>Inactivo</option>
                                        <option value="1" <?php echo (int) $usuario->estado === 1 ? 'selected' : ''; ?>>Activo</option>
                                    </select>

                                    <button type="submit" <?php echo ((int) $usuario->confirmado !== 1) ? 'disabled' : ''; ?>>
                                        Guardar
                                    </button>
                                </form>

                                <?php if ((int) $usuario->confirmado !== 1) : ?>
                                    <p class="usuarios-acciones__nota">Debe confirmar su correo primero.</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="usuarios-empty">
                <i class="bi bi-people"></i>
                <h2>No se encontraron usuarios</h2>
                <p>No hay usuarios que coincidan con los filtros seleccionados.</p>
                <a href="/usuarios">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>