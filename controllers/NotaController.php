<?php

namespace Controllers;

use MVC\Router;
use Model\Nota;
use Model\Proyecto;
use Model\Cliente;
use Model\Usuario;
use Classes\Paginacion;

class NotaController
{
    private static function validarSesion()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        return $_SESSION['id'];
    }

    private static function notasDesbloqueadas()
    {
        if (!isset($_SESSION['notas_desbloqueadas'])) {
            return false;
        }

        if (!isset($_SESSION['notas_desbloqueadas_expira'])) {
            return false;
        }

        if (time() > $_SESSION['notas_desbloqueadas_expira']) {
            unset($_SESSION['notas_desbloqueadas']);
            unset($_SESSION['notas_desbloqueadas_expira']);
            return false;
        }

        return true;
    }

    private static function exigirNotasDesbloqueadas($proyectoId = null, $clienteId = null)
    {
        $notasBaseDesbloqueadas =
            isset($_SESSION['notas_desbloqueadas']) &&
            $_SESSION['notas_desbloqueadas'] === true &&
            isset($_SESSION['notas_desbloqueadas_expira']) &&
            $_SESSION['notas_desbloqueadas_expira'] > time();

        if (!$notasBaseDesbloqueadas) {
            header('Location: /notas/desbloquear');
            exit;
        }

        // Si se desbloqueó el módulo completo de notas, puede navegar y filtrar dentro de /notas
        if (
            isset($_SESSION['notas_modulo_desbloqueado']) &&
            $_SESSION['notas_modulo_desbloqueado'] === true
        ) {
            return;
        }

        // Si viene desde detalle de proyecto, solo permite ese proyecto
        if ($proyectoId) {
            $proyectoDesbloqueado =
                isset($_SESSION['notas_proyecto_desbloqueado_id']) &&
                (int) $_SESSION['notas_proyecto_desbloqueado_id'] === (int) $proyectoId;

            if (!$proyectoDesbloqueado) {
                header('Location: /notas/desbloquear');
                exit;
            }

            return;
        }

        // Si viene desde detalle de cliente, solo permite ese cliente
        if ($clienteId) {
            $clienteDesbloqueado =
                isset($_SESSION['notas_cliente_desbloqueado_id']) &&
                (int) $_SESSION['notas_cliente_desbloqueado_id'] === (int) $clienteId;

            if (!$clienteDesbloqueado) {
                header('Location: /notas/desbloquear');
                exit;
            }

            return;
        }

        header('Location: /notas/desbloquear');
        exit;
    }

    private static function agruparNotasPorCliente($notas)
    {
        $grupos = [];

        foreach ($notas as $nota) {
            $clienteId = $nota->cliente_id ?? 0;

            $clienteNombre = trim(($nota->cliente_nombre ?? '') . ' ' . ($nota->cliente_apellido ?? ''));

            if (!$clienteNombre && !empty($nota->cliente_empresa)) {
                $clienteNombre = $nota->cliente_empresa;
            }

            if (!$clienteNombre) {
                $clienteNombre = 'Notas generales';
            }

            if (!isset($grupos[$clienteId])) {
                $grupos[$clienteId] = [
                    'cliente_id' => $clienteId,
                    'cliente_nombre' => $clienteNombre,
                    'cliente_empresa' => $nota->cliente_empresa ?? '',
                    'notas' => []
                ];
            }

            $grupos[$clienteId]['notas'][] = $nota;
        }

        return $grupos;
    }

    public static function desbloquear(Router $router)
    {
        $usuarioId = self::validarSesion();

        $error = $_SESSION['error_notas'] ?? null;
        unset($_SESSION['error_notas']);

        $router->render('notas/desbloquear', [
            'titulo' => 'Desbloquear notas',
            'pagina' => 'notas',
            'error' => $error
        ]);
    }

    public static function verificar()
    {
        $usuarioId = self::validarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /notas/desbloquear');
            exit;
        }

        $password = $_POST['password'] ?? '';

        if (!$password) {
            $_SESSION['error_notas'] = 'Debes ingresar tu contraseña.';
            header('Location: /notas/desbloquear');
            exit;
        }

        $usuario = Usuario::find($usuarioId);

        if (!$usuario || !password_verify($password, $usuario->password)) {
            $_SESSION['error_notas'] = 'La contraseña ingresada no es correcta.';
            header('Location: /notas/desbloquear');
            exit;
        }

        $_SESSION['notas_desbloqueadas'] = true;
        $_SESSION['notas_desbloqueadas_expira'] = time() + 600;

        // Este desbloqueo es para todo el módulo Notas
        $_SESSION['notas_modulo_desbloqueado'] = true;

        // Limpiamos desbloqueos contextuales para evitar conflicto
        unset($_SESSION['notas_proyecto_desbloqueado_id']);
        unset($_SESSION['notas_cliente_desbloqueado_id']);

        header('Location: /notas');
        exit;
    }

    public static function index(Router $router)
    {
        session_start();

        self::validarSesion();

        $usuarioId = $_SESSION['id'];

        $clienteId = $_GET['cliente_id'] ?? '';
        $clienteId = $clienteId ? filter_var($clienteId, FILTER_VALIDATE_INT) : '';

        $proyectoId = $_GET['proyecto_id'] ?? '';
        $proyectoId = $proyectoId ? filter_var($proyectoId, FILTER_VALIDATE_INT) : '';

        self::exigirNotasDesbloqueadas($proyectoId, $clienteId);


        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'cliente_id' => $clienteId,
            'proyecto_id' => $proyectoId,
            'color' => $_GET['color'] ?? ''
        ];

        $clienteSeleccionado = null;
        $proyectoSeleccionado = null;

        if ($clienteId) {
            $clienteSeleccionado = Cliente::find($clienteId);

            if (!$clienteSeleccionado || (int) $clienteSeleccionado->usuario_id !== (int) $usuarioId) {
                header('Location: /notas');
                exit;
            }
        }

        if ($proyectoId) {
            $proyectoSeleccionado = Proyecto::find($proyectoId);

            if (!$proyectoSeleccionado || (int) $proyectoSeleccionado->usuario_id !== (int) $usuarioId) {
                header('Location: /notas');
                exit;
            }
        }

        $totalNotas = Nota::totalVisiblesPorUsuario($usuarioId, $filtros);

        $paginacion = new Paginacion(
            $_GET['page'] ?? 1,
            $_GET['per_page'] ?? 10,
            $totalNotas
        );

        if ($paginacion->paginaFueraDeRango()) {
            $query = $_GET;
            $query['page'] = 1;
            $query['per_page'] = $paginacion->registrosPorPagina;

            header('Location: /notas?' . http_build_query($query));
            exit;
        }

        $notas = Nota::visiblesPorUsuario(
            $usuarioId,
            $filtros,
            $paginacion->registrosPorPagina,
            $paginacion->offset()
        );

        $clientes = Cliente::visiblesPorUsuario($usuarioId);
        $proyectos = Proyecto::visiblesPorUsuario($usuarioId);

        $notasPorCliente = self::agruparNotasPorCliente($notas);

        $router->render('notas/index', [
            'titulo' => 'Notas',
            'pagina' => 'notas',
            'notas' => $notas,
            'notasPorCliente' => $notasPorCliente,
            'clientes' => $clientes,
            'proyectos' => $proyectos,
            'filtros' => $filtros,
            'clienteSeleccionado' => $clienteSeleccionado,
            'proyectoSeleccionado' => $proyectoSeleccionado,
            'paginacion' => $paginacion
        ]);
    }

    public static function crear(Router $router)
    {
        $usuarioId = self::validarSesion();

        $proyectoId = $_GET['proyecto_id'] ?? null;
        $proyectoId = $proyectoId ? filter_var($proyectoId, FILTER_VALIDATE_INT) : null;

        $proyectoSeleccionado = null;
        $clienteId = null;

        if ($proyectoId) {
            $proyectoSeleccionado = Proyecto::detallePorUsuario($proyectoId, $usuarioId);

            if (!$proyectoSeleccionado) {
                header('Location: /proyectos');
                exit;
            }

            $clienteId = $proyectoSeleccionado->cliente_id;
        }

        $nota = new Nota();

        $proyectos = Proyecto::visiblesPorUsuario($usuarioId);
        $clientes = Cliente::visiblesPorUsuario($usuarioId);

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'usuario_id' => $usuarioId,
                'proyecto_id' => $proyectoSeleccionado ? $proyectoSeleccionado->id : ($_POST['proyecto_id'] ?? null),
                'cliente_id' => $proyectoSeleccionado ? $clienteId : ($_POST['cliente_id'] ?? null),
                'titulo' => $_POST['titulo'] ?? '',
                'contenido' => $_POST['contenido'] ?? '',
                'color' => $_POST['color'] ?? 'amarillo',
                'fija' => isset($_POST['fija']) ? 1 : 0,
                'protegida' => 1,
                'eliminado' => 0
            ];

            if ($datos['proyecto_id'] === '') {
                $datos['proyecto_id'] = null;
            }

            if ($datos['cliente_id'] === '') {
                $datos['cliente_id'] = null;
            }

            $nota = new Nota($datos);
            $alertas = $nota->validar();

            if (empty($alertas)) {
                $resultado = $nota->guardar();

                if ($resultado) {
                    if ($proyectoSeleccionado) {
                        header('Location: /proyectos/detalle?id=' . $proyectoSeleccionado->id);
                    } else {
                        header('Location: /notas/desbloquear');
                    }

                    exit;
                }
            }
        }

        $router->render('notas/crear', [
            'titulo' => 'Crear nota',
            'pagina' => 'notas',
            'nota' => $nota,
            'proyectos' => $proyectos,
            'clientes' => $clientes,
            'proyectoSeleccionado' => $proyectoSeleccionado,
            'alertas' => $alertas
        ]);
    }

    public static function detalle(Router $router)
    {
        $usuarioId = self::validarSesion();
        self::exigirNotasDesbloqueadas();

        $id = $_GET['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /notas');
            exit;
        }

        $nota = Nota::detallePorUsuario($id, $usuarioId);

        if (!$nota) {
            header('Location: /notas');
            exit;
        }

        $error = $_SESSION['error_nota_detalle'] ?? null;
        $exito = $_SESSION['exito_nota_detalle'] ?? null;

        unset($_SESSION['error_nota_detalle']);
        unset($_SESSION['exito_nota_detalle']);

        $router->render('notas/detalle', [
            'titulo' => 'Detalle nota',
            'pagina' => 'notas',
            'nota' => $nota,
            'error' => $error,
            'exito' => $exito
        ]);
    }

    public static function actualizar()
    {
        $usuarioId = self::validarSesion();
        self::exigirNotasDesbloqueadas();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /notas');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /notas');
            exit;
        }

        $nota = Nota::detallePorUsuario($id, $usuarioId);

        if (!$nota) {
            header('Location: /notas');
            exit;
        }

        $passwordConfirmacion = $_POST['password_confirmacion'] ?? '';

        if (!$passwordConfirmacion) {
            $_SESSION['error_nota_detalle'] = 'Debes ingresar tu contraseña para modificar la nota.';
            header('Location: /notas/detalle?id=' . $nota->id);
            exit;
        }

        $usuario = Usuario::find($usuarioId);

        if (!$usuario || !password_verify($passwordConfirmacion, $usuario->password)) {
            $_SESSION['error_nota_detalle'] = 'La contraseña ingresada no es correcta.';
            header('Location: /notas/detalle?id=' . $nota->id);
            exit;
        }

        $datos = [
            'usuario_id' => $usuarioId,
            'proyecto_id' => $nota->proyecto_id ?: null,
            'cliente_id' => $nota->cliente_id ?: null,
            'titulo' => $_POST['titulo'] ?? '',
            'contenido' => $_POST['contenido'] ?? '',
            'color' => $_POST['color'] ?? 'amarillo',
            'fija' => isset($_POST['fija']) ? 1 : 0,
            'protegida' => 1,
            'eliminado' => 0
        ];

        $nota->sincronizar($datos);

        $alertas = $nota->validarActualizacion();

        if (!empty($alertas)) {
            $_SESSION['error_nota_detalle'] = implode(' ', $alertas['error'] ?? ['No se pudo actualizar la nota.']);
            header('Location: /notas/detalle?id=' . $nota->id);
            exit;
        }

        $resultado = $nota->guardar();

        if ($resultado) {
            $_SESSION['exito_nota_detalle'] = 'Nota actualizada correctamente.';
        } else {
            $_SESSION['error_nota_detalle'] = 'No se pudo actualizar la nota.';
        }

        header('Location: /notas/detalle?id=' . $nota->id);
        exit;
    }

    public static function eliminar()
    {
        $usuarioId = self::validarSesion();
        self::exigirNotasDesbloqueadas();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /notas');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /notas');
            exit;
        }

        $nota = Nota::detallePorUsuario($id, $usuarioId);

        if (!$nota) {
            header('Location: /notas');
            exit;
        }

        $passwordConfirmacion = $_POST['password_confirmacion'] ?? '';

        if (!$passwordConfirmacion) {
            $_SESSION['error_nota_detalle'] = 'Debes ingresar tu contraseña para eliminar la nota.';
            header('Location: /notas/detalle?id=' . $nota->id);
            exit;
        }

        $usuario = Usuario::find($usuarioId);

        if (!$usuario || !password_verify($passwordConfirmacion, $usuario->password)) {
            $_SESSION['error_nota_detalle'] = 'La contraseña ingresada no es correcta.';
            header('Location: /notas/detalle?id=' . $nota->id);
            exit;
        }

        $resultado = Nota::eliminarLogico($nota->id, $usuarioId);

        if ($resultado) {
            $_SESSION['exito_notas'] = 'Nota eliminada correctamente.';
            header('Location: /notas');
            exit;
        }

        $_SESSION['error_nota_detalle'] = 'No se pudo eliminar la nota.';
        header('Location: /notas/detalle?id=' . $nota->id);
        exit;
    }

    public static function desbloquearAjax()
    {
        session_start();

        header('Content-Type: application/json');

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Sesión no válida.'
            ]);
            exit;
        }

        $password = $_POST['password'] ?? '';

        $proyectoId = $_POST['proyecto_id'] ?? null;
        $proyectoId = $proyectoId ? filter_var($proyectoId, FILTER_VALIDATE_INT) : null;

        $clienteId = $_POST['cliente_id'] ?? null;
        $clienteId = $clienteId ? filter_var($clienteId, FILTER_VALIDATE_INT) : null;

        if (!$password) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Ingresa tu contraseña.'
            ]);
            exit;
        }

        if (!$proyectoId && !$clienteId) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'No se pudo identificar el origen de las notas.'
            ]);
            exit;
        }

        $usuario = \Model\Usuario::find($_SESSION['id']);

        if (!$usuario) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Usuario no encontrado.'
            ]);
            exit;
        }

        if (!password_verify($password, $usuario->password)) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'La contraseña ingresada no es correcta.'
            ]);
            exit;
        }

        $_SESSION['notas_desbloqueadas'] = true;
        $_SESSION['notas_desbloqueadas_expira'] = time() + 600;

        // Este desbloqueo viene desde cliente/proyecto, no desde el módulo completo
        unset($_SESSION['notas_modulo_desbloqueado']);
        unset($_SESSION['notas_proyecto_desbloqueado_id']);
        unset($_SESSION['notas_cliente_desbloqueado_id']);

        if ($proyectoId) {
            $_SESSION['notas_proyecto_desbloqueado_id'] = $proyectoId;
        }

        if ($clienteId) {
            $_SESSION['notas_cliente_desbloqueado_id'] = $clienteId;
        }

        echo json_encode([
            'ok' => true,
            'mensaje' => 'Notas desbloqueadas correctamente.',
            'proyecto_id' => $proyectoId,
            'cliente_id' => $clienteId
        ]);
        exit;
    }

    public static function bloquearAjax()
    {
        session_start();

        header('Content-Type: application/json');

        unset($_SESSION['notas_desbloqueadas']);
        unset($_SESSION['notas_desbloqueadas_expira']);
        unset($_SESSION['notas_proyecto_desbloqueado_id']);
        unset($_SESSION['notas_cliente_desbloqueado_id']);
        unset($_SESSION['notas_modulo_desbloqueado']);

        echo json_encode([
            'ok' => true,
            'mensaje' => 'Notas bloqueadas.'
        ]);
        exit;
    }
}
