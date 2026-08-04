<?php

namespace Model;

class Cliente extends ActiveRecord
{
    protected static $tabla = 'clientes';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'nombre',
        'apellido',
        'empresa',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'identificacion',
        'tipo_cliente',
        'fuente_contacto',
        'estado',
        'eliminado',
        'observaciones'
    ];

    public $id;
    public $usuario_id;
    public $nombre;
    public $apellido;
    public $empresa;
    public $telefono;
    public $email;
    public $direccion;
    public $ciudad;
    public $identificacion;
    public $tipo_cliente;
    public $fuente_contacto;
    public $estado;
    public $eliminado;
    public $eliminado_en;
    public $observaciones;
    public $creado;
    public $actualizado;

    public $total_proyectos;
    public $saldo_pendiente;

    public $proyectos_activos;
    public $proyectos_entregados;
    public $total_facturado;
    public $ultimo_pago;

    public $proyecto_id;
    public $proyecto_nombre;
    public $proyecto_fecha_entrega;
    public $proyecto_valor_total;
    public $proyecto_prioridad;
    public $proyecto_estado;
    public $proyecto_tipo_cobro;
    public $proyecto_descripcion;

    public $pago_id;
    public $pago_fecha;
    public $pago_metodo;
    public $pago_monto;
    public $pago_estado;
    public $pago_referencia;
    public $pago_proyecto_nombre;

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
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->empresa = $args['empresa'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->ciudad = $args['ciudad'] ?? '';
        $this->identificacion = $args['identificacion'] ?? '';
        $this->tipo_cliente = $args['tipo_cliente'] ?? '';
        $this->fuente_contacto = $args['fuente_contacto'] ?? '';
        $this->estado = $args['estado'] ?? 1;
        $this->eliminado = $args['eliminado'] ?? 0;
        $this->eliminado_en = $args['eliminado_en'] ?? null;
        $this->observaciones = $args['observaciones'] ?? '';
        $this->creado = $args['creado'] ?? null;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    public function validar()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del cliente es obligatorio';
        }

        if ($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El correo electrónico no es válido';
        }

        if ($this->estado === '') {
            self::$alertas['error'][] = 'El estado del cliente es obligatorio';
        }

        return self::$alertas;
    }

    public static function visiblesPorUsuario($usuarioId, $filtros = [], $limite = null, $offset = null)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
                c.*,
                COUNT(p.id) AS total_proyectos,
                COALESCE(SUM(
                    CASE 
                        WHEN p.eliminado = 0 
                        THEN GREATEST(
                            p.valor_total - COALESCE((
                                SELECT SUM(pg.monto_pagado)
                                FROM pagos pg
                                WHERE pg.proyecto_id = p.id
                                AND pg.usuario_id = c.usuario_id
                                AND pg.eliminado = 0
                                AND pg.estado = 'Cobrado'
                            ), 0),
                            0
                        )
                        ELSE 0
                    END
                ), 0) AS saldo_pendiente
              FROM clientes c
              LEFT JOIN proyectos p 
                ON p.cliente_id = c.id
                AND p.usuario_id = c.usuario_id
                AND p.eliminado = 0
              WHERE c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
            OR c.email LIKE '%{$busqueda}%'
            OR c.telefono LIKE '%{$busqueda}%'
        )";
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND c.estado = '{$estado}'";
        }

        if (!empty($filtros['tipo_cliente'])) {
            $tipoCliente = self::$db->escape_string($filtros['tipo_cliente']);
            $query .= " AND c.tipo_cliente = '{$tipoCliente}'";
        }

        $query .= " GROUP BY c.id
            ORDER BY c.id DESC";

        if ($limite !== null && $offset !== null) {
            $limite = (int) $limite;
            $offset = (int) $offset;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return self::consultarSQL($query);
    }

    public static function resumenDetalle($clienteId, $usuarioId)
    {
        $clienteId = self::$db->escape_string($clienteId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $resumen = [
            'proyectos_asociados' => 0,
            'proyectos_activos' => 0,
            'proyectos_entregados' => 0,
            'saldo_pendiente' => 0,
            'total_facturado' => 0,
            'ultimo_pago' => 'Sin pagos'
        ];

        $query = "SELECT
                COUNT(p.id) AS proyectos_asociados,
                SUM(CASE 
                    WHEN p.estado NOT IN ('Entregado', 'Cancelado') 
                    THEN 1 
                    ELSE 0 
                END) AS proyectos_activos,
                SUM(CASE 
                    WHEN p.estado = 'Entregado' 
                    THEN 1 
                    ELSE 0 
                END) AS proyectos_entregados,
                COALESCE(SUM(p.valor_total), 0) AS total_facturado
              FROM proyectos p
              WHERE p.cliente_id = '{$clienteId}'
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0";

        $resultado = self::$db->query($query);
        $datos = $resultado->fetch_assoc();

        $resumen['proyectos_asociados'] = (int) ($datos['proyectos_asociados'] ?? 0);
        $resumen['proyectos_activos'] = (int) ($datos['proyectos_activos'] ?? 0);
        $resumen['proyectos_entregados'] = (int) ($datos['proyectos_entregados'] ?? 0);
        $resumen['total_facturado'] = (float) ($datos['total_facturado'] ?? 0);

        $queryPagos = "SELECT
                    COALESCE(SUM(CASE 
                        WHEN pg.estado = 'Cobrado' 
                        THEN pg.monto_pagado 
                        ELSE 0 
                    END), 0) AS total_cobrado,
                    MAX(CASE 
                        WHEN pg.estado = 'Cobrado' 
                        THEN pg.fecha_pago 
                        ELSE NULL 
                    END) AS ultimo_pago
                  FROM pagos pg
                  INNER JOIN proyectos p ON pg.proyecto_id = p.id
                  WHERE p.cliente_id = '{$clienteId}'
                  AND p.usuario_id = '{$usuarioId}'
                  AND pg.usuario_id = '{$usuarioId}'
                  AND p.eliminado = 0
                  AND pg.eliminado = 0";

        $resultadoPagos = self::$db->query($queryPagos);
        $pagos = $resultadoPagos->fetch_assoc();

        $totalCobrado = (float) ($pagos['total_cobrado'] ?? 0);

        $resumen['saldo_pendiente'] = max($resumen['total_facturado'] - $totalCobrado, 0);

        if (!empty($pagos['ultimo_pago'])) {
            $resumen['ultimo_pago'] = date('d/m/Y', strtotime($pagos['ultimo_pago']));
        }

        return $resumen;
    }

    public static function proyectosRecientes($clienteId, $usuarioId)
    {
        $clienteId = self::$db->escape_string($clienteId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
                p.id AS proyecto_id,
                p.nombre AS proyecto_nombre,
                p.fecha_entrega AS proyecto_fecha_entrega,
                p.valor_total AS proyecto_valor_total,
                p.prioridad AS proyecto_prioridad,
                p.estado AS proyecto_estado,
                p.tipo_cobro AS proyecto_tipo_cobro,
                p.descripcion AS proyecto_descripcion
              FROM proyectos p
              WHERE p.cliente_id = '{$clienteId}'
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              ORDER BY p.id DESC
              LIMIT 5";

        return self::consultarSQL($query);
    }

    public static function pagosRecientes($clienteId, $usuarioId)
    {
        $clienteId = self::$db->escape_string($clienteId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                pg.id AS pago_id,
                pg.fecha_pago AS pago_fecha,
                pg.metodo_pago AS pago_metodo,
                pg.monto_pagado AS pago_monto,
                pg.estado AS pago_estado,
                pg.referencia AS pago_referencia,
                p.nombre AS pago_proyecto_nombre
              FROM pagos pg
              INNER JOIN proyectos p ON pg.proyecto_id = p.id
              WHERE p.cliente_id = '{$clienteId}'
              AND p.usuario_id = '{$usuarioId}'
              AND pg.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND pg.eliminado = 0
              ORDER BY pg.fecha_pago DESC, pg.id DESC
              LIMIT 2";

        return self::consultarSQL($query);
    }

    public static function notasRecientes($clienteId, $usuarioId)
    {
        $clienteId = self::$db->escape_string($clienteId);
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
              WHERE n.cliente_id = '{$clienteId}'
              AND n.usuario_id = '{$usuarioId}'
              AND n.eliminado = 0
              ORDER BY n.fija DESC, n.id DESC
              LIMIT 2";

        return self::consultarSQL($query);
    }

    public static function totalVisiblesPorUsuario($usuarioId, $filtros = [])
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT COUNT(c.id) AS total
              FROM clientes c
              WHERE c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
            OR c.email LIKE '%{$busqueda}%'
            OR c.telefono LIKE '%{$busqueda}%'
            OR c.identificacion LIKE '%{$busqueda}%'
            OR c.ciudad LIKE '%{$busqueda}%'
        )";
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND c.estado = '{$estado}'";
        }

        if (!empty($filtros['tipo_cliente'])) {
            $tipoCliente = self::$db->escape_string($filtros['tipo_cliente']);
            $query .= " AND c.tipo_cliente = '{$tipoCliente}'";
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
    }

    public static function normalizarIdentificacion($identificacion)
    {
        return preg_replace('/\D/', '', trim($identificacion ?? ''));
    }

    public static function baseIdentificacion($identificacion)
    {
        $identificacion = self::normalizarIdentificacion($identificacion);

        if (strlen($identificacion) === 13 && substr($identificacion, -3) === '001') {
            return substr($identificacion, 0, 10);
        }

        return $identificacion;
    }

    public static function tipoIdentificacion($identificacion)
    {
        $identificacion = self::normalizarIdentificacion($identificacion);

        if (strlen($identificacion) === 13 && substr($identificacion, -3) === '001') {
            return 'RUC';
        }

        if (strlen($identificacion) === 10) {
            return 'cédula';
        }

        return 'identificación';
    }

    public static function buscarDuplicadoIdentificacion($usuarioId, $identificacion, $ignorarId = null)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $identificacion = self::normalizarIdentificacion($identificacion);
        $base = self::baseIdentificacion($identificacion);

        if (!$identificacion || !$base) {
            return null;
        }

        $ruc = strlen($base) === 10 ? $base . '001' : $base;

        $identificacion = self::$db->escape_string($identificacion);
        $base = self::$db->escape_string($base);
        $ruc = self::$db->escape_string($ruc);

        $campoIdentificacion = "REPLACE(REPLACE(REPLACE(c.identificacion, '-', ''), ' ', ''), '.', '')";

        $query = "SELECT c.*
              FROM clientes c
              WHERE c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0
              AND (
                    {$campoIdentificacion} = '{$identificacion}'
                    OR {$campoIdentificacion} = '{$base}'
                    OR {$campoIdentificacion} = '{$ruc}'
              )";

        if ($ignorarId !== null) {
            $ignorarId = self::$db->escape_string($ignorarId);
            $query .= " AND c.id != '{$ignorarId}'";
        }

        $query .= " LIMIT 1";

        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
    }

    public static function totalNotasCliente($clienteId, $usuarioId)
    {
        $clienteId = self::$db->escape_string($clienteId);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT COUNT(n.id) AS total
              FROM notas n
              WHERE n.cliente_id = '{$clienteId}'
              AND n.usuario_id = '{$usuarioId}'
              AND n.eliminado = 0";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
    }
}
