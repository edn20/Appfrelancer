<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;

class PerfilController
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

        $alertas = [];

        $rol = self::obtenerRol($usuario->rol_id ?? null);
        $estado = ((int) ($usuario->estado ?? 0) === 1) ? 'Activo' : 'Inactivo';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST;

            // El correo no se actualiza desde perfil
            unset($datos['email']);

            $usuario->sincronizar($datos);

            $alertas = self::validarPerfil($usuario);

            if (empty($alertas)) {
                $avatarAnterior = $usuario->avatar ?? null;

                if (!empty($_POST['avatar_base64'])) {
                    $resultadoAvatar = self::procesarAvatarBase64($_POST['avatar_base64'], $avatarAnterior);

                    if (!$resultadoAvatar['ok']) {
                        $alertas['error'][] = $resultadoAvatar['mensaje'];
                    } else {
                        $usuario->avatar = $resultadoAvatar['archivo'];
                    }
                }
            }

            if (empty($alertas)) {
                $resultado = $usuario->guardar();

                if ($resultado) {
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['apellido'] = $usuario->apellido;

                    if (!empty($usuario->avatar)) {
                        $_SESSION['avatar'] = $usuario->avatar;
                    }

                    header('Location: /perfil?actualizado=1');
                    exit;
                }

                $alertas['error'][] = 'No se pudo actualizar el perfil.';
            }
        }

        if (isset($_GET['actualizado'])) {
            $alertas['exito'][] = 'Perfil actualizado correctamente.';
        }

        $router->render('perfil/index', [
            'titulo' => 'Mi perfil',
            'pagina' => 'configuracion',
            'usuario' => $usuario,
            'rol' => $rol,
            'estado' => $estado,
            'alertas' => $alertas
        ]);
    }

    public static function password(Router $router)
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

        $alertas = [];

        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNuevo = $_POST['password_nuevo'] ?? '';
        $passwordConfirmar = $_POST['password_confirmar'] ?? '';

        if (!$passwordActual) {
            $alertas['error'][] = 'Ingresa tu contraseña actual.';
        }

        if (!$passwordNuevo) {
            $alertas['error'][] = 'Ingresa la nueva contraseña.';
        }

        if (!$passwordConfirmar) {
            $alertas['error'][] = 'Repite la nueva contraseña.';
        }

        if ($passwordNuevo && strlen($passwordNuevo) < 8) {
            $alertas['error'][] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        }

        if ($passwordNuevo && $passwordConfirmar && $passwordNuevo !== $passwordConfirmar) {
            $alertas['error'][] = 'Las nuevas contraseñas no coinciden.';
        }

        if (empty($alertas) && !password_verify($passwordActual, $usuario->password)) {
            $alertas['error'][] = 'La contraseña actual no es correcta.';
        }

        if (empty($alertas) && password_verify($passwordNuevo, $usuario->password)) {
            $alertas['error'][] = 'La nueva contraseña no puede ser igual a la contraseña actual.';
        }

        if (empty($alertas)) {
            $usuario->password = password_hash($passwordNuevo, PASSWORD_BCRYPT);

            $resultado = $usuario->guardar();

            if ($resultado) {
                $_SESSION = [];

                session_destroy();

                header('Location: /login?password_actualizada=1');
                exit;
            }

            $alertas['error'][] = 'No se pudo actualizar la contraseña.';
        }

        $rol = self::obtenerRol($usuario->rol_id ?? null);
        $estado = ((int) ($usuario->estado ?? 0) === 1) ? 'Activo' : 'Inactivo';

        $router->render('perfil/index', [
            'titulo' => 'Mi perfil',
            'pagina' => 'configuracion',
            'usuario' => $usuario,
            'rol' => $rol,
            'estado' => $estado,
            'alertas' => $alertas
        ]);
    }

    private static function validarPerfil(Usuario $usuario): array
    {
        $alertas = [];

        if (!$usuario->nombre) {
            $alertas['error'][] = 'El nombre es obligatorio.';
        }

        if (!$usuario->apellido) {
            $alertas['error'][] = 'El apellido es obligatorio.';
        }

        return $alertas;
    }



    private static function obtenerRol($rolId): string
    {
        return match ((int) $rolId) {
            2 => 'Freelancer',
            3 => 'Administrador',
            default => 'Usuario'
        };
    }

    private static function procesarAvatarBase64($avatarBase64, $avatarAnterior = null): array
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $avatarBase64)) {
            return [
                'ok' => false,
                'mensaje' => 'El formato del avatar no es válido.'
            ];
        }

        $avatarBase64 = preg_replace('/^data:image\/(jpeg|jpg|png|webp);base64,/', '', $avatarBase64);
        $avatarBase64 = str_replace(' ', '+', $avatarBase64);

        $imagenDecodificada = base64_decode($avatarBase64, true);

        if (!$imagenDecodificada) {
            return [
                'ok' => false,
                'mensaje' => 'No se pudo procesar la imagen.'
            ];
        }

        $maxSize = 2 * 1024 * 1024;

        if (strlen($imagenDecodificada) > $maxSize) {
            return [
                'ok' => false,
                'mensaje' => 'El avatar no debe superar los 2MB.'
            ];
        }

        $infoImagen = getimagesizefromstring($imagenDecodificada);

        if (!$infoImagen) {
            return [
                'ok' => false,
                'mensaje' => 'El archivo no corresponde a una imagen válida.'
            ];
        }

        $tiposPermitidos = [
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_WEBP
        ];

        if (!in_array($infoImagen[2], $tiposPermitidos)) {
            return [
                'ok' => false,
                'mensaje' => 'El avatar debe ser una imagen JPG, PNG o WEBP.'
            ];
        }

        $carpeta = __DIR__ . '/../public/uploads/avatars';

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nombreArchivo = md5(uniqid(rand(), true)) . '.jpg';
        $rutaDestino = $carpeta . '/' . $nombreArchivo;

        if (!file_put_contents($rutaDestino, $imagenDecodificada)) {
            return [
                'ok' => false,
                'mensaje' => 'No se pudo guardar la imagen.'
            ];
        }

        if ($avatarAnterior) {
            $rutaAnterior = $carpeta . '/' . $avatarAnterior;

            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

        return [
            'ok' => true,
            'archivo' => $nombreArchivo
        ];
    }
}
