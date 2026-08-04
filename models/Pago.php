<?php

namespace Model;

class Pago extends ActiveRecord
{
    protected static $tabla = 'pagos';

    protected static $columnasDB = [
        'id',
        'usuario_id',
        'proyecto_id',
        'metodo_pago',
        'estado',
        'fecha_pago',
        'fecha_vencimiento',
        'referencia',
        'monto_total',
        'monto_pagado',
        'descripcion',
        'notas_internas',
        'eliminado'
    ];

    public $id;
    public $usuario_id;
    public $proyecto_id;
    public $metodo_pago;
    public $estado;
    public $fecha_pago;
    public $fecha_vencimiento;
    public $referencia;
    public $monto_total;
    public $monto_pagado;
    public $descripcion;
    public $notas_internas;
    public $eliminado;
    public $eliminado_en;
    public $creado;
    public $actualizado;

    public $proyecto_nombre;
    public $proyecto_valor_total;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_empresa;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->proyecto_id = $args['proyecto_id'] ?? '';
        $this->metodo_pago = $args['metodo_pago'] ?? '';
        $this->estado = $args['estado'] ?? 'Pendiente';
        $this->fecha_pago = $args['fecha_pago'] ?? null;
        $this->fecha_vencimiento = $args['fecha_vencimiento'] ?? null;
        $this->referencia = $args['referencia'] ?? '';
        $this->monto_total = $args['monto_total'] ?? 0;
        $this->monto_pagado = $args['monto_pagado'] ?? 0;
        $this->descripcion = $args['descripcion'] ?? '';
        $this->notas_internas = $args['notas_internas'] ?? '';
        $this->eliminado = $args['eliminado'] ?? 0;
        $this->eliminado_en = $args['eliminado_en'] ?? null;
        $this->creado = $args['creado'] ?? null;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    public function validar($saldoPendienteActual = null)
    {
        if (!$this->proyecto_id) {
            self::$alertas['error'][] = 'Debes seleccionar un proyecto';
        }

        if (!$this->metodo_pago) {
            self::$alertas['error'][] = 'El método de pago es obligatorio';
        }

        if (!$this->estado) {
            self::$alertas['error'][] = 'El estado del pago es obligatorio';
        }

        if ($this->monto_total === '' || !is_numeric($this->monto_total)) {
            self::$alertas['error'][] = 'El monto total debe ser un número válido';
        }

        if ($this->monto_pagado === '' || !is_numeric($this->monto_pagado)) {
            self::$alertas['error'][] = 'El monto pagado debe ser un número válido';
        }

        if ((float) $this->monto_pagado < 0) {
            self::$alertas['error'][] = 'El monto pagado no puede ser negativo';
        }

        if ((float) $this->monto_pagado <= 0) {
            self::$alertas['error'][] = 'El monto pagado debe ser mayor a 0';
        }

        if ((float) $this->monto_pagado > (float) $this->monto_total) {
            self::$alertas['error'][] = 'El monto pagado no puede ser mayor al monto total del proyecto';
        }

        if ($saldoPendienteActual !== null && (float) $this->monto_pagado > (float) $saldoPendienteActual) {
            self::$alertas['error'][] = 'El monto pagado no puede ser mayor al saldo pendiente del proyecto';
        }

        return self::$alertas;
    }

    public static function totalPagadoPorProyecto($proyectoId, $excluirPagoId = null)
    {
        $proyectoId = self::$db->escape_string($proyectoId);

        $query = "SELECT SUM(monto_pagado) AS total_pagado
              FROM " . static::$tabla . "
              WHERE proyecto_id = '{$proyectoId}'
              AND eliminado = 0
              AND estado = 'Cobrado'";

        if ($excluirPagoId) {
            $excluirPagoId = self::$db->escape_string($excluirPagoId);
            $query .= " AND id != '{$excluirPagoId}'";
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (float) ($registro['total_pagado'] ?? 0);
    }

    public static function saldoPendienteProyecto($proyecto)
    {
        $valorTotal = (float) ($proyecto->valor_total ?? 0);
        $totalPagado = self::totalPagadoPorProyecto($proyecto->id);

        return max($valorTotal - $totalPagado, 0);
    }

    public static function visiblesPorUsuario($usuarioId, $filtros = [], $limite = null, $offset = null)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                pg.*,
                p.nombre AS proyecto_nombre,
                p.valor_total AS proyecto_valor_total,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa
              FROM pagos pg
              INNER JOIN proyectos p ON pg.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE pg.usuario_id = '{$usuarioId}'
              AND pg.eliminado = 0
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            p.nombre LIKE '%{$busqueda}%'
            OR pg.descripcion LIKE '%{$busqueda}%'
            OR pg.referencia LIKE '%{$busqueda}%'
            OR pg.notas_internas LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $query .= " AND pg.proyecto_id = '{$proyectoId}'";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $query .= " AND c.id = '{$clienteId}'";
        }

        if (!empty($filtros['cliente'])) {
            $cliente = self::$db->escape_string($filtros['cliente']);

            $query .= " AND (
            c.nombre LIKE '%{$cliente}%'
            OR c.apellido LIKE '%{$cliente}%'
            OR c.empresa LIKE '%{$cliente}%'
        )";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND pg.estado = '{$estado}'";
        }

        if (!empty($filtros['metodo_pago'])) {
            $metodoPago = self::$db->escape_string($filtros['metodo_pago']);
            $query .= " AND pg.metodo_pago = '{$metodoPago}'";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'vencidos') {
                $query .= " AND pg.fecha_vencimiento IS NOT NULL
                    AND pg.fecha_vencimiento <= '{$limite}'
                    AND pg.estado IN ('Pendiente', 'Por confirmar')";
            }
        }

        $query .= " ORDER BY pg.id DESC";

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

        $where = "WHERE p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $where .= " AND (
            p.nombre LIKE '%{$busqueda}%'
            OR pg.descripcion LIKE '%{$busqueda}%'
            OR pg.referencia LIKE '%{$busqueda}%'
            OR pg.notas_internas LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $where .= " AND p.id = '{$proyectoId}'";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $where .= " AND c.id = '{$clienteId}'";
        }

        if (!empty($filtros['cliente'])) {
            $cliente = self::$db->escape_string($filtros['cliente']);

            $where .= " AND (
            c.nombre LIKE '%{$cliente}%'
            OR c.apellido LIKE '%{$cliente}%'
            OR c.empresa LIKE '%{$cliente}%'
        )";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $where .= " AND pg.estado = '{$estado}'";
        }

        if (!empty($filtros['metodo_pago'])) {
            $metodoPago = self::$db->escape_string($filtros['metodo_pago']);
            $where .= " AND pg.metodo_pago = '{$metodoPago}'";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'vencidos') {
                $query .= " AND pg.fecha_vencimiento IS NOT NULL
                    AND pg.fecha_vencimiento <= '{$limite}'
                    AND pg.estado IN ('Pendiente', 'Por confirmar')";
            }
        }

        $query = "SELECT
                SUM(resumen.proyecto_total) AS total_facturado,
                SUM(resumen.total_cobrado) AS total_recibido,
                SUM(resumen.total_por_confirmar) AS pendiente,
                SUM(GREATEST(resumen.proyecto_total - resumen.total_cobrado, 0)) AS por_cobrar
              FROM (
                    SELECT
                        p.id,
                        p.valor_total AS proyecto_total,
                        COALESCE(SUM(CASE 
                            WHEN pg.estado = 'Cobrado' 
                            AND pg.eliminado = 0 
                            THEN pg.monto_pagado 
                            ELSE 0 
                        END), 0) AS total_cobrado,
                        COALESCE(SUM(CASE 
                            WHEN pg.estado = 'Por confirmar' 
                            AND pg.eliminado = 0 
                            THEN pg.monto_pagado 
                            ELSE 0 
                        END), 0) AS total_por_confirmar
                    FROM proyectos p
                    INNER JOIN clientes c ON p.cliente_id = c.id
                    LEFT JOIN pagos pg ON pg.proyecto_id = p.id
                    {$where}
                    GROUP BY p.id
              ) AS resumen";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return [
            'total_facturado' => (float) ($registro['total_facturado'] ?? 0),
            'total_recibido' => (float) ($registro['total_recibido'] ?? 0),
            'pendiente' => (float) ($registro['pendiente'] ?? 0),
            'vencido' => 0,
            'por_cobrar' => (float) ($registro['por_cobrar'] ?? 0)
        ];
    }

    public static function detallePorUsuario($id, $usuarioId)
    {
        $id = self::$db->escape_string($id);
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT 
                pg.*,
                p.nombre AS proyecto_nombre,
                p.valor_total AS proyecto_valor_total,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa
              FROM pagos pg
              INNER JOIN proyectos p ON pg.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE pg.id = '{$id}'
              AND pg.usuario_id = '{$usuarioId}'
              AND pg.eliminado = 0
              AND p.eliminado = 0
              AND c.eliminado = 0
              LIMIT 1";

        $resultado = self::consultarSQL($query);

        return array_shift($resultado);
    }

    public function validarActualizacion($saldoDisponible = null)
    {
        if (!$this->estado) {
            self::$alertas['error'][] = 'El estado del pago es obligatorio';
        }

        if ($this->monto_pagado === '' || !is_numeric($this->monto_pagado)) {
            self::$alertas['error'][] = 'El monto cobrado debe ser un número válido';
        }

        if ((float) $this->monto_pagado < 0) {
            self::$alertas['error'][] = 'El monto cobrado no puede ser negativo';
        }

        if ((float) $this->monto_pagado > (float) $this->monto_total) {
            self::$alertas['error'][] = 'El monto cobrado no puede ser mayor al monto total del proyecto';
        }

        if ($saldoDisponible !== null && (float) $this->monto_pagado > (float) $saldoDisponible) {
            self::$alertas['error'][] = 'El monto cobrado no puede ser mayor al saldo disponible del proyecto';
        }

        return self::$alertas;
    }

    public static function totalVisiblesPorUsuario($usuarioId, $filtros = [])
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT COUNT(pg.id) AS total
              FROM pagos pg
              INNER JOIN proyectos p ON pg.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE pg.usuario_id = '{$usuarioId}'
              AND pg.eliminado = 0
              AND p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            p.nombre LIKE '%{$busqueda}%'
            OR pg.descripcion LIKE '%{$busqueda}%'
            OR pg.referencia LIKE '%{$busqueda}%'
            OR pg.notas_internas LIKE '%{$busqueda}%'
            OR c.nombre LIKE '%{$busqueda}%'
            OR c.apellido LIKE '%{$busqueda}%'
            OR c.empresa LIKE '%{$busqueda}%'
        )";
        }

        if (!empty($filtros['proyecto_id'])) {
            $proyectoId = self::$db->escape_string($filtros['proyecto_id']);
            $query .= " AND pg.proyecto_id = '{$proyectoId}'";
        }

        if (!empty($filtros['cliente_id'])) {
            $clienteId = self::$db->escape_string($filtros['cliente_id']);
            $query .= " AND c.id = '{$clienteId}'";
        }

        if (!empty($filtros['cliente'])) {
            $cliente = self::$db->escape_string($filtros['cliente']);

            $query .= " AND (
            c.nombre LIKE '%{$cliente}%'
            OR c.apellido LIKE '%{$cliente}%'
            OR c.empresa LIKE '%{$cliente}%'
        )";
        }

        if (!empty($filtros['estado'])) {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND pg.estado = '{$estado}'";
        }

        if (!empty($filtros['metodo_pago'])) {
            $metodoPago = self::$db->escape_string($filtros['metodo_pago']);
            $query .= " AND pg.metodo_pago = '{$metodoPago}'";
        }

        if (!empty($filtros['alerta'])) {
            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+5 days'));

            if ($filtros['alerta'] === 'vencidos') {
                $query .= " AND pg.fecha_vencimiento IS NOT NULL
                    AND pg.fecha_vencimiento <= '{$limite}'
                    AND pg.estado IN ('Pendiente', 'Por confirmar')";
            }
        }

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
    }
}
