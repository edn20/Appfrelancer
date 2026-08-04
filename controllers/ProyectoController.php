<?php

namespace Controllers;

use MVC\Router;
use Model\Cliente;
use Model\Proyecto;
use Classes\Paginacion;
use Model\Nota;

class ProyectoController
{
    public static function index(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $error = $_SESSION['error_proyecto'] ?? null;
        unset($_SESSION['error_proyecto']);

        $usuarioId = $_SESSION['id'];

        $clienteId = $_GET['cliente_id'] ?? '';
        $clienteId = $clienteId ? filter_var($clienteId, FILTER_VALIDATE_INT) : '';

        $alerta = $_GET['alerta'] ?? '';

        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'cliente_id' => $clienteId,
            'estado' => $_GET['estado'] ?? '',
            'prioridad' => $_GET['prioridad'] ?? '',
            'alerta' => $alerta
        ];

        $totalProyectos = Proyecto::totalVisiblesPorUsuario($usuarioId, $filtros);

        $paginacion = new Paginacion(
            $_GET['page'] ?? 1,
            $_GET['per_page'] ?? 10,
            $totalProyectos
        );

        if ($paginacion->paginaFueraDeRango()) {
            $query = $_GET;
            $query['page'] = 1;
            $query['per_page'] = $paginacion->registrosPorPagina;

            header('Location: /proyectos?' . http_build_query($query));
            exit;
        }

        $clientes = Cliente::visiblesPorUsuario($usuarioId);

        $proyectos = Proyecto::visiblesPorUsuario(
            $usuarioId,
            $filtros,
            $paginacion->registrosPorPagina,
            $paginacion->offset()
        );

        $resumen = Proyecto::resumenPorUsuario($usuarioId, $filtros);

        $router->render('proyectos/index', [
            'titulo' => 'Proyectos',
            'pagina' => 'proyectos',
            'proyectos' => $proyectos,
            'clientes' => $clientes,
            'resumen' => $resumen,
            'filtros' => $filtros,
            'error' => $error,
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

        $alertas = [];
        $usuarioId = $_SESSION['id'];

        $clienteId = $_GET['cliente_id'] ?? null;
        $clienteId = filter_var($clienteId, FILTER_VALIDATE_INT);

        $clienteSeleccionado = null;

        if ($clienteId) {
            $clienteSeleccionado = Cliente::find($clienteId);

            if (!$clienteSeleccionado) {
                header('Location: /clientes');
                exit;
            }

            if ((int) $clienteSeleccionado->usuario_id !== (int) $usuarioId) {
                header('Location: /clientes');
                exit;
            }

            if ((int) ($clienteSeleccionado->eliminado ?? 0) === 1) {
                header('Location: /clientes');
                exit;
            }
        }

        $clientes = Cliente::visiblesPorUsuario($usuarioId);

        $proyecto = new Proyecto([
            'cliente_id' => $clienteSeleccionado->id ?? '',
            'estado' => 'Pendiente'
        ]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valorTotal = $_POST['valor_total'] ?? 0;
            $valorTotal = $valorTotal === '' ? 0 : $valorTotal;

            $fechaInicio = $_POST['fecha_inicio'] ?? null;
            $fechaInicio = $fechaInicio === '' ? null : $fechaInicio;

            $fechaEntrega = $_POST['fecha_entrega'] ?? null;
            $fechaEntrega = $fechaEntrega === '' ? null : $fechaEntrega;

            $datos = [
                'usuario_id' => $usuarioId,
                'cliente_id' => $_POST['cliente_id'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'fecha_inicio' => $fechaInicio,
                'fecha_entrega' => $fechaEntrega,
                'valor_total' => $valorTotal,
                'prioridad' => $_POST['prioridad'] ?? '',
                'estado' => $_POST['estado'] ?? 'Pendiente',
                'tipo_cobro' => $_POST['tipo_cobro'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'objetivos' => $_POST['objetivos'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? '',
                'eliminado' => 0
            ];

            $proyecto = new Proyecto($datos);
            $alertas = $proyecto->validar();

            if (empty($alertas)) {
                $clienteProyecto = Cliente::find($proyecto->cliente_id);

                if (!$clienteProyecto) {
                    Proyecto::setAlerta('error', 'El cliente seleccionado no existe');
                    $alertas = Proyecto::getAlertas();
                } elseif ((int) $clienteProyecto->usuario_id !== (int) $usuarioId) {
                    Proyecto::setAlerta('error', 'No puedes asignar el proyecto a este cliente');
                    $alertas = Proyecto::getAlertas();
                } elseif ((int) ($clienteProyecto->eliminado ?? 0) === 1) {
                    Proyecto::setAlerta('error', 'No puedes asignar proyectos a un cliente eliminado');
                    $alertas = Proyecto::getAlertas();
                } else {
                    $resultado = $proyecto->guardar();

                    if ($resultado) {
                        header('Location: /clientes/detalle?id=' . $proyecto->cliente_id);
                        exit;
                    }
                }
            }
        }

        $router->render('proyectos/crear', [
            'titulo' => 'Nuevo proyecto',
            'pagina' => 'proyectos',
            'proyecto' => $proyecto,
            'clientes' => $clientes,
            'clienteSeleccionado' => $clienteSeleccionado,
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
            header('Location: /proyectos');
            exit;
        }

        $proyecto = Proyecto::find($id);

        if (!$proyecto) {
            header('Location: /proyectos');
            exit;
        }

        if ((int) $proyecto->usuario_id !== (int) $usuarioId) {
            header('Location: /proyectos');
            exit;
        }

        if ((int) ($proyecto->eliminado ?? 0) === 1) {
            header('Location: /proyectos');
            exit;
        }

        $clienteSeleccionado = Cliente::find($proyecto->cliente_id);

        if (!$clienteSeleccionado) {
            header('Location: /proyectos');
            exit;
        }

        if ((int) $clienteSeleccionado->usuario_id !== (int) $usuarioId) {
            header('Location: /proyectos');
            exit;
        }

        if ((int) ($clienteSeleccionado->eliminado ?? 0) === 1) {
            header('Location: /proyectos');
            exit;
        }

        $clientes = Cliente::visiblesPorUsuario($usuarioId);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valorTotal = $_POST['valor_total'] ?? 0;
            $valorTotal = $valorTotal === '' ? 0 : $valorTotal;

            $fechaInicio = $_POST['fecha_inicio'] ?? null;
            $fechaInicio = $fechaInicio === '' ? null : $fechaInicio;

            $fechaEntrega = $_POST['fecha_entrega'] ?? null;
            $fechaEntrega = $fechaEntrega === '' ? null : $fechaEntrega;

            $datos = [
                'usuario_id' => $usuarioId,
                'cliente_id' => $proyecto->cliente_id,
                'nombre' => $_POST['nombre'] ?? '',
                'fecha_inicio' => $fechaInicio,
                'fecha_entrega' => $fechaEntrega,
                'valor_total' => $valorTotal,
                'prioridad' => $_POST['prioridad'] ?? '',
                'estado' => $_POST['estado'] ?? 'Pendiente',
                'tipo_cobro' => $_POST['tipo_cobro'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'objetivos' => $_POST['objetivos'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            $proyecto->sincronizar($datos);

            $alertas = $proyecto->validar();

            if (empty($alertas)) {
                $clienteProyecto = Cliente::find($proyecto->cliente_id);

                if (!$clienteProyecto) {
                    Proyecto::setAlerta('error', 'El cliente seleccionado no existe');
                    $alertas = Proyecto::getAlertas();
                } elseif ((int) $clienteProyecto->usuario_id !== (int) $usuarioId) {
                    Proyecto::setAlerta('error', 'No puedes asignar el proyecto a este cliente');
                    $alertas = Proyecto::getAlertas();
                } elseif ((int) ($clienteProyecto->eliminado ?? 0) === 1) {
                    Proyecto::setAlerta('error', 'No puedes asignar proyectos a un cliente eliminado');
                    $alertas = Proyecto::getAlertas();
                } else {
                    $resultado = $proyecto->guardar();

                    if ($resultado) {
                        header('Location: /proyectos');
                        exit;
                    }
                }
            }
        }

        $router->render('proyectos/editar', [
            'titulo' => 'Editar proyecto',
            'pagina' => 'proyectos',
            'proyecto' => $proyecto,
            'clientes' => $clientes,
            'clienteSeleccionado' => $clienteSeleccionado,
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
            header('Location: /proyectos');
            exit;
        }

        $proyecto = Proyecto::detallePorUsuario($id, $usuarioId);

        if (!$proyecto) {
            header('Location: /proyectos');
            exit;
        }

        $resumen = Proyecto::resumenDetalle($proyecto->id);

        $valorTotal = (float) ($proyecto->valor_total ?? 0);
        $totalPagado = (float) ($resumen['total_pagado'] ?? 0);
        $saldoPendiente = max($valorTotal - $totalPagado, 0);

        $resumen['saldo_pendiente'] = $saldoPendiente;

        $tareas = Proyecto::tareasRecientes($proyecto->id, $usuarioId);
        $pagos = Proyecto::pagosRecientes($proyecto->id, $usuarioId);
        $totalNotasProyecto = Proyecto::totalNotasProyecto($proyecto->id, $usuarioId);

        $notasDesbloqueadas = false;

        if (
            $totalNotasProyecto > 0 &&
            isset($_SESSION['notas_desbloqueadas']) &&
            $_SESSION['notas_desbloqueadas'] === true &&
            isset($_SESSION['notas_desbloqueadas_expira']) &&
            $_SESSION['notas_desbloqueadas_expira'] > time() &&
            isset($_SESSION['notas_proyecto_desbloqueado_id']) &&
            (int) $_SESSION['notas_proyecto_desbloqueado_id'] === (int) $proyecto->id
        ) {
            $notasDesbloqueadas = true;
        }

        $notas = [];

        if ($notasDesbloqueadas) {
            $notas = Proyecto::notasProyecto($proyecto->id, $usuarioId);
        }

        $router->render('proyectos/detalle', [
            'titulo' => 'Detalle del proyecto',
            'pagina' => 'proyectos',
            'proyecto' => $proyecto,
            'resumen' => $resumen,
            'tareas' => $tareas,
            'pagos' => $pagos,
            'notas' => $notas,
            'notasDesbloqueadas' => $notasDesbloqueadas,
            'totalNotasProyecto' => $totalNotasProyecto
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
                header('Location: /proyectos');
                exit;
            }

            $proyecto = Proyecto::find($id);

            if (!$proyecto) {
                header('Location: /proyectos');
                exit;
            }

            if ((int) $proyecto->usuario_id !== (int) $_SESSION['id']) {
                header('Location: /proyectos');
                exit;
            }

            if ((int) ($proyecto->eliminado ?? 0) === 1) {
                header('Location: /proyectos');
                exit;
            }

            $validacion = $proyecto->puedeEliminar();

            if (!$validacion['puede']) {
                $_SESSION['error_proyecto'] = $validacion['mensaje'];

                header('Location: /proyectos');
                exit;
            }

            Proyecto::eliminarLogico($proyecto->id, $_SESSION['id']);
        }

        header('Location: /proyectos');
        exit;
    }
}
