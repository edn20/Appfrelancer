<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Email;

class AuthController
{
    public static function login(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            $configuracionPreferencias = \Model\ConfiguracionPreferencia::porUsuario($usuarioExiste->id);

            if (empty($alertas)) {
                $usuarioExiste = Usuario::where('email', $usuario->email);

                if (!$usuarioExiste) {
                    Usuario::setAlerta('error', 'El usuario no existe');
                } else if (!password_verify($usuario->password, $usuarioExiste->password)) {
                    Usuario::setAlerta('error', 'La contraseña es incorrecta');
                } else if ($usuarioExiste->confirmado != 1) {
                    Usuario::setAlerta('error', 'Debes confirmar tu correo electrónico antes de iniciar sesión');
                } else if ($usuarioExiste->estado != 1) {
                    Usuario::setAlerta('error', 'Tu cuenta está confirmada, pero aún no ha sido dada de alta por el administrador');
                } else if (!in_array((int) $usuarioExiste->rol_id, [2, 3])) {
                    Usuario::setAlerta('error', 'Tu cuenta aún no tiene permisos para ingresar al sistema');
                } else {
                    session_start();

                    $_SESSION['id'] = $usuarioExiste->id;
                    $_SESSION['nombre'] = $usuarioExiste->nombre;
                    $_SESSION['apellido'] = $usuarioExiste->apellido;
                    $_SESSION['email'] = $usuarioExiste->email;
                    $_SESSION['rol_id'] = $usuarioExiste->rol_id;
                    $_SESSION['avatar'] = $usuarioExiste->avatar ?? '';
                    $configuracionPreferencias = \Model\ConfiguracionPreferencia::porUsuario($usuarioExiste->id);
                    $_SESSION['formato_fecha'] = $configuracionPreferencias->formato_fecha ?? 'dd_mm_yyyy';
                    $_SESSION['login'] = true;


                    header('Location: /dashboard');
                    exit;
                }
            }

            $alertas = Usuario::getAlertas();
        }

        $router->render('auth/login', [
            'titulo' => 'Iniciar sesión',
            'alertas' => $alertas
        ]);
    }

    public static function registro(Router $router)
    {
        $alertas = [];
        $usuario = new Usuario();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'apellido' => $_POST['apellido'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'password2' => $_POST['password2'] ?? ''
            ];

            $usuario = new Usuario($datos);
            $alertas = $usuario->validar_cuenta();

            if (empty($alertas)) {
                $usuarioExiste = Usuario::where('email', $usuario->email);

                if ($usuarioExiste) {
                    Usuario::setAlerta('error', 'El correo electrónico ya está registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    $usuario->hashPassword();
                    $usuario->crearToken();

                    $usuario->confirmado = 0;
                    $usuario->rol_id = 1;
                    $usuario->estado = 0;

                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        $email = new Email(
                            $usuario->email,
                            $usuario->nombre,
                            $usuario->token
                        );

                        $email->enviarConfirmacion();

                        header('Location: /mensaje');
                        exit;
                    } else {
                        Usuario::setAlerta('error', 'No se pudo guardar el usuario en la base de datos');
                        $alertas = Usuario::getAlertas();
                    }
                }
            }
        }

        $router->render('auth/registro', [
            'titulo' => 'Crear cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada'
        ]);
    }

    public static function confirmar(Router $router)
    {
        $token = $_GET['token'] ?? '';

        if (!$token) {
            Usuario::setAlerta('error', 'Token no válido');

            $alertas = Usuario::getAlertas();

            $router->render('auth/confirmar', [
                'titulo' => 'Confirmar cuenta',
                'alertas' => $alertas
            ]);

            return;
        }

        $usuario = Usuario::where('token', $token);

        if (!$usuario) {
            Usuario::setAlerta('error', 'Token no válido o cuenta ya confirmada');

            $alertas = Usuario::getAlertas();

            $router->render('auth/confirmar', [
                'titulo' => 'Confirmar cuenta',
                'alertas' => $alertas
            ]);

            return;
        }

        $usuario->confirmado = 1;
        $usuario->token = ''; // Pendiente de aprobación por administrador
        $usuario->guardar();

        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['alta_nombre'] = $usuario->nombre;
        $_SESSION['alta_apellido'] = $usuario->apellido;
        $_SESSION['alta_email'] = $usuario->email;

        header('Location: /solicitar-alta');
        exit;
    }

    public static function solicitarAlta(Router $router)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $nombre = $_SESSION['alta_nombre'] ?? 'Usuario';
        $apellido = $_SESSION['alta_apellido'] ?? '';
        $email = $_SESSION['alta_email'] ?? '';

        $telefono = '593959435217';

        $mensaje = "Hola, soy {$nombre} {$apellido}. Ya confirmé mi cuenta en Freelance Manager EDN con el correo {$email}. Por favor, solicito que me den de alta en el sistema para poder acceder al dashboard.";

        $whatsappUrl = 'https://wa.me/' . $telefono . '?text=' . urlencode($mensaje);

        $router->render('auth/solicitar-alta', [
            'titulo' => 'Solicitar alta',
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'whatsappUrl' => $whatsappUrl
        ]);
    }

    public static function logout()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: /login');
        exit;
    }
}
