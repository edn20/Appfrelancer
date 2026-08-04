<?php include_once __DIR__ . '/../templates/dashboard-header.php'; ?>

<section class="notas-page">
    <div class="notas-page__top">
        <div>
            <h1>Notas</h1>
            <p>Notas rápidas protegidas de clientes y proyectos.</p>
        </div>

        <a href="/notas/crear">
            <i class="bi bi-plus-circle"></i>
            Nueva nota
        </a>
    </div>

    <form id="form-filtros-notas" method="GET" action="/notas" class="notas-filtros">
        <input type="hidden" name="page" value="1">

        <div class="notas__busqueda">
            <i class="bi bi-search"></i>

            <input
                type="text"
                id="busqueda-notas"
                name="busqueda"
                placeholder="Buscar nota, cliente o proyecto..."
                autocomplete="off"
                value="<?php echo $filtros['busqueda'] ?? ''; ?>">
        </div>

        <div class="notas__select">
            <label for="cliente_id">Cliente</label>

            <select id="cliente_id" name="cliente_id">
                <option value="">Todos los clientes</option>

                <?php foreach ($clientes ?? [] as $cliente) : ?>
                    <?php
                    $nombreCliente = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));

                    if (!empty($cliente->empresa)) {
                        $nombreCliente .= $nombreCliente
                            ? ' - ' . $cliente->empresa
                            : $cliente->empresa;
                    }

                    $nombreCliente = $nombreCliente ?: 'Cliente sin nombre';
                    ?>

                    <option
                        value="<?php echo $cliente->id; ?>"
                        <?php echo (string) ($filtros['cliente_id'] ?? '') === (string) $cliente->id ? 'selected' : ''; ?>>
                        <?php echo $nombreCliente; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="notas__select">
            <label for="proyecto_id">Proyecto</label>

            <select id="proyecto_id" name="proyecto_id">
                <option value="">Todos los proyectos</option>

                <?php foreach ($proyectos ?? [] as $proyecto) : ?>
                    <option
                        value="<?php echo $proyecto->id; ?>"
                        <?php echo (string) ($filtros['proyecto_id'] ?? '') === (string) $proyecto->id ? 'selected' : ''; ?>>
                        <?php echo $proyecto->nombre; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="notas__select">
            <label for="color">Color</label>

            <select id="color" name="color">
                <option value="">Todos los colores</option>
                <option value="amarillo" <?php echo ($filtros['color'] ?? '') === 'amarillo' ? 'selected' : ''; ?>>Amarillo</option>
                <option value="azul" <?php echo ($filtros['color'] ?? '') === 'azul' ? 'selected' : ''; ?>>Azul</option>
                <option value="verde" <?php echo ($filtros['color'] ?? '') === 'verde' ? 'selected' : ''; ?>>Verde</option>
                <option value="rosa" <?php echo ($filtros['color'] ?? '') === 'rosa' ? 'selected' : ''; ?>>Rosa</option>
                <option value="gris" <?php echo ($filtros['color'] ?? '') === 'gris' ? 'selected' : ''; ?>>Gris</option>
            </select>
        </div>
    </form>

    <?php
    $hayBusqueda = !empty($filtros['busqueda']);
    $hayCliente = !empty($filtros['cliente_id']);
    $hayProyecto = !empty($filtros['proyecto_id']);
    $hayColor = !empty($filtros['color']);

    $hayFiltros = $hayBusqueda || $hayCliente || $hayProyecto || $hayColor;

    $nombreClienteFiltro = '';
    $nombreProyectoFiltro = '';

    if ($hayCliente && !empty($clientes)) {
        foreach ($clientes as $cliente) {
            if ((string) $cliente->id === (string) $filtros['cliente_id']) {
                $nombreClienteFiltro = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));

                if (!empty($cliente->empresa)) {
                    $nombreClienteFiltro .= $nombreClienteFiltro
                        ? ' - ' . $cliente->empresa
                        : $cliente->empresa;
                }

                $nombreClienteFiltro = $nombreClienteFiltro ?: 'Cliente seleccionado';
                break;
            }
        }
    }

    if ($hayProyecto && !empty($proyectos)) {
        foreach ($proyectos as $proyecto) {
            if ((string) $proyecto->id === (string) $filtros['proyecto_id']) {
                $nombreProyectoFiltro = $proyecto->nombre;
                break;
            }
        }
    }
    ?>

    <?php if ($hayFiltros) : ?>
        <?php if (!empty($notas)) : ?>
            <div class="notas-filtro-alerta notas-filtro-alerta--success">
                <i class="bi bi-check-circle"></i>

                <p>
                    Mostrando notas filtradas
                    <?php if ($hayBusqueda) : ?>
                        por búsqueda <strong>"<?php echo $filtros['busqueda']; ?>"</strong>
                    <?php endif; ?>

                    <?php if ($hayCliente && $nombreClienteFiltro) : ?>
                        del cliente <strong><?php echo $nombreClienteFiltro; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayProyecto && $nombreProyectoFiltro) : ?>
                        del proyecto <strong><?php echo $nombreProyectoFiltro; ?></strong>
                    <?php endif; ?>

                    <?php if ($hayColor) : ?>
                        con color <strong><?php echo ucfirst($filtros['color']); ?></strong>
                        <?php endif; ?>.
                </p>

                <a href="/notas">Limpiar filtros</a>
            </div>
        <?php else : ?>
            <div class="notas-filtro-alerta notas-filtro-alerta--empty">
                <i class="bi bi-info-circle"></i>

                <p>No se encontraron notas con los filtros seleccionados.</p>

                <a href="/notas">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (empty($notas)) : ?>
        <div class="notas-empty">
            <i class="bi bi-sticky"></i>
            <h2>No tienes notas registradas</h2>
            <p>Crea una nota rápida para guardar puntos importantes.</p>
        </div>
    <?php else : ?>

        <?php foreach ($notasPorCliente as $grupo) : ?>
            <section class="notas-grupo">
                <div class="notas-grupo__header">
                    <div>
                        <h2><?php echo $grupo['cliente_nombre']; ?></h2>

                        <?php if (!empty($grupo['cliente_empresa'])) : ?>
                            <p><?php echo $grupo['cliente_empresa']; ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($grupo['cliente_id'])) : ?>
                        <a href="/notas/crear?cliente_id=<?php echo $grupo['cliente_id']; ?>">
                            <i class="bi bi-plus-lg"></i>
                            Nueva nota
                        </a>
                    <?php endif; ?>
                </div>

                <div class="notas-grid">
                    <?php foreach ($grupo['notas'] as $nota) : ?>
                        <article class="nota-sticky nota-sticky--<?php echo $nota->color; ?>">
                            <?php if ((int) $nota->fija === 1) : ?>
                                <span class="nota-sticky__pin">
                                    <i class="bi bi-pin-angle-fill"></i>
                                </span>
                            <?php endif; ?>

                            <h2><?php echo $nota->titulo; ?></h2>

                            <p class="nota-sticky__contenido">
                                <?php
                                $contenido = strip_tags($nota->contenido);
                                echo nl2br(strlen($contenido) > 180 ? substr($contenido, 0, 180) . '...' : $contenido);
                                ?>
                            </p>

                            <div class="nota-sticky__meta">
                                <?php if (!empty($nota->proyecto_nombre)) : ?>
                                    <span>
                                        <i class="bi bi-folder"></i>
                                        <?php echo $nota->proyecto_nombre; ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($grupo['cliente_nombre']) && $grupo['cliente_nombre'] !== 'Notas generales') : ?>
                                    <span>
                                        <i class="bi bi-person"></i>
                                        <?php echo $grupo['cliente_nombre']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="nota-sticky__acciones">
                                <a href="/notas/detalle?id=<?php echo $nota->id; ?>">
                                    Ver nota
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <div class="notas__footer">
            <p>
                Mostrando
                <?php echo $paginacion->inicio(); ?>
                a
                <?php echo $paginacion->fin(); ?>
                de
                <?php echo $paginacion->totalRegistros; ?>
                notas
            </p>

            <?php echo $paginacion->selectorPorPagina('form-filtros-notas'); ?>

            <?php if ($paginacion->totalPaginas() > 1) : ?>
                <div class="notas__paginacion">

                    <?php if ($paginacion->paginaAnterior()) : ?>
                        <a href="<?php echo $paginacion->crearUrl($paginacion->paginaAnterior()); ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php
                    $paginaActual = (int) $paginacion->paginaActual;
                    $totalPaginas = (int) $paginacion->totalPaginas();

                    $paginasVisibles = [];

                    if ($paginaActual > 1) {
                        $paginasVisibles[] = $paginaActual - 1;
                    }

                    $paginasVisibles[] = $paginaActual;

                    if ($paginaActual < $totalPaginas) {
                        $paginasVisibles[] = $paginaActual + 1;
                    }
                    ?>

                    <?php foreach ($paginasVisibles as $numeroPagina) : ?>
                        <?php if ($numeroPagina === $paginaActual) : ?>
                            <span class="notas__paginacion-activo">
                                <?php echo $numeroPagina; ?>
                            </span>
                        <?php else : ?>
                            <a href="<?php echo $paginacion->crearUrl($numeroPagina); ?>">
                                <?php echo $numeroPagina; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($paginacion->paginaSiguiente()) : ?>
                        <a href="<?php echo $paginacion->crearUrl($paginacion->paginaSiguiente()); ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</section>

<?php include_once __DIR__ . '/../templates/dashboard-footer.php'; ?>