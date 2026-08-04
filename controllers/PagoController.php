<?php

namespace Controllers;

use MVC\Router;
use Model\Proyecto;
use Model\Pago;
use Model\PagoAdjunto;
use Model\Usuario;
use Classes\Paginacion;
use Model\Cliente;

class PagoController
{
    public static function index(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $proyectoId = $_GET['proyecto_id'] ?? '';
        $proyectoId = $proyectoId ? filter_var($proyectoId, FILTER_VALIDATE_INT) : '';
        $clienteId = $_GET['cliente_id'] ?? '';
        $clienteId = $clienteId ? filter_var($clienteId, FILTER_VALIDATE_INT) : '';

        $clienteSeleccionado = null;

        if ($clienteId) {
            $clienteSeleccionado = Cliente::find($clienteId);

            if (!$clienteSeleccionado) {
                header('Location: /pagos');
                exit;
            }

            if ((int) $clienteSeleccionado->usuario_id !== (int) $usuarioId) {
                header('Location: /pagos');
                exit;
            }

            if ((int) ($clienteSeleccionado->eliminado ?? 0) === 1) {
                header('Location: /pagos');
                exit;
            }
        }

        $proyectoSeleccionado = null;

        if ($proyectoId) {
            $proyectoSeleccionado = Proyecto::find($proyectoId);

            if (!$proyectoSeleccionado) {
                header('Location: /pagos');
                exit;
            }

            if ((int) $proyectoSeleccionado->usuario_id !== (int) $usuarioId) {
                header('Location: /pagos');
                exit;
            }

            if ((int) ($proyectoSeleccionado->eliminado ?? 0) === 1) {
                header('Location: /pagos');
                exit;
            }
        }

        $alerta = $_GET['alerta'] ?? '';

        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'proyecto_id' => $proyectoId,
            'cliente_id' => $clienteId,
            'clienteSeleccionado' => $clienteSeleccionado,
            'estado' => $_GET['estado'] ?? '',
            'metodo_pago' => $_GET['metodo_pago'] ?? '',
            'alerta' => $alerta
        ];

        $totalPagos = Pago::totalVisiblesPorUsuario($usuarioId, $filtros);

        $paginacion = new Paginacion(
            $_GET['page'] ?? 1,
            $_GET['per_page'] ?? 10,
            $totalPagos
        );

        if ($paginacion->paginaFueraDeRango()) {
            $query = $_GET;
            $query['page'] = 1;
            $query['per_page'] = $paginacion->registrosPorPagina;

            header('Location: /pagos?' . http_build_query($query));
            exit;
        }

        $proyectos = Proyecto::visiblesPorUsuario($usuarioId);

        $pagos = Pago::visiblesPorUsuario(
            $usuarioId,
            $filtros,
            $paginacion->registrosPorPagina,
            $paginacion->offset()
        );

        $resumen = Pago::resumenPorUsuario($usuarioId, $filtros);
        $clientes = Cliente::visiblesPorUsuario($usuarioId);

        $router->render('pagos/index', [
            'titulo' => 'Pagos',
            'pagina' => 'pagos',
            'pagos' => $pagos,
            'proyectos' => $proyectos,
            'proyectoSeleccionado' => $proyectoSeleccionado,
            'filtros' => $filtros,
            'resumen' => $resumen,
            'clientes' => $clientes,
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
        $alertas = [];

        $proyectoId = $_GET['proyecto_id'] ?? null;
        $proyectoId = filter_var($proyectoId, FILTER_VALIDATE_INT);

        $proyectoSeleccionado = null;
        $montoTotalProyecto = 0;
        $totalPagadoProyecto = 0;
        $saldoPendienteActual = 0;

        if ($proyectoId) {
            $proyectoSeleccionado = Proyecto::detallePorUsuario($proyectoId, $usuarioId);

            if (!$proyectoSeleccionado) {
                header('Location: /proyectos');
                exit;
            }

            $montoTotalProyecto = (float) $proyectoSeleccionado->valor_total;
            $totalPagadoProyecto = Pago::totalPagadoPorProyecto($proyectoSeleccionado->id);
            $saldoPendienteActual = max($montoTotalProyecto - $totalPagadoProyecto, 0);
        }

        $proyectos = Proyecto::visiblesPorUsuario($usuarioId);

        $pago = new Pago([
            'proyecto_id' => $proyectoSeleccionado->id ?? '',
            'estado' => 'Pendiente',
            'monto_total' => $montoTotalProyecto,
            'monto_pagado' => 0
        ]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyectoPostId = $_POST['proyecto_id'] ?? '';
            $proyectoPostId = filter_var($proyectoPostId, FILTER_VALIDATE_INT);

            if (!$proyectoPostId) {
                Pago::setAlerta('error', 'Debes seleccionar un proyecto');
                $alertas = Pago::getAlertas();
            } else {
                $proyectoPago = Proyecto::detallePorUsuario($proyectoPostId, $usuarioId);

                if (!$proyectoPago) {
                    Pago::setAlerta('error', 'El proyecto seleccionado no existe o no tienes acceso');
                    $alertas = Pago::getAlertas();
                } else {
                    $montoTotalProyecto = (float) $proyectoPago->valor_total;
                    $totalPagadoProyecto = Pago::totalPagadoPorProyecto($proyectoPago->id);
                    $saldoPendienteActual = max($montoTotalProyecto - $totalPagadoProyecto, 0);

                    $fechaPago = $_POST['fecha_pago'] ?? null;
                    $fechaPago = $fechaPago === '' ? null : $fechaPago;

                    $fechaVencimiento = $_POST['fecha_vencimiento'] ?? null;
                    $fechaVencimiento = $fechaVencimiento === '' ? null : $fechaVencimiento;

                    $montoPagado = $_POST['monto_pagado'] ?? 0;
                    $montoPagado = $montoPagado === '' ? 0 : $montoPagado;

                    $datos = [
                        'usuario_id' => $usuarioId,
                        'proyecto_id' => $proyectoPago->id,
                        'metodo_pago' => $_POST['metodo_pago'] ?? '',
                        'estado' => $_POST['estado'] ?? 'Cobrado',
                        'fecha_pago' => $fechaPago,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'referencia' => trim($_POST['referencia'] ?? '') ?: 'Sin factura registrada',
                        'monto_total' => $montoTotalProyecto,
                        'monto_pagado' => $montoPagado,
                        'descripcion' => $_POST['descripcion'] ?? '',
                        'notas_internas' => $_POST['notas_internas'] ?? '',
                        'eliminado' => 0
                    ];

                    $pago = new Pago($datos);
                    $alertas = $pago->validar($saldoPendienteActual);

                    $erroresComprobantes = self::validarComprobantesPago($pago->metodo_pago);

                    foreach ($erroresComprobantes as $error) {
                        Pago::setAlerta('error', $error);
                    }

                    $alertas = Pago::getAlertas();

                    if (empty($alertas)) {
                        $resultado = $pago->guardar();

                        if ($resultado) {
                            $pagoId = $resultado['id'] ?? null;

                            if ($pagoId) {
                                self::guardarAdjuntosPago($pagoId, $usuarioId, $proyectoPago);
                            }

                            header('Location: /proyectos/detalle?id=' . $pago->proyecto_id);
                            exit;
                        }
                    }

                    $proyectoSeleccionado = $proyectoPago;
                }
            }
        }

        $router->render('pagos/crear', [
            'titulo' => 'Nuevo pago',
            'pagina' => 'pagos',
            'pago' => $pago,
            'proyectos' => $proyectos,
            'proyectoSeleccionado' => $proyectoSeleccionado,
            'montoTotalProyecto' => $montoTotalProyecto,
            'totalPagadoProyecto' => $totalPagadoProyecto,
            'saldoPendienteActual' => $saldoPendienteActual,
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
            header('Location: /pagos');
            exit;
        }

        $pago = Pago::detallePorUsuario($id, $usuarioId);

        if (!$pago) {
            header('Location: /pagos');
            exit;
        }

        $adjuntos = PagoAdjunto::porPago($pago->id, $usuarioId);

        $totalPagadoSinEste = Pago::totalPagadoPorProyecto($pago->proyecto_id, $pago->id);
        $saldoDisponible = max((float) $pago->monto_total - $totalPagadoSinEste, 0);

        $error = $_SESSION['error_pago_detalle'] ?? null;
        $exito = $_SESSION['exito_pago_detalle'] ?? null;

        unset($_SESSION['error_pago_detalle']);
        unset($_SESSION['exito_pago_detalle']);

        $router->render('pagos/detalle', [
            'titulo' => 'Detalle del pago',
            'pagina' => 'pagos',
            'pago' => $pago,
            'adjuntos' => $adjuntos,
            'saldoDisponible' => $saldoDisponible,
            'error' => $error,
            'exito' => $exito
        ]);
    }

    public static function actualizar()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pagos');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $id = $_POST['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /pagos');
            exit;
        }

        $pago = Pago::detallePorUsuario($id, $usuarioId);

        if (!$pago) {
            header('Location: /pagos');
            exit;
        }

        $passwordConfirmacion = $_POST['password_confirmacion'] ?? '';

        if (!$passwordConfirmacion) {
            $_SESSION['error_pago_detalle'] = 'Debes ingresar tu contraseña para modificar el pago.';
            header('Location: /pagos/detalle?id=' . $pago->id);
            exit;
        }

        $usuario = Usuario::find($usuarioId);

        if (!$usuario || !password_verify($passwordConfirmacion, $usuario->password)) {
            $_SESSION['error_pago_detalle'] = 'La contraseña ingresada no es correcta.';
            header('Location: /pagos/detalle?id=' . $pago->id);
            exit;
        }

        $montoPagado = $_POST['monto_pagado'] ?? 0;
        $montoPagado = $montoPagado === '' ? 0 : $montoPagado;

        $estado = $_POST['estado'] ?? $pago->estado;

        $datos = [
            'usuario_id' => $usuarioId,
            'proyecto_id' => $pago->proyecto_id,
            'metodo_pago' => $pago->metodo_pago,
            'estado' => $estado,
            'fecha_pago' => $pago->fecha_pago,
            'fecha_vencimiento' => $pago->fecha_vencimiento,
            'referencia' => $pago->referencia,
            'monto_total' => $pago->monto_total,
            'monto_pagado' => $montoPagado,
            'descripcion' => $pago->descripcion,
            'notas_internas' => $_POST['notas_internas'] ?? $pago->notas_internas,
            'eliminado' => 0
        ];

        $pago->sincronizar($datos);

        $totalPagadoSinEste = Pago::totalPagadoPorProyecto($pago->proyecto_id, $pago->id);
        $saldoDisponible = max((float) $pago->monto_total - $totalPagadoSinEste, 0);

        $alertas = $pago->validarActualizacion($saldoDisponible);

        if (!empty($alertas)) {
            $_SESSION['error_pago_detalle'] = implode(' ', $alertas['error'] ?? ['No se pudo actualizar el pago.']);
            header('Location: /pagos/detalle?id=' . $pago->id);
            exit;
        }

        $resultado = $pago->guardar();

        if ($resultado) {
            $_SESSION['exito_pago_detalle'] = 'Pago actualizado correctamente.';
        } else {
            $_SESSION['error_pago_detalle'] = 'No se pudo actualizar el pago.';
        }

        header('Location: /pagos/detalle?id=' . $pago->id);
        exit;
    }

    public static function descargarAdjunto()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $adjuntoId = $_GET['id'] ?? null;
        $adjuntoId = filter_var($adjuntoId, FILTER_VALIDATE_INT);

        if (!$adjuntoId) {
            header('Location: /pagos');
            exit;
        }

        $adjunto = PagoAdjunto::protegidoPorUsuario($adjuntoId, $usuarioId);

        if (!$adjunto) {
            http_response_code(403);
            echo 'No tienes permisos para descargar este archivo.';
            exit;
        }

        $ruta = $adjunto->ruta;

        if (!$ruta || !file_exists($ruta) || !is_file($ruta)) {
            http_response_code(404);
            echo 'El archivo no existe en el servidor.';
            exit;
        }

        $nombreDescarga = $adjunto->nombre_original ?: basename($ruta);
        $tipo = $adjunto->tipo ?: 'application/octet-stream';
        $peso = filesize($ruta);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $tipo);
        header('Content-Disposition: attachment; filename="' . basename($nombreDescarga) . '"');
        header('Content-Length: ' . $peso);
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($ruta);
        exit;
    }

    private static function guardarAdjuntosPago($pagoId, $usuarioId, $proyecto)
    {
        if (!isset($_FILES['comprobantes'])) {
            return;
        }

        $archivos = $_FILES['comprobantes'];

        if (empty($archivos['name'][0])) {
            return;
        }

        if (count($archivos['name']) > 2) {
            return;
        }

        $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        $pesoMaximo = 10 * 1024 * 1024;

        $clienteNombre = trim(($proyecto->cliente_nombre ?? '') . ' ' . ($proyecto->cliente_apellido ?? ''));
        $clienteNombre = $clienteNombre ?: ($proyecto->cliente_empresa ?? 'cliente');

        $clienteSlug = self::slug($clienteNombre);
        $proyectoSlug = self::slug($proyecto->nombre ?? 'proyecto');

        $directorioRelativo = '/uploads/clientes/' . $clienteSlug . '/proyectos/' . $proyectoSlug . '/pagos';
        $directorioFisico = __DIR__ . '/../storage' . $directorioRelativo;

        if (!is_dir($directorioFisico)) {
            mkdir($directorioFisico, 0775, true);
        }

        for ($i = 0; $i < count($archivos['name']); $i++) {
            if ($archivos['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            if ($archivos['size'][$i] > $pesoMaximo) {
                continue;
            }

            $extension = strtolower(pathinfo($archivos['name'][$i], PATHINFO_EXTENSION));

            if (!in_array($extension, $extensionesPermitidas)) {
                continue;
            }

            $nombreArchivo = uniqid('comprobante_', true) . '.' . $extension;
            $rutaFisica = $directorioFisico . '/' . $nombreArchivo;

            if (!move_uploaded_file($archivos['tmp_name'][$i], $rutaFisica)) {
                continue;
            }

            $adjunto = new PagoAdjunto([
                'usuario_id' => $usuarioId,
                'pago_id' => $pagoId,
                'nombre_original' => $archivos['name'][$i],
                'nombre_archivo' => $nombreArchivo,
                'ruta' => $rutaFisica,
                'tipo' => $archivos['type'][$i] ?? '',
                'peso' => $archivos['size'][$i] ?? 0,
                'eliminado' => 0
            ]);

            $adjunto->guardar();
        }
    }

    private static function slug($texto)
    {
        $texto = trim($texto);
        $texto = mb_strtolower($texto, 'UTF-8');

        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n'],
            $texto
        );

        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        $texto = trim($texto, '-');

        return $texto ?: 'sin-nombre';
    }

    private static function validarComprobantesPago($metodoPago)
    {
        $errores = [];

        $metodoPago = trim($metodoPago);

        if ($metodoPago === 'Efectivo') {
            return $errores;
        }

        if (!isset($_FILES['comprobantes']) || empty($_FILES['comprobantes']['name'][0])) {
            $errores[] = 'Debes adjuntar al menos un comprobante si el método de pago no es efectivo.';
            return $errores;
        }

        $archivos = $_FILES['comprobantes'];

        $cantidadArchivos = 0;

        foreach ($archivos['name'] as $nombre) {
            if (!empty($nombre)) {
                $cantidadArchivos++;
            }
        }

        if ($cantidadArchivos > 2) {
            $errores[] = 'Solo puedes adjuntar máximo 2 comprobantes.';
        }

        $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        $pesoMaximo = 10 * 1024 * 1024;

        for ($i = 0; $i < count($archivos['name']); $i++) {
            if (empty($archivos['name'][$i])) {
                continue;
            }

            if ($archivos['error'][$i] !== UPLOAD_ERR_OK) {
                $errores[] = 'Uno de los comprobantes no se pudo cargar correctamente.';
                continue;
            }

            if ($archivos['size'][$i] > $pesoMaximo) {
                $errores[] = 'Cada comprobante debe pesar máximo 10MB.';
                continue;
            }

            $extension = strtolower(pathinfo($archivos['name'][$i], PATHINFO_EXTENSION));

            if (!in_array($extension, $extensionesPermitidas)) {
                $errores[] = 'Los comprobantes solo pueden ser PDF, JPG, JPEG, PNG o WEBP.';
                continue;
            }
        }

        return $errores;
    }
}
