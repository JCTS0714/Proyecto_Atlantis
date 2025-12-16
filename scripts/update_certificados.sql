-- Agregar columna 'tipo' a la tabla 'certificados'
ALTER TABLE certificados
ADD COLUMN tipo ENUM('OSE', 'PSE') NOT NULL DEFAULT 'OSE';