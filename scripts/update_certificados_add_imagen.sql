-- Add optional image column for certificados
ALTER TABLE certificados
  ADD COLUMN imagen VARCHAR(255) NULL AFTER tipo;

-- Optional: backfill existing rows with NULL (no-op but explicit)
-- UPDATE certificados SET imagen = NULL WHERE imagen IS NULL;
