<?php

namespace Model;

class ConfiguracionPreferencia extends ActiveRecord
{
    protected static $tabla = 'configuracion_preferencias';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'formato_fecha'
    ];

    public $id;
    public $usuario_id;
    public $formato_fecha;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->formato_fecha = $args['formato_fecha'] ?? 'dd_mm_yyyy';
    }

    public static function porUsuario($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT * FROM " . static::$tabla . "
                  WHERE usuario_id = '{$usuarioId}'
                  LIMIT 1";

        $resultado = self::consultarSQL($query);
        $preferencias = array_shift($resultado);

        if ($preferencias) {
            return $preferencias;
        }

        $preferencias = new self([
            'usuario_id' => $usuarioId,
            'formato_fecha' => 'dd_mm_yyyy'
        ]);

        $preferencias->guardar();

        return self::porUsuario($usuarioId);
    }

    public static function formatosFecha()
    {
        return [
            'dd_mm_yyyy' => '03/08/2026',
            'dd_mes_yyyy' => '03 Agosto 2026',
            'dia_dd_mes_yyyy' => 'Lunes, 03 de Agosto del 2026',
            'dd_mm_yy' => '03/08/26',
            'mes_dd_yyyy' => 'Agosto, 03 del 2026'
        ];
    }

    public static function formatoValido($formato): bool
    {
        return array_key_exists($formato, self::formatosFecha());
    }
}
