-- Migration: add 'motivo' column to clientes table
-- Run this against your MySQL database used by the app (e.g., via phpMyAdmin or mysql CLI)

ALTER TABLE clientes
  ADD COLUMN motivo TEXT NULL AFTER correo;

-- After running, reload the app. Existing inserts will leave this column NULL.
-- If you want a default empty string instead, change to: ADD COLUMN motivo TEXT NOT NULL DEFAULT '';
