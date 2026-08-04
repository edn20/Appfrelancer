<?php

namespace Model;

class ConfiguracionNotificacion extends ActiveRecord
{
    protected static $tabla = 'configuracion_notificaciones';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'tareas_vencidas',
        'tareas_hoy',
        'tareas_proximas',
        'pagos_vencidos',
        'pagos_proximos',
        'proyectos_atrasados',
        'proyectos_proximos',
        'obligaciones_proximas'
    ];

    public $id;
    public $usuario_id;
    public $tareas_vencidas;
    public $tareas_hoy;
    public $tareas_proximas;
    public $pagos_vencidos;
    public $pagos_proximos;
    public $proyectos_atrasados;
    public $proyectos_proximos;
    public $obligaciones_proximas;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->tareas_vencidas = $args['tareas_vencidas'] ?? 1;
        $this->tareas_hoy = $args['tareas_hoy'] ?? 1;
        $this->tareas_proximas = $args['tareas_proximas'] ?? 1;
        $this->pagos_vencidos = $args['pagos_vencidos'] ?? 1;
        $this->pagos_proximos = $args['pagos_proximos'] ?? 1;
        $this->proyectos_atrasados = $args['proyectos_atrasados'] ?? 1;
        $this->proyectos_proximos = $args['proyectos_proximos'] ?? 1;
        $this->obligaciones_proximas = $args['obligaciones_proximas'] ?? 0;
    }

    public static function porUsuario($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT * FROM " . static::$tabla . "
                  WHERE usuario_id = '{$usuarioId}'
                  LIMIT 1";

        $resultado = self::consultarSQL($query);
        $configuracion = array_shift($resultado);

        if ($configuracion) {
            return $configuracion;
        }

        $configuracion = new self([
            'usuario_id' => $usuarioId,
            'tareas_vencidas' => 1,
            'tareas_hoy' => 1,
            'tareas_proximas' => 1,
            'pagos_vencidos' => 1,
            'pagos_proximos' => 1,
            'proyectos_atrasados' => 1,
            'proyectos_proximos' => 1,
            'obligaciones_proximas' => 0
        ]);

        $configuracion->guardar();

        return self::porUsuario($usuarioId);
    }

    public function estaActiva($clave): bool
    {
        return isset($this->$clave) && (int) $this->$clave === 1;
    }
}
