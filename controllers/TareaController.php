<?php

namespace Controllers;

use MVC\Router;
use Model\Proyecto;
use Model\Tarea;
use Model\TareaAdjunto;
use Classes\Paginacion;

class TareaController
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
        $alerta = $_GET['alerta'] ?? '';

        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'proyecto_id' => $proyectoId,
            'estado' => $_GET['estado'] ?? '',
            'prioridad' => $_GET['prioridad'] ?? '',
            'vencidas' => $_GET['vencidas'] ?? '',
            'alerta' => $alerta
        ];

        $totalTareas = Tarea::totalVisiblesPorUsuario($usuarioId, $filtros);

        $paginacion = new Paginacion(
            $_GET['page'] ?? 1,
            $_GET['per_page'] ?? 10,
            $totalTareas
        );

        if ($paginacion->paginaFueraDeRango()) {
            $query = $_GET;
            $query['page'] = 1;
            $query['per_page'] = $paginacion->registrosPorPagina;

            header('Location: /tareas?' . http_build_query($query));
            exit;
        }

        $proyectos = Proyecto::visiblesPorUsuario($usuarioId);

        $tareas = Tarea::visiblesPorUsuario(
            $usuarioId,
            $filtros,
            $paginacion->registrosPorPagina,
            $paginacion->offset()
        );

        $resumen = Tarea::resumenPorUsuario($usuarioId, $filtros);

        $router->render('tareas/index', [
            'titulo' => 'Tareas',
            'pagina' => 'tareas',
            'tareas' => $tareas,
            'proyectos' => $proyectos,
            'filtros' => $filtros,
            'resumen' => $resumen,
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

        $proyectoId = $_GET['proyecto_id'] ?? null;
        $proyectoId = filter_var($proyectoId, FILTER_VALIDATE_INT);

        $proyectoSeleccionado = null;

        if ($proyectoId) {
            $proyectoSeleccionado = Proyecto::find($proyectoId);

            if (!$proyectoSeleccionado) {
                header('Location: /proyectos');
                exit;
            }

            if ((int) $proyectoSeleccionado->usuario_id !== (int) $usuarioId) {
                header('Location: /proyectos');
                exit;
            }

            if ((int) ($proyectoSeleccionado->eliminado ?? 0) === 1) {
                header('Location: /proyectos');
                exit;
            }
        }

        $proyectos = Proyecto::visiblesPorUsuario($usuarioId);

        $tarea = new Tarea([
            'proyecto_id' => $proyectoSeleccionado->id ?? '',
            'estado' => 'Pendiente',
            'avance' => 0
        ]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fechaLimite = $_POST['fecha_limite'] ?? null;
            $fechaLimite = $fechaLimite === '' ? null : $fechaLimite;

            $avance = $_POST['avance'] ?? 0;
            $avance = $avance === '' ? 0 : $avance;

            $datos = [
                'usuario_id' => $usuarioId,
                'proyecto_id' => $_POST['proyecto_id'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'fecha_limite' => $fechaLimite,
                'prioridad' => $_POST['prioridad'] ?? '',
                'estado' => $_POST['estado'] ?? 'Pendiente',
                'avance' => $avance,
                'descripcion' => $_POST['descripcion'] ?? '',
                'objetivo' => $_POST['objetivo'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? '',
                'eliminado' => 0
            ];

            $tarea = new Tarea($datos);
            $alertas = $tarea->validar();

            if (empty($alertas)) {
                $proyectoTarea = Proyecto::find($tarea->proyecto_id);

                if (!$proyectoTarea) {
                    Tarea::setAlerta('error', 'El proyecto seleccionado no existe');
                    $alertas = Tarea::getAlertas();
                } elseif ((int) $proyectoTarea->usuario_id !== (int) $usuarioId) {
                    Tarea::setAlerta('error', 'No puedes asignar la tarea a este proyecto');
                    $alertas = Tarea::getAlertas();
                } elseif ((int) ($proyectoTarea->eliminado ?? 0) === 1) {
                    Tarea::setAlerta('error', 'No puedes asignar tareas a un proyecto eliminado');
                    $alertas = Tarea::getAlertas();
                } else {
                    $resultado = $tarea->guardar();

                    if ($resultado) {
                        header('Location: /proyectos/detalle?id=' . $tarea->proyecto_id);
                        exit;
                    }
                }
            }
        }

        $router->render('tareas/crear', [
            'titulo' => 'Nueva tarea',
            'pagina' => 'tareas',
            'tarea' => $tarea,
            'proyectos' => $proyectos,
            'proyectoSeleccionado' => $proyectoSeleccionado,
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
            header('Location: /tareas');
            exit;
        }

        $tarea = Tarea::find($id);

        if (!$tarea) {
            header('Location: /tareas');
            exit;
        }

        if ((int) $tarea->usuario_id !== (int) $usuarioId) {
            header('Location: /tareas');
            exit;
        }

        if ((int) ($tarea->eliminado ?? 0) === 1) {
            header('Location: /tareas');
            exit;
        }

        $proyectoSeleccionado = Proyecto::find($tarea->proyecto_id);

        if (!$proyectoSeleccionado) {
            header('Location: /tareas');
            exit;
        }

        if ((int) $proyectoSeleccionado->usuario_id !== (int) $usuarioId) {
            header('Location: /tareas');
            exit;
        }

        if ((int) ($proyectoSeleccionado->eliminado ?? 0) === 1) {
            header('Location: /tareas');
            exit;
        }

        $proyectos = Proyecto::visiblesPorUsuario($usuarioId);
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fechaLimite = $_POST['fecha_limite'] ?? null;
            $fechaLimite = $fechaLimite === '' ? null : $fechaLimite;

            $avance = $_POST['avance'] ?? 0;
            $avance = $avance === '' ? 0 : $avance;

            $datos = [
                'usuario_id' => $usuarioId,
                'proyecto_id' => $tarea->proyecto_id,
                'nombre' => $_POST['nombre'] ?? '',
                'fecha_limite' => $fechaLimite,
                'prioridad' => $_POST['prioridad'] ?? '',
                'estado' => $_POST['estado'] ?? 'Pendiente',
                'avance' => $avance,
                'descripcion' => $_POST['descripcion'] ?? '',
                'objetivo' => $_POST['objetivo'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? ''
            ];

            $tarea->sincronizar($datos);

            $alertas = $tarea->validar();

            if (empty($alertas)) {
                $resultado = $tarea->guardar();

                if ($resultado) {
                    header('Location: /tareas');
                    exit;
                }
            }
        }

        $router->render('tareas/editar', [
            'titulo' => 'Editar tarea',
            'pagina' => 'tareas',
            'tarea' => $tarea,
            'proyectos' => $proyectos,
            'proyectoSeleccionado' => $proyectoSeleccionado,
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
            header('Location: /tareas');
            exit;
        }

        $tarea = Tarea::detallePorUsuario($id, $usuarioId);

        if (!$tarea) {
            header('Location: /tareas');
            exit;
        }

        $adjuntos = TareaAdjunto::porTarea($tarea->id, $usuarioId);

        $router->render('tareas/detalle', [
            'titulo' => 'Detalle de tarea',
            'pagina' => 'tareas',
            'tarea' => $tarea,
            'adjuntos' => $adjuntos
        ]);
    }

    public static function subirAdjunto()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tareas');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $tareaId = $_POST['tarea_id'] ?? null;
        $tareaId = filter_var($tareaId, FILTER_VALIDATE_INT);

        if (!$tareaId) {
            header('Location: /tareas');
            exit;
        }

        $tarea = Tarea::detallePorUsuario($tareaId, $usuarioId);

        if (!$tarea) {
            header('Location: /tareas');
            exit;
        }

        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error_tarea_detalle'] = 'No se pudo subir el archivo. Intenta nuevamente.';
            header('Location: /tareas/detalle?id=' . $tarea->id);
            exit;
        }

        $archivo = $_FILES['archivo'];

        $pesoMaximo = 20 * 1024 * 1024;

        if ($archivo['size'] > $pesoMaximo) {
            $_SESSION['error_tarea_detalle'] = 'El archivo no puede superar los 20MB.';
            header('Location: /tareas/detalle?id=' . $tarea->id);
            exit;
        }

        $extensionesPermitidas = [
            'pdf',
            'jpg',
            'jpeg',
            'png',
            'webp',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'csv',
            'txt',
            'zip',
            'rar',
            '7z',
            'xml',
            'json',
            'p12',
            'pfx'
        ];

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            $_SESSION['error_tarea_detalle'] = 'Tipo de archivo no permitido. Puedes subir PDF, Word, Excel, imágenes, ZIP, XML, JSON, P12 o PFX.';
            header('Location: /tareas/detalle?id=' . $tarea->id);
            exit;
        }

        $clienteNombre = trim(($tarea->cliente_nombre ?? '') . ' ' . ($tarea->cliente_apellido ?? ''));
        $clienteNombre = $clienteNombre ?: ($tarea->cliente_empresa ?? 'cliente');

        $clienteSlug = self::slug($clienteNombre);
        $proyectoSlug = self::slug($tarea->proyecto_nombre ?? 'proyecto');
        $tareaSlug = self::slug($tarea->nombre ?? 'tarea');

        $directorioRelativo = '/uploads/clientes/' . $clienteSlug . '/proyectos/' . $proyectoSlug . '/tareas/' . $tareaSlug;
        $directorioFisico = __DIR__ . '/../storage' . $directorioRelativo;

        if (!is_dir($directorioFisico)) {
            mkdir($directorioFisico, 0775, true);
        }

        $nombreArchivo = uniqid('adjunto_', true) . '.' . $extension;
        $rutaFisica = $directorioFisico . '/' . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaFisica)) {
            $_SESSION['error_tarea_detalle'] = 'No se pudo guardar el archivo en el servidor.';
            header('Location: /tareas/detalle?id=' . $tarea->id);
            exit;
        }

        $adjunto = new TareaAdjunto([
            'usuario_id' => $usuarioId,
            'tarea_id' => $tarea->id,
            'nombre_original' => $archivo['name'],
            'nombre_archivo' => $nombreArchivo,
            'ruta' => $rutaFisica,
            'tipo' => $archivo['type'] ?? '',
            'peso' => $archivo['size'] ?? 0,
            'eliminado' => 0
        ]);

        $adjunto->guardar();

        $_SESSION['exito_tarea_detalle'] = 'Archivo subido correctamente.';

        header('Location: /tareas/detalle?id=' . $tarea->id);
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
            header('Location: /tareas');
            exit;
        }

        $adjunto = TareaAdjunto::protegidoPorUsuario($adjuntoId, $usuarioId);

        if (!$adjunto) {
            http_response_code(403);
            echo 'No tienes permisos para descargar este archivo.';
            exit;
        }

        $ruta = $adjunto->ruta;

        if (!$ruta || !file_exists($ruta)) {
            http_response_code(404);
            echo 'El archivo no existe en el servidor.';
            exit;
        }

        if (!is_file($ruta)) {
            http_response_code(404);
            echo 'Archivo no válido.';
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

    public static function eliminarAdjunto()
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tareas');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $adjuntoId = $_POST['adjunto_id'] ?? null;
        $adjuntoId = filter_var($adjuntoId, FILTER_VALIDATE_INT);

        $tareaId = $_POST['tarea_id'] ?? null;
        $tareaId = filter_var($tareaId, FILTER_VALIDATE_INT);

        if (!$adjuntoId || !$tareaId) {
            header('Location: /tareas');
            exit;
        }

        TareaAdjunto::eliminarLogico($adjuntoId, $usuarioId);

        $_SESSION['exito_tarea_detalle'] = 'Adjunto eliminado del listado.';

        header('Location: /tareas/detalle?id=' . $tareaId);
        exit;
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
                header('Location: /tareas');
                exit;
            }

            $tarea = Tarea::find($id);

            if (!$tarea) {
                header('Location: /tareas');
                exit;
            }

            if ((int) $tarea->usuario_id !== (int) $_SESSION['id']) {
                header('Location: /tareas');
                exit;
            }

            if ((int) ($tarea->eliminado ?? 0) === 1) {
                header('Location: /tareas');
                exit;
            }

            $validacion = $tarea->puedeEliminar();

            if (!$validacion['puede']) {
                $_SESSION['error_tarea'] = $validacion['mensaje'];

                header('Location: /tareas');
                exit;
            }

            Tarea::eliminarLogico($tarea->id, $_SESSION['id']);
        }

        header('Location: /tareas');
        exit;
    }
}
