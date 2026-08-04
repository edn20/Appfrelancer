<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\ConfiguracionNotificacion;
use Model\ConfiguracionPreferencia;

class ConfiguracionController
{
    public static function index(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuario = Usuario::find($_SESSION['id']);

        if (!$usuario) {
            header('Location: /login');
            exit;
        }

        $rol = 'Usuario';

        if ((int) ($usuario->rol_id ?? 0) === 2) {
            $rol = 'Freelancer';
        }

        if ((int) ($usuario->rol_id ?? 0) === 3) {
            $rol = 'Administrador';
        }

        $configuracionNotificaciones = ConfiguracionNotificacion::porUsuario($_SESSION['id']);
        $configuracionPreferencias = ConfiguracionPreferencia::porUsuario($_SESSION['id']);
        $formatosFecha = ConfiguracionPreferencia::formatosFecha();

        $router->render('configuracion/index', [
            'titulo' => 'Configuración',
            'pagina' => 'configuracion',
            'usuario' => $usuario,
            'rol' => $rol,
            'configuracionNotificaciones' => $configuracionNotificaciones,
            'configuracionPreferencias' => $configuracionPreferencias,
            'formatosFecha' => $formatosFecha
        ]);
    }

    public static function notificaciones()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $configuracion = ConfiguracionNotificacion::porUsuario($_SESSION['id']);

        $campos = [
            'tareas_vencidas',
            'tareas_hoy',
            'tareas_proximas',
            'pagos_vencidos',
            'pagos_proximos',
            'proyectos_atrasados',
            'proyectos_proximos',
            'obligaciones_proximas'
        ];

        foreach ($campos as $campo) {
            $configuracion->$campo = isset($_POST[$campo]) ? 1 : 0;
        }

        $resultado = $configuracion->guardar();

        if ($resultado) {
            header('Location: /configuracion?notificaciones=1#notificaciones');
            exit;
        }

        header('Location: /configuracion?error_notificaciones=1#notificaciones');
        exit;
    }

    public static function preferencias()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $configuracion = ConfiguracionPreferencia::porUsuario($_SESSION['id']);

        $formatoFecha = $_POST['formato_fecha'] ?? 'dd_mm_yyyy';

        if (!ConfiguracionPreferencia::formatoValido($formatoFecha)) {
            header('Location: /configuracion?error_preferencias=1#preferencias');
            exit;
        }

        $configuracion->formato_fecha = $formatoFecha;

        $resultado = $configuracion->guardar();

        if ($resultado) {
            $_SESSION['formato_fecha'] = $formatoFecha;

            header('Location: /configuracion?preferencias=1#preferencias');
            exit;
        }

        header('Location: /configuracion?error_preferencias=1#preferencias');
        exit;
    }
}
