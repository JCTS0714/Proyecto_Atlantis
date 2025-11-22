-- Migration: add archivado flag to reuniones
ALTER TABLE `reuniones` 
  ADD COLUMN `archivado` TINYINT(1) NOT NULL DEFAULT 0 AFTER `estado`,
  ADD COLUMN `archivado_por` INT NULL AFTER `archivado`,
  ADD COLUMN `archivado_en` DATETIME NULL AFTER `archivado_por`;

-- Optional: add index to archivado for faster queries
CREATE INDEX idx_reuniones_archivado ON reuniones(archivado);
