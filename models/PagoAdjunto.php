<?php

namespace Model;

class PagoAdjunto extends ActiveRecord
{
    protected static $tabla = 'pago_adjuntos';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'pago_id',
        'nombre_original',
        'nombre_archivo',
        'ruta',
        'tipo',
        'peso',
        'eliminado'
    ];

    public $id;
    public $usuario_id;
    public $pago_id;
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
        $this->pago_id = $args['pago_id'] ?? '';
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

    public static function porPago($pagoId, $usuarioId)
    {
        $pagoId = self::$db->escape_string($pagoId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT * FROM " . static::$tabla . "
              WHERE pago_id = '{$pagoId}'
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
              FROM pago_adjuntos a
              INNER JOIN pagos pg ON a.pago_id = pg.id
              INNER JOIN proyectos p ON pg.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE a.id = '{$adjuntoId}'
              AND a.usuario_id = '{$usuarioId}'
              AND pg.usuario_id = '{$usuarioId}'
              AND p.usuario_id = '{$usuarioId}'
              AND c.usuario_id = '{$usuarioId}'
              AND a.eliminado = 0
              AND pg.eliminado = 0
              AND p.eliminado = 0
              AND c.eliminado = 0
              LIMIT 1";

        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
    }
}
