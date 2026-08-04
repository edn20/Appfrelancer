<?php

namespace Model;

class Reporte extends ActiveRecord
{
    public $id;
    public $nombre;
    public $apellido;
    public $empresa;
    public $email;
    public $telefono;
    public $estado;
    public $tipo_cliente;

    public $cliente_id;
    public $cliente_nombre;
    public $cliente_apellido;
    public $cliente_empresa;

    public $proyecto_id;
    public $proyecto_nombre;
    public $proyecto_estado;
    public $proyecto_prioridad;
    public $fecha_inicio;
    public $fecha_entrega;
    public $valor_total;

    public $total_proyectos;
    public $proyectos_activos;
    public $proyectos_entregados;
    public $total_facturado;
    public $total_cobrado;
    public $saldo_pendiente;
    public $total_tareas;
    public $tareas_pendientes;
    public $tareas_completadas;

    public static function resumenGeneral($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                    (
                        SELECT COUNT(*)
                        FROM clientes
                        WHERE usuario_id = '{$usuarioId}'
                        AND eliminado = 0
                    ) AS total_clientes,

                    (
                        SELECT COUNT(*)
                        FROM proyectos
                        WHERE usuario_id = '{$usuarioId}'
                        AND eliminado = 0
                    ) AS total_proyectos,

                    (
                        SELECT COUNT(*)
                        FROM tareas
                        WHERE usuario_id = '{$usuarioId}'
                        AND eliminado = 0
                    ) AS total_tareas,

                    (
                        SELECT COUNT(*)
                        FROM pagos
                        WHERE usuario_id = '{$usuarioId}'
                        AND eliminado = 0
                    ) AS total_pagos";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return [
            'total_clientes' => (int) ($registro['total_clientes'] ?? 0),
            'total_proyectos' => (int) ($registro['total_proyectos'] ?? 0),
            'total_tareas' => (int) ($registro['total_tareas'] ?? 0),
            'total_pagos' => (int) ($registro['total_pagos'] ?? 0)
        ];
    }

    public static function reporteFinanciero($usuarioId, $meses = 6)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $meses = (int) $meses;
        $fechaInicio = date('Y-m-01', strtotime("-" . ($meses - 1) . " months"));
        $fechaFin = date('Y-m-t');

        $query = "SELECT
                COALESCE(SUM(resumen.valor_total), 0) AS total_facturado,
                COALESCE(SUM(resumen.total_cobrado), 0) AS total_cobrado,
                COALESCE(SUM(resumen.total_por_confirmar), 0) AS total_por_confirmar,
                COALESCE(SUM(GREATEST(resumen.valor_total - resumen.total_cobrado, 0)), 0) AS saldo_pendiente
              FROM (
                    SELECT
                        p.id,
                        p.valor_total,
                        COALESCE(SUM(CASE 
                            WHEN pg.estado = 'Cobrado'
                            THEN pg.monto_pagado 
                            ELSE 0 
                        END), 0) AS total_cobrado,
                        COALESCE(SUM(CASE 
                            WHEN pg.estado = 'Por confirmar'
                            THEN pg.monto_pagado 
                            ELSE 0 
                        END), 0) AS total_por_confirmar
                    FROM proyectos p
                    INNER JOIN clientes c ON p.cliente_id = c.id
                    LEFT JOIN pagos pg 
                        ON pg.proyecto_id = p.id
                        AND pg.usuario_id = '{$usuarioId}'
                        AND pg.eliminado = 0
                        AND pg.fecha_pago BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
                    WHERE p.usuario_id = '{$usuarioId}'
                    AND p.eliminado = 0
                    AND c.usuario_id = '{$usuarioId}'
                    AND c.eliminado = 0
                    GROUP BY p.id
              ) AS resumen";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return [
            'total_facturado' => (float) ($registro['total_facturado'] ?? 0),
            'total_cobrado' => (float) ($registro['total_cobrado'] ?? 0),
            'total_por_confirmar' => (float) ($registro['total_por_confirmar'] ?? 0),
            'saldo_pendiente' => (float) ($registro['saldo_pendiente'] ?? 0)
        ];
    }

    public static function reporteClientes($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                    c.id AS cliente_id,
                    c.nombre AS cliente_nombre,
                    c.apellido AS cliente_apellido,
                    c.empresa AS cliente_empresa,
                    c.email,
                    c.telefono,
                    c.estado,
                    c.tipo_cliente,
                    COUNT(DISTINCT p.id) AS total_proyectos,
                    COALESCE(SUM(DISTINCT p.valor_total), 0) AS total_facturado,
                    COALESCE((
                        SELECT SUM(pg.monto_pagado)
                        FROM pagos pg
                        INNER JOIN proyectos pp ON pg.proyecto_id = pp.id
                        WHERE pp.cliente_id = c.id
                        AND pp.usuario_id = '{$usuarioId}'
                        AND pp.eliminado = 0
                        AND pg.usuario_id = '{$usuarioId}'
                        AND pg.eliminado = 0
                        AND pg.estado = 'Cobrado'
                    ), 0) AS total_cobrado
                  FROM clientes c
                  LEFT JOIN proyectos p 
                    ON p.cliente_id = c.id 
                    AND p.usuario_id = '{$usuarioId}'
                    AND p.eliminado = 0
                  WHERE c.usuario_id = '{$usuarioId}'
                  AND c.eliminado = 0
                  GROUP BY c.id
                  ORDER BY total_facturado DESC, c.id DESC
                  LIMIT 10";

        $clientes = self::consultarSQL($query);

        foreach ($clientes as $cliente) {
            $cliente->saldo_pendiente = max(
                (float) $cliente->total_facturado - (float) $cliente->total_cobrado,
                0
            );
        }

        return $clientes;
    }

    public static function reporteProyectos($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $query = "SELECT
                    p.id AS proyecto_id,
                    p.nombre AS proyecto_nombre,
                    p.estado AS proyecto_estado,
                    p.prioridad AS proyecto_prioridad,
                    p.fecha_inicio,
                    p.fecha_entrega,
                    p.valor_total,
                    c.id AS cliente_id,
                    c.nombre AS cliente_nombre,
                    c.apellido AS cliente_apellido,
                    c.empresa AS cliente_empresa,
                    COUNT(DISTINCT t.id) AS total_tareas,
                    SUM(CASE WHEN t.estado = 'Pendiente' THEN 1 ELSE 0 END) AS tareas_pendientes,
                    SUM(CASE WHEN t.estado = 'Completada' THEN 1 ELSE 0 END) AS tareas_completadas,
                    COALESCE(SUM(CASE 
                        WHEN pg.estado = 'Cobrado' AND pg.eliminado = 0 
                        THEN pg.monto_pagado 
                        ELSE 0 
                    END), 0) AS total_cobrado
                  FROM proyectos p
                  INNER JOIN clientes c ON p.cliente_id = c.id
                  LEFT JOIN tareas t 
                    ON t.proyecto_id = p.id
                    AND t.usuario_id = '{$usuarioId}'
                    AND t.eliminado = 0
                  LEFT JOIN pagos pg 
                    ON pg.proyecto_id = p.id
                    AND pg.usuario_id = '{$usuarioId}'
                    AND pg.eliminado = 0
                  WHERE p.usuario_id = '{$usuarioId}'
                  AND p.eliminado = 0
                  AND c.usuario_id = '{$usuarioId}'
                  AND c.eliminado = 0
                  GROUP BY p.id
                  ORDER BY p.id DESC
                  LIMIT 10";

        $proyectos = self::consultarSQL($query);

        foreach ($proyectos as $proyecto) {
            $proyecto->saldo_pendiente = max(
                (float) $proyecto->valor_total - (float) $proyecto->total_cobrado,
                0
            );
        }

        return $proyectos;
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

    public static function ingresosMensuales($usuarioId, $meses = 6)
    {
        $usuarioId = self::$db->escape_string($usuarioId);

        $meses = (int) $meses;
        $fechaInicio = date('Y-m-01', strtotime("-" . ($meses - 1) . " months"));
        $fechaFin = date('Y-m-t');

        $query = "SELECT
                DATE_FORMAT(fecha_pago, '%Y-%m') AS periodo,
                YEAR(fecha_pago) AS anio,
                MONTH(fecha_pago) AS mes,
                COALESCE(SUM(CASE WHEN estado = 'Cobrado' THEN monto_pagado ELSE 0 END), 0) AS total_cobrado,
                COALESCE(SUM(CASE WHEN estado = 'Por confirmar' THEN monto_pagado ELSE 0 END), 0) AS total_por_confirmar,
                COUNT(*) AS total_pagos
              FROM pagos
              WHERE usuario_id = '{$usuarioId}'
              AND eliminado = 0
              AND fecha_pago IS NOT NULL
              AND fecha_pago BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
              GROUP BY periodo, anio, mes
              ORDER BY periodo ASC";

        $resultado = self::$db->query($query);

        $datosBD = [];

        while ($registro = $resultado->fetch_assoc()) {
            $datosBD[$registro['periodo']] = [
                'periodo' => $registro['periodo'],
                'anio' => (int) $registro['anio'],
                'mes' => (int) $registro['mes'],
                'nombre_mes' => self::nombreMes((int) $registro['mes']) . ' ' . $registro['anio'],
                'total_cobrado' => (float) $registro['total_cobrado'],
                'total_por_confirmar' => (float) $registro['total_por_confirmar'],
                'total_pagos' => (int) $registro['total_pagos']
            ];
        }

        $mesesCompletos = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = strtotime("-{$i} months");
            $periodo = date('Y-m', $fecha);
            $numeroMes = (int) date('n', $fecha);
            $anio = (int) date('Y', $fecha);

            if (isset($datosBD[$periodo])) {
                $mesesCompletos[] = $datosBD[$periodo];
            } else {
                $mesesCompletos[] = [
                    'periodo' => $periodo,
                    'anio' => $anio,
                    'mes' => $numeroMes,
                    'nombre_mes' => self::nombreMes($numeroMes) . ' ' . $anio,
                    'total_cobrado' => 0,
                    'total_por_confirmar' => 0,
                    'total_pagos' => 0
                ];
            }
        }

        return $mesesCompletos;
    }


    public static function analisisIngresosMensuales($ingresosMensuales)
    {
        $mayor = null;
        $menor = null;
        $totalPeriodo = 0;
        $mesesConIngresos = 0;

        foreach ($ingresosMensuales as $mes) {
            $total = (float) $mes['total_cobrado'];
            $totalPeriodo += $total;

            if ($total > 0) {
                $mesesConIngresos++;
            }

            if ($mayor === null || $total > (float) $mayor['total_cobrado']) {
                $mayor = $mes;
            }

            if ($total > 0 && ($menor === null || $total < (float) $menor['total_cobrado'])) {
                $menor = $mes;
            }
        }

        return [
            'mayor_ingreso' => $mayor,
            'menor_ingreso' => $menor,
            'total_periodo' => $totalPeriodo,
            'promedio_mensual' => count($ingresosMensuales) > 0 ? $totalPeriodo / count($ingresosMensuales) : 0,
            'meses_con_ingresos' => $mesesConIngresos,
            'maximo_grafico' => $mayor ? max((float) $mayor['total_cobrado'], 1) : 1
        ];
    }

    private static function nombreMes($mes)
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        return $meses[$mes] ?? '';
    }
}
