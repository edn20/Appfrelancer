<aside class="sidebar">
    <div class="sidebar__brand">
        <div class="sidebar__brand-icon">
            <i class="bi bi-briefcase"></i>
        </div>

        <p class="sidebar__brand-text">Freelance Manager EDN</p>
    </div>


    <nav class="sidebar__nav">
        <a class="sidebar__link <?php echo ($pagina ?? '') === 'dashboard' ? 'sidebar__link--activo' : ''; ?>" href="/dashboard">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <?php if ((int) ($_SESSION['rol_id'] ?? 0) === 3) : ?>
            <a href="/usuarios" class="sidebar__link <?php echo $pagina === 'usuarios' ? 'sidebar__link--activo' : ''; ?>">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
        <?php endif; ?>


        <a class="sidebar__link <?php echo ($pagina ?? '') === 'clientes' ? 'sidebar__link--activo' : ''; ?>" href="/clientes">
            <i class="bi bi-people"></i>
            <span>Clientes</span>
        </a>

        <a class="sidebar__link <?php echo ($pagina ?? '') === 'proyectos' ? 'sidebar__link--activo' : ''; ?>" href="/proyectos">
            <i class="bi bi-folder"></i>
            <span>Proyectos</span>
        </a>

        <a class="sidebar__link <?php echo ($pagina ?? '') === 'tareas' ? 'sidebar__link--activo' : ''; ?>" href="/tareas">
            <i class="bi bi-list-check"></i>
            <span>Tareas</span>
        </a>

        <a class="sidebar__link <?php echo ($pagina ?? '') === 'pagos' ? 'sidebar__link--activo' : ''; ?>" href="/pagos">
            <i class="bi bi-currency-dollar"></i>
            <span>Pagos</span>
        </a>

        <a class="sidebar__link <?php echo ($pagina ?? '') === 'notas' ? 'sidebar__link--activo' : ''; ?>" href="/notas">
            <i class="bi bi-journal-text"></i>
            <span>Notas</span>
        </a>

        <a class="sidebar__link <?php echo ($pagina ?? '') === 'reportes' ? 'sidebar__link--activo' : ''; ?>" href="/reportes">
            <i class="bi bi-bar-chart"></i>
            <span>Reportes</span>
        </a>

        <a class="sidebar__link <?php echo ($pagina ?? '') === 'configuracion' ? 'sidebar__link--activo' : ''; ?>" href="/configuracion">
            <i class="bi bi-gear"></i>
            <span>Configuración</span>
        </a>
    </nav>

    <div class="sidebar__bottom">
        <a class="sidebar__link js-cerrar-sesion" href="/logout">
            <i class="bi bi-box-arrow-left"></i>
            <span>Cerrar sesión</span>
        </a>
    </div>
</aside>