-- Migration: add incidencias.columna_backlog
-- Date: 2025-12-17
-- Notes:
-- - Safe to run multiple times (checks information_schema first)
-- - If the column already exists, it will not attempt to re-add it.

SET @db := DATABASE();

SET @col_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db
    AND TABLE_NAME = 'incidencias'
    AND COLUMN_NAME = 'columna_backlog'
);

SET @sql := IF(
  @col_exists = 0,
  "ALTER TABLE incidencias ADD COLUMN columna_backlog VARCHAR(50) DEFAULT 'En proceso'",
  "SELECT 'incidencias.columna_backlog already exists' AS info"
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Backfill for existing rows (keeps current values if already set)
UPDATE incidencias
SET columna_backlog = CASE
  WHEN LOWER(prioridad) = 'alta' THEN 'En proceso'
  WHEN LOWER(prioridad) = 'media' THEN 'Validado'
  WHEN LOWER(prioridad) = 'baja' THEN 'Terminado'
  ELSE 'En proceso'
END
WHERE columna_backlog IS NULL OR columna_backlog = '';
