<?php

namespace Model;

class Nota extends ActiveRecord
{
    protected static $tabla = 'notas';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'proyecto_id',
        'cliente_id',
        'titulo',
        'contenido',
        'color',
        'fija',
        'protegida',
        'eliminado'
    ];

    public $id;
    public $usuario_id;
    public $proyecto_id;
    public $cliente_id;
    public $titulo;
    public $contenido;
    public $color;
    public $fija;
    public $protegida;
    public $eliminado;
    public $eliminado_en;
    public $creado;
    public $actualizado;

    public $proyecto_nombre;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_empresa;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->proyecto_id = $args['proyecto_id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->titulo = $args['titulo'] ?? '';
        $this->contenido = $args['contenido'] ?? '';
        $this->color = $args['color'] ?? 'amarillo';
        $this->fija = $args['fija'] ?? 0;
        $this->protegida = $args['protegida'] ?? 1;
        $this->eliminado = $args['eliminado'] ?? 0;
        $this->eliminado_en = $args['eliminado_en'] ?? null;
        $this->creado = $args['creado'] ?? null;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    public function validar()
    {
        if (!$this->titulo) {
            self::$alertas['error'][] = 'El título de la nota es obligatorio';
        }

        if (!$this->contenido) {
            self::$alertas['error'][] = 'El contenido de la nota es obligatorio';
        }

        if (!in_array($this->color, ['amarillo', 'verde', 'azul', 'rosa', 'gris'])) {
            self::$alertas['error'][] = 'El color seleccionado no es válido';
        }

        return self::$alertas;
    }

    public static function visiblesPorUsuario($usuarioId, $filtros = [], $limite = null, $offset = null)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                n.*,
                p.nombre AS proyecto_nombre,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa
              FROM notas n
              LEFT JOIN proyectos p ON n.proyecto_id = p.id
              LEFT JOIN clientes c ON n.cliente_id = c.id
              WHERE n.usuario_id = '{$usuarioId}'
              AND n.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            n.titulo LIKE '%{$busqueda}%'
            OR n.contenido LIKE '%{$busqueda}%'
            OR p.nombre LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $query .= " AND n.cliente_id = '{$clienteId}'";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $query .= " AND n.proyecto_id = '{$proyectoId}'";
        }

        if (!empty($filtros['color'])) {
            $color = self::$db->escape_string($filtros['color']);
            $query .= " AND n.color = '{$color}'";
        }

        $query .= " ORDER BY 
                    CASE WHEN c.nombre IS NULL THEN 1 ELSE 0 END,
                    c.nombre ASC,
                    c.apellido ASC,
                    n.fija DESC,
                    n.id DESC";

        if ($limite !== null && $offset !== null) {
            $limite = (int) $limite;
            $offset = (int) $offset;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return self::consultarSQL($query);
    }

    public static function detallePorUsuario($id, $usuarioId)
    {
        $id = self::$db->escape_string($id);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
                    n.*,
                    p.nombre AS proyecto_nombre,
                    c.nombre AS cliente_nombre,
                    c.apellido AS cliente_apellido,
                    c.empresa AS cliente_empresa
                  FROM notas n
                  LEFT JOIN proyectos p ON n.proyecto_id = p.id
                  LEFT JOIN clientes c ON n.cliente_id = c.id
                  WHERE n.id = '{$id}'
                  AND n.usuario_id = '{$usuarioId}'
                  AND n.eliminado = 0
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

    public function validarActualizacion()
    {
        if (!$this->titulo) {
            self::$alertas['error'][] = 'El título de la nota es obligatorio';
        }

        if (!$this->contenido) {
            self::$alertas['error'][] = 'El contenido de la nota es obligatorio';
        }

        if (!in_array($this->color, ['amarillo', 'verde', 'azul', 'rosa', 'gris'])) {
            self::$alertas['error'][] = 'El color seleccionado no es válido';
        }

        return self::$alertas;
    }

    public static function totalVisiblesPorUsuario($usuarioId, $filtros = [])
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT COUNT(n.id) AS total
              FROM notas n
              LEFT JOIN proyectos p ON n.proyecto_id = p.id
              LEFT JOIN clientes c ON n.cliente_id = c.id
              WHERE n.usuario_id = '{$usuarioId}'
              AND n.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            n.titulo LIKE '%{$busqueda}%'
            OR n.contenido LIKE '%{$busqueda}%'
            OR p.nombre LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $query .= " AND n.cliente_id = '{$clienteId}'";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $query .= " AND n.proyecto_id = '{$proyectoId}'";
        }

        if (!empty($filtros['color'])) {
            $color = self::$db->escape_string($filtros['color']);
            $query .= " AND n.color = '{$color}'";
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
    }
}
