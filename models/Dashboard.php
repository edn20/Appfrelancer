<?php

namespace Model;

class Dashboard extends ActiveRecord
{

    public $proyecto_id;
    public $proyecto_nombre;
    public $fecha_entrega;
    public $estado;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_empresa;

    public $anio;
    public $mes;
    public $total;


    public static function resumenGeneral($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $hoy = date('Y-m-d');

        $query = "SELECT
                (
                    SELECT COUNT(*)
                    FROM clientes
                    WHERE usuario_id = '{$usuarioId}'
                    AND eliminado = 0
                    AND estado = 1
                ) AS clientes_activos,

                (
                    SELECT COUNT(*)
                    FROM proyectos
                    WHERE usuario_id = '{$usuarioId}'
                    AND eliminado = 0
                    AND estado = 'En proceso'
                ) AS proyectos_en_proceso,

                (
                    SELECT COUNT(*)
                    FROM proyectos
                    WHERE usuario_id = '{$usuarioId}'
                    AND eliminado = 0
                    AND estado = 'Entregado'
                ) AS proyectos_completados,

                (
                    SELECT COUNT(*)
                    FROM tareas
                    WHERE usuario_id = '{$usuarioId}'
                    AND eliminado = 0
                    AND estado = 'Pendiente'
                ) AS tareas_pendientes,

                (
                    SELECT COUNT(*)
                    FROM tareas
                    WHERE usuario_id = '{$usuarioId}'
                    AND eliminado = 0
                    AND estado NOT IN ('Completada', 'Anulada')
                    AND fecha_limite IS NOT NULL
                    AND fecha_limite < '{$hoy}'
                ) AS tareas_vencidas";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return [
            'clientes_activos' => (int) ($registro['clientes_activos'] ?? 0),
            'proyectos_en_proceso' => (int) ($registro['proyectos_en_proceso'] ?? 0),
            'proyectos_completados' => (int) ($registro['proyectos_completados'] ?? 0),
            'tareas_pendientes' => (int) ($registro['tareas_pendientes'] ?? 0),
            'tareas_vencidas' => (int) ($registro['tareas_vencidas'] ?? 0)
        ];
    }

    public static function resumenFinanciero($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $inicioMes = date('Y-m-01');
        $finMes = date('Y-m-t');

        $query = "SELECT
                    (
                        SELECT COALESCE(SUM(monto_pagado), 0)
                        FROM pagos
                        WHERE usuario_id = '{$usuarioId}'
                        AND eliminado = 0
                        AND estado = 'Cobrado'
                        AND fecha_pago BETWEEN '{$inicioMes}' AND '{$finMes}'
                    ) AS cobrado_mes,

                    (
                        SELECT COALESCE(SUM(p.valor_total), 0)
                        FROM proyectos p
                        INNER JOIN clientes c ON p.cliente_id = c.id
                        WHERE p.usuario_id = '{$usuarioId}'
                        AND p.eliminado = 0
                        AND c.usuario_id = '{$usuarioId}'
                        AND c.eliminado = 0
                    ) AS total_facturado,

                    (
                        SELECT COALESCE(SUM(pg.monto_pagado), 0)
                        FROM pagos pg
                        INNER JOIN proyectos p ON pg.proyecto_id = p.id
                        INNER JOIN clientes c ON p.cliente_id = c.id
                        WHERE pg.usuario_id = '{$usuarioId}'
                        AND pg.eliminado = 0
                        AND pg.estado = 'Cobrado'
                        AND p.usuario_id = '{$usuarioId}'
                        AND p.eliminado = 0
                        AND c.usuario_id = '{$usuarioId}'
                        AND c.eliminado = 0
                    ) AS total_cobrado";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        $totalFacturado = (float) ($registro['total_facturado'] ?? 0);
        $totalCobrado = (float) ($registro['total_cobrado'] ?? 0);
        $porCobrar = max($totalFacturado - $totalCobrado, 0);

        return [
            'cobrado_mes' => (float) ($registro['cobrado_mes'] ?? 0),
            'por_cobrar' => $porCobrar,
            'total_ingresos' => $totalCobrado
        ];
    }

    public static function proyectosPorEstado($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $estados = [
            'Pendiente' => 0,
            'En proceso' => 0,
            'En revisión' => 0,
            'Entregado' => 0,
            'Pausado' => 0,
            'Cancelado' => 0
        ];

        $query = "SELECT estado, COUNT(*) AS total
                  FROM proyectos
                  WHERE usuario_id = '{$usuarioId}'
                  AND eliminado = 0
                  GROUP BY estado";

        $resultado = self::$db->query($query);

        while ($registro = $resultado->fetch_assoc()) {
            if (array_key_exists($registro['estado'], $estados)) {
                $estados[$registro['estado']] = (int) $registro['total'];
            }
        }

        return $estados;
    }

    public static function proximasEntregas($usuarioId, $limite = 5)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $limite = (int) $limite;
        $hoy = date('Y-m-d');

        $query = "SELECT 
                p.id AS proyecto_id,
                p.nombre AS proyecto_nombre,
                p.fecha_entrega AS fecha_entrega,
                p.estado AS estado,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa
              FROM proyectos p
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.usuario_id = '{$usuarioId}'
              AND c.eliminado = 0
              AND p.fecha_entrega IS NOT NULL
              AND p.fecha_entrega >= '{$hoy}'
              AND p.estado NOT IN ('Entregado', 'Cancelado')
              ORDER BY p.fecha_entrega ASC
              LIMIT {$limite}";

        return self::consultarSQL($query);
    }

    public static function clientesPorMes($usuarioId, $anio)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $anio = (int) $anio;

        $meses = [
            1 => ['nombre' => 'Enero', 'corto' => 'Ene', 'total' => 0],
            2 => ['nombre' => 'Febrero', 'corto' => 'Feb', 'total' => 0],
            3 => ['nombre' => 'Marzo', 'corto' => 'Mar', 'total' => 0],
            4 => ['nombre' => 'Abril', 'corto' => 'Abr', 'total' => 0],
            5 => ['nombre' => 'Mayo', 'corto' => 'May', 'total' => 0],
            6 => ['nombre' => 'Junio', 'corto' => 'Jun', 'total' => 0],
            7 => ['nombre' => 'Julio', 'corto' => 'Jul', 'total' => 0],
            8 => ['nombre' => 'Agosto', 'corto' => 'Ago', 'total' => 0],
            9 => ['nombre' => 'Septiembre', 'corto' => 'Sep', 'total' => 0],
            10 => ['nombre' => 'Octubre', 'corto' => 'Oct', 'total' => 0],
            11 => ['nombre' => 'Noviembre', 'corto' => 'Nov', 'total' => 0],
            12 => ['nombre' => 'Diciembre', 'corto' => 'Dic', 'total' => 0]
        ];

        $query = "SELECT 
                MONTH(creado) AS mes,
                COUNT(*) AS total
              FROM clientes
              WHERE usuario_id = '{$usuarioId}'
              AND eliminado = 0
              AND YEAR(creado) = '{$anio}'
              GROUP BY MONTH(creado)
              ORDER BY MONTH(creado) ASC";

        $resultado = self::$db->query($query);

        while ($registro = $resultado->fetch_assoc()) {
            $mes = (int) $registro['mes'];

            if (isset($meses[$mes])) {
                $meses[$mes]['total'] = (int) $registro['total'];
            }
        }

        return $meses;
    }

    public static function aniosClientes($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT DISTINCT YEAR(creado) AS anio
              FROM clientes
              WHERE usuario_id = '{$usuarioId}'
              AND eliminado = 0
              AND creado IS NOT NULL
              ORDER BY anio DESC";

        $resultado = self::$db->query($query);

        $anios = [];

        while ($registro = $resultado->fetch_assoc()) {
            if (!empty($registro['anio'])) {
                $anios[] = (int) $registro['anio'];
            }
        }

        if (empty($anios)) {
            $anios[] = (int) date('Y');
        }

        return $anios;
    }
}
