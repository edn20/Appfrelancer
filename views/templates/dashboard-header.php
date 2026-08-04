<div class="panel">
    <?php include_once __DIR__ . '/sidebar.php'; ?>
    <div class="panel__contenido">

        <?php

        use Model\Notificacion;

        $notificacionesTopbar = [];
        $totalNotificacionesTopbar = 0;

        if (isset($_SESSION['login']) && $_SESSION['login'] === true && isset($_SESSION['id'])) {
            $notificacionesTopbar = Notificacion::obtenerTopbarPorUsuario($_SESSION['id']);
            $totalNotificacionesTopbar = Notificacion::totalPorUsuario($_SESSION['id']);
        }
        ?>

        <?php include_once __DIR__ . '/topbar.php'; ?>

        <main class="panel__main">