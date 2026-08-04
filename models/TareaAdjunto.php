<?php

namespace Model;

class TareaAdjunto extends ActiveRecord
{
    protected static $tabla = 'tarea_adjuntos';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'tarea_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'tipo',
        'peso',
        'eliminado'
    ];

    public $id;
    public $usuario_id;
    public $tarea_id;
    public $nombre_original;
    public $nombre_archivo;
    public $ruta;
    public $tipo;
    public $peso;
    public $eliminado;
    public $eliminado_en;
    public $creado;
    public $actualizado;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->tarea_id = $args['tarea_id'] ?? '';
        $this->nombre_original = $args['nombre_original'] ?? '';
        $this->nombre_archivo = $args['nombre_archivo'] ?? '';
        $this->ruta = $args['ruta'] ?? '';
        $this->tipo = $args['tipo'] ?? '';
        $this->peso = $args['peso'] ?? 0;
        $this->eliminado = $args['eliminado'] ?? 0;
        $this->eliminado_en = $args['eliminado_en'] ?? null;
        $this->creado = $args['creado'] ?? null;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    public static function porTarea($tareaId, $usuarioId)
    {
        $tareaId = self::$db->escape_string($tareaId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT * FROM " . static::$tabla . "
                  WHERE tarea_id = '{$tareaId}'
                  AND usuario_id = '{$usuarioId}'
                  AND eliminado = 0
                  ORDER BY id DESC";

        return self::consultarSQL($query);
    }

    public static function protegidoPorUsuario($adjuntoId, $usuarioId)
    {
        $adjuntoId = self::$db->escape_string($adjuntoId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
                a.*
              FROM tarea_adjuntos a
              INNER JOIN tareas t ON a.tarea_id = t.id
              INNER JOIN proyectos p ON t.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE a.id = '{$adjuntoId}'
              AND a.usuario_id = '{$usuarioId}'
              AND t.usuario_id = '{$usuarioId}'
              AND p.usuario_id = '{$usuarioId}'
              AND c.usuario_id = '{$usuarioId}'
              AND a.eliminado = 0
              AND t.eliminado = 0
              AND p.eliminado = 0
              AND c.eliminado = 0
              LIMIT 1";

        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
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
}
