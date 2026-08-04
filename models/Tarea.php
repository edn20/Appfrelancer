<?php

namespace Model;

class Tarea extends ActiveRecord
{
    protected static $tabla = 'tareas';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'proyecto_id',
        'nombre',
        'fecha_limite',
        'prioridad',
        'estado',
        'avance',
        'descripcion',
        'objetivo',
        'observaciones',
        'eliminado'
    ];

    public $id;
    public $usuario_id;
    public $proyecto_id;
    public $nombre;
    public $fecha_limite;
    public $prioridad;
    public $estado;
    public $avance;
    public $descripcion;
    public $objetivo;
    public $observaciones;
    public $eliminado;
    public $eliminado_en;
    public $creado;
    public $actualizado;
    public $proyecto_nombre;
    public $proyecto_estado;
    public $cliente_id;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_empresa;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->proyecto_id = $args['proyecto_id'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->fecha_limite = $args['fecha_limite'] ?? null;
        $this->prioridad = $args['prioridad'] ?? '';
        $this->estado = $args['estado'] ?? 'Pendiente';
        $this->avance = $args['avance'] ?? 0;
        $this->descripcion = $args['descripcion'] ?? '';
        $this->objetivo = $args['objetivo'] ?? '';
        $this->observaciones = $args['observaciones'] ?? '';
        $this->eliminado = $args['eliminado'] ?? 0;
        $this->eliminado_en = $args['eliminado_en'] ?? null;
        $this->creado = $args['creado'] ?? null;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    public function validar()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la tarea es obligatorio';
        }

        if (!$this->proyecto_id) {
            self::$alertas['error'][] = 'Debes seleccionar un proyecto';
        }

        if (!$this->estado) {
            self::$alertas['error'][] = 'El estado de la tarea es obligatorio';
        }
        if ($this->avance !== '' && (!is_numeric($this->avance) || $this->avance < 0 || $this->avance > 100)) {
            self::$alertas['error'][] = 'El porcentaje de avance debe estar entre 0 y 100';
        }

        return self::$alertas;
    }

    public static function visiblesPorUsuario($usuarioId, $filtros = [], $limite = null, $offset = null)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
                t.*,
                p.nombre AS proyecto_nombre,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa
              FROM tareas t
              INNER JOIN proyectos p ON t.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE t.usuario_id = '{$usuarioId}'
              AND t.eliminado = 0
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            t.nombre LIKE '%{$busqueda}%'
            OR t.descripcion LIKE '%{$busqueda}%'
            OR t.objetivo LIKE '%{$busqueda}%'
            OR t.observaciones LIKE '%{$busqueda}%'
            OR p.nombre LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $query .= " AND t.proyecto_id = '{$proyectoId}'";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND t.estado = '{$estado}'";
        }

        if (!empty($filtros['prioridad'])) {
            $prioridad = self::$db->escape_string($filtros['prioridad']);
            $query .= " AND t.prioridad = '{$prioridad}'";
        }

        if (!empty($filtros['vencidas'])) {
            $hoy = date('Y-m-d');

            $query .= " AND t.fecha_limite IS NOT NULL
                AND t.fecha_limite < '{$hoy}'
                AND t.estado NOT IN ('Completada', 'Anulada')";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'vencidas') {
                $query .= " AND t.fecha_limite IS NOT NULL
                    AND t.fecha_limite <= '{$limite}'
                    AND t.estado NOT IN ('Completada', 'Anulada')";
            }
        }

        $query .= " ORDER BY t.id DESC";

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
        $hoy = date('Y-m-d');

        $query = "SELECT
                COUNT(t.id) AS total,
                SUM(CASE WHEN t.estado = 'En proceso' THEN 1 ELSE 0 END) AS en_progreso,
                SUM(CASE WHEN t.estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN t.estado = 'Completada' THEN 1 ELSE 0 END) AS completadas,
                SUM(CASE 
                    WHEN t.fecha_limite IS NOT NULL
                    AND t.fecha_limite < '{$hoy}'
                    AND t.estado NOT IN ('Completada', 'Anulada')
                    THEN 1 ELSE 0 
                END) AS retrasadas
              FROM tareas t
              INNER JOIN proyectos p ON t.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE t.usuario_id = '{$usuarioId}'
              AND t.eliminado = 0
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            t.nombre LIKE '%{$busqueda}%'
            OR t.descripcion LIKE '%{$busqueda}%'
            OR t.objetivo LIKE '%{$busqueda}%'
            OR t.observaciones LIKE '%{$busqueda}%'
            OR p.nombre LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $query .= " AND t.proyecto_id = '{$proyectoId}'";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND t.estado = '{$estado}'";
        }

        if (!empty($filtros['prioridad'])) {
            $prioridad = self::$db->escape_string($filtros['prioridad']);
            $query .= " AND t.prioridad = '{$prioridad}'";
        }

        if (!empty($filtros['vencidas'])) {
            $query .= " AND t.fecha_limite IS NOT NULL
                    AND t.fecha_limite < '{$hoy}'
                    AND t.estado NOT IN ('Completada', 'Anulada')";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'vencidas') {
                $query .= " AND t.fecha_limite IS NOT NULL
                    AND t.fecha_limite <= '{$limite}'
                    AND t.estado NOT IN ('Completada', 'Anulada')";
            }
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return [
            'total' => (int) ($registro['total'] ?? 0),
            'en_progreso' => (int) ($registro['en_progreso'] ?? 0),
            'pendientes' => (int) ($registro['pendientes'] ?? 0),
            'completadas' => (int) ($registro['completadas'] ?? 0),
            'retrasadas' => (int) ($registro['retrasadas'] ?? 0)
        ];
    }

    public function puedeEliminar()
    {
        if ($this->estado !== 'Anulada') {
            return [
                'puede' => false,
                'mensaje' => 'Solo puedes eliminar tareas que estén en estado Anulada. Las tareas completadas deben conservarse para historial.'
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
                t.*,
                p.nombre AS proyecto_nombre,
                p.estado AS proyecto_estado,
                c.id AS cliente_id,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa
              FROM tareas t
              INNER JOIN proyectos p ON t.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE t.id = '{$id}'
              AND t.usuario_id = '{$usuarioId}'
              AND t.eliminado = 0
              AND p.eliminado = 0
              AND c.eliminado = 0
              LIMIT 1";

        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
    }

    public static function totalVisiblesPorUsuario($usuarioId, $filtros = [])
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT COUNT(t.id) AS total
              FROM tareas t
              INNER JOIN proyectos p ON t.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE t.usuario_id = '{$usuarioId}'
              AND t.eliminado = 0
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            t.nombre LIKE '%{$busqueda}%'
            OR t.descripcion LIKE '%{$busqueda}%'
            OR t.objetivo LIKE '%{$busqueda}%'
            OR t.observaciones LIKE '%{$busqueda}%'
            OR p.nombre LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $query .= " AND t.proyecto_id = '{$proyectoId}'";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND t.estado = '{$estado}'";
        }

        if (!empty($filtros['prioridad'])) {
            $prioridad = self::$db->escape_string($filtros['prioridad']);
            $query .= " AND t.prioridad = '{$prioridad}'";
        }

        if (!empty($filtros['vencidas'])) {
            $hoy = date('Y-m-d');

            $query .= " AND t.fecha_limite IS NOT NULL
                AND t.fecha_limite < '{$hoy}'
                AND t.estado NOT IN ('Completada', 'Anulada')";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'vencidas') {
                $query .= " AND t.fecha_limite IS NOT NULL
                    AND t.fecha_limite <= '{$limite}'
                    AND t.estado NOT IN ('Completada', 'Anulada')";
            }
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
    }
}
