<?php

namespace Controllers;

use MVC\Router;
use Model\Cliente;
use Classes\Paginacion;

class ClienteController
{
    public static function index(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'tipo_cliente' => $_GET['tipo_cliente'] ?? ''
        ];

        $totalClientes = Cliente::totalVisiblesPorUsuario($usuarioId, $filtros);

        $paginacion = new Paginacion(
            $_GET['page'] ?? 1,
            $_GET['per_page'] ?? 10,
            $totalClientes
        );

        if ($paginacion->paginaFueraDeRango()) {
            $query = $_GET;
            $query['page'] = 1;
            $query['per_page'] = $paginacion->registrosPorPagina;

            header('Location: /clientes?' . http_build_query($query));
            exit;
        }

        $clientes = Cliente::visiblesPorUsuario(
            $usuarioId,
            $filtros,
            $paginacion->registrosPorPagina,
            $paginacion->offset()
        );

        $router->render('clientes/index', [
            'titulo' => 'Clientes',
            'pagina' => 'clientes',
            'clientes' => $clientes,
            'filtros' => $filtros,
            'paginacion' => $paginacion
        ]);
    }

    public static function crear(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $cliente = new Cliente();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'usuario_id' => $usuarioId,
                'nombre' => $_POST['nombre'] ?? '',
                'apellido' => $_POST['apellido'] ?? '',
                'empresa' => $_POST['empresa'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'ciudad' => $_POST['ciudad'] ?? '',
                'identificacion' => Cliente::normalizarIdentificacion($_POST['identificacion'] ?? ''),
                'tipo_cliente' => $_POST['tipo_cliente'] ?? '',
                'fuente_contacto' => $_POST['fuente_contacto'] ?? '',
                'estado' => $_POST['estado'] ?? 1,
                'eliminado' => 0,
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            if (
                isset($_POST['actualizar_identificacion']) &&
                $_POST['actualizar_identificacion'] === '1'
            ) {
                $clienteExistenteId = filter_var($_POST['cliente_existente_id'] ?? null, FILTER_VALIDATE_INT);
                $nuevaIdentificacion = Cliente::normalizarIdentificacion($_POST['nueva_identificacion'] ?? $datos['identificacion']);

                if (!$clienteExistenteId || !$nuevaIdentificacion) {
                    $alertas['error'][] = 'No se pudo actualizar la identificación del cliente.';
                } else {
                    $clienteExistente = Cliente::find($clienteExistenteId);

                    if (!$clienteExistente) {
                        $alertas['error'][] = 'El cliente que intentas actualizar no existe.';
                    } elseif ((int) $clienteExistente->usuario_id !== (int) $usuarioId) {
                        $alertas['error'][] = 'No tienes permisos para actualizar este cliente.';
                    } elseif ((int) ($clienteExistente->eliminado ?? 0) === 1) {
                        $alertas['error'][] = 'Este cliente fue eliminado.';
                    } else {
                        $duplicado = Cliente::buscarDuplicadoIdentificacion(
                            $usuarioId,
                            $nuevaIdentificacion,
                            $clienteExistente->id
                        );

                        if ($duplicado) {
                            $alertas['error'][] = 'Ya existe otro cliente con esa identificación.';
                        } else {
                            $clienteExistente->identificacion = $nuevaIdentificacion;

                            $resultado = $clienteExistente->guardar();

                            if ($resultado) {
                                header('Location: /clientes/detalle?id=' . $clienteExistente->id);
                                exit;
                            }

                            $alertas['error'][] = 'No se pudo actualizar la identificación.';
                        }
                    }
                }
            } else {
                $cliente = new Cliente($datos);
                $alertas = $cliente->validar();

                $duplicado = Cliente::buscarDuplicadoIdentificacion($usuarioId, $datos['identificacion']);

                if ($duplicado) {
                    $tipoDuplicado = Cliente::tipoIdentificacion($duplicado->identificacion);

                    $alertas['error'][] = 'Este cliente ya está registrado con ' . $tipoDuplicado . '.';
                }

                if (empty($alertas)) {
                    $resultado = $cliente->guardar();

                    if ($resultado) {
                        header('Location: /clientes');
                        exit;
                    }
                }
            }
        }

        $router->render('clientes/crear', [
            'titulo' => 'Nuevo cliente',
            'pagina' => 'clientes',
            'cliente' => $cliente,
            'alertas' => $alertas
        ]);
    }

    public static function editar(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $id = $_GET['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /clientes');
            exit;
        }

        $cliente = Cliente::find($id);

        if (!$cliente) {
            header('Location: /clientes');
            exit;
        }

        // Seguridad: evita que un usuario edite clientes de otro usuario
        if ((int) $cliente->usuario_id !== (int) $usuarioId) {
            header('Location: /clientes');
            exit;
        }

        // Seguridad: evita editar clientes eliminados lógicamente
        if ((int) ($cliente->eliminado ?? 0) === 1) {
            header('Location: /clientes');
            exit;
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'usuario_id' => $usuarioId,
                'nombre' => $_POST['nombre'] ?? '',
                'apellido' => $_POST['apellido'] ?? '',
                'empresa' => $_POST['empresa'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'ciudad' => $_POST['ciudad'] ?? '',
                'identificacion' => $_POST['identificacion'] ?? '',
                'tipo_cliente' => $_POST['tipo_cliente'] ?? '',
                'fuente_contacto' => $_POST['fuente_contacto'] ?? '',
                'estado' => $_POST['estado'] ?? 1,
                'eliminado' => 0,
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            $cliente->sincronizar($datos);

            $alertas = $cliente->validar();

            if (empty($alertas)) {
                $resultado = $cliente->guardar();

                if ($resultado) {
                    header('Location: /clientes/detalle?id=' . $cliente->id);
                    exit;
                }
            }
        }

        $resumen = Cliente::resumenDetalle($cliente->id, $usuarioId);

        $router->render('clientes/editar', [
            'titulo' => 'Editar cliente',
            'pagina' => 'clientes',
            'cliente' => $cliente,
            'resumen' => $resumen,
            'alertas' => $alertas
        ]);
    }

    public static function detalle(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $id = $_GET['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /clientes');
            exit;
        }

        $cliente = Cliente::find($id);

        if (!$cliente) {
            header('Location: /clientes');
            exit;
        }

        // Seguridad: evita ver clientes de otro usuario
        if ((int) $cliente->usuario_id !== (int) $usuarioId) {
            header('Location: /clientes');
            exit;
        }

        // Seguridad: evita ver clientes eliminados lógicamente
        if ((int) ($cliente->eliminado ?? 0) === 1) {
            header('Location: /clientes');
            exit;
        }

        $resumen = Cliente::resumenDetalle($cliente->id, $usuarioId);
        $proyectos = Cliente::proyectosRecientes($cliente->id, $usuarioId);
        $pagos = Cliente::pagosRecientes($cliente->id, $usuarioId);
        $totalNotasCliente = Cliente::totalNotasCliente($cliente->id, $usuarioId);

        $notasDesbloqueadas = false;

        if (
            $totalNotasCliente > 0 &&
            isset($_SESSION['notas_desbloqueadas']) &&
            $_SESSION['notas_desbloqueadas'] === true &&
            isset($_SESSION['notas_desbloqueadas_expira']) &&
            $_SESSION['notas_desbloqueadas_expira'] > time() &&
            isset($_SESSION['notas_cliente_desbloqueado_id']) &&
            (int) $_SESSION['notas_cliente_desbloqueado_id'] === (int) $cliente->id
        ) {
            $notasDesbloqueadas = true;
        }

        $notas = [];

        if ($notasDesbloqueadas) {
            $notas = Cliente::notasRecientes($cliente->id, $usuarioId);
        }

        $router->render('clientes/detalle', [
            'titulo' => 'Detalle del cliente',
            'pagina' => 'clientes',
            'cliente' => $cliente,
            'resumen' => $resumen,
            'proyectos' => $proyectos,
            'pagos' => $pagos,
            'notas' => $notas,
            'notasDesbloqueadas' => $notasDesbloqueadas,
            'totalNotasCliente' => $totalNotasCliente
        ]);
    }

    public static function eliminar()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if (!$id) {
                header('Location: /clientes');
                exit;
            }

            $cliente = Cliente::find($id);

            if (!$cliente) {
                header('Location: /clientes');
                exit;
            }

            if ((int) $cliente->usuario_id !== (int) $_SESSION['id']) {
                header('Location: /clientes');
                exit;
            }

            $cliente->sincronizar([
                'estado' => 0,
                'eliminado' => 1,
                'eliminado_en' => date('Y-m-d H:i:s')
            ]);

            $cliente->guardar();
        }

        header('Location: /clientes');
        exit;
    }

    public static function verificarIdentificacion()
    {
        session_start();

        header('Content-Type: application/json');

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Sesión no válida'
            ]);
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $identificacion = $_POST['identificacion'] ?? '';
        $identificacionNormalizada = Cliente::normalizarIdentificacion($identificacion);

        if (!$identificacionNormalizada) {
            echo json_encode([
                'ok' => true,
                'existe' => false
            ]);
            exit;
        }

        $cliente = Cliente::buscarDuplicadoIdentificacion($usuarioId, $identificacionNormalizada);

        if (!$cliente) {
            echo json_encode([
                'ok' => true,
                'existe' => false
            ]);
            exit;
        }

        $identificacionRegistrada = Cliente::normalizarIdentificacion($cliente->identificacion);
        $baseIngresada = Cliente::baseIdentificacion($identificacionNormalizada);
        $baseRegistrada = Cliente::baseIdentificacion($identificacionRegistrada);

        $exacta = $identificacionNormalizada === $identificacionRegistrada;

        $tipoIngresada = Cliente::tipoIdentificacion($identificacionNormalizada);
        $tipoRegistrada = Cliente::tipoIdentificacion($identificacionRegistrada);

        $puedeActualizar = !$exacta && $baseIngresada === $baseRegistrada;

        echo json_encode([
            'ok' => true,
            'existe' => true,
            'exacta' => $exacta,
            'puede_actualizar' => $puedeActualizar,
            'cliente_id' => $cliente->id,
            'cliente_nombre' => trim($cliente->nombre . ' ' . $cliente->apellido),
            'identificacion_registrada' => $identificacionRegistrada,
            'identificacion_ingresada' => $identificacionNormalizada,
            'tipo_registrada' => $tipoRegistrada,
            'tipo_ingresada' => $tipoIngresada
        ]);
        exit;
    }
}
