/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `configuracion_notificaciones`;
DROP TABLE IF EXISTS `configuracion_preferencias`;
DROP TABLE IF EXISTS `notas`;
DROP TABLE IF EXISTS `pago_adjuntos`;
DROP TABLE IF EXISTS `pagos`;
DROP TABLE IF EXISTS `proyectos`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `tarea_adjuntos`;
DROP TABLE IF EXISTS `tareas`;
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `nombre` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `empresa` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(180) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ciudad` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `identificacion` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_cliente` varchar(40) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fuente_contacto` varchar(40) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_en` datetime DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_clientes_usuarios` (`usuario_id`),
  CONSTRAINT `fk_clientes_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `configuracion_notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tareas_vencidas` tinyint(1) DEFAULT '1',
  `tareas_hoy` tinyint(1) DEFAULT '1',
  `tareas_proximas` tinyint(1) DEFAULT '1',
  `pagos_vencidos` tinyint(1) DEFAULT '1',
  `pagos_proximos` tinyint(1) DEFAULT '1',
  `proyectos_atrasados` tinyint(1) DEFAULT '1',
  `proyectos_proximos` tinyint(1) DEFAULT '1',
  `obligaciones_proximas` tinyint(1) DEFAULT '0',
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `configuracion_notificaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `configuracion_preferencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `formato_fecha` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'dd_mm_yyyy',
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `configuracion_preferencias_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `proyecto_id` int DEFAULT NULL,
  `cliente_id` int DEFAULT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `contenido` text COLLATE utf8mb4_general_ci NOT NULL,
  `color` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'amarillo',
  `fija` tinyint(1) NOT NULL DEFAULT '0',
  `protegida` tinyint(1) NOT NULL DEFAULT '1',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_en` datetime DEFAULT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notas_usuarios` (`usuario_id`),
  KEY `fk_notas_proyectos` (`proyecto_id`),
  KEY `fk_notas_clientes` (`cliente_id`),
  CONSTRAINT `fk_notas_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_proyectos` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pago_adjuntos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `pago_id` int NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ruta` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `peso` int DEFAULT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_en` datetime DEFAULT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pago_adjuntos_usuarios` (`usuario_id`),
  KEY `fk_pago_adjuntos_pagos` (`pago_id`),
  CONSTRAINT `fk_pago_adjuntos_pagos` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pago_adjuntos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `proyecto_id` int NOT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pendiente',
  `fecha_pago` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `referencia` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `monto_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descripcion` text COLLATE utf8mb4_general_ci,
  `notas_internas` text COLLATE utf8mb4_general_ci,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_en` datetime DEFAULT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pagos_usuarios` (`usuario_id`),
  KEY `fk_pagos_proyectos` (`proyecto_id`),
  CONSTRAINT `fk_pagos_proyectos` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pagos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `proyectos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `prioridad` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pendiente',
  `tipo_cobro` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `objetivos` text COLLATE utf8mb4_general_ci,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_en` datetime DEFAULT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_proyectos_usuarios` (`usuario_id`),
  KEY `fk_proyectos_clientes` (`cliente_id`),
  CONSTRAINT `fk_proyectos_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_proyectos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tarea_adjuntos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tarea_id` int NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ruta` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `peso` int DEFAULT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_en` datetime DEFAULT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tarea_adjuntos_usuarios` (`usuario_id`),
  KEY `fk_tarea_adjuntos_tareas` (`tarea_id`),
  CONSTRAINT `fk_tarea_adjuntos_tareas` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tarea_adjuntos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tareas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `proyecto_id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_limite` date DEFAULT NULL,
  `prioridad` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pendiente',
  `avance` int NOT NULL DEFAULT '0',
  `descripcion` text COLLATE utf8mb4_general_ci,
  `objetivo` text COLLATE utf8mb4_general_ci,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `eliminado_en` datetime DEFAULT NULL,
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tareas_usuarios` (`usuario_id`),
  KEY `fk_tareas_proyectos` (`proyecto_id`),
  CONSTRAINT `fk_tareas_proyectos` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_tareas_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `confirmado` tinyint(1) NOT NULL DEFAULT '0',
  `rol_id` int NOT NULL DEFAULT '1',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `creado` datetime DEFAULT CURRENT_TIMESTAMP,
  `actualizado` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuarios_roles` (`rol_id`),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `clientes` (`id`, `usuario_id`, `nombre`, `apellido`, `empresa`, `telefono`, `email`, `direccion`, `ciudad`, `identificacion`, `tipo_cliente`, `fuente_contacto`, `estado`, `eliminado`, `eliminado_en`, `observaciones`, `creado`, `actualizado`) VALUES
(1, 1, 'Cesar ', 'Abarca', 'AUTOMAGIC S.A.S.', '0967990710', 'automagic202412@gmail.com', 'Cdla. Garzota', 'Guayaquil', '0993392598001', 'Recurrente', 'Otro', 1, 0, NULL, 'Régimen\r\nRIMPE - EMPRENDEDOR\r\nInicio de actividades\r\n06/08/2024\r\nObligado a llevar contabilidad\r\nSi', '2026-08-03 17:57:53', '2026-08-03 20:22:41'),
(2, 1, 'ROMINA BELEN', 'RIOS CARDENAS', 'ROMINA RIOS ', '0983964263', 'riosr4323@gmail.com', 'Barrio: COOP 5 DE DICIEMBRE Número: SOLAR 16 Conjunto: PASCUALES Número de piso: PB Manzana: 692 Referencia: A TRES CUADRAS DEL TERMINAL DE PASCUALES', 'Guayaquil', '0931901102001', 'Recurrente', 'Otro', 1, 0, NULL, 'Régimen GENERAL | Inicio de actividades 01/11/2023 | Obligado a llevar contabilidad No |\r\n', '2026-08-03 21:12:52', '2026-08-03 21:14:18');
INSERT INTO `configuracion_notificaciones` (`id`, `usuario_id`, `tareas_vencidas`, `tareas_hoy`, `tareas_proximas`, `pagos_vencidos`, `pagos_proximos`, `proyectos_atrasados`, `proyectos_proximos`, `obligaciones_proximas`, `creado`, `actualizado`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '2026-08-03 17:51:30', '2026-08-03 17:51:30');
INSERT INTO `configuracion_preferencias` (`id`, `usuario_id`, `formato_fecha`, `creado`, `actualizado`) VALUES
(1, 1, 'dd_mm_yyyy ', '2026-08-03 17:51:48', '2026-08-03 17:51:48');
INSERT INTO `notas` (`id`, `usuario_id`, `proyecto_id`, `cliente_id`, `titulo`, `contenido`, `color`, `fija`, `protegida`, `eliminado`, `eliminado_en`, `creado`, `actualizado`) VALUES
(1, 1, 1, 1, 'Firma Electronica', 'Clave de Firma Electronica\r\n\r\nauto2024FIR', 'rosa', 0, 1, 0, NULL, '2026-08-03 18:05:58', '2026-08-03 18:05:58'),
(2, 1, 2, 1, 'SRI ACCESOS', 'RUC: 0993392598001\r\nCLAVE: Auto2024magic.*', 'amarillo', 0, 1, 0, NULL, '2026-08-03 19:09:21', '2026-08-03 19:09:21'),
(3, 1, 4, 2, 'Firma Electronica', 'Clave Firma Electronica\r\nDalia2024', 'amarillo', 0, 1, 0, NULL, '2026-08-03 21:19:34', '2026-08-03 21:19:34');

INSERT INTO `pagos` (`id`, `usuario_id`, `proyecto_id`, `metodo_pago`, `estado`, `fecha_pago`, `fecha_vencimiento`, `referencia`, `monto_total`, `monto_pagado`, `descripcion`, `notas_internas`, `eliminado`, `eliminado_en`, `creado`, `actualizado`) VALUES
(1, 1, 1, 'Efectivo', 'Cobrado', '2025-12-02', '2025-12-02', 'Emision de firma electronica con RUC para facturacion', '20.00', '20.00', '', '', 0, NULL, '2026-08-03 18:04:30', '2026-08-03 18:04:30'),
(2, 1, 3, 'Efectivo', 'Cobrado', '2026-07-31', '2026-07-31', 'Facturacion de Enero a Marzo', '40.00', '40.00', '', '', 0, NULL, '2026-08-03 21:08:06', '2026-08-03 21:08:06'),
(3, 1, 4, 'Efectivo', 'Cobrado', '2026-07-15', '2026-07-15', 'Emision de firma electronica con RUC para facturacion', '22.00', '22.00', '', '', 0, NULL, '2026-08-03 22:25:17', '2026-08-03 22:25:17');
INSERT INTO `proyectos` (`id`, `usuario_id`, `cliente_id`, `nombre`, `fecha_inicio`, `fecha_entrega`, `valor_total`, `prioridad`, `estado`, `tipo_cobro`, `descripcion`, `objetivos`, `observaciones`, `eliminado`, `eliminado_en`, `creado`, `actualizado`) VALUES
(1, 1, 1, 'Firma Electronica Anual 2025 -2026', '2025-12-02', '2025-12-02', '20.00', 'Alta', 'Entregado', 'Fijo', 'Emision de firma electronica con RUC para facturacion', 'Emision de firma electronica con RUC para facturacion', '', 0, NULL, '2026-08-03 18:01:40', '2026-08-03 18:23:42'),
(2, 1, 1, 'Contibilidad Cliente 2026', '2025-12-02', '2026-12-02', '100.00', 'Media', 'En proceso', 'Por avance', 'Proyecto enfocado a la contabilida del cliente', 'Documentar, Obtener y Organizar toda la informacion referente al cliente para llevar la contabilidad ', '', 0, NULL, '2026-08-03 18:27:24', '2026-08-03 20:32:29'),
(3, 1, 1, 'Facturacion 2026', '2026-01-01', '2026-12-31', '40.00', 'Baja', 'En proceso', 'Por avance', 'Facturacion Mensual Mediante el facturador del sri', 'Facturacion Mensual Mediante el facturador del sri', '', 0, NULL, '2026-08-03 20:58:33', '2026-08-03 21:07:11'),
(4, 1, 2, 'Firma Electronica Anual 2026-2027', '2026-07-15', '2026-07-15', '22.00', 'Media', 'Entregado', 'Fijo', 'Realizacion de Tramite para firma Electronica Anual ', 'Realizacion de Tramite para firma Electronica Anual ', '', 0, NULL, '2026-08-03 21:17:42', '2026-08-03 21:17:42');
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'user', 'Usuario registrado sin permisos administrativos'),
(2, 'freelancer', 'Usuario autorizado para gestionar clientes, proyectos, tareas, pagos y notas'),
(3, 'admin', 'Administrador general del sistema');
INSERT INTO `tarea_adjuntos` (`id`, `usuario_id`, `tarea_id`, `nombre_original`, `nombre_archivo`, `ruta`, `tipo`, `peso`, `eliminado`, `eliminado_en`, `creado`, `actualizado`) VALUES
(1, 1, 1, '20453737_0926666140.p12', 'adjunto_6a711e341d9c28.05544095.p12', 'C:\\Users\\ASUS VIVOBOOK\\Desktop\\PROYECTOS\\appfreelancer\\controllers/../storage/uploads/clientes/cesar-abarca/proyectos/firma-electronica-anual/tareas/emision-de-firma-electronica/adjunto_6a711e341d9c28.05544095.p12', 'application/x-pkcs12', 4013, 0, NULL, '2026-08-03 18:03:16', '2026-08-03 18:03:16'),
(2, 1, 2, 'RCR1785799697939890.pdf', 'adjunto_6a71244fc760a7.59073663.pdf', 'C:\\Users\\ASUS VIVOBOOK\\Desktop\\PROYECTOS\\appfreelancer\\controllers/../storage/uploads/clientes/cesar-abarca/proyectos/contibilidad-cliente/tareas/ruc/adjunto_6a71244fc760a7.59073663.pdf', 'application/pdf', 10035, 0, NULL, '2026-08-03 18:29:19', '2026-08-03 18:29:19'),
(3, 1, 8, 'EMITIDO AGOSTO.pdf', 'adjunto_6a7147ccdd0546.85942842.pdf', 'C:\\Users\\ASUS VIVOBOOK\\Desktop\\PROYECTOS\\appfreelancer\\controllers/../storage/uploads/clientes/cesar-abarca/proyectos/facturacion-2026/tareas/factura-agosto/adjunto_6a7147ccdd0546.85942842.pdf', 'application/pdf', 21591, 0, NULL, '2026-08-03 21:00:44', '2026-08-03 21:00:44'),
(4, 1, 9, '23674949_identity_0931901102.p12', 'adjunto_6a714c107b3e85.62620418.p12', 'C:\\Users\\ASUS VIVOBOOK\\Desktop\\PROYECTOS\\appfreelancer\\controllers/../storage/uploads/clientes/romina-belen-rios-cardenas/proyectos/firma-electronica-anual-2026-2027/tareas/firma-electronica-anual/adjunto_6a714c107b3e85.62620418.p12', 'application/x-pkcs12', 3933, 0, NULL, '2026-08-03 21:18:56', '2026-08-03 21:18:56');
INSERT INTO `tareas` (`id`, `usuario_id`, `proyecto_id`, `nombre`, `fecha_limite`, `prioridad`, `estado`, `avance`, `descripcion`, `objetivo`, `observaciones`, `eliminado`, `eliminado_en`, `creado`, `actualizado`) VALUES
(1, 1, 1, 'Emision de Firma Electronica', '2025-12-02', 'Alta', 'Completada', 100, 'Emision de firma electronica con RUC para facturacion', 'Emision de firma electronica con RUC para facturacion', '', 0, NULL, '2026-08-03 18:02:23', '2026-08-03 18:02:23'),
(2, 1, 2, 'RUC', '2025-12-02', 'Media', 'Completada', 100, 'Registro Único de Contribuyentes', 'Registro Único de Contribuyentes', '', 0, NULL, '2026-08-03 18:29:01', '2026-08-03 18:29:01'),
(3, 1, 2, '1021 - DECLARACIÓN DE IMPUESTO A LA RENTA SOCIEDADES', '2026-08-06', 'Media', 'En proceso', 50, 'Realizar DECLARACIÓN DE IMPUESTO A LA RENTA SOCIEDADES Anual', 'buscar inmgresos, egresos y retenciones del periodo a declarar', 'Pendiente Corregir declaracion ya que se subio en 0', 0, NULL, '2026-08-03 20:25:18', '2026-08-03 22:26:40'),
(4, 1, 2, 'REPORTE DE BENEFICIARIOS FINALES Y DE COMPOSICION SOCIETARIA REBEFICS ANUAL', '2026-08-06', 'Media', 'Completada', 100, 'Realizar el REPORTE DE BENEFICIARIOS FINALES Y DE COMPOSICION SOCIETARIA REBEFICS ANUAL', 'REPORTE DE BENEFICIARIOS FINALES Y DE COMPOSICION SOCIETARIA REBEFICS ANUAL', '', 0, NULL, '2026-08-03 20:28:27', '2026-08-03 20:28:27'),
(5, 1, 2, 'ANEXO DE DIVIDENDOS, UTILIDADES O BENEFICIOS - ADI', '2026-08-06', 'Media', 'Completada', 100, 'Realizar ANEXO DE DIVIDENDOS, UTILIDADES O BENEFICIOS - ADI A entregar Anual', 'ANEXO DE DIVIDENDOS, UTILIDADES O BENEFICIOS - ADI', '', 0, NULL, '2026-08-03 20:30:47', '2026-08-03 20:30:47'),
(6, 1, 2, 'ANEXO TRANSACCIONAL SIMPLIFICADO', '2026-08-06', 'Media', 'En proceso', 0, 'Realizar ANEXO TRANSACCIONAL SIMPLIFICADO JULIO Mensual', 'ANEXO TRANSACCIONAL SIMPLIFICADO', '', 0, NULL, '2026-08-03 20:32:10', '2026-08-03 20:32:10'),
(7, 1, 2, '2021 - DECLARACIÓN SEMESTRAL IVA', '2026-08-06', 'Media', 'Completada', 100, 'Realizar 2021 - DECLARACIÓN SEMESTRAL IVA semestral', '2021 - DECLARACIÓN SEMESTRAL IVA', '', 0, NULL, '2026-08-03 20:55:57', '2026-08-03 20:55:57'),
(8, 1, 3, 'Factura Agosto', '2026-08-03', 'Baja', 'Anulada', 100, 'Realice facturacion a Planifikt', 'MANTENIMIENTO COMPLETO CORRECTIVO DE AUTOMOVILES por $10222.22', 'El cliente pidio anular la factura ', 0, NULL, '2026-08-03 21:00:16', '2026-08-03 21:01:45'),
(9, 1, 4, 'Firma Electronica Anual', '2026-07-15', 'Media', 'Completada', 100, 'Realizacion de Tramite para firma Electronica Anual ', 'Realizacion de Tramite para firma Electronica Anual ', '', 0, NULL, '2026-08-03 21:18:11', '2026-08-03 21:18:11');
INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `avatar`, `password`, `token`, `confirmado`, `rol_id`, `estado`, `creado`, `actualizado`) VALUES
(1, ' Edinson', 'Aguirre', 'correo@correo.com', '4b422de0bed472104eac8fdfb61bbff2.jpg', '$2y$10$SzixJeys7F3EUw6.eGrZkOSSIZaqnI/NXnPDgHr31yOMENUvaHW8q', '', 1, 3, 1, '2026-07-15 23:40:25', '2026-07-31 22:53:58'),
(2, ' Irene', 'Peña', 'correo1@correo.com', '1227032841b1a2004ff1469febe879b4.jpg', '$2y$10$boY60dkGFNR.lKtsIsO0u.cOwrVmQlvPOhU3plo840L4uh7fmDKWK', '', 1, 2, 1, '2026-07-31 22:50:13', '2026-08-01 00:30:04');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;