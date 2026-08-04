<?php

namespace Controllers;

use MVC\Router;
use Model\Notificacion;

class NotificacionController
{
    public static function index(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $notificaciones = Notificacion::obtenerPorUsuario($usuarioId);
        $resumen = Notificacion::resumenPorUsuario($usuarioId);

        $router->render('notificaciones/index', [
            'titulo' => 'Notificaciones',
            'pagina' => 'notificaciones',
            'notificaciones' => $notificaciones,
            'resumen' => $resumen
        ]);
    }
}
