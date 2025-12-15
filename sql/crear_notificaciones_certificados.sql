-- Crea la tabla para registrar envíos de notificaciones de certificados
CREATE TABLE IF NOT EXISTS `notificaciones_certificados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `certificado_id` int(11) NOT NULL,
  `tipo` tinyint(3) NOT NULL COMMENT 'Número de días antes (7 o 3)',
  `fecha_envio` datetime NOT NULL,
  `usuario_id` int(11) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_certificado_fecha` (`certificado_id`, `fecha_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: ejecutar este script en la base de datos de producción para activar el registro de notificaciones.
