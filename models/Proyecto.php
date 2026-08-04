<?php

namespace Model;

class Proyecto extends ActiveRecord
{
    protected static $tabla = 'proyectos';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'cliente_id',
        'nombre',
        'fecha_inicio',
        'fecha_entrega',
        'valor_total',
        'prioridad',
        'estado',
        'tipo_cobro',
        'descripcion',
        'objetivos',
        'observaciones',
        'eliminado'
    ];

    public $id;
    public $usuario_id;
    public $cliente_id;
    public $nombre;
    public $fecha_inicio;
    public $fecha_entrega;
    public $valor_total;
    public $prioridad;
    public $estado;
    public $tipo_cobro;
    public $descripcion;
    public $objetivos;
    public $observaciones;
    public $eliminado;
    public $eliminado_en;
    public $creado;
    public $actualizado;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_empresa;

    // Tareas recientes
    public $tarea_id;
    public $tarea_nombre;
    public $tarea_estado;
    public $tarea_prioridad;
    public $tarea_fecha_limite;
    public $tarea_avance;

    // Pagos registrados
    public $pago_id;
    public $pago_fecha;
    public $pago_metodo;
    public $pago_monto;
    public $pago_estado;
    public $pago_referencia;

    // Notas del proyecto
    public $nota_id;
    public $nota_titulo;
    public $nota_contenido;
    public $nota_color;
    public $nota_fija;
    public $nota_creado;
    public $nota_proyecto_nombre;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->fecha_inicio = $args['fecha_inicio'] ?? null;
        $this->fecha_entrega = $args['fecha_entrega'] ?? null;
        $this->valor_total = $args['valor_total'] ?? 0;
        $this->prioridad = $args['prioridad'] ?? '';
        $this->estado = $args['estado'] ?? 'Pendiente';
        $this->tipo_cobro = $args['tipo_cobro'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->objetivos = $args['objetivos'] ?? '';
        $this->observaciones = $args['observaciones'] ?? '';
        $this->eliminado = $args['eliminado'] ?? 0;
        $this->eliminado_en = $args['eliminado_en'] ?? null;
        $this->creado = $args['creado'] ?? null;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    public function validar()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del proyecto es obligatorio';
        }

        if (!$this->cliente_id) {
            self::$alertas['error'][] = 'Debes seleccionar un cliente';
        }

        if (!$this->estado) {
            self::$alertas['error'][] = 'El estado del proyecto es obligatorio';
        }

        if ($this->valor_total !== '' && !is_numeric($this->valor_total)) {
            self::$alertas['error'][] = 'El valor total debe ser un número válido';
        }

        return self::$alertas;
    }

    public static function visiblesPorUsuario($usuarioId, $filtros = [], $limite = null, $offset = null)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
            p.*,
            c.nombre AS cliente_nombre,
            c.apellido AS cliente_apellido,
            c.empresa AS cliente_empresa
          FROM proyectos p
          INNER JOIN clientes c ON p.cliente_id = c.id
          WHERE p.usuario_id = '{$usuarioId}'
          AND p.eliminado = 0
          AND c.usuario_id = '{$usuarioId}'
          AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            p.nombre LIKE '%{$busqueda}%'
            OR p.descripcion LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $query .= " AND p.cliente_id = '{$clienteId}'";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND p.estado = '{$estado}'";
        }

        if (!empty($filtros['prioridad'])) {
            $prioridad = self::$db->escape_string($filtros['prioridad']);
            $query .= " AND p.prioridad = '{$prioridad}'";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'atrasados') {
                $query .= " AND p.fecha_entrega IS NOT NULL
                    AND p.fecha_entrega < '{$hoy}'
                    AND p.estado NOT IN ('Entregado', 'Cancelado')";
            }

            if ($filtros['alerta'] === 'proximos') {
                $query .= " AND p.fecha_entrega IS NOT NULL
                    AND p.fecha_entrega >= '{$hoy}'
                    AND p.fecha_entrega <= '{$limite}'
                    AND p.estado NOT IN ('Entregado', 'Cancelado')";
            }
        }

        $query .= " ORDER BY p.id DESC";

        if ($limite !== null && $offset !== null) {
            $limite = (int) $limite;
            $offset = (int) $offset;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return self::consultarSQL($query);
    }

    public static function resumenPorUsuario($usuarioId, $filtros = [])
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                COUNT(p.id) AS total,
                SUM(CASE WHEN p.estado = 'En proceso' THEN 1 ELSE 0 END) AS en_proceso,
                SUM(CASE WHEN p.estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN p.estado = 'Entregado' THEN 1 ELSE 0 END) AS entregados,
                SUM(CASE WHEN p.estado IN ('Pausado', 'Cancelado') THEN 1 ELSE 0 END) AS pausados_cancelados
              FROM proyectos p
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            p.nombre LIKE '%{$busqueda}%'
            OR p.descripcion LIKE '%{$busqueda}%'
            OR p.objetivos LIKE '%{$busqueda}%'
            OR p.observaciones LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $query .= " AND p.cliente_id = '{$clienteId}'";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND p.estado = '{$estado}'";
        }

        if (!empty($filtros['prioridad'])) {
            $prioridad = self::$db->escape_string($filtros['prioridad']);
            $query .= " AND p.prioridad = '{$prioridad}'";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'atrasados') {
                $query .= " AND p.fecha_entrega IS NOT NULL
                    AND p.fecha_entrega < '{$hoy}'
                    AND p.estado NOT IN ('Entregado', 'Cancelado')";
            }

            if ($filtros['alerta'] === 'proximos') {
                $query .= " AND p.fecha_entrega IS NOT NULL
                    AND p.fecha_entrega >= '{$hoy}'
                    AND p.fecha_entrega <= '{$limite}'
                    AND p.estado NOT IN ('Entregado', 'Cancelado')";
            }
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return [
            'total' => (int) ($registro['total'] ?? 0),
            'en_proceso' => (int) ($registro['en_proceso'] ?? 0),
            'pendientes' => (int) ($registro['pendientes'] ?? 0),
            'entregados' => (int) ($registro['entregados'] ?? 0),
            'pausados_cancelados' => (int) ($registro['pausados_cancelados'] ?? 0)
        ];
    }

    public static function tablaTareasExiste()
    {
        $query = "SHOW TABLES LIKE 'tareas'";
        $resultado = self::$db->query($query);

        return $resultado && $resultado->num_rows > 0;
    }

    public static function tieneTareasBloqueantes($proyectoId)
    {
        $proyectoId = self::$db->escape_string($proyectoId);

        // Si todavía no existe la tabla tareas, significa que el proyecto no tiene tareas registradas
        if (!self::tablaTareasExiste()) {
            return false;
        }

        $query = "SELECT COUNT(*) AS total
              FROM tareas
              WHERE proyecto_id = '{$proyectoId}'
              AND eliminado = 0
              AND LOWER(estado) NOT IN ('completada', 'anulada')";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return ((int) $registro['total']) > 0;
    }

    public function puedeEliminar()
    {
        $estadosPermitidos = ['Entregado', 'Cancelado'];

        if (!in_array($this->estado, $estadosPermitidos)) {
            return [
                'puede' => false,
                'mensaje' => 'Solo puedes eliminar proyectos que estén Entregados o Cancelados.'
            ];
        }

        if (self::tieneTareasBloqueantes($this->id)) {
            return [
                'puede' => false,
                'mensaje' => 'Este proyecto tiene tareas pendientes. Solo puedes eliminarlo si no tiene tareas o si todas están Completadas o Anuladas.'
            ];
        }

        return [
            'puede' => true,
            'mensaje' => ''
        ];
    }

    public static function eliminarLogico($id, $usuarioId)
    {
        $id = self::$db->escape_string($id);
        $usuarioId = self::$db->escape_string($usuarioId);
        $fecha = date('Y-m-d H:i:s');

        $query = "UPDATE " . static::$tabla . "
              SET eliminado = 1,
                  eliminado_en = '{$fecha}'
              WHERE id = '{$id}'
              AND usuario_id = '{$usuarioId}'
              LIMIT 1";

        return self::$db->query($query);
    }

    public static function detallePorUsuario($id, $usuarioId)
    {
        $id = self::$db->escape_string($id);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
                p.*,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa
              FROM proyectos p
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE p.id = '{$id}'
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.eliminado = 0
              LIMIT 1";

        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
    }

    public static function tablaExiste($tabla)
    {
        $tabla = self::$db->escape_string($tabla);

        $query = "SHOW TABLES LIKE '{$tabla}'";
        $resultado = self::$db->query($query);

        return $resultado && $resultado->num_rows > 0;
    }

    public static function resumenDetalle($proyectoId)
    {
        $proyectoId = self::$db->escape_string($proyectoId);

        $resumen = [
            'tareas_asociadas' => 0,
            'tareas_completadas' => 0,
            'saldo_pendiente' => 0,
            'total_pagado' => 0,
            'ultimo_pago' => 'Sin pagos',
            'avance_general' => 0
        ];

        if (self::tablaExiste('tareas')) {
            $queryTareas = "SELECT 
                            COUNT(*) AS total,
                            SUM(CASE WHEN LOWER(estado) IN ('completada', 'finalizada') THEN 1 ELSE 0 END) AS completadas
                        FROM tareas
                        WHERE proyecto_id = '{$proyectoId}'
                        AND eliminado = 0";

            $resultadoTareas = self::$db->query($queryTareas);
            $tareas = $resultadoTareas->fetch_assoc();

            $resumen['tareas_asociadas'] = (int) ($tareas['total'] ?? 0);
            $resumen['tareas_completadas'] = (int) ($tareas['completadas'] ?? 0);

            if ($resumen['tareas_asociadas'] > 0) {
                $resumen['avance_general'] = round(($resumen['tareas_completadas'] / $resumen['tareas_asociadas']) * 100);
            }
        }

        if (self::tablaExiste('pagos')) {
            $queryPagos = "SELECT 
                    SUM(CASE 
                        WHEN estado = 'Cobrado' 
                        THEN monto_pagado 
                        ELSE 0 
                    END) AS total_pagado,
                    MAX(CASE 
                        WHEN estado = 'Cobrado' 
                        THEN fecha_pago 
                        ELSE NULL 
                    END) AS ultimo_pago
               FROM pagos
               WHERE proyecto_id = '{$proyectoId}'
               AND eliminado = 0";

            $resultadoPagos = self::$db->query($queryPagos);
            $pagos = $resultadoPagos->fetch_assoc();

            $resumen['total_pagado'] = (float) ($pagos['total_pagado'] ?? 0);

            if (!empty($pagos['ultimo_pago'])) {
                $resumen['ultimo_pago'] = date('d M Y', strtotime($pagos['ultimo_pago']));
            }
        }

        return $resumen;
    }

    public static function tareasRecientes($proyectoId, $usuarioId)
    {
        $proyectoId = self::$db->escape_string($proyectoId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                t.id AS tarea_id,
                t.nombre AS tarea_nombre,
                t.estado AS tarea_estado,
                t.prioridad AS tarea_prioridad,
                t.fecha_limite AS tarea_fecha_limite,
                t.avance AS tarea_avance
              FROM tareas t
              WHERE t.proyecto_id = '{$proyectoId}'
              AND t.usuario_id = '{$usuarioId}'
              AND t.eliminado = 0
              ORDER BY 
                CASE 
                    WHEN t.estado = 'Pendiente' THEN 1
                    WHEN t.estado = 'En proceso' THEN 2
                    WHEN t.estado = 'En revisión' THEN 3
                    WHEN t.estado = 'Completada' THEN 4
                    ELSE 5
                END,
                t.fecha_limite ASC,
                t.id DESC
              LIMIT 5";

        return self::consultarSQL($query);
    }

    public static function pagosRecientes($proyectoId, $usuarioId)
    {
        $proyectoId = self::$db->escape_string($proyectoId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                pg.id AS pago_id,
                pg.fecha_pago AS pago_fecha,
                pg.metodo_pago AS pago_metodo,
                pg.monto_pagado AS pago_monto,
                pg.estado AS pago_estado,
                pg.referencia AS pago_referencia
              FROM pagos pg
              WHERE pg.proyecto_id = '{$proyectoId}'
              AND pg.usuario_id = '{$usuarioId}'
              AND pg.eliminado = 0
              ORDER BY pg.fecha_pago DESC, pg.id DESC
              LIMIT 5";

        return self::consultarSQL($query);
    }

    public static function totalNotasProyecto($proyectoId, $usuarioId)
    {
        $proyectoId = self::$db->escape_string($proyectoId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT COUNT(n.id) AS total
              FROM notas n
              WHERE n.proyecto_id = '{$proyectoId}'
              AND n.usuario_id = '{$usuarioId}'
              AND n.eliminado = 0";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
    }

    public static function notasProyecto($proyectoId, $usuarioId)
    {
        $proyectoId = self::$db->escape_string($proyectoId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                n.id AS nota_id,
                n.titulo AS nota_titulo,
                n.contenido AS nota_contenido,
                n.color AS nota_color,
                n.fija AS nota_fija,
                n.creado AS nota_creado,
                p.nombre AS nota_proyecto_nombre
              FROM notas n
              LEFT JOIN proyectos p ON n.proyecto_id = p.id
              WHERE n.proyecto_id = '{$proyectoId}'
              AND n.usuario_id = '{$usuarioId}'
              AND n.eliminado = 0
              ORDER BY n.fija DESC, n.id DESC
              LIMIT 4";

        return self::consultarSQL($query);
    }

    public static function totalVisiblesPorUsuario($usuarioId, $filtros = [])
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT COUNT(p.id) AS total
              FROM proyectos p
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            p.nombre LIKE '%{$busqueda}%'
            OR p.descripcion LIKE '%{$busqueda}%'
            OR p.objetivos LIKE '%{$busqueda}%'
            OR p.observaciones LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $query .= " AND p.cliente_id = '{$clienteId}'";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND p.estado = '{$estado}'";
        }

        if (!empty($filtros['prioridad'])) {
            $prioridad = self::$db->escape_string($filtros['prioridad']);
            $query .= " AND p.prioridad = '{$prioridad}'";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'atrasados') {
                $query .= " AND p.fecha_entrega IS NOT NULL
                    AND p.fecha_entrega < '{$hoy}'
                    AND p.estado NOT IN ('Entregado', 'Cancelado')";
            }

            if ($filtros['alerta'] === 'proximos') {
                $query .= " AND p.fecha_entrega IS NOT NULL
                    AND p.fecha_entrega >= '{$hoy}'
                    AND p.fecha_entrega <= '{$limite}'
                    AND p.estado NOT IN ('Entregado', 'Cancelado')";
            }
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
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

        if (!$password) {
            echo json_encode([
                'ok' => false,
                'mensaje' => 'Ingresa tu contraseña.'
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

        echo json_encode([
            'ok' => true,
            'mensaje' => 'Notas desbloqueadas correctamente.'
        ]);
        exit;
    }
}
