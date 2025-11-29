-- Script para crear la tabla `contador`
-- Mapea los campos solicitados a nombres de columnas válidos en SQL
-- Uso: ejecutar en MySQL / MariaDB (utf8mb4)

CREATE TABLE IF NOT EXISTS `contador` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nro` VARCHAR(50) NOT NULL COMMENT 'N\u00b0',
  `comercio` VARCHAR(255) NOT NULL,
  `nom_contador` VARCHAR(255) DEFAULT NULL,
  `titular_tlf` VARCHAR(100) DEFAULT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  `telefono_actu` VARCHAR(50) DEFAULT NULL,
  `link` VARCHAR(512) DEFAULT NULL,
  `usuario` VARCHAR(150) DEFAULT NULL,
  `contrasena` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: He usado nombres de columna sin caracteres especiales:
-- "N°" -> `nro`
-- "contraseña" -> `contrasena`
-- Si prefieres otros nombres, dímelo y ajusto el script.
