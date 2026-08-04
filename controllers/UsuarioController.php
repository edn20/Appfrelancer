<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;

class UsuarioController
{
    public static function index(Router $router)
    {
        session_start();

        self::esAdmin();

        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'confirmado' => $_GET['confirmado'] ?? '',
            'rol_id' => $_GET['rol_id'] ?? '',
            'pendientes' => $_GET['pendientes'] ?? ''
        ];

        $usuarios = Usuario::visiblesParaAdmin($filtros);
        $resumen = Usuario::resumenUsuarios();

        $router->render('usuarios/index', [
            'titulo' => 'Gestión de usuarios',
            'pagina' => 'usuarios',
            'usuarios' => $usuarios,
            'resumen' => $resumen,
            'filtros' => $filtros
        ]);
    }

    public static function actualizar()
    {
        session_start();

        self::esAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /usuarios');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);

        $rolId = $_POST['rol_id'] ?? null;
        $rolId = filter_var($rolId, FILTER_VALIDATE_INT);

        $estado = $_POST['estado'] ?? null;
        $estado = filter_var($estado, FILTER_VALIDATE_INT);

        if (!$id || !$rolId || $estado === false || !in_array($rolId, [1, 2, 3]) || !in_array($estado, [0, 1])) {
            header('Location: /usuarios?error=1');
            exit;
        }

        $usuario = Usuario::find($id);

        if (!$usuario) {
            header('Location: /usuarios?error=1');
            exit;
        }

        if ((int) $usuario->confirmado !== 1 && (int) $estado === 1) {
            header('Location: /usuarios?sin_confirmar=1');
            exit;
        }

        if ((int) $usuario->id === (int) $_SESSION['id']) {
            if ((int) $estado === 0) {
                header('Location: /usuarios?no_auto_desactivar=1');
                exit;
            }

            if ((int) $rolId !== 3) {
                header('Location: /usuarios?no_auto_rol=1');
                exit;
            }
        }

        if ((int) $usuario->rol_id === 3 && (int) $rolId !== 3) {
            $totalAdmins = Usuario::totalAdministradoresActivos();

            if ($totalAdmins <= 1 && (int) $usuario->estado === 1) {
                header('Location: /usuarios?ultimo_admin=1');
                exit;
            }
        }

        if ((int) $usuario->rol_id === 3 && (int) $estado === 0) {
            $totalAdmins = Usuario::totalAdministradoresActivos();

            if ($totalAdmins <= 1) {
                header('Location: /usuarios?ultimo_admin=1');
                exit;
            }
        }

        $usuario->rol_id = $rolId;
        $usuario->estado = $estado;

        $resultado = $usuario->guardar();

        if ($resultado) {
            header('Location: /usuarios?actualizado=1');
            exit;
        }

        header('Location: /usuarios?error=1');
        exit;
    }

    private static function esAdmin()
    {
        if (
            !isset($_SESSION['login']) ||
            $_SESSION['login'] !== true ||
            (int) ($_SESSION['rol_id'] ?? 0) !== 3
        ) {
            header('Location: /dashboard');
            exit;
        }
    }
}
